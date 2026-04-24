<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $design?->name ?? 'Preview' }} — Preview</title>
    <link rel="stylesheet" href="/app-assets/css/bootstrap.css">
    <style>
        :root {
            --theme-color: {{ $design?->theme_color ?? '#7367f0' }};
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            @if ($design?->background_image_path)
            background-image: url('/storage/{{ $design->background_image_path }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            @elseif ($design?->background_color_gradient_start && $design?->background_color_gradient_end)
            background: linear-gradient(135deg, {{ $design->background_color_gradient_start }} 0%, {{ $design->background_color_gradient_end }} 100%);
            @else
            background: #EEF2FF;
            @endif
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            transition: background 0.3s;
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
        .preview-banner .preview-name { color: rgba(255,255,255,0.7); margin-left: 6px; }
        .preview-banner .btn-close-preview {
            background: rgba(255,255,255,0.15);
            border: none; color: #fff; border-radius: 6px;
            padding: 4px 12px; font-size: 12px; cursor: pointer; white-space: nowrap;
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
            margin-top: 52px;
        }

        .location-logo {
            height: 80px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1.5rem;
        }
        .location-logo img { max-height: 100%; max-width: 100%; object-fit: contain; }
        .location-logo-placeholder {
            background: #f0f0f0; width: 100%; height: 100%;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: #999; font-size: 13px;
        }

        .welcome-text {
            text-align: center; font-size: 1rem; font-weight: 600;
            color: #1a1a2e; margin-bottom: 0.5rem;
        }
        .login-instructions {
            text-align: center; font-size: 0.875rem; color: #555;
            line-height: 1.6; margin-bottom: 1.5rem;
        }

        .login-button {
            display: block; width: 100%;
            background-color: var(--theme-color);
            color: #fff; border: none; border-radius: 8px;
            padding: 12px 24px; font-size: 1rem; font-weight: 500;
            text-align: center; cursor: default;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .footer {
            margin-top: 2rem; border-top: 1px solid #eee;
            padding-top: 1.25rem; text-align: center;
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
            <span class="preview-name" id="preview-name">{{ $design?->name ?? '' }}</span>
        </div>
        <button class="btn-close-preview" onclick="window.close()">✕ Close preview</button>
    </div>

    <div class="portal-container">
        <div class="location-logo" id="preview-logo-wrap">
            @if ($design?->location_logo_path)
                <img src="/storage/{{ $design->location_logo_path }}" alt="Location Logo">
            @else
                <div class="location-logo-placeholder">Location Logo</div>
            @endif
        </div>

        <div class="welcome-text" id="preview-welcome">{{ $design?->welcome_message ?? 'Welcome to our WiFi' }}</div>
        <div class="login-instructions" id="preview-instructions" @if(!$design?->login_instructions) style="display:none" @endif>
            {{ $design?->login_instructions ?? '' }}
        </div>

        <div class="login-button" id="preview-btn">{{ $design?->button_text ?? 'Connect to WiFi' }}</div>

        <div class="footer">
            <div class="brand-logo">
                <img src="/assets/images/Mr-Wifi.PNG" alt="Monsieur WiFi">
            </div>
            <div id="preview-terms" style="margin-bottom:0.5rem; {{ $design?->show_terms ? '' : 'display:none' }}">
                <span class="terms-text">
                    By connecting, you agree to our
                    <a href="#" onclick="return false;">Terms of Service</a> and
                    <a href="#" onclick="return false;">Privacy Policy</a>.
                </span>
            </div>
            <div class="terms-text">Powered by Monsieur WiFi</div>
        </div>
    </div>

    <script>
        // Apply localStorage draft (written by designer before opening this tab).
        (function () {
            try {
                const raw = localStorage.getItem('cp_preview_draft');
                if (!raw) return;
                const d = JSON.parse(raw);
                // Clear immediately so stale data doesn't persist across unrelated opens.
                localStorage.removeItem('cp_preview_draft');

                if (d.name)            document.getElementById('preview-name').textContent = d.name;
                if (d.welcome_message) document.getElementById('preview-welcome').textContent = d.welcome_message;

                const instrEl = document.getElementById('preview-instructions');
                if (d.login_instructions) {
                    instrEl.textContent = d.login_instructions;
                    instrEl.style.display = '';
                } else {
                    instrEl.style.display = 'none';
                }

                if (d.button_text) document.getElementById('preview-btn').textContent = d.button_text;

                if (d.theme_color) {
                    document.documentElement.style.setProperty('--theme-color', d.theme_color);
                }

                if (d.background_color_gradient_start && d.background_color_gradient_end) {
                    document.body.style.background =
                        'linear-gradient(135deg, ' + d.background_color_gradient_start + ' 0%, ' + d.background_color_gradient_end + ' 100%)';
                }

                const termsEl = document.getElementById('preview-terms');
                if (termsEl) termsEl.style.display = d.show_terms ? '' : 'none';
            } catch (e) { /* ignore */ }
        })();
    </script>
</body>
</html>
