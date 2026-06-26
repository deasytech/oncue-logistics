<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Your Event Has Been Activated - {{ config('app.name') }}</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 30px;">
    <div
        style="max-width: 600px; margin: auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <div style="background-color: #8b0055; color: white; padding: 20px; text-align: center;">
            <h1 style="margin: 0;">Event Activated!</h1>
        </div>
        <div style="padding: 30px;">
            <p>Hello {{ $event->customer->full_name }},</p>

            <p>Great news! Your event has been reviewed and is now <strong>active</strong>. You can now manage your guests and proceed with event preparation.</p>

            <div
                style="background-color: #f9f0f5; border-left: 4px solid #8b0055; border-radius: 4px; padding: 16px; margin: 20px 0;">
                <p style="margin: 0 0 8px 0; font-size: 13px; color: #666; text-transform: uppercase; letter-spacing: 0.5px;">Event Details</p>
                <p style="margin: 0 0 6px 0;"><strong>Event:</strong> {{ $event->name }}</p>
                @if ($event->event_date)
                    <p style="margin: 0 0 6px 0;"><strong>Date:</strong> {{ $event->event_date->format('F j, Y') }}</p>
                @endif
                @if ($event->location)
                    <p style="margin: 0 0 6px 0;"><strong>Location:</strong> {{ $event->location }}</p>
                @endif
            </div>

            <p>You can now log in to your dashboard to manage your guest list, track RSVPs, and organise event details.</p>

            <p style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/guests') }}"
                    style="display: inline-block; padding: 12px 24px; background-color: #8b0055; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;">
                    Go to Guest Management
                </a>
            </p>

            <p>If you have any questions or need assistance, please don't hesitate to reach out to us.</p>

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
