<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }} - Let's Create Amazing Events!</title>
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
                            <h1 style="margin:0;font-size:26px;line-height:1.3;">🎉 Welcome to
                                {{ config('app.name') }}!</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 24px;">
                            <p style="margin:0 0 16px;font-size:16px;">Hi <strong>{{ $customer->first_name }}</strong>,
                            </p>
                            <p style="margin:0 0 20px;font-size:15px;color:#4a5568;line-height:1.6;">
                                We're absolutely thrilled to have you join our community. Your journey to planning an
                                unforgettable event starts now.
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f7fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:20px;">
                                <tr>
                                    <td style="padding:16px 18px;">
                                        <h3 style="margin:0 0 12px;font-size:18px;color:#2d3748;">Your Next Steps</h3>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:8px 0;font-size:14px;color:#4a5568;">1. Set up your
                                                    account</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:8px 0;font-size:14px;color:#4a5568;">2. Create your
                                                    first event</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:8px 0;font-size:14px;color:#4a5568;">3. Add and
                                                    manage
                                                    guests</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:8px 0;font-size:14px;color:#4a5568;">4. Enjoy your
                                                    event day</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#f7fafc;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:20px;">
                                <tr>
                                    <td style="padding:16px 18px;">
                                        <h3 style="margin:0 0 12px;font-size:18px;color:#2d3748;">Everything You Need
                                            for
                                            Event Success</h3>
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:6px 0;font-size:14px;color:#4a5568;">📋 Event
                                                    Logistics</td>
                                                <td style="padding:6px 0;font-size:14px;color:#4a5568;">👥 Guest
                                                    Management</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;font-size:14px;color:#4a5568;">📱 RSVP Tracking
                                                </td>
                                                <td style="padding:6px 0;font-size:14px;color:#4a5568;">📊 Analytics
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0;font-size:14px;color:#4a5568;">🎨 Customized
                                                    Packages</td>
                                                <td style="padding:6px 0;font-size:14px;color:#4a5568;">🚚 Delivery
                                                    Services</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                                style="background:#404040;border-radius:10px;margin-bottom:20px;">
                                <tr>
                                    <td style="padding:18px 18px;text-align:center;color:#ffffff;">
                                        <p style="margin:0 0 10px;font-size:14px;opacity:0.9;">Set up your account in
                                            just 2 minutes and unlock all features</p>
                                        <a href="{{ $setupUrl }}"
                                            style="display:inline-block;background:#ffffff;color:#404040;text-decoration:none;padding:12px 22px;border-radius:25px;font-weight:bold;font-size:14px;">
                                            🚀 Set Up My Account Now
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 16px;font-size:14px;color:#4a5568;line-height:1.6;">
                                If you have any questions or need assistance, our dedicated support team is just an
                                email away.
                            </p>

                            <p style="margin:0;font-size:14px;color:#4a5568;">
                                Welcome aboard!<br>
                                <strong style="color:#8b0055;">The {{ config('app.name') }} Team</strong>
                            </p>
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
                            <p style="margin:0 0 6px;">You're receiving this because you recently joined
                                <strong>{{ config('app.name') }}</strong>.
                            </p>
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
