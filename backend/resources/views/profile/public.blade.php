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
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        .animate-slide-in {
            animation: slideIn 0.5s ease-out;
        }
        .animate-scale-in {
            animation: scaleIn 0.5s ease-out;
        }
        .grid-item {
            animation: fadeIn 0.4s ease-out forwards;
            opacity: 0;
        }
        .grid-item:nth-child(1) { animation-delay: 0.1s; }
        .grid-item:nth-child(2) { animation-delay: 0.15s; }
        .grid-item:nth-child(3) { animation-delay: 0.2s; }
        .grid-item:nth-child(4) { animation-delay: 0.25s; }
        .grid-item:nth-child(5) { animation-delay: 0.3s; }
        .grid-item:nth-child(6) { animation-delay: 0.35s; }
        .grid-item:nth-child(n+7) { animation-delay: 0.4s; }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6" style="background-color: #F2F2F2; min-height: 100vh;">
        {{-- Profile Header --}}
        <section class="flex flex-col md:flex-row md:items-start md:space-x-12 animate-fade-in">
            {{-- Avatar --}}
            <div class="flex justify-center md:block md:shrink-0">
                <div class="relative w-24 h-24 sm:w-32 sm:h-32 md:w-40 md:h-40 rounded-full overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-xl" 
                     style="ring: 3px solid #B6B09F;">
                    @if ($user->profile_picture)
                        <img src="{{ asset('storage/'.$user->profile_picture) }}" 
                             alt="{{ $user->name }}" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-white" 
                             style="background: linear-gradient(135deg, #000000 0%, #B6B09F 100%);">
                            <span class="text-2xl font-semibold">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Profile Meta --}}
            <div class="flex-1 mt-4 md:mt-0 animate-slide-in">
                {{-- Username + Actions --}}
                <div class="flex items-center gap-2 sm:gap-4 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-semibold" style="color: #000000;">{{ $user->username }}</h1>
                    <button class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px] hover:shadow-lg" 
                            style="background-color: #000000; color: #F2F2F2;">
                        Send Request
                    </button>
                </div>

                {{-- Stats --}}
                <ul class="mt-4 flex items-center gap-6 text-sm" style="color: #000000;">
                    <li class="transition-all duration-300 hover:scale-110">
                        <span class="font-semibold">{{ $postsCount }}</span> posts
                    </li>
                    <li class="transition-all duration-300 hover:scale-110">
                        <span class="font-semibold">{{ $storiesCount }}</span> stories
                    </li>
                    <li class="transition-all duration-300 hover:scale-110">
                        <span class="font-semibold">0</span> followers
                    </li>
                    <li class="transition-all duration-300 hover:scale-110">
                        <span class="font-semibold">0</span> following
                    </li>
                </ul>

                {{-- Bio & Extra Info --}}
                <div class="mt-4 space-y-1 text-sm leading-relaxed">
                    <p class="font-semibold" style="color: #000000;">{{ $user->name }}</p>
                    @if($user->bio)
                        <p style="color: #000000;">{{ $user->bio }}</p>
                    @endif
                    @if($user->dob)
                        <p style="color: #000000;">
                            <span class="font-semibold">DOB:</span> {{ \Carbon\Carbon::parse($user->dob)->format('F j, Y') }}
                        </p>
                    @endif
                    @if($user->marital_status)
                        <p style="color: #000000;">
                            <span class="font-semibold">Marital Status:</span> {{ $user->marital_status }}
                        </p>
                    @endif
                    @if($user->education)
                        <p style="color: #000000;">
                            <span class="font-semibold">Education:</span> {{ $user->education }}
                        </p>
                    @endif
                    @if($user->occupation)
                        <p style="color: #000000;">
                            <span class="font-semibold">Occupation:</span> {{ $user->occupation }}
                        </p>
                    @endif
                </div>
            </div>
        </section>

        {{-- Stories Row --}}
        @if($user->stories->count() > 0)
            <div class="flex space-x-6 overflow-x-auto py-6 mt-6 no-scrollbar animate-slide-in" 
                 style="border-top: 2px solid #B6B09F;">
                @foreach($user->stories as $story)
                    <div class="flex flex-col items-center transition-all duration-300 hover:scale-110 cursor-pointer">
                        <div class="w-16 h-16 rounded-full overflow-hidden" style="border: 2px solid #000000;">
                            @if($story->type === 'image')
                                <img src="{{ asset('storage/'.$story->content) }}" class="w-full h-full object-cover">
                            @elseif($story->type === 'video')
                                <video class="w-full h-full object-cover">
                                    <source src="{{ asset('storage/'.$story->content) }}" type="video/mp4">
                                </video>
                            @endif
                        </div>
                        <p class="text-xs mt-1 w-16 text-center truncate" style="color: #B6B09F;">
                            {{ $story->caption ?? 'Story' }}
                        </p>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Tabs --}}
        <div x-data="{ activeTab: 'posts' }">
            <nav class="mt-6" style="border-top: 2px solid #B6B09F;">
                <ul class="flex items-center justify-center gap-10 text-xs tracking-widest uppercase font-semibold">
                    <li>
                        <button 
                            @click="activeTab = 'posts'" 
                            :class="activeTab === 'posts' ? 'border-t-2 -mt-[1px]' : ''"
                            :style="activeTab === 'posts' ? 'border-color: #000000; color: #000000;' : 'color: #B6B09F;'"
                            class="flex items-center gap-2 py-3 transition-all duration-300 hover:scale-105">
                            Posts
                        </button>
                    </li>
                    <li>
                        <button 
                            @click="activeTab = 'threads'" 
                            :class="activeTab === 'threads' ? 'border-t-2 -mt-[1px]' : ''"
                            :style="activeTab === 'threads' ? 'border-color: #000000; color: #000000;' : 'color: #B6B09F;'"
                            class="flex items-center gap-2 py-3 transition-all duration-300 hover:scale-105">
                            Threads
                        </button>
                    </li>
                </ul>
            </nav>

            {{-- Posts Section --}}
            <section x-show="activeTab === 'posts'" class="mt-2" aria-label="Posts">
                @php
                    $mediaPosts = $user->posts->filter(fn($p) => in_array($p->media_type, ['image', 'video']));
                @endphp

                @if($mediaPosts->count())
                    <div class="grid grid-cols-3 gap-1 sm:gap-2">
                        @foreach($mediaPosts as $post)
                            <a href="{{ route('user.feed', ['user' => $user->username, 'postId' => $post->id]) }}">
                                <article class="grid-item group relative aspect-square rounded-lg overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-xl" 
                                         style="background-color: #EAE4D5; border: 1px solid #B6B09F;">
                                    @if($post->media_type === 'image')
                                        <img src="{{ asset('storage/' . $post->media_path) }}" 
                                            alt="Post media"
                                            class="w-full h-full object-cover" />
                                    @elseif($post->media_type === 'video')
                                        <video class="w-full h-full object-cover">
                                            <source src="{{ asset('storage/' . $post->media_path) }}" type="video/mp4">
                                        </video>
                                        <div class="absolute top-2 right-2" style="color: #F2F2F2;">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    {{-- Hover Overlay --}}
                                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center" 
                                         style="background-color: rgba(0, 0, 0, 0.7);">
                                        <div class="flex items-center space-x-4" style="color: #F2F2F2;">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                                </svg>
                                                <span>{{ $post->likes_count ?? 0 }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M21 6h-2l-1.27-1.27A2 2 0 0 0 16.32 4H15V2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h2v2a2 2 0 0 0 2 2h8.32a2 2 0 0 0 1.41-.59L21 15.93A2 2 0 0 0 21 14V8a2 2 0 0 0-2-2z"/>
                                                </svg>
                                                <span>{{ $post->comments_count ?? 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 animate-fade-in">
                        <svg class="w-16 h-16 mx-auto mb-4" style="color: #B6B09F;" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4 4h7v7H4V4zm0 9h7v7H4v-7zm9-9h7v7h-7V4zm0 9h7v7h-7v-7z"/>
                        </svg>
                        <h3 class="text-2xl font-semibold mb-2" style="color: #B6B09F;">No Posts Yet</h3>
                        <p style="color: #B6B09F;">When {{ $user->name }} shares photos and videos, you'll see them here.</p>
                    </div>
                @endif
            </section>

            {{-- Threads Section --}}
            <section x-show="activeTab === 'threads'" class="mt-2" aria-label="Threads">
                @php
                    $threadPosts = $user->posts->filter(fn($p) => !in_array($p->media_type, ['image', 'video']));
                @endphp

                @if($threadPosts->count())
                    <div class="space-y-4">
                        @foreach($threadPosts as $post)
                            <div class="p-4 rounded-lg shadow-sm animate-fade-in transition-all duration-300 hover:shadow-lg" 
                                 style="background-color: #EAE4D5; border: 1px solid #B6B09F;">
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 rounded-full overflow-hidden shrink-0">
                                        @if ($user->profile_picture)
                                            <img src="{{ asset('storage/'.$user->profile_picture) }}" 
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="flex items-center justify-center w-full h-full text-sm font-bold" 
                                                 style="background-color: #B6B09F; color: #F2F2F2;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <h4 class="font-semibold" style="color: #000000;">{{ $user->username }}</h4>
                                            <span class="text-sm" style="color: #B6B09F;">{{ $post->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="whitespace-pre-line" style="color: #000000;">{{ $post->caption ?? $post->content }}</p>
                                        <div class="flex items-center space-x-4 mt-3 text-sm" style="color: #B6B09F;">
                                            <button class="flex items-center space-x-1 transition-colors duration-300 hover:text-red-500">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                                </svg>
                                                <span>{{ $post->likes_count ?? 0 }}</span>
                                            </button>
                                            <button class="flex items-center space-x-1 transition-colors duration-300 hover:text-blue-500">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M21 6h-2l-1.27-1.27A2 2 0 0 0 16.32 4H15V2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h2v2a2 2 0 0 0 2 2h8.32a2 2 0 0 0 1.41-.59L21 15.93A2 2 0 0 0 21 14V8a2 2 0 0 0-2-2z"/>
                                                </svg>
                                                <span>{{ $post->comments_count ?? 0 }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 animate-fade-in">
                        <svg class="w-16 h-16 mx-auto mb-4" style="color: #B6B09F;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <h3 class="text-2xl font-semibold mb-2" style="color: #B6B09F;">No Threads Yet</h3>
                        <p style="color: #B6B09F;">When {{ $user->name }} shares thoughts and updates, you'll see them here.</p>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>