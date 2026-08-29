<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Expired Voucher Refund Policy
    |--------------------------------------------------------------------------
    | Governs what a customer receives when they request a refund on an order
    | that generated a deferred Gold voucher, after that voucher has expired.
    |
    | Confirmed business decision: 'zero' — no refund if the voucher expired
    | unused. Customer received the promotional benefit window and chose not
    | to act within 30 days.
    |
    | Options:
    |   'zero' — Refund = ₹0 for the voucher portion (confirmed)
    |   'full' — Refund = Order #1 Paid in full (safer fallback)
    |
    */
    'expired_voucher_refund_policy' => env('EXPIRED_VOUCHER_REFUND_POLICY', 'zero'),

    /*
    |--------------------------------------------------------------------------
    | Gold Deferred Voucher Value (₹)
    |--------------------------------------------------------------------------
    | Single source of truth for the ₹2,600 amount.
    | Cart copy, DB record, and redemption math all read from here.
    | Change once here — not in multiple controllers and blade files.
    |
    */
    'gold_deferred_voucher_value' => env('GOLD_VOUCHER_VALUE', 2600.00),

    /*
    |--------------------------------------------------------------------------
    | Gold Deferred Voucher Validity (Days)
    |--------------------------------------------------------------------------
    */
    'gold_deferred_voucher_days' => env('GOLD_VOUCHER_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Instant Single-Pair Coupon Discount (₹)
    |--------------------------------------------------------------------------
    | The flat ₹1,500 instant discount for Choice C (Coupon SINGLE).
    | Distinct from the ₹2,600 deferred voucher — must never show same value.
    |
    */
    'single_pair_instant_discount' => env('SINGLE_PAIR_DISCOUNT', 1500.00),

    /*
    |--------------------------------------------------------------------------
    | Gold Deferred Voucher Code Prefix
    |--------------------------------------------------------------------------
    */
    'gold_voucher_code_prefix' => 'GV2600-',

];
