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

        .register-container {
            animation: fadeInUp 0.8s ease-out;
            position: relative;
            overflow: hidden;
        }

        .register-container::before {
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
            width: 100%;
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
            display: block;
        }

        .register-button {
            background-color: #000000;
            color: #EAE4D5;
            padding: 0.875rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            width: 100%;
        }

        .register-button:hover {
            background-color: #1a1a1a;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .register-button:active {
            transform: translateY(0);
        }

        .login-link {
            color: #000000;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            text-decoration: none;
            font-weight: 600;
            border-bottom: 1px solid transparent;
        }

        .login-link:hover {
            color: #000000;
            border-bottom: 1px solid #000000;
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
            margin-bottom: 1.25rem;
            animation: fadeInUp 0.8s ease-out backwards;
        }

        .input-group:nth-child(2) { animation-delay: 0.1s; }
        .input-group:nth-child(3) { animation-delay: 0.2s; }
        .input-group:nth-child(4) { animation-delay: 0.3s; }
        .input-group:nth-child(5) { animation-delay: 0.4s; }
        .input-group:nth-child(6) { animation-delay: 0.5s; }

        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.75rem;
            color: #B6B09F;
        }

        .strength-meter {
            height: 3px;
            background-color: #B6B09F;
            border-radius: 2px;
            margin-top: 0.5rem;
            overflow: hidden;
            opacity: 0.3;
        }

        .strength-bar {
            height: 100%;
            background-color: #000000;
            width: 0%;
            transition: width 0.3s ease;
        }
    </style>

    <div class="register-container">
        <h1 class="title-text">Join Us Today</h1>
        <p class="subtitle-text">Create your account and start connecting</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="input-group">
                <label for="name" class="form-label">Full Name</label>
                <input id="name" class="form-input" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="John Doe" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Username -->
            <div class="input-group">
                <label for="username" class="form-label">Username</label>
                <input id="username" class="form-input" type="text" name="username" value="{{ old('username') }}" required autocomplete="username" placeholder="@johndoe" />
                <x-input-error :messages="$errors->get('username')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="input-group">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="john@example.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="input-group">
                <label for="password" class="form-label">Password</label>
                <input id="password" class="form-input" type="password" name="password" required autocomplete="new-password" placeholder="Create a strong password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="input-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input id="password_confirmation" class="form-input" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Re-enter your password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <button type="submit" class="register-button" style="animation: fadeInUp 0.8s ease-out 0.6s backwards;">
                Create Account
            </button>

            <div style="text-align: center; margin-top: 1.5rem; animation: fadeInUp 0.8s ease-out 0.7s backwards;">
                <span class="text-sm" style="color: #B6B09F;">Already have an account? </span>
                <a href="{{ route('login') }}" class="login-link">Sign in</a>
            </div>
        </form>
    </div>

    <script>
        // Add smooth focus animations
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.01)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</x-guest-layout>