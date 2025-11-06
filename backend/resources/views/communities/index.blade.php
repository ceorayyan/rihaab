<x-app-layout>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        .animate-slide-in {
            animation: slideIn 0.5s ease-out;
        }
        .community-card {
            animation: fadeIn 0.4s ease-out forwards;
            opacity: 0;
        }
        .community-card:nth-child(1) { animation-delay: 0.1s; }
        .community-card:nth-child(2) { animation-delay: 0.15s; }
        .community-card:nth-child(3) { animation-delay: 0.2s; }
        .community-card:nth-child(4) { animation-delay: 0.25s; }
        .community-card:nth-child(n+5) { animation-delay: 0.3s; }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6" style="background-color: #F2F2F2; min-height: 100vh;">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6 animate-fade-in">
            <div>
                <h1 class="text-3xl font-bold" style="color: #000000;">Communities</h1>
                <p class="text-sm mt-1" style="color: #B6B09F;">Discover and join amazing communities</p>
            </div>
            <a href="{{ route('communities.create') }}" 
               class="px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:translate-y-[-2px] hover:shadow-xl flex items-center space-x-2"
               style="background-color: #000000; color: #F2F2F2;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Create Community</span>
            </a>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg animate-slide-in" style="background-color: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7;">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        {{-- My Communities Section --}}
        @if($myCommunities->count() > 0)
            <div class="mb-8 animate-fade-in">
                <h2 class="text-xl font-semibold mb-4" style="color: #000000;">My Communities</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($myCommunities as $community)
                        <a href="{{ route('communities.show', $community->slug) }}" 
                           class="community-card group rounded-lg p-4 transition-all duration-300 hover:scale-105 hover:shadow-xl"
                           style="background-color: #FFFFFF; border: 2px solid #B6B09F;">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 rounded-lg overflow-hidden shrink-0" style="background-color: #EAE4D5;">
                                    @if($community->icon)
                                        <img src="{{ asset('storage/'.$community->icon) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center font-bold text-lg" style="color: #000000;">
                                            {{ strtoupper(substr($community->name, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold truncate" style="color: #000000;">{{ $community->name }}</h3>
                                    <p class="text-xs" style="color: #B6B09F;">{{ $community->members_count }} members</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- All Communities Section --}}
        <div class="animate-fade-in">
            <h2 class="text-xl font-semibold mb-4" style="color: #000000;">Discover Communities</h2>
            
            @if($communities->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($communities as $community)
                        <div class="community-card rounded-lg overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-xl"
                             style="background-color: #FFFFFF; border: 1px solid #B6B09F;">
                            {{-- Banner --}}
                            <div class="h-32 relative" style="background: linear-gradient(135deg, #000000 0%, #B6B09F 100%);">
                                @if($community->banner)
                                    <img src="{{ asset('storage/'.$community->banner) }}" class="w-full h-full object-cover">
                                @endif
                                
                                {{-- Icon Overlay --}}
                                <div class="absolute -bottom-8 left-4">
                                    <div class="w-16 h-16 rounded-lg overflow-hidden" style="background-color: #EAE4D5; border: 3px solid #FFFFFF;">
                                        @if($community->icon)
                                            <img src="{{ asset('storage/'.$community->icon) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center font-bold text-xl" style="color: #000000;">
                                                {{ strtoupper(substr($community->name, 0, 2)) }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="p-4 pt-10">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-bold" style="color: #000000;">{{ $community->name }}</h3>
                                        @if($community->is_private)
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium mt-1" 
                                                  style="background-color: #EAE4D5; color: #000000;">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Private
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <p class="text-sm mb-4 line-clamp-2" style="color: #B6B09F;">
                                    {{ $community->description ?? 'No description available.' }}
                                </p>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4 text-xs" style="color: #B6B09F;">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                            </svg>
                                            {{ $community->members_count }} members
                                        </span>
                                    </div>

                                    <a href="{{ route('communities.show', $community->slug) }}"
                                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px]"
                                       style="background-color: #000000; color: #F2F2F2;">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $communities->links() }}
                </div>
            @else
                <div class="text-center py-12 rounded-lg" style="background-color: #FFFFFF; border: 1px solid #B6B09F;">
                    <svg class="w-16 h-16 mx-auto mb-4" style="color: #B6B09F;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="text-xl font-semibold mb-2" style="color: #B6B09F;">No Communities Yet</h3>
                    <p class="mb-4" style="color: #B6B09F;">Be the first to create a community!</p>
                    <a href="{{ route('communities.create') }}" 
                       class="inline-block px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:translate-y-[-2px]"
                       style="background-color: #000000; color: #F2F2F2;">
                        Create Community
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>