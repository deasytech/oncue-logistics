<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>You're Invited - {{ config('app.name') }}</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 30px;">
    <div
        style="max-width: 600px; margin: auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <div style="background-color: #8b0055; color: white; padding: 20px; text-align: center;">
            <h1>You're Invited!</h1>
        </div>
        <div style="padding: 20px;">
            <p>Hello {{ $guest->full_name }},</p>
            <p>You have been invited to the upcoming event
                <strong>"{{ $guest->events->first()->name ?? 'our special event' }}"</strong> on
                {{ $guest->events->first()->event_date?->format('F j, Y') ?? 'To be announced' }}.
            </p>
            <p>Kindly confirm your attendance using the link below:</p>
            <p style="text-align: center;">
                <a href="{{ url('/rsvp/' . $guest->events->first()->pivot->rsvp_token) }}"
                    style="display: inline-block; padding: 12px 20px; background-color: #8b0055; color: white; text-decoration: none; border-radius: 6px;">
                    Confirm RSVP
                </a>
            </p>
            <p style="text-align: center; font-size: 12px; color: #666;">
                RSVP expires:
                {{ \Carbon\Carbon::parse($guest->events->first()->pivot->rsvp_expires_at)->format('M d, Y h:i A') }}
            </p>
            <p style="text-align: center; font-size: 12px; color: #666;">
                Host: {{ $guest->customer_name }}
            </p>
            <p>If you have any questions, kindly contact us using the details below:</p>
            <p>Best regards,<br>Oncue Logistics</p>
            <div
                style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; font-size: 12px; color: #666;">
                <div style="color: #8b0055; font-weight: bold; margin-bottom: 8px;">Get in Touch</div>
                <div>📞 Phone: +234 708 909 1600</div>
                <div>🌐 Website: oncuelogistics.com</div>
                <div>📸 Instagram: @oncuelogistics</div>
            </div>
        </div>
    </div>
</body>

</html>
