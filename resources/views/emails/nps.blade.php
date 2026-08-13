<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Feedback request</title>
</head>
<body>
  <p>Hi {{ $sale->cust_name ?? 'Customer' }},</p>

  <p>Thank you for your purchase on {{ \Carbon\Carbon::parse($sale->sale_date)->format('d M, Y') }}.</p>

  <p>We would love your feedback — it takes 1 minute:</p>

  <p>
    <a href="{{ $link }}" style="display:inline-block;padding:10px 16px;background:#1e88e5;color:#fff;text-decoration:none;border-radius:4px;">
      Give Feedback
    </a>
  </p>

  <p>Thanks,<br>Your Company</p>
</body>
</html>