<?php

if (!function_exists('numberToWords')) {
    function numberToWords($number)
    {
        $formatter = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        return ucfirst($formatter->format($number));
    }
}

if (!function_exists('extractFirstValidImageName')) {
    /**
     * Safely extract the first valid image filename from raw DB inputs (strings, JSON arrays, comma lists).
     *
     * @param mixed $raw
     * @return string|null Clean valid filename or null
     */
    function extractFirstValidImageName($raw)
    {
        if (empty($raw)) {
            return null;
        }

        if (is_array($raw)) {
            foreach ($raw as $item) {
                $candidate = extractFirstValidImageName($item);
                if ($candidate) return $candidate;
            }
            return null;
        }

        if (!is_string($raw)) {
            return null;
        }

        $trimmed = trim($raw);

        // Filter out obvious empty/null representations
        if (in_array(strtolower($trimmed), ['', '[]', '[ ]', '{}', 'null', 'undefined', 'n/a', 'none', 'false', '0'])) {
            return null;
        }

        // If it's a JSON array string e.g. ["img1.png", "img2.png"] or [""]
        if ((str_starts_with($trimmed, '[') && str_ends_with($trimmed, ']')) || str_starts_with($trimmed, '{')) {
            $decoded = json_decode($trimmed, true);
            if (is_array($decoded)) {
                return extractFirstValidImageName($decoded);
            }
        }

        // If comma-separated
        if (str_contains($trimmed, ',')) {
            $parts = explode(',', $trimmed);
            foreach ($parts as $p) {
                $candidate = extractFirstValidImageName($p);
                if ($candidate) return $candidate;
            }
        }

        // Clean quotes and brackets
        $clean = trim($trimmed, "\"' \t\n\r\0\x0B[]");
        if (empty($clean) || in_array(strtolower($clean), ['', '[]', 'null', 'undefined', 'n/a', 'none'])) {
            return null;
        }

        // Must be a URL or contain an image file extension / dot
        if (preg_match('/^(https?:|\/\/|data:image)/i', $clean)) {
            return $clean;
        }

        if (preg_match('/\.(png|jpe?g|webp|svg|gif|avif|bmp)$/i', $clean)) {
            return $clean;
        }

        // If it contains a dot with any extension
        if (str_contains($clean, '.')) {
            return $clean;
        }

        return null;
    }
}

