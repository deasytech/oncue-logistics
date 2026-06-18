<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
</head>

<body
    style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f4f4f7; color: #51545e; margin: 0; padding: 0;">
    <div style="width: 100%; background-color: #f4f4f7; padding: 40px 0;">
        <div
            style="max-width: 600px; margin: 0 auto; background: #ffffff; padding: 40px; border-radius: 6px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);">
            <div style="text-align: center; padding-bottom: 24px;">
                <h1 style="margin: 0; font-size: 24px; color: #333333;">{{ $subject }}</h1>
            </div>
            <div>
                {!! $content !!}

                @if (isset($callToActionText) && isset($callToActionUrl))
                    <p style="text-align: center;">
                        <a href="{{ $callToActionUrl }}"
                            style="display: inline-block; margin-top: 24px; padding: 12px 24px; background-color: #C70085; color: #ffffff !important; text-decoration: none; font-weight: bold; border-radius: 4px;">{{ $callToActionText }}</a>
                    </p>
                @endif

                <p style="font-size: 16px; line-height: 1.5em;">Thanks,<br>
                    The {{ config('app.name') }} Team</p>
            </div>
            <div
                style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; font-size: 12px; color: #999999;">
                <div style="color: #C70085; font-weight: bold; margin-bottom: 10px;">Get in Touch</div>
                <div style="line-height: 1.8;">
                    <div>📞 Phone: +234 708 909 1600</div>
                    <div>🌐 Website: oncuelogistics.com</div>
                    <div>📸 Instagram: @oncuelogistics</div>
                </div>
            </div>
            <div style="margin-top: 20px; text-align: center; font-size: 12px; color: #999999;">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </div>
</body>

</html>
