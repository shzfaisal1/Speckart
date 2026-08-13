<!DOCTYPE html>
<html lang="en">
<head>
    <title>Barcode Label</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        /* Set exact page size */
        @page {
            size: {{$barcode_setting->paper_width}}mm {{$barcode_setting->paper_height}}mm;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .barcode-card {
            width: {{ $barcode_setting->paper_width }}mm;
            height: {{ $barcode_setting->paper_height }}mm;
            box-sizing: border-box;
            display: flex;
            align-items: center;
            font-size: 10px;
            font-weight: bold;
            border: 1px solid #ddd;
            padding: 0 5mm;
        }
        
        .barcode-left {
            margin: 8px;
        }

        .barcode-left p {
            margin: 0;
            font-weight: bold;
        }

        .barcode-right {
            text-align: center;
        }

        .barcode-right p {
            margin: 0;
            font-weight: bold;
        }

        .barcode-right img {
            height: 4mm;
            width: 18mm;
        }
    </style>
</head>
<body>
    <section>
        @foreach($barcodes as $barcode) 
        <div class="barcode-card">
            <div class="barcode-left">
                <p>SPECKARTS</p>
                <p>Rs {{ $barcode['retail_price'] }}</p>
                <p>{{ $barcode['product_code'] }}</p>
            </div>
            <div class="barcode-right">
                <p>Speckarts.com</p>
                <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($barcode['product_code'], 'C128') }}" alt="barcode">
                <p>{{ $barcode['barcode_no'] }}</p>
            </div>
        </div>
        @endforeach
    </section>
</body>
</html>