if (!function_exists('getProductImageUrl')) {
    /**
     * Dynamically resolve the absolute public URL for any product image across all upload folder structures.
     *
     * @param object|array|int|string|null $product Product model, DB stdClass, or array
     * @param string|null $imageName Optional specific image filename
     * @return string Valid public URL to the product image
     */
    function getProductImageUrl($product, $imageName = null)
    {
        if (empty($product)) {
            return asset('website/assets/img/bg/Eyeglasses7.png');
        }

        // If an ID was passed, convert to object from DB if possible
        if (is_numeric($product) || (is_string($product) && !is_object($product))) {
            $productObj = \Illuminate\Support\Facades\DB::table('tbl_product_code')
                ->where('id', $product)
                ->orWhere('product_id', $product)
                ->orWhere('product_code', $product)
                ->first();
            if ($productObj) {
                $product = $productObj;
            } else {
                $product = (object) ['id' => $product];
            }
        } elseif (is_array($product)) {
            $product = (object) $product;
        }

        $typeLower = strtolower(trim($product->product_type ?? ''));
        $typeField = strtolower(trim($product->Type ?? ''));
        $catField  = strtolower(trim($product->category_name ?? ''));
        $combinedType = $typeLower . ' ' . $typeField . ' ' . $catField;

        // Default fallback based on type
        $isSunglass = str_contains($combinedType, 'sunglass') || str_contains($combinedType, 'goggle');
        $defaultFallback = $isSunglass
            ? asset('website/assets/img/bg/Sunglasses1.png')
            : asset('website/assets/img/bg/Eyeglasses7.png');

        // Candidate folder IDs (parent code, product_id, product_code, id)
        $folderIds = array_unique(array_filter([
            $product->parent_product_code ?? null,
            $product->product_id ?? null,
            $product->product_code ?? null,
            $product->id ?? null,
        ]));

        // Folder types prioritized by product category
        if ($isSunglass) {
            $folderTypes = ['goggles', 'frame', 'product', 'glass', 'other'];
        } elseif (str_contains($combinedType, 'frame') || str_contains($combinedType, 'eye')) {
            $folderTypes = ['frame', 'product', 'glass', 'goggles', 'other'];
        } elseif (str_contains($combinedType, 'glass')) {
            $folderTypes = ['glass', 'frame', 'product', 'goggles', 'other'];
        } elseif (str_contains($combinedType, 'lens') || str_contains($combinedType, 'contact')) {
            $folderTypes = ['lens', 'product', 'solution', 'other', 'frame'];
        } elseif (str_contains($combinedType, 'solution')) {
            $folderTypes = ['solution', 'product', 'lens', 'other'];
        } else {
            $folderTypes = ['frame', 'goggles', 'product', 'glass', 'lens', 'solution', 'other'];
        }

        if ($typeLower && !in_array($typeLower, $folderTypes)) {
            array_unshift($folderTypes, $typeLower);
        }

        // 1. Determine image filename if explicitly provided or in product fields
        $validImageName = extractFirstValidImageName($imageName);

        if (empty($validImageName)) {
            $validImageName = extractFirstValidImageName($product->main_image ?? null);
        }
        if (empty($validImageName)) {
            $validImageName = extractFirstValidImageName($product->product_image ?? null);
        }
        if (empty($validImageName)) {
            $validImageName = extractFirstValidImageName($product->image ?? null);
        }

        // 2. If DB has no image filename, scan physical folders on disk for this product
        if (empty($validImageName)) {
            $imgExts = ['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg'];
            foreach ($folderTypes as $ft) {
                foreach ($folderIds as $fid) {
                    $possibleDirs = [
                        public_path("uploads/{$ft}/product/{$fid}"),
                        public_path("uploads/{$ft}/{$fid}"),
                        public_path("uploads/product/{$fid}"),
                    ];
                    foreach ($possibleDirs as $pDir) {
                        if (is_dir($pDir)) {
                            $scanned = @scandir($pDir) ?: [];
                            $imgFiles = [];
                            foreach ($scanned as $sf) {
                                if ($sf === '.' || $sf === '..') continue;
                                $ext = strtolower(pathinfo($sf, PATHINFO_EXTENSION));
                                if (in_array($ext, $imgExts)) {
                                    $imgFiles[] = $sf;
                                }
                            }
                            if (!empty($imgFiles)) {
                                // Prefer files with 'main' or '0' or first file
                                usort($imgFiles, function($a, $b) {
                                    $aMain = str_contains(strtolower($a), 'main') ? 0 : 1;
                                    $bMain = str_contains(strtolower($b), 'main') ? 0 : 1;
                                    if ($aMain !== $bMain) return $aMain - $bMain;
                                    return strcmp($a, $b);
                                });
                                $rel = str_replace(public_path() . DIRECTORY_SEPARATOR, '', $pDir . DIRECTORY_SEPARATOR . $imgFiles[0]);
                                $rel = str_replace('\\', '/', $rel);
                                return asset($rel);
                            }
                        }
                    }
                }
            }

            // Also check if sibling variants in DB have a valid image
            if (!empty($product->parent_product_code)) {
                $sibling = \Illuminate\Support\Facades\DB::table('tbl_product_code')
                    ->where('parent_product_code', $product->parent_product_code)
                    ->where('id', '!=', $product->id ?? 0)
                    ->whereNotNull('main_image')
                    ->where('main_image', '!=', '')
                    ->where('main_image', '!=', '[]')
                    ->first();
                if ($sibling) {
                    $siblingImg = extractFirstValidImageName($sibling->main_image);
                    if ($siblingImg) {
                        $validImageName = $siblingImg;
                    }
                }
            }
        }

        if (empty($validImageName)) {
            return $defaultFallback;
        }

        // 3. Direct external or data URLs
        if (preg_match('/^(https?:|\/\/|data:image)/i', $validImageName)) {
            return $validImageName;
        }

        $clean = ltrim($validImageName, '/\\');

        // 4. Direct public paths
        if (file_exists(public_path($clean))) {
            return asset($clean);
        }
        if (file_exists(public_path('uploads/' . $clean))) {
            return asset('uploads/' . $clean);
        }
        if (file_exists(public_path('website/' . $clean))) {
            return asset('website/' . $clean);
        }

        // 5. Search candidate folders on disk: uploads/{folderType}/product/{folderId}/{clean}
        foreach ($folderTypes as $ft) {
            foreach ($folderIds as $fid) {
                $candidate = "uploads/{$ft}/product/{$fid}/{$clean}";
                if (file_exists(public_path($candidate))) {
                    return asset($candidate);
                }
                $candidate2 = "uploads/{$ft}/{$fid}/{$clean}";
                if (file_exists(public_path($candidate2))) {
                    return asset($candidate2);
                }
            }
            $candidate3 = "uploads/{$ft}/{$clean}";
            if (file_exists(public_path($candidate3))) {
                return asset($candidate3);
            }
        }

        // Check uploads/product/{folderId}/{clean}
        foreach ($folderIds as $fid) {
            $candidate4 = "uploads/product/{$fid}/{$clean}";
            if (file_exists(public_path($candidate4))) {
                return asset($candidate4);
            }
        }

        // 6. If target folder exists on disk, check if file exists or return candidate
        if (preg_match('/\.(png|jpe?g|webp|svg|gif|avif|bmp)$/i', $clean)) {
            $primaryType = $folderTypes[0];
            $primaryId   = !empty($product->parent_product_code) ? $product->parent_product_code : ($product->product_id ?? $product->id ?? null);
            if ($primaryId && is_dir(public_path("uploads/{$primaryType}/product/{$primaryId}"))) {
                return asset("uploads/{$primaryType}/product/{$primaryId}/{$clean}");
            }
        }

        return $defaultFallback;
    }
}

