<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
            }
            
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            .logo-container {
                animation: float 3s ease-in-out infinite;
            }
            
            body {
                animation: fadeIn 0.5s ease-in;
            }
            
            .background-pattern {
                background-color: #F2F2F2;
                background-image: 
                    radial-gradient(circle at 20% 50%, rgba(182, 176, 159, 0.1) 0%, transparent 50%),
                    radial-gradient(circle at 80% 80%, rgba(234, 228, 213, 0.15) 0%, transparent 50%);
            }
        </style>
    </head>
    <body class="font-sans antialiased background-pattern">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div class="logo-container">
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current" style="color: #000000;" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 shadow-md overflow-hidden sm:rounded-lg" style="background-color: #EAE4D5;">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>