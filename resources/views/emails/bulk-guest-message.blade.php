<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
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
                            <h1 style="margin:0;font-size:26px;line-height:1.3;color:#ffffff;">{{ $title }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 24px;">
                            <div style="font-size:15px;line-height:1.6;color:#2d3748;">
                                {!! $body !!}
                            </div>
                            <p style="margin:20px 0 0;font-size:15px;line-height:1.6;color:#2d3748;">Thanks,<br>
                                <strong>The {{ config('app.name') }} Team</strong>
                            </p>
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
                            <p style="margin:0;">© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
