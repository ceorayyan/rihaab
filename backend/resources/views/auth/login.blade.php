<x-guest-layout>
    <style>
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

        @keyframes slideRight {
            from {
                width: 0%;
            }
            to {
                width: 100%;
            }
        }

        .login-container {
            animation: fadeInUp 0.8s ease-out;
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background-color: #000000;
            animation: slideRight 1s ease-out;
        }

        .form-input {
            transition: all 0.3s ease;
            border: 2px solid #B6B09F;
            border-radius: 8px;
            padding: 0.875rem 1rem;
            font-size: 0.95rem;
            background-color: #F2F2F2;
            color: #000000;
        }

        .form-input::placeholder {
            color: #B6B09F;
        }

        .form-input:focus {
            border-color: #000000;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
            outline: none;
        }

        .form-label {
            font-weight: 600;
            color: #000000;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            letter-spacing: 0.3px;
        }

        .login-button {
            background-color: #000000;
            color: #EAE4D5;
            padding: 0.875rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .login-button:hover {
            background-color: #1a1a1a;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .forgot-link {
            color: #000000;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            text-decoration: none;
            border-bottom: 1px solid transparent;
        }

        .forgot-link:hover {
            color: #000000;
            border-bottom: 1px solid #000000;
        }

        .checkbox-custom {
            width: 1.125rem;
            height: 1.125rem;
            border-radius: 4px;
            border: 2px solid #B6B09F;
            transition: all 0.2s ease;
            cursor: pointer;
            accent-color: #000000;
        }

        .checkbox-custom:checked {
            background-color: #000000;
            border-color: #000000;
        }

        .title-text {
            font-size: 2rem;
            font-weight: 800;
            color: #000000;
            margin-bottom: 0.5rem;
            text-align: center;
            letter-spacing: -0.5px;
        }

        .subtitle-text {
            color: #B6B09F;
            text-align: center;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }

        .input-group {
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.8s ease-out backwards;
        }

        .input-group:nth-child(2) { animation-delay: 0.1s; }
        .input-group:nth-child(3) { animation-delay: 0.2s; }
        .input-group:nth-child(4) { animation-delay: 0.3s; }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
            color: #B6B09F;
            font-size: 0.875rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #B6B09F;
        }

        .divider span {
            padding: 0 1rem;
        }
    </style>

    <div class="login-container">
        <h1 class="title-text">Welcome Back</h1>
        <p class="subtitle-text">Sign in to continue your journey</p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="input-group">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" class="form-input block w-full" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Enter your email" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="input-group">
                <label for="password" class="form-label">Password</label>
                <input id="password" class="form-input block w-full" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="input-group flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="checkbox-custom" name="remember">
                    <span class="ml-2 text-sm" style="color: #000000;">Remember me</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="forgot-link" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>

            <button type="submit" class="login-button w-full" style="animation: fadeInUp 0.8s ease-out 0.4s backwards;">
                Sign In
            </button>

            <div style="text-align: center; margin-top: 1.5rem; animation: fadeInUp 0.8s ease-out 0.5s backwards;">
                <span class="text-sm" style="color: #B6B09F;">Don't have an account? </span>
                <a href="{{ route('register') }}" class="forgot-link font-semibold">Create one</a>
            </div>
        </form>
    </div>
</x-guest-layout>