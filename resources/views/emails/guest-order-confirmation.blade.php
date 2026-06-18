<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - {{ $fabricSelection->reference ?? 'Guest Order' }}</title>
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
                            @if ($fabricSelection->payment_status === 'paid' || $fabricSelection->payment_status === 'completed')
                                <h1 style="margin:0;font-size:26px;line-height:1.3;color:#ffffff;">🎉 Order Confirmed!
                                </h1>
                            @else
                                <h1 style="margin:0;font-size:26px;line-height:1.3;color:#ffffff;">📝 Order Pending</h1>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 24px;">
                            <p style="margin:0 0 16px;font-size:16px;">Dear <strong>{{ $guest->title }}
                                    {{ $guest->last_name }}</strong>,</p>
                            <p style="margin:0 0 20px;font-size:15px;color:#4a5568;">
                                We’re excited to confirm that we've received your fabric selection for
                                <strong>{{ $event->name }}</strong>.
                                Below is a summary of your order details.
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f7fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:20px;">
                                <tr>
                                    <td style="padding:16px 18px;">
                                        <h3 style="margin:0 0 12px;font-size:18px;color:#2d3748;">Selected Fabrics</h3>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            @foreach ($fabricSelections as $fabric)
                                                <tr>
                                                    <td style="padding:8px 0;font-size:14px;color:#4a5568;">
                                                        {{ $fabric['name'] }}</td>
                                                    <td align="right"
                                                        style="padding:8px 0;font-size:14px;color:#8b0055;font-weight:bold;">
                                                        ₦{{ number_format($fabric['price'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td
                                                    style="padding-top:12px;border-top:1px solid #e2e8f0;font-size:14px;font-weight:bold;">
                                                    Total Fabric Cost</td>
                                                <td align="right"
                                                    style="padding-top:12px;border-top:1px solid #e2e8f0;font-size:14px;font-weight:bold;color:#8b0055;">
                                                    ₦{{ number_format($fabricSelection->total_fabric_cost, 2) }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            @if ($fabricSelection->delivery_cost > 0)
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                    style="background:#f7fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:20px;">
                                    <tr>
                                        <td style="padding:16px 18px;">
                                            <h3 style="margin:0 0 12px;font-size:18px;color:#2d3748;">Delivery
                                                Information</h3>
                                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td style="padding:6px 0;font-size:14px;color:#4a5568;">Delivery
                                                        Zone</td>
                                                    <td align="right"
                                                        style="padding:6px 0;font-size:14px;color:#2d3748;">
                                                        {{ $deliveryZone['name'] ?? 'Selected Zone' }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding:6px 0;font-size:14px;color:#4a5568;">Delivery
                                                        Cost</td>
                                                    <td align="right"
                                                        style="padding:6px 0;font-size:14px;color:#2d3748;">
                                                        ₦{{ number_format($fabricSelection->delivery_cost, 2) }}</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f7fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:20px;">
                                <tr>
                                    <td style="padding:16px 18px;">
                                        <h3 style="margin:0 0 12px;font-size:18px;color:#2d3748;">Payment Information
                                        </h3>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:6px 0;font-size:14px;color:#4a5568;">Payment Method
                                                </td>
                                                <td align="right" style="padding:6px 0;font-size:14px;color:#2d3748;">
                                                    {{ ucfirst($fabricSelection->payment_method) }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;font-size:14px;color:#4a5568;">Payment Status
                                                </td>
                                                <td align="right" style="padding:6px 0;font-size:14px;color:#2d3748;">
                                                    {{ ucfirst($fabricSelection->payment_status) }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#8b0055;border-radius:10px;margin-bottom:20px;">
                                <tr>
                                    <td style="padding:16px 18px;text-align:center;color:#ffffff;">
                                        <div style="font-size:13px;opacity:0.9;">Total Amount</div>
                                        <div style="font-size:22px;font-weight:bold;margin-top:6px;">
                                            ₦{{ number_format($totalAmount, 2) }}</div>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f7fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:20px;">
                                <tr>
                                    <td style="padding:16px 18px;">
                                        <h3 style="margin:0 0 12px;font-size:18px;color:#2d3748;">Delivery Address</h3>
                                        <p style="margin:0;font-size:14px;color:#4a5568;line-height:1.5;">
                                            {{ $guest->address }}<br>
                                            {{ $guest->city->name ?? 'N/A' }}, {{ $guest->state->name ?? 'N/A' }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            @if ($fabricSelection->payment_method === 'online' && $fabricSelection->payment_status === 'pending')
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                    style="background:#fff8e1;border-radius:10px;border:1px solid #ffe0b2;margin-bottom:20px;">
                                    <tr>
                                        <td style="padding:16px 18px;font-size:14px;color:#8b0055;">
                                            <strong>Complete Your Payment:</strong> Please complete your payment to
                                            finalize your order. You will be redirected to our secure payment gateway.
                                        </td>
                                    </tr>
                                </table>

                                @if ($fabricSelection->guest->events()->first() && $fabricSelection->guest->events()->first()->pivot)
                                    <div style="text-align:center;margin:24px 0 20px;">
                                        <a href="{{ route('payment.summary', ['token' => $fabricSelection->guest->events()->first()->pivot->rsvp_token, 'order_id' => $fabricSelection->id]) }}"
                                            style="display:inline-block;background:#8b0055;color:#ffffff;text-decoration:none;padding:14px 28px;border-radius:6px;font-weight:bold;font-size:16px;">
                                            Complete Payment Now
                                        </a>
                                    </div>
                                @endif
                            @endif

                            @if ($fabricSelection->payment_method === 'offline')
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                    style="background:#ffc9ea;border-radius:10px;border:1px solid #fe30af;margin-bottom:20px;">
                                    <tr>
                                        <td style="padding:16px 18px;font-size:14px;">
                                            <h3 style="margin:0 0 12px;font-size:18px;color:#2d3748;">Bank Transfer
                                            </h3>
                                            <p style="margin:0;font-size:14px;color:#8b0055;line-height:1.5;">
                                                <strong>Account Name:</strong> On Cue Logistics Limited <br />
                                                <strong>Account Number:</strong> 2007399144 <br />
                                                <strong>Bank:</strong> FCMB
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            @endif

                            @if ($fabricSelection->guest->events()->first() && $fabricSelection->guest->events()->first()->pivot)
                                <div style="text-align:center;margin:24px 0 10px;">
                                    <a href="{{ route('rsvp.show', $fabricSelection->guest->events()->first()->pivot->rsvp_token) }}"
                                        style="display:inline-block;background:#8b0055;color:#ffffff;text-decoration:none;padding:12px 22px;border-radius:6px;font-weight:bold;font-size:14px;">
                                        View Your RSVP Details
                                    </a>
                                </div>
                            @endif

                            <p style="margin:18px 0 6px;text-align:center;font-size:14px;color:#4a5568;">Thank you for
                                choosing <strong>{{ config('app.name') }}</strong>!</p>
                            <p style="margin:0;text-align:center;font-size:13px;color:#718096;">Best
                                regards,<br><strong>The {{ config('app.name') }} Team</strong></p>
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
                            <p style="margin:0 0 6px;">This email was sent to {{ $guest->email }}</p>
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
