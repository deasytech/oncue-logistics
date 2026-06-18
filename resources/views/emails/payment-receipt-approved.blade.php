<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmed - Guest Management Access</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #C70085 0%, #8B0055 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
        }

        .header .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .content {
            padding: 40px 30px;
        }

        .welcome-text {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 25px;
            font-weight: 500;
        }

        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #C70085;
            padding: 20px;
            margin: 25px 0;
            border-radius: 6px;
        }

        .info-box h3 {
            margin: 0 0 10px 0;
            color: #C70085;
            font-size: 16px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }

        .detail-item {
            background: #ffffff;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .detail-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 16px;
            color: #2c3e50;
            font-weight: 500;
        }

        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #C70085 0%, #8B0055 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            margin: 20px 0;
            transition: all 0.3s ease;
        }

        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(199, 0, 133, 0.3);
        }

        .features {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin: 25px 0;
        }

        .features h4 {
            margin: 0 0 15px 0;
            color: #495057;
        }

        .feature-list {
            list-style: none;
            padding: 0;
        }

        .feature-list li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
        }

        .feature-list li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #C70085;
            font-weight: bold;
        }

        .footer {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }

        .support-info {
            background: #f8e6f0;
            border: 1px solid #C70085;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }

        @media (max-width: 600px) {
            .details-grid {
                grid-template-columns: 1fr;
            }

            .header,
            .content {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="icon">🎉</div>
            <h1>Payment Confirmed!</h1>
            <p>Your offline payment has been approved and guest management access is now enabled</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="welcome-text">
                Dear {{ $customer->full_name }},
            </div>

            <p>Great news! We have reviewed and approved your payment receipt. Your offline payment has been confirmed
                and you now have full access to our guest management features.</p>

            <!-- Success Info Box -->
            <div class="info-box">
                <h3>✅ Payment Status: APPROVED</h3>
                <p>Your receipt dated {{ $receipt->created_at->format('F j, Y') }} has been verified and approved by our
                    team.</p>
            </div>

            <!-- Payment Details -->
            @if ($delivery)
                <div class="details-grid">
                    <div class="detail-item">
                        <div class="detail-label">Delivery Service</div>
                        <div class="detail-value">{{ $delivery->deliveryService->name }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Amount Paid</div>
                        <div class="detail-value">₦{{ number_format($delivery->cost, 2) }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Payment Method</div>
                        <div class="detail-value">Offline Payment</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Approval Date</div>
                        <div class="detail-value">{{ now()->format('F j, Y') }}</div>
                    </div>
                </div>
            @endif

            <!-- Action Button -->
            <div style="text-align: center;">
                <a href="{{ url('/guests') }}" class="action-button">
                    🎯 Start Managing Your Guests
                </a>
            </div>

            <!-- Features -->
            <div class="features">
                <h4>🌟 What You Can Do Now:</h4>
                <ul class="feature-list">
                    <li>Upload and manage your guest list</li>
                    <li>Send RSVP invitations to guests</li>
                    <li>Track guest responses and attendance</li>
                    <li>Manage guest information and preferences</li>
                    <li>Export guest data for your event planning</li>
                </ul>
            </div>

            <!-- Support Info -->
            <div class="support-info">
                <strong>💡 Need Help?</strong> Our support team is here to assist you with any questions about managing
                your guests. Simply reply to this email or contact us through your dashboard.
            </div>

            <p>Thank you for choosing our platform for your event. We wish you a successful and memorable celebration!
            </p>

            <p>Best regards,<br>
                <strong>The Oncue Team</strong>
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div style="border-top: 1px solid #dee2e6; padding-top: 20px; margin-bottom: 20px;">
                <div style="color: #C70085; font-weight: bold; margin-bottom: 10px; font-size: 14px;">Get in Touch</div>
                <div style="line-height: 1.8; font-size: 12px;">
                    <div>📞 Phone: +234 708 909 1600</div>
                    <div>🌐 Website: oncuelogistics.com</div>
                    <div>📸 Instagram: @oncuelogistics</div>
                </div>
            </div>
            <p>This is an automated notification from Oncue Event Management Platform</p>
            <p>If you have any questions, please don't hesitate to contact our support team.</p>
        </div>
    </div>
</body>

</html>
