<!-- Add this in your <head> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<nav class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            {{-- Left Side: Logo --}}
            <div class="flex items-center">
                <a href="{{ route('posts.index') }}">
                    <img src="favicon.png" alt="Rihaab" class="h-10">
                </a>
            </div>

            {{-- Right Side: Icons --}}
            <div class="flex items-center space-x-6 text-gray-700">

              {{-- Navbar Search --}}
<div x-data="{ open: false }" class="flex items-center">
    <!-- Search Icon / Close -->
    <button @click="open = !open" class="mr-2 text-gray-600 hover:text-black focus:outline-none">
        <i x-show="!open" class="fa-solid fa-magnifying-glass text-xl"></i>
        <i x-show="open" class="fa-solid fa-xmark text-xl"></i>
    </button>

    <!-- Expanding Input -->
    <form method="GET" action="{{ route('people') }}"
          x-show="open"
          x-transition:enter="transition-all ease-out duration-300"
          x-transition:enter-start="w-0 opacity-0"
          x-transition:enter-end="w-64 opacity-100"
          x-transition:leave="transition-all ease-in duration-200"
          x-transition:leave-start="w-64 opacity-100"
          x-transition:leave-end="w-0 opacity-0"
          class="overflow-hidden">
        <input type="text" name="search" placeholder="Search users..."
               class="border rounded px-3 py-1 w-64 focus:outline-none focus:ring focus:ring-blue-400"
               @blur="open = false">
    </form>
</div>

              

                {{-- Notifications (heart) --}}
                <a href="{{ url('notifications/incoming') }}" class="hover:text-black">
                    <i class="fa-regular fa-heart text-xl"></i>
                </a>

               

              {{-- Profile --}}
<x-dropdown align="right" width="48">
    <x-slot name="trigger">
        <button class="flex items-center focus:outline-none text-gray-700 hover:text-black">
            <i class="fa-regular fa-user text-xl"></i>
        </button>
    </x-slot>

    <x-slot name="content">
        <x-dropdown-link :href="route('profile')">
            {{ __('Profile') }}
        </x-dropdown-link>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-dropdown-link :href="route('logout')"
                onclick="event.preventDefault(); this.closest('form').submit();">
                {{ __('Log Out') }}
            </x-dropdown-link>
        </form>
    </x-slot>
</x-dropdown>

            </div>
        </div>
    </div>
</nav>
