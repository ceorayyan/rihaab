
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel App') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto py-6">
        {{-- Navigation --}}
        <nav class="bg-white shadow rounded p-4 mb-6 flex justify-between items-center">
            <a href="{{ route('dashboard') }}" class="font-bold text-lg">Dashboard</a>
            <div>
                <a href="{{ route('profile.edit') }}" class="text-blue-600 hover:underline mr-4">Profile</a>
                <a href="{{ route('keyrequest.incoming') }}" class="text-blue-600 hover:underline">Incoming Requests</a>
            </div>
        </nav>

        {{-- Page Content --}}
        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>