if (!function_exists('getProductGalleryImages')) {
    /**
     * Dynamically retrieve all image URLs for a product gallery.
     *
     * @param object|array|int|string|null $product
     * @return array List of valid absolute image URLs
     */
    function getProductGalleryImages($product)
    {
        if (empty($product)) {
            return [asset('website/assets/img/bg/Eyeglasses7.png')];
        }

        if (is_numeric($product) || (is_string($product) && !is_object($product))) {
            $productObj = \Illuminate\Support\Facades\DB::table('tbl_product_code')
                ->where('id', $product)
                ->orWhere('product_id', $product)
                ->orWhere('product_code', $product)
                ->first();
            if ($productObj) {
                $product = $productObj;
            } else {
                $product = (object) ['id' => $product];
            }
        } elseif (is_array($product)) {
            $product = (object) $product;
        }

        $images = [];
        $mainUrl = getProductImageUrl($product);
        if (!empty($mainUrl) && !str_contains($mainUrl, '/bg/')) {
            $images[] = $mainUrl;
        }

        // 1. Check product_image column (JSON or comma string)
        if (!empty($product->product_image)) {
            $rawList = is_array($product->product_image)
                ? $product->product_image
                : (json_decode($product->product_image, true) ?: explode(',', $product->product_image));
            if (is_array($rawList)) {
                foreach ($rawList as $item) {
                    $candidateName = extractFirstValidImageName($item);
                    if ($candidateName) {
                        $url = getProductImageUrl($product, $candidateName);
                        if (!in_array($url, $images) && !str_contains($url, '/bg/')) {
                            $images[] = $url;
                        }
                    }
                }
            }
        }

        // 2. Scan physical folders on disk for all product images
        $folderIds = array_unique(array_filter([
            $product->parent_product_code ?? null,
            $product->product_id ?? null,
            $product->product_code ?? null,
            $product->id ?? null,
        ]));

        $folderTypes = ['frame', 'goggles', 'product', 'glass', 'lens', 'solution', 'other'];
        $imgExtensions = ['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg'];

        foreach ($folderTypes as $ft) {
            foreach ($folderIds as $fid) {
                $dirs = [
                    public_path("uploads/{$ft}/product/{$fid}"),
                    public_path("uploads/{$ft}/{$fid}"),
                    public_path("uploads/product/{$fid}"),
                ];
                foreach ($dirs as $dir) {
                    if (is_dir($dir)) {
                        $files = @scandir($dir) ?: [];
                        foreach ($files as $f) {
                            if ($f === '.' || $f === '..') continue;
                            $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                            if (in_array($ext, $imgExtensions)) {
                                $rel = str_replace(public_path() . DIRECTORY_SEPARATOR, '', $dir . DIRECTORY_SEPARATOR . $f);
                                $rel = str_replace('\\', '/', $rel);
                                $url = asset($rel);
                                if (!in_array($url, $images)) {
                                    $images[] = $url;
                                }
                            }
                        }
                    }
                }
            }
        }

        // Sibling variants in DB sharing parent_product_code
        if (!empty($product->parent_product_code)) {
            $siblingImages = \Illuminate\Support\Facades\DB::table('tbl_product_code')
                ->where('parent_product_code', $product->parent_product_code)
                ->where('id', '!=', $product->id ?? 0)
                ->where('status', 1)
                ->where('is_b2c', 1)
                ->pluck('main_image')
                ->toArray();
            foreach ($siblingImages as $sImg) {
                $cName = extractFirstValidImageName($sImg);
                if ($cName) {
                    $url = getProductImageUrl($product, $cName);
                    if (!in_array($url, $images) && !str_contains($url, '/bg/')) {
                        $images[] = $url;
                    }
                }
            }
        }

        if (empty($images)) {
            $images = [$mainUrl ?: asset('website/assets/img/bg/Eyeglasses7.png')];
        }

        return $images;
    }
}