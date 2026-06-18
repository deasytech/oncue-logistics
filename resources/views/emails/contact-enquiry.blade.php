<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Enquiry - {{ config('app.name') }}</title>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; line-height: 1.6; color: #333333;">
    <!-- Gmail Compatible Email Template -->
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8f9fa;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <!-- Main Container -->
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background-color: #ffffff; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

                    <!-- Header Section -->
                    <tr>
                        <td style="background-color: #C70085; padding: 40px 30px; text-align: center;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="font-size: 48px; margin-bottom: 15px;">📧</td>
                                </tr>
                                <tr>
                                    <td align="center"
                                        style="color: #ffffff; font-size: 28px; font-weight: bold; margin: 0 0 10px 0;">
                                        New Contact Enquiry
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="color: #ffffff; font-size: 16px; opacity: 0.9;">
                                        You've received a new message from your website
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Content Section -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td
                                        style="font-size: 18px; color: #2c3e50; margin-bottom: 20px; font-weight: bold;">
                                        Enquiry Details
                                    </td>
                                </tr>

                                <!-- Contact Information -->
                                <tr>
                                    <td
                                        style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 25px; margin: 25px 0;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td
                                                    style="font-size: 16px; color: #495057; margin: 0 0 15px 0; font-weight: bold;">
                                                    Contact Information
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 10px 0;">
                                                    <table width="100%" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                            <td width="30%"
                                                                style="font-size: 14px; color: #6c757d; font-weight: bold;">
                                                                Name:</td>
                                                            <td width="70%" style="font-size: 14px; color: #495057;">
                                                                {{ $enquiry->name }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td width="30%"
                                                                style="font-size: 14px; color: #6c757d; font-weight: bold; padding-top: 10px;">
                                                                Email:</td>
                                                            <td width="70%"
                                                                style="font-size: 14px; color: #495057; padding-top: 10px;">
                                                                <a href="mailto:{{ $enquiry->email }}"
                                                                    style="color: #C70085; text-decoration: none;">{{ $enquiry->email }}</a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="30%"
                                                                style="font-size: 14px; color: #6c757d; font-weight: bold; padding-top: 10px;">
                                                                Phone:</td>
                                                            <td width="70%"
                                                                style="font-size: 14px; color: #495057; padding-top: 10px;">
                                                                <a href="tel:{{ $enquiry->phone }}"
                                                                    style="color: #C70085; text-decoration: none;">{{ $enquiry->phone }}</a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td width="30%"
                                                                style="font-size: 14px; color: #6c757d; font-weight: bold; padding-top: 10px;">
                                                                Subject:</td>
                                                            <td width="70%"
                                                                style="font-size: 14px; color: #495057; padding-top: 10px;">
                                                                {{ $enquiry->subject }}</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Message Content -->
                                <tr>
                                    <td
                                        style="background-color: #ffffff; border: 2px solid #C70085; border-radius: 8px; padding: 25px; margin: 25px 0;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td
                                                    style="font-size: 16px; color: #495057; margin: 0 0 15px 0; font-weight: bold;">
                                                    Message Content
                                                </td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="font-size: 14px; color: #555555; line-height: 1.8; padding: 15px; background-color: #f8f9fa; border-radius: 5px;">
                                                    {{ $enquiry->message }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Timestamp -->
                                <tr>
                                    <td style="padding: 20px 0; text-align: center;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="font-size: 12px; color: #6c757d; text-align: center;">
                                                    <strong>Received:</strong>
                                                    {{ $enquiry->created_at->format('F j, Y \a\t g:i A') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <!-- Action Section -->
                                <tr>
                                    <td
                                        style="text-align: center; padding: 30px 20px; background-color: #e3f2fd; border-radius: 10px; margin: 25px 0;">
                                        <table width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td
                                                    style="color: #1976d2; font-size: 14px; margin: 0 0 15px 0; font-weight: bold;">
                                                    📞 Ready to respond? Contact the sender directly using the details
                                                    above.
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color: #424242; font-size: 12px; padding-top: 10px;">
                                                    Remember to follow up promptly for the best customer experience.
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer Section -->
                    <tr>
                        <td
                            style="background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e9ecef;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding-bottom: 15px;">
                                        <table width="100%" cellpadding="0" cellspacing="0"
                                            style="border-top: 1px solid #dee2e6; padding-top: 15px;">
                                            <tr>
                                                <td style="text-align: center; padding-bottom: 10px;">
                                                    <span
                                                        style="color: #C70085; font-size: 14px; font-weight: bold;">Get
                                                        in Touch</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="color: #6c757d; font-size: 12px; line-height: 1.8; text-align: center;">
                                                    <div>📞 Phone: +234 708 909 1600</div>
                                                    <div>🌐 Website: oncuelogistics.com</div>
                                                    <div>📸 Instagram: @oncuelogistics</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #6c757d; font-size: 12px; margin: 0 0 10px 0; padding-top: 10px;">
                                        This enquiry was submitted through the <span
                                            style="color: #C70085; font-weight: bold;">{{ config('app.name') }}</span>
                                        website contact form
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: #6c757d; font-size: 12px;">
                                        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
