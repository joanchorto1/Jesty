<!DOCTYPE html>
<html>
<head>
    <title>Payroll Details</title>
</head>
<body>
<h1>Dear {{ $employee['name'] }},</h1>
<p>Please find your payroll details below:</p>
<ul>
    <li><strong>Amount:</strong> ${{ $payroll['net_pay'] }}</li>
    <li><strong>Date:</strong> {{ $payroll['payment_date'] }}</li>
</ul>
<p>Thank you,</p>
<p>Your Company</p>
</body>
</html>
