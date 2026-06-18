<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
</head>

<body style="margin:0;padding:0;background-color:#f4f5f7;font-family:Arial, sans-serif;color:#2d3748;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
        style="background-color:#f4f5f7;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" width="600"
                    style="width:100%;max-width:600px;background:#ffffff;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="background-color:#8b0055;padding:32px 24px;text-align:center;color:#ffffff;">
                            <h1 style="margin:0;font-size:26px;line-height:1.3;">Invoice</h1>
                            <p style="margin:10px 0 0;font-size:15px;opacity:0.9;">You have a new invoice from
                                {{ $companyName }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 24px;">
                            <p style="margin:0 0 16px;font-size:16px;">Dear
                                <strong>{{ $invoice->customer_name }}</strong>,
                            </p>
                            <p style="margin:0 0 20px;font-size:15px;color:#4a5568;line-height:1.6;">
                                Please find below the details of your invoice
                                <strong>#{{ $invoice->invoice_number }}</strong>.
                                @if ($invoice->due_date)
                                    <br>This invoice is due on
                                    <strong>{{ $invoice->due_date->format('F j, Y') }}</strong>.
                                @endif
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f7fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:20px;">
                                <tr>
                                    <td style="padding:16px 18px;">
                                        <h3 style="margin:0 0 12px;font-size:18px;color:#2d3748;">Invoice Details</h3>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:6px 0;font-size:14px;color:#4a5568;">Invoice Number
                                                </td>
                                                <td align="right" style="padding:6px 0;font-size:14px;color:#2d3748;">
                                                    {{ $invoice->invoice_number }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;font-size:14px;color:#4a5568;">Invoice Date
                                                </td>
                                                <td align="right" style="padding:6px 0;font-size:14px;color:#2d3748;">
                                                    {{ $invoice->created_at->format('F j, Y') }}</td>
                                            </tr>
                                            @if ($invoice->due_date)
                                                <tr>
                                                    <td style="padding:6px 0;font-size:14px;color:#4a5568;">Due Date
                                                    </td>
                                                    <td align="right"
                                                        style="padding:6px 0;font-size:14px;color:#2d3748;">
                                                        {{ $invoice->due_date->format('F j, Y') }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td style="padding:6px 0;font-size:14px;color:#4a5568;">Status</td>
                                                <td align="right" style="padding:6px 0;font-size:14px;color:#2d3748;">
                                                    {{ ucfirst($invoice->status) }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <h3 style="margin:0 0 12px;font-size:18px;color:#2d3748;">Invoice Items</h3>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;margin-bottom:20px;">
                                <tr style="background:#f7fafc;">
                                    <th align="left" style="padding:10px 12px;font-size:13px;color:#4a5568;">
                                        Description</th>
                                    <th align="center" style="padding:10px 12px;font-size:13px;color:#4a5568;">Qty</th>
                                    <th align="right" style="padding:10px 12px;font-size:13px;color:#4a5568;">Unit
                                        Price</th>
                                    <th align="right" style="padding:10px 12px;font-size:13px;color:#4a5568;">Amount
                                    </th>
                                </tr>
                                @foreach ($invoice->items as $item)
                                    <tr>
                                        <td style="padding:12px;font-size:13px;color:#2d3748;">
                                            {{ $item->description }}
                                        </td>
                                        <td align="center" style="padding:12px;font-size:13px;color:#2d3748;">
                                            {{ number_format($item->quantity, 2) }}</td>
                                        <td align="right" style="padding:12px;font-size:13px;color:#2d3748;">
                                            ₦{{ number_format($item->unit_price, 2) }}</td>
                                        <td align="right" style="padding:12px;font-size:13px;color:#2d3748;">
                                            ₦{{ number_format($item->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr style="background:#f7fafc;">
                                    <td colspan="3" align="right"
                                        style="padding:10px 12px;font-size:13px;color:#4a5568;">
                                        Subtotal</td>
                                    <td align="right" style="padding:10px 12px;font-size:13px;color:#2d3748;">
                                        ₦{{ number_format($invoice->subtotal, 2) }}</td>
                                </tr>
                                @if ($invoice->tax_amount > 0)
                                    <tr style="background:#f7fafc;">
                                        <td colspan="3" align="right"
                                            style="padding:10px 12px;font-size:13px;color:#4a5568;">
                                            Tax</td>
                                        <td align="right" style="padding:10px 12px;font-size:13px;color:#2d3748;">
                                            ₦{{ number_format($invoice->tax_amount, 2) }}</td>
                                    </tr>
                                @endif
                                @if ($invoice->discount_amount > 0)
                                    <tr style="background:#f7fafc;">
                                        <td colspan="3" align="right"
                                            style="padding:10px 12px;font-size:13px;color:#4a5568;">
                                            Discount</td>
                                        <td align="right" style="padding:10px 12px;font-size:13px;color:#2d3748;">
                                            -₦{{ number_format($invoice->discount_amount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr style="background:#8b0055;color:#ffffff;">
                                    <td colspan="3" align="right"
                                        style="padding:12px;font-size:14px;font-weight:bold;color:#ffffff;">Total Amount
                                    </td>
                                    <td align="right"
                                        style="padding:12px;font-size:14px;font-weight:bold;color:#ffffff;">
                                        ₦{{ number_format($invoice->total_amount, 2) }}</td>
                                </tr>
                            </table>

                            @if ($invoice->balance_due > 0)
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                    style="background:#e6fffa;border-radius:10px;border:1px solid #b2f5ea;margin-bottom:20px;">
                                    <tr>
                                        <td style="padding:20px;text-align:center;">
                                            <p style="margin:0 0 16px;font-size:16px;color:#234e52;">
                                                <strong>Amount Due:
                                                    ₦{{ number_format($invoice->balance_due, 2) }}</strong>
                                            </p>
                                            <a href="{{ $paymentUrl }}"
                                                style="display:inline-block;background-color:#8b0055;color:#ffffff;text-decoration:none;padding:14px 32px;border-radius:6px;font-size:16px;font-weight:bold;">
                                                Pay Now with Paystack
                                            </a>
                                            <p style="margin:12px 0 0;font-size:12px;color:#718096;">
                                                Click the button above to securely pay your invoice online.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            @else
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                    style="background:#d4edda;border-radius:10px;border:1px solid #c3e6cb;margin-bottom:20px;">
                                    <tr>
                                        <td style="padding:20px;text-align:center;">
                                            <p style="margin:0;font-size:16px;color:#155724;">
                                                <strong>This invoice has been paid in full. Thank you!</strong>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            @if ($invoice->notes)
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                    style="background:#f7fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:20px;">
                                    <tr>
                                        <td style="padding:16px 18px;font-size:14px;color:#4a5568;">
                                            <strong>Notes:</strong><br>
                                            {{ $invoice->notes }}
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f7fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:10px;">
                                <tr>
                                    <td style="padding:16px 18px;font-size:14px;color:#4a5568;">
                                        <strong>Need Help?</strong><br>
                                        Phone: +234 708 909 1600<br>
                                        Email: info@oncuelogistics.com<br>
                                        Hours: Monday - Saturday, 9:00 AM - 6:00 PM
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:12px 0 0;font-size:14px;color:#4a5568;">Thank you for your business!</p>
                            <p style="margin:8px 0 0;font-size:14px;color:#4a5568;">Best
                                regards,<br><strong>{{ $companyName }} Team</strong></p>
                        </td>
                    </tr>
                    <tr>
                        <td
                            style="background:#f7fafc;padding:18px 24px;text-align:center;color:#718096;font-size:12px;">
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="border-top: 1px solid #e2e8f0; padding-top: 15px; margin-bottom: 15px;">
                                <tr>
                                    <td style="text-align: center; padding-bottom: 10px;">
                                        <span style="color: #8b0055; font-size: 14px; font-weight: bold;">Get in
                                            Touch</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #718096; font-size: 12px; line-height: 1.8; text-align: center;">
                                        <div>📞 Phone: +234 708 909 1600</div>
                                        <div>🌐 Website: oncuelogistics.com</div>
                                        <div>📸 Instagram: @oncuelogistics</div>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin:0 0 6px;">This email was sent to {{ $invoice->customer_email }}</p>
                            <p style="margin:0;">© {{ date('Y') }} {{ $companyName }}. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
