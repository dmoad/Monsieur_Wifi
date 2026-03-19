<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - Monsieur WiFi</title>
    <style>
        body {
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #7367f0 0%, #9055ff 100%);
            padding: 40px 30px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #333333;
            margin-bottom: 20px;
        }
        .message {
            font-size: 15px;
            color: #555555;
            margin-bottom: 30px;
        }
        .button-container {
            text-align: center;
            margin: 35px 0;
        }
        .reset-button {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #7367f0 0%, #9055ff 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 12px rgba(115, 103, 240, 0.4);
        }
        .reset-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(115, 103, 240, 0.5);
        }
        .link-section {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f8f8;
            border-radius: 8px;
            border-left: 4px solid #7367f0;
        }
        .link-section p {
            margin: 0 0 10px 0;
            font-size: 13px;
            color: #666666;
        }
        .link-text {
            word-break: break-all;
            font-size: 12px;
            color: #7367f0;
            text-decoration: none;
        }
        .warning {
            margin-top: 25px;
            padding: 15px;
            background-color: #fff3cd;
            border-radius: 8px;
            border-left: 4px solid #ffc107;
        }
        .warning p {
            margin: 0;
            font-size: 13px;
            color: #856404;
        }
        .footer {
            padding: 30px;
            text-align: center;
            background-color: #f8f8f8;
            border-top: 1px solid #e0e0e0;
        }
        .footer p {
            margin: 5px 0;
            font-size: 13px;
            color: #888888;
        }
        .footer a {
            color: #7367f0;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background-color: #e0e0e0;
            margin: 25px 0;
        }
        .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="icon">🔐</div>
            <h1>Password Reset Request</h1>
            <p>Monsieur WiFi Network Management</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Hello{{ $userName ? ' ' . $userName : '' }},
            </div>
            
            <div class="message">
                <p>We received a request to reset the password for your Monsieur WiFi account. If you made this request, click the button below to reset your password:</p>
            </div>
            
            <div class="button-container">
                <a href="{{ $resetUrl }}" class="reset-button">Reset Your Password</a>
            </div>
            
            <div class="link-section">
                <p><strong>If the button doesn't work, copy and paste this link into your browser:</strong></p>
                <a href="{{ $resetUrl }}" class="link-text">{{ $resetUrl }}</a>
            </div>
            
            <div class="warning">
                <p><strong>⚠️ Security Notice:</strong> This password reset link will expire in {{ $expiresIn }} minutes. If you didn't request a password reset, please ignore this email or contact support if you have concerns about your account security.</p>
            </div>
            
            <div class="divider"></div>
            
            <div class="message">
                <p style="font-size: 13px; color: #888888;">
                    For your security, we recommend:
                </p>
                <ul style="font-size: 13px; color: #888888; padding-left: 20px;">
                    <li>Using a strong, unique password</li>
                    <li>Not sharing your password with anyone</li>
                    <li>Changing your password regularly</li>
                </ul>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>Monsieur WiFi</strong></p>
            <p>WiFi Network Management System</p>
            <p style="margin-top: 15px;">
                © {{ date('Y') }} Monsieur WiFi. All rights reserved.
            </p>
            <p style="margin-top: 10px;">
                <a href="{{ config('app.url') }}">Visit our website</a>
            </p>
        </div>
    </div>
</body>
</html>

