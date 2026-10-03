<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Registration Successful</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    {{-- <link rel="icon" type="image/png" href="/favicon.png"> --}}
    <link rel="shortcut icon" href="{{{ asset('assets/pages/img/logo-fieldma.png') }}}" />
    <style>
        :root {
            --primary-color: #dd3234;
            --primary-hover: #b90d10;
            --success-color: #10b981;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --text-light: #9ca3af;
            --bg-primary: #ffffff;
            --bg-secondary: #f9fafb;
            --shadow-soft: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-medium: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --border-radius: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, var(--bg-secondary) 0%, #e5f3ff 100%);
            color: var(--text-primary);
            line-height: 1.6;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .success-container {
            background: var(--bg-primary);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-medium);
            padding: 3rem 2rem;
            text-align: center;
            max-width: 480px;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .success-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--success-color));
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--success-color), #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            position: relative;
            animation: successPulse 2s infinite;
        }

        .success-icon::before {
            content: '✓';
            color: white;
            font-size: 2rem;
            font-weight: 700;
        }

        @keyframes successPulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .success-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 1rem;
            letter-spacing: -0.025em;
        }

        .success-subtitle {
            font-size: 1.125rem;
            color: var(--text-secondary);
            margin: 0 0 2.5rem;
            font-weight: 400;
        }

        .action-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: var(--transition);
            box-shadow: var(--shadow-soft);
            position: relative;
            overflow: hidden;
        }

        .action-button:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 12px 20px -4px rgba(39, 169, 225, 0.3);
        }

        .action-button:active {
            transform: translateY(0);
        }

        .action-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s;
        }

        .action-button:hover::before {
            left: 100%;
        }

        .decorative-elements {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            overflow: hidden;
            border-radius: var(--border-radius);
        }

        .decorative-circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), transparent);
            opacity: 0.1;
        }

        .circle-1 {
            width: 120px;
            height: 120px;
            top: -60px;
            right: -60px;
        }

        .circle-2 {
            width: 80px;
            height: 80px;
            bottom: -40px;
            left: -40px;
            background: linear-gradient(135deg, var(--success-color), transparent);
        }

        /* Mobile Responsiveness */
        @media (max-width: 480px) {
            body {
                padding: 0.5rem;
            }

            .success-container {
                padding: 2rem 1.5rem;
                margin: 0;
            }

            .success-icon {
                width: 64px;
                height: 64px;
                margin-bottom: 1.5rem;
            }

            .success-icon::before {
                font-size: 1.5rem;
            }

            .success-title {
                font-size: 1.75rem;
                margin-bottom: 0.75rem;
            }

            .success-subtitle {
                font-size: 1rem;
                margin-bottom: 2rem;
            }

            .action-button {
                width: 100%;
                padding: 1rem;
            }
        }

        /* Accessibility */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Focus styles for accessibility */
        .action-button:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(39, 169, 225, 0.3);
        }
    </style>
</head>

<body>
    <div class="success-container">
        <div class="decorative-elements">
            <div class="decorative-circle circle-1"></div>
            <div class="decorative-circle circle-2"></div>
        </div>

        <div class="success-icon"></div>

        <h1 class="success-title">Site Form Submitted!</h1>
        <p class="success-subtitle">Thank you for registering site details.</p>

        <a href="digitalpaints://formsubmitted?status=success" class="action-button">
            <span>Continue to App</span>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14M12 5l7 7-7 7" />
            </svg>
        </a>
    </div>
</body>

</html>
