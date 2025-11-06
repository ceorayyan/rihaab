<!-- ============================================ -->
<!-- 1. MY PROFILE PAGE (profile.blade.php) -->
<!-- ============================================ -->
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
    </style>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6" style="background-color: #F2F2F2; min-height: 100vh;">
        <!-- Top Section -->
        <section class="flex flex-col md:flex-row md:items-start md:space-x-12 animate-fade-in">
            <!-- Avatar -->
            <div class="flex justify-center md:block md:shrink-0">
                <div class="relative w-24 h-24 sm:w-32 sm:h-32 md:w-40 md:h-40 rounded-full overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-xl" 
                     style="ring: 3px solid #B6B09F;">
                    @if ($user->profile_picture)
                        <img src="{{ asset($user->profile_picture) }}" alt="{{ $user->name }}" class="w-full h-full object-cover" />
                    @else
                        <div class="w-full h-full flex items-center justify-center text-white" style="background: linear-gradient(135deg, #000000 0%, #B6B09F 100%);">
                            <span class="text-2xl font-semibold">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Profile Meta -->
            <div class="flex-1 mt-4 md:mt-0 animate-slide-in">
                <!-- Username + Actions -->
                <div class="flex items-center gap-2 sm:gap-4 flex-wrap">
                    <h1 class="text-xl sm:text-2xl font-semibold" style="color: #000000;">{{ $user->name }}</h1>
                    <a href="{{ route('profile.edit') }}" 
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px] hover:shadow-lg" 
                       style="background-color: #EAE4D5; color: #000000; border: 2px solid #B6B09F;">
                        Edit Profile
                    </a>
                </div>

                <!-- Stats -->
                <ul class="mt-4 flex items-center gap-6 text-sm" style="color: #000000;">
                    <li class="transition-all duration-300 hover:scale-110"><span class="font-semibold">{{ $posts->count() }}</span> posts</li>
                    <li class="transition-all duration-300 hover:scale-110"><span class="font-semibold">0</span> followers</li>
                    <li class="transition-all duration-300 hover:scale-110"><span class="font-semibold">0</span> following</li>
                </ul>

                <!-- Bio & Extra Info -->
                <div class="mt-4 space-y-1 text-sm leading-relaxed">
                    <p class="font-semibold" style="color: #000000;">{{ $user->name }}</p>
                    <p style="color: #B6B09F;">{{ $user->email }}</p>
                    @if($user->bio)
                        <p style="color: #000000;">{{ $user->bio }}</p>
                    @endif
                    @if($user->education)
                        <p style="color: #000000;"><span class="font-semibold">Education:</span> {{ $user->education }}</p>
                    @endif
                    @if($user->occupation)
                        <p style="color: #000000;"><span class="font-semibold">Occupation:</span> {{ $user->occupation }}</p>
                    @endif
                    @if($user->marital_status)
                        <p style="color: #000000;"><span class="font-semibold">Marital Status:</span> {{ $user->marital_status }}</p>
                    @endif
                    @if($user->dob)
                        <p style="color: #000000;"><span class="font-semibold">DOB:</span> {{ \Carbon\Carbon::parse($user->dob)->format('F j, Y') }}</p>
                    @endif
                </div>
            </div>
        </section>

        <!-- Tabs -->
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

            <!-- Posts Section -->
            <section x-show="activeTab === 'posts'" class="mt-2" aria-label="Posts">
                @php
                    $mediaPosts = $posts->filter(fn($p) => in_array($p->media_type, ['image', 'video']));
                @endphp

                @if($mediaPosts->count())
                    <div class="grid grid-cols-3 gap-1 sm:gap-2">
                        @foreach($mediaPosts as $post)
                            <a href="{{ route('profile.feed', ['id' => $post->id]) }}">
                                <article class="grid-item group relative aspect-square rounded-lg overflow-hidden transition-all duration-300 hover:scale-105 hover:shadow-xl" 
                                         style="background-color: #EAE4D5; border: 1px solid #B6B09F;">
                                    @if($post->media_type === 'image')
                                        <img src="{{ asset('storage/' . $post->media_path) }}" 
                                            alt="Post media"
                                            class="w-full h-full object-cover" />
                                    @elseif($post->media_type === 'video')
                                        <video class="w-full h-full object-cover" controls>
                                            <source src="{{ asset('storage/' . $post->media_path) }}" type="video/mp4">
                                        </video>
                                    @endif
                                    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center" 
                                         style="background-color: rgba(0, 0, 0, 0.5);">
                                        <span class="font-semibold text-sm" style="color: #F2F2F2;">View</span>
                                    </div>
                                </article>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-center mt-8" style="color: #B6B09F;">No media posts yet.</p>
                @endif
            </section>

            <!-- Threads Section -->
            <section x-show="activeTab === 'threads'" class="mt-2" aria-label="Threads">
                @php
                    $threadPosts = $posts->filter(fn($p) => !in_array($p->media_type, ['image', 'video']));
                @endphp

                @if($threadPosts->count())
                    <div class="space-y-4">
                        @foreach($threadPosts as $post)
                            <div class="p-4 rounded-lg shadow-sm animate-fade-in transition-all duration-300 hover:shadow-lg" 
                                 style="background-color: #EAE4D5; border: 1px solid #B6B09F;">
                                <p class="whitespace-pre-line" style="color: #000000;">{{ $post->content ?? 'Untitled thread' }}</p>
                                <span class="text-xs" style="color: #B6B09F;">{{ $post->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center mt-8" style="color: #B6B09F;">No threads yet.</p>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
