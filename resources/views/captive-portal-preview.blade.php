<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $design->name }} — Preview</title>
    <link rel="stylesheet" href="/app-assets/css/bootstrap.css">
    <style>
        :root {
            --theme-color: {{ $design->theme_color ?? '#7367f0' }};
            --theme-color-dark: {{ $design->theme_color ?? '#7367f0' }};
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            @if ($design->background_image_path)
            background-image: url('/storage/{{ $design->background_image_path }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            @elseif ($design->background_color_gradient_start && $design->background_color_gradient_end)
            background: linear-gradient(135deg, {{ $design->background_color_gradient_start }} 0%, {{ $design->background_color_gradient_end }} 100%);
            @else
            background: #f0f2f5;
            @endif
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        /* ── Preview banner ── */
        .preview-banner {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 9999;
            background: rgba(0,0,0,0.72);
            backdrop-filter: blur(4px);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 20px;
            font-size: 13px;
            gap: 12px;
        }
        .preview-banner strong { font-weight: 600; }
        .preview-banner .preview-name {
            color: rgba(255,255,255,0.7);
            margin-left: 6px;
        }
        .preview-banner .btn-close-preview {
            background: rgba(255,255,255,0.15);
            border: none;
            color: #fff;
            border-radius: 6px;
            padding: 4px 12px;
            font-size: 12px;
            cursor: pointer;
            white-space: nowrap;
        }
        .preview-banner .btn-close-preview:hover { background: rgba(255,255,255,0.25); }

        /* ── Portal card ── */
        .portal-container {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
            width: 100%;
            max-width: 420px;
            padding: 2rem;
            margin-top: 52px; /* clear banner */
        }

        .location-logo {
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        .location-logo img { max-height: 100%; max-width: 100%; object-fit: contain; }
        .location-logo-placeholder {
            background: #f0f0f0;
            width: 100%; height: 100%;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #999; font-size: 13px;
        }

        .welcome-text {
            text-align: center;
            font-size: 1rem;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 0.5rem;
        }
        .login-instructions {
            text-align: center;
            font-size: 0.875rem;
            color: #555;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .login-button {
            display: block;
            width: 100%;
            background-color: var(--theme-color);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 500;
            text-align: center;
            cursor: default;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .footer {
            margin-top: 2rem;
            border-top: 1px solid #eee;
            padding-top: 1.25rem;
            text-align: center;
        }
        .brand-logo {
            height: 28px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 0.75rem;
        }
        .brand-logo img { max-height: 100%; object-fit: contain; }
        .terms-text { font-size: 0.8rem; color: #888; }
        .terms-text a { color: var(--theme-color); text-decoration: none; }
    </style>
</head>
<body>
    <div class="preview-banner">
        <div>
            <strong>Preview</strong>
            <span class="preview-name">{{ $design->name }}</span>
        </div>
        <button class="btn-close-preview" onclick="window.close()">✕ Close preview</button>
    </div>

    <div class="portal-container">
        <div class="location-logo">
            @if ($design->location_logo_path)
                <img src="/storage/{{ $design->location_logo_path }}" alt="Location Logo">
            @else
                <div class="location-logo-placeholder">Location Logo</div>
            @endif
        </div>

        <div class="welcome-text">{{ $design->welcome_message ?? 'Welcome to our WiFi' }}</div>

        @if ($design->login_instructions)
            <div class="login-instructions">{{ $design->login_instructions }}</div>
        @endif

        <div class="login-button">{{ $design->button_text ?? 'Connect to WiFi' }}</div>

        <div class="footer">
            <div class="brand-logo">
                <img src="/assets/images/Mr-Wifi.PNG" alt="Monsieur WiFi">
            </div>
            @if ($design->show_terms)
                <div class="terms-text" style="margin-bottom:0.5rem;">
                    By connecting, you agree to our
                    <a href="#" onclick="return false;">Terms of Service</a> and
                    <a href="#" onclick="return false;">Privacy Policy</a>.
                </div>
            @endif
            <div class="terms-text">Powered by Monsieur WiFi</div>
        </div>
    </div>
</body>
</html>
