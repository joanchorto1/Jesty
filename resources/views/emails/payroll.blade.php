<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payroll Details</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            background-color: #f9fafb;
            color: #111827;
        }

        .wrapper {
            padding: 32px 16px;
        }

        .card {
            max-width: 520px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 32px;
        }

        h1 {
            margin: 0 0 16px;
            font-size: 20px;
            font-weight: 600;
        }

        p {
            margin: 8px 0;
            color: #374151;
        }

        ul {
            margin: 16px 0 0;
            padding: 0;
            list-style: none;
            color: #374151;
        }

        li {
            padding: 6px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        li:last-child {
            border-bottom: none;
        }

        .footer {
            margin-top: 20px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
            font-size: 13px;
            color: #6b7280;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <h1>Hello {{ $employee['name'] }},</h1>
        <p>Please find your payroll details below:</p>
        <ul>
            <li><strong>Amount:</strong> ${{ $payroll['net_pay'] }}</li>
            <li><strong>Date:</strong> {{ $payroll['payment_date'] }}</li>
        </ul>
        <p class="footer">Thank you,<br>Your Company</p>
    </div>
</div>
</body>
</html>
