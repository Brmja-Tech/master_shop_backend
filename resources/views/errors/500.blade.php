<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - {{ __('dashboard.server-error') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 50%, #fecfef 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .error-container {
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            padding: 3rem 2rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            max-width: 500px;
            width: 90%;
            position: relative;
            animation: slideInUp 0.8s ease-out;
        }

        .error-code {
            font-size: 8rem;
            font-weight: bold;
            color: #edc80e;
            text-shadow: 0 4px 8px rgba(237, 200, 14, 0.3);
            animation: gentlePulse 3s infinite;
            margin-bottom: 1rem;
        }

        .error-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 1rem;
            animation: fadeInUp 0.8s ease-out 0.3s both;
        }

        .error-message {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
            animation: fadeInUp 0.8s ease-out 0.5s both;
        }

        .server-icon {
            font-size: 4rem;
            color: #edc80e;
            margin-bottom: 1rem;
            animation: shake 1s infinite;
        }

        .btn {
            display: inline-block;
            padding: 12px 30px;
            background: #edc80e;
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            transition: all 0.3s ease;
            animation: fadeInUp 0.8s ease-out 0.7s both;
            box-shadow: 0 4px 15px rgba(237, 200, 14, 0.3);
        }

        .btn:hover {
            background: #d4b00a;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(237, 200, 14, 0.4);
        }

        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }

        .shape {
            position: absolute;
            background: rgba(237, 200, 14, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 60px;
            height: 60px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 100px;
            height: 100px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        .shape:nth-child(4) {
            width: 40px;
            height: 40px;
            top: 40%;
            right: 30%;
            animation-delay: 1s;
        }

        .shape:nth-child(5) {
            width: 70px;
            height: 70px;
            top: 10%;
            right: 20%;
            animation-delay: 3s;
        }

        .error-details {
            background: rgba(255, 0, 0, 0.1);
            border: 1px solid rgba(255, 0, 0, 0.2);
            border-radius: 10px;
            padding: 1rem;
            margin: 1rem 0;
            font-size: 0.9rem;
            color: #d32f2f;
            animation: fadeInUp 0.8s ease-out 0.9s both;
        }

        .loading-dots {
            display: inline-block;
            margin-left: 10px;
        }

        .loading-dots::after {
            content: '';
            animation: dots 1.5s infinite;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes gentlePulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.02);
                opacity: 0.9;
            }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-3px); }
            20%, 40%, 60%, 80% { transform: translateX(3px); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        @keyframes dots {
            0%, 20% { content: ''; }
            40% { content: '.'; }
            60% { content: '..'; }
            80%, 100% { content: '...'; }
        }

        @media (max-width: 768px) {
            .error-code { font-size: 6rem; }
            .error-title { font-size: 1.5rem; }
            .error-container { padding: 2rem 1.5rem; }
        }
    </style>
</head>

<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="error-container">
        <div class="error-code">500</div>
        <h1 class="error-title">{{ __('dashboard.server-error') }}</h1>
        <p class="error-message">
            {{ __('dashboard.internal-error-text') }}
            <span class="loading-dots"></span>
        </p>
        <div class="error-details">
            <strong>{{ __('dashboard.error-details') }}:</strong><br>
            {{ __('dashboard.internal-error-desc') }}
        </div>
        <a href="{{ route('login') }}" class="btn">{{ __('dashboard.back-to-home') }}</a>
    </div>
</body>

</html>
