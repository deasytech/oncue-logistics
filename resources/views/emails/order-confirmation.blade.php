<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - {{ $payment->reference }}</title>
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
                            @if ($payment->status === 'paid' || $payment->status === 'completed')
                                <h1 style="margin:0;font-size:26px;line-height:1.3;">🎉 Order Confirmed!</h1>
                                <p style="margin:10px 0 0;font-size:15px;opacity:0.9;">Thank you for your order. We're
                                    preparing your package.</p>
                            @else
                                <h1 style="margin:0;font-size:26px;line-height:1.3;">📝 Order Pending</h1>
                                <p style="margin:10px 0 0;font-size:15px;opacity:0.9;">Please complete your payment to
                                    finalize your order.</p>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 24px;">
                            <p style="margin:0 0 16px;font-size:16px;">Dear
                                <strong>{{ $payment->customer_info['name'] ?? ($payment->customer->full_name ?? 'Valued Customer') }}</strong>,
                            </p>
                            <p style="margin:0 0 20px;font-size:15px;color:#4a5568;line-height:1.6;">
                                We're excited to confirm that we've received your order. Your order reference is
                                <strong>{{ $payment->reference }}</strong>.
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f7fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:20px;">
                                <tr>
                                    <td style="padding:16px 18px;">
                                        <h3 style="margin:0 0 12px;font-size:18px;color:#2d3748;">Order Details</h3>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:6px 0;font-size:14px;color:#4a5568;">Order Reference
                                                </td>
                                                <td align="right" style="padding:6px 0;font-size:14px;color:#2d3748;">
                                                    {{ $payment->reference }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;font-size:14px;color:#4a5568;">Order Date</td>
                                                <td align="right" style="padding:6px 0;font-size:14px;color:#2d3748;">
                                                    {{ $payment->created_at->format('F j, Y g:i A') }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;font-size:14px;color:#4a5568;">Payment Method
                                                </td>
                                                <td align="right" style="padding:6px 0;font-size:14px;color:#2d3748;">
                                                    Offline Payment</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;font-size:14px;color:#4a5568;">Payment Status
                                                </td>
                                                <td align="right"
                                                    style="padding:6px 0;font-size:14px;color:#d69e2e;font-weight:bold;">
                                                    Pending Payment</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f7fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:20px;">
                                <tr>
                                    <td style="padding:16px 18px;">
                                        <h3 style="margin:0 0 12px;font-size:18px;color:#2d3748;">Customer Information
                                        </h3>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:6px 0;font-size:14px;color:#4a5568;">Name</td>
                                                <td align="right" style="padding:6px 0;font-size:14px;color:#2d3748;">
                                                    {{ $payment->customer_info['name'] ?? ($payment->customer->full_name ?? 'N/A') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;font-size:14px;color:#4a5568;">Email</td>
                                                <td align="right" style="padding:6px 0;font-size:14px;color:#2d3748;">
                                                    {{ $payment->customer_info['email'] ?? ($payment->customer->email ?? 'N/A') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;font-size:14px;color:#4a5568;">Phone</td>
                                                <td align="right" style="padding:6px 0;font-size:14px;color:#2d3748;">
                                                    {{ $payment->customer_info['phone'] ?? ($payment->customer->phone ?? 'N/A') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;font-size:14px;color:#4a5568;">Address</td>
                                                <td align="right" style="padding:6px 0;font-size:14px;color:#2d3748;">
                                                    {{ $payment->customer_info['address'] ?? ($payment->customer->location ?? 'N/A') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <h3 style="margin:0 0 12px;font-size:18px;color:#2d3748;">Order Items</h3>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;margin-bottom:20px;">
                                <tr style="background:#f7fafc;">
                                    <th align="left" style="padding:10px 12px;font-size:13px;color:#4a5568;">Item</th>
                                    <th align="center" style="padding:10px 12px;font-size:13px;color:#4a5568;">Qty</th>
                                    <th align="right" style="padding:10px 12px;font-size:13px;color:#4a5568;">Unit</th>
                                    <th align="right" style="padding:10px 12px;font-size:13px;color:#4a5568;">Total
                                    </th>
                                </tr>
                                @foreach ($items as $item)
                                    <tr>
                                        <td style="padding:12px;font-size:13px;color:#2d3748;">
                                            <strong>{{ $item->package->name ?? 'Custom Package' }}</strong>
                                            @if ($item->material || $item->color || $item->font)
                                                <div style="font-size:12px;color:#718096;margin-top:4px;">
                                                    @if ($item->material)
                                                        Material: {{ $item->material->name }}
                                                    @endif
                                                    @if ($item->color)
                                                        • Color: {{ $item->color->name }}
                                                    @endif
                                                    @if ($item->font)
                                                        • Font: {{ $item->font->name }}
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td align="center" style="padding:12px;font-size:13px;color:#2d3748;">
                                            {{ $item->quantity }}</td>
                                        <td align="right" style="padding:12px;font-size:13px;color:#2d3748;">
                                            ₦{{ number_format($item->unit_price, 2) }}</td>
                                        <td align="right" style="padding:12px;font-size:13px;color:#2d3748;">
                                            ₦{{ number_format($item->total_price, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr style="background:#f7fafc;">
                                    <td colspan="3"
                                        style="padding:12px;font-size:13px;font-weight:bold;color:#2d3748;">Total Amount
                                    </td>
                                    <td align="right"
                                        style="padding:12px;font-size:13px;font-weight:bold;color:#8b0055;">
                                        ₦{{ number_format($payment->amount, 2) }}</td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#fff8e1;border-radius:10px;border:1px solid #ffe0b2;margin-bottom:20px;">
                                <tr>
                                    <td style="padding:16px 18px;font-size:14px;color:#8b5e00;">
                                        <strong>Payment Instructions:</strong> Please make payment within 24 hours to
                                        confirm your order.
                                        <div style="margin:12px 0 0;font-size:13px;color:#8b5e00;">
                                            Bank Name: FCMB<br>
                                            Account Name: ON CUE LOGISTICS LIMITED<br>
                                            Account Number: 2007399144<br>
                                            Amount: ₦{{ number_format($payment->amount, 2) }}
                                        </div>
                                        <p style="margin:12px 0 0;font-size:13px;color:#8b5e00;">
                                            Please use your order reference <strong>{{ $payment->reference }}</strong>
                                            as the payment description/remark.
                                        </p>
                                        <p style="margin:12px 0 0;font-size:13px;color:#8b5e00;">After making payment,
                                            send proof to:</p>
                                        <p style="margin:6px 0 0;font-size:13px;color:#8b5e00;">📧
                                            payments@oncuelogistics.com</p>
                                        <p style="margin:4px 0 0;font-size:13px;color:#8b5e00;">📱 +234 803 456 7890
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#e6fffa;border-radius:10px;border:1px solid #b2f5ea;margin-bottom:20px;">
                                <tr>
                                    <td style="padding:16px 18px;font-size:14px;color:#234e52;">
                                        <strong>What Happens Next?</strong>
                                        <ol style="margin:8px 0 0;padding-left:18px;">
                                            <li>Make payment using the details above</li>
                                            <li>Send proof of payment to us</li>
                                            <li>We'll confirm your payment within 2 hours</li>
                                            <li>Your order will be processed and prepared</li>
                                            <li>You'll receive tracking information once ready</li>
                                        </ol>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f7fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:10px;">
                                <tr>
                                    <td style="padding:16px 18px;font-size:14px;color:#4a5568;">
                                        <strong>Need Help?</strong><br>
                                        Phone: +234 803 456 7890<br>
                                        Email: info@oncuelogistics.com<br>
                                        Hours: Monday - Saturday, 9:00 AM - 6:00 PM
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:12px 0 0;font-size:14px;color:#4a5568;">Thank you for choosing Oncue! We
                                look forward to serving you.</p>
                            <p style="margin:8px 0 0;font-size:14px;color:#4a5568;">Best regards,<br><strong>The Oncue
                                    Team</strong></p>
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
                            <p style="margin:0 0 6px;">This email was sent to
                                {{ $payment->customer_info['email'] ?? '' }}</p>
                            <p style="margin:0;">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
