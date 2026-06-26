<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Your Payment</title>
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
                            <h1 style="margin:0;font-size:26px;line-height:1.3;color:#ffffff;">⏳ Payment Incomplete</h1>
                            <p style="margin:12px 0 0;font-size:14px;opacity:0.9;">Your order is reserved — complete payment to confirm it.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 24px;">
                            <p style="margin:0 0 16px;font-size:16px;">Dear <strong>{{ $guest->title }} {{ $guest->last_name }}</strong>,</p>
                            <p style="margin:0 0 20px;font-size:15px;color:#4a5568;">
                                It looks like your payment for <strong>{{ $event->name }}</strong> was not completed.
                                Your fabric selection is still reserved — click the button below to complete your payment and secure your order.
                            </p>

                            <div style="text-align:center;margin:24px 0;">
                                @if ($fabricSelection->guest->events()->first() && $fabricSelection->guest->events()->first()->pivot)
                                    <a href="{{ route('payment.summary', ['token' => $fabricSelection->guest->events()->first()->pivot->rsvp_token, 'order_id' => $fabricSelection->id]) }}"
                                        style="display:inline-block;background:#8b0055;color:#ffffff;text-decoration:none;padding:16px 32px;border-radius:6px;font-weight:bold;font-size:16px;">
                                        Complete Payment Now
                                    </a>
                                @endif
                            </div>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f7fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:20px;">
                                <tr>
                                    <td style="padding:16px 18px;">
                                        <h3 style="margin:0 0 12px;font-size:18px;color:#2d3748;">Your Order Summary</h3>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            @foreach ($fabricSelections as $fabric)
                                                <tr>
                                                    <td style="padding:8px 0;font-size:14px;color:#4a5568;">{{ $fabric['name'] }}</td>
                                                    <td align="right" style="padding:8px 0;font-size:14px;color:#8b0055;font-weight:bold;">
                                                        ₦{{ number_format($fabric['price'], 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @if ($fabricSelection->delivery_cost > 0)
                                                <tr>
                                                    <td style="padding:8px 0;font-size:14px;color:#4a5568;">
                                                        Delivery ({{ $deliveryZone['name'] ?? 'Selected Zone' }})
                                                    </td>
                                                    <td align="right" style="padding:8px 0;font-size:14px;color:#4a5568;">
                                                        ₦{{ number_format($fabricSelection->delivery_cost, 2) }}
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td style="padding-top:12px;border-top:1px solid #e2e8f0;font-size:15px;font-weight:bold;">
                                                    Total Amount
                                                </td>
                                                <td align="right" style="padding-top:12px;border-top:1px solid #e2e8f0;font-size:15px;font-weight:bold;color:#8b0055;">
                                                    ₦{{ number_format($totalAmount, 2) }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#fff8e1;border-radius:10px;border:1px solid #ffe0b2;margin-bottom:20px;">
                                <tr>
                                    <td style="padding:14px 18px;font-size:14px;color:#5a3e00;">
                                        If you did not intend to cancel, please complete your payment as soon as possible.
                                        If you need assistance, reply to this email or contact us directly.
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:18px 0 6px;text-align:center;font-size:14px;color:#4a5568;">Thank you for choosing <strong>{{ config('app.name') }}</strong>!</p>
                            <p style="margin:0;text-align:center;font-size:13px;color:#718096;">Best regards,<br><strong>The {{ config('app.name') }} Team</strong></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background:#f7fafc;padding:18px 24px;text-align:center;color:#718096;font-size:12px;">
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="border-top: 1px solid #e2e8f0; padding-top: 15px; margin-bottom: 15px;">
                                <tr>
                                    <td style="text-align: center; padding-bottom: 10px;">
                                        <span style="color: #8b0055; font-size: 14px; font-weight: bold;">Get in Touch</span>
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
                            <p style="margin:0 0 6px;">This email was sent to {{ $guest->email }}</p>
                            <p style="margin:0;">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
