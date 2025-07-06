<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2px;
            border-radius: 12px;
        }
        .inner-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header .subtitle {
            margin: 10px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #2d3748;
            margin-bottom: 25px;
        }
        .message-content {
            background: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 25px;
            margin: 25px 0;
            white-space: pre-wrap;
            font-size: 16px;
            line-height: 1.7;
        }
        .sender-info {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
        }
        .sender-info h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        .sender-info p {
            margin: 5px 0;
            opacity: 0.9;
        }
        .footer {
            background: #2d3748;
            color: white;
            padding: 30px;
            text-align: center;
            font-size: 14px;
        }
        .footer .logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .contact-info {
            margin: 20px 0;
            padding: 20px;
            background: #edf2f7;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .contact-info h4 {
            margin: 0 0 10px 0;
            color: #2d3748;
        }
        .timestamp {
            text-align: center;
            color: #718096;
            font-size: 12px;
            margin: 20px 0;
            padding: 10px;
            background: #f7fafc;
            border-radius: 4px;
        }
        @media (max-width: 600px) {
            .container {
                margin: 10px;
            }
            .content {
                padding: 20px 15px;
            }
            .header {
                padding: 20px 15px;
            }
            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="inner-container">
            <!-- Header -->
            <div class="header">
                <h1>DocraTech Medical Platform</h1>
                <p class="subtitle">Administrative Communication</p>
            </div>

            <!-- Content -->
            <div class="content">
                <div class="greeting">
                    <strong>Hello {{ $user->first_name }} {{ $user->last_name }},</strong>
                </div>

                <p>You have received a message from our administrative team:</p>

                <div class="message-content">{{ $emailBody }}</div>

                @if($sentBy)
                <div class="sender-info">
                    <h3>📧 Message sent by:</h3>
                    <p><strong>{{ $sentBy->first_name }} {{ $sentBy->last_name }}</strong></p>
                    <p>{{ $sentBy->email }}</p>
                    <p>DocraTech Administrator</p>
                </div>
                @endif

                <div class="contact-info">
                    <h4>🏥 Need Assistance?</h4>
                    <p>If you have any questions or need immediate support, please contact our team:</p>
                    <p><strong>Email:</strong> support@docratech.com</p>
                    <p><strong>Phone:</strong> +1 (555) 123-4567</p>
                    <p><strong>Hours:</strong> Monday-Friday, 9:00 AM - 6:00 PM</p>
                </div>

                <div class="timestamp">
                    📅 Sent on {{ $sentAt->format('F j, Y \a\t g:i A T') }}
                </div>

                <p style="color: #718096; font-size: 14px; margin-top: 30px;">
                    This email was sent from DocraTech Medical Website Platform. 
                    Please do not reply directly to this email - use the contact information above for any responses.
                </p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div class="logo">DocraTech</div>
                <p>Professional Medical Website Platform</p>
                <p style="margin: 15px 0 5px 0;">
                    © {{ date('Y') }} DocraTech Medical Solutions. All rights reserved.
                </p>
                <p style="font-size: 12px; opacity: 0.8;">
                    Secure • Professional • Reliable
                </p>
            </div>
        </div>
    </div>
</body>
</html> 