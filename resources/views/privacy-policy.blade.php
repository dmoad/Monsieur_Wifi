<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Mr WiFi</title>
    <link rel="stylesheet" type="text/css" href="/app-assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/colors.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/css/components.css">
    <link rel="stylesheet" type="text/css" href="/app-assets/fonts/font-awesome/css/font-awesome.min.css">
    <style>
        :root {
            --theme-color: #7367f0;
            --theme-color-dark: #5e50ee;
        }

        body {
            min-height: 100vh;
            background-image: url('/assets/images/captive-portal/images/background.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            padding: 2rem 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .legal-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            padding: 2rem;
            margin: 0 auto;
        }

        .legal-header {
            text-align: center;
            margin-bottom: 2rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 1rem;
        }

        .legal-header h1 {
            color: var(--theme-color);
            font-weight: 600;
        }

        .legal-section {
            margin-bottom: 2rem;
        }

        .legal-section h2 {
            color: #333;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .legal-section p,
        .legal-section li {
            margin-bottom: 1rem;
            color: #555;
            line-height: 1.6;
        }

        .legal-section ul {
            padding-left: 1.5rem;
        }

        .legal-section a {
            color: var(--theme-color);
        }

        .footer {
            text-align: center;
            margin-top: 3rem;
            border-top: 1px solid #eee;
            padding-top: 1.5rem;
        }

        .brand-logo {
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            max-width: 200px;
        }

        .brand-logo img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
        }

        .btn-back {
            background-color: var(--theme-color);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            margin-top: 1rem;
        }

        .btn-back:hover {
            background-color: var(--theme-color-dark);
            color: white;
        }

        @media (max-width: 768px) {
            .legal-container {
                margin: 0 1rem;
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="legal-container">
        <div class="legal-header">
            <h1>Privacy Policy</h1>
            <p class="text-muted">Last Updated: {{ date('F j, Y') }}</p>
        </div>

        <div class="legal-section">
            <h2>1. Introduction</h2>
            <p>Welcome to Mr WiFi. We respect your privacy and are committed to protecting your personal data. This privacy policy explains how we collect, use, and safeguard your information when you use our website and WiFi services.</p>
        </div>

        <div class="legal-section">
            <h2>2. The Data We Collect</h2>
            <p>We may collect, use, store, and transfer different kinds of personal data about you, including:</p>
            <ul>
                <li><strong>Identity Data:</strong> First name, last name, username, or similar identifier.</li>
                <li><strong>Contact Data:</strong> Email address, telephone numbers, and physical address.</li>
                <li><strong>Technical Data:</strong> IP address, login data, browser type and version, device information, and other technology identifiers.</li>
                <li><strong>WiFi Usage Data:</strong> Information about your connection to and usage of WiFi networks through our service.</li>
                <li><strong>Location Data:</strong> Approximate or precise location data to provide relevant WiFi networks.</li>
            </ul>
        </div>

        <div class="legal-section">
            <h2>3. How We Use Your Data</h2>
            <p>We use your personal data to:</p>
            <ul>
                <li>Provide and maintain our service</li>
                <li>Notify you about changes to our service</li>
                <li>Provide customer support</li>
                <li>Improve our service through analysis</li>
                <li>Monitor usage and detect technical issues</li>
                <li>Send news, special offers, and information about related services (where permitted)</li>
            </ul>
        </div>

        <div class="legal-section">
            <h2>4. Data Security</h2>
            <p>We implement appropriate security measures to prevent unauthorized access, loss, or misuse of your personal data. Access is limited to employees, agents, and contractors who have a business need to know.</p>
        </div>

        <div class="legal-section">
            <h2>5. Data Retention</h2>
            <p>We retain personal data only as long as necessary for the purposes collected, including legal, accounting, or reporting requirements.</p>
        </div>

        <div class="legal-section">
            <h2>6. Your Legal Rights</h2>
            <p>Depending on applicable law, you may have the right to:</p>
            <ul>
                <li>Request access to your personal data</li>
                <li>Request correction of your personal data</li>
                <li>Request erasure of your personal data</li>
                <li>Object to or restrict processing of your personal data</li>
                <li>Request transfer of your personal data</li>
                <li>Withdraw consent where processing is consent-based</li>
            </ul>
        </div>

        <div class="legal-section">
            <h2>7. Cookies</h2>
            <p>We use cookies and similar technologies to track activity on our service and store certain information. Cookies are small files that may include an anonymous unique identifier.</p>
        </div>

        <div class="legal-section">
            <h2>8. Changes to This Privacy Policy</h2>
            <p>We may update this Privacy Policy from time to time. Changes will be posted on this page with an updated &ldquo;Last Updated&rdquo; date.</p>
        </div>

        <div class="legal-section">
            <h2>9. Contact Us</h2>
            <p>If you have questions about this Privacy Policy, contact us at:</p>
            <ul>
                <li>Email: privacy@mrwifi.cnctdwifi.com</li>
                <li>Phone: +91 9826000770</li>
            </ul>
            <p>See also our <a href="{{ route('tos') }}">Terms of Service</a>.</p>
        </div>

        <div class="footer">
            <div class="brand-logo">
                <img src="/assets/images/Mr-Wifi.PNG" alt="Mr WiFi Logo">
            </div>
            <div class="text-muted small">Powered by Mr WiFi</div>
            <a href="javascript:history.back()" class="btn btn-back mt-3">
                <i class="fa fa-arrow-left mr-1"></i> Go Back
            </a>
        </div>
    </div>
</body>
</html>
