<x-app-layout>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
    </style>

    <div class="max-w-lg mx-auto min-h-screen" style="background-color: #F2F2F2;">
        {{-- Header --}}
        <div class="sticky top-0 z-10 px-4 py-3 animate-fade-in" style="background-color: #F2F2F2; border-bottom: 2px solid #B6B09F;">
            <div class="flex items-center justify-between">
                <a href="{{ route('profile.public', ['user' => $user->username]) }}" style="color: #000000;">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                </a>
                <h1 class="font-semibold text-lg" style="color: #000000;">{{ $user->username }}</h1>
                <div class="w-6"></div>
            </div>
        </div>

        {{-- Feed Posts --}}
        <div class="pb-4">
            @foreach($mediaPosts as $index => $post)
                <div class="mb-6 animate-fade-in rounded-lg overflow-hidden" 
                     id="post-{{ $post->id }}" 
                     data-post-index="{{ $index }}"
                     style="background-color: #FFFFFF; border: 1px solid #B6B09F;">
                    {{-- Post Header --}}
                    <div class="flex items-center px-4 py-3">
                        <div class="w-8 h-8 rounded-full overflow-hidden mr-3">
                            @if ($user->profile_picture)
                                <img src="{{ asset('storage/'.$user->profile_picture) }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="flex items-center justify-center w-full h-full text-xs font-bold" 
                                     style="background-color: #B6B09F; color: #F2F2F2;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-sm" style="color: #000000;">{{ $user->username }}</h4>
                        </div>
                        <button style="color: #B6B09F;">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Post Media --}}
                    <div class="relative" style="background-color: #000000;">
                        @if($post->media_type === 'image')
                            <img src="{{ asset('storage/'.$post->media_path) }}" 
                                 class="w-full h-auto max-h-96 object-contain">
                        @elseif($post->media_type === 'video')
                            <video controls class="w-full h-auto max-h-96 object-contain">
                                <source src="{{ asset('storage/'.$post->media_path) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @endif
                    </div>

                    {{-- Post Actions --}}
                    <div class="px-4 py-3">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center space-x-4">
                                <button class="like-btn transition-colors duration-300 hover:text-red-500" 
                                        data-post-id="{{ $post->id }}"
                                        style="color: #000000;">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </button>
                                <button class="transition-colors duration-300 hover:text-blue-500" style="color: #000000;">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                </button>
                                <button class="transition-colors duration-300 hover:text-green-500" style="color: #000000;">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                    </svg>
                                </button>
                            </div>
                            <button class="save-btn transition-colors duration-300 hover:text-yellow-500" 
                                    data-post-id="{{ $post->id }}"
                                    style="color: #000000;">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Likes Count --}}
                        <div class="mb-2">
                            <p class="font-semibold text-sm likes-count" style="color: #000000;">
                                {{ $post->likes_count ?? 0 }} likes
                            </p>
                        </div>

                        {{-- Caption --}}
                        @if($post->caption)
                            <div class="mb-2">
                                <p class="text-sm" style="color: #000000;">
                                    <span class="font-semibold">{{ $user->username }}</span>
                                    {{ $post->caption }}
                                </p>
                            </div>
                        @endif

                        {{-- Comments Link --}}
                        @if($post->comments_count > 0)
                            <button class="text-sm mb-2 transition-colors duration-300" style="color: #B6B09F;">
                                View all {{ $post->comments_count }} comments
                            </button>
                        @endif

                        {{-- Post Date --}}
                        <p class="text-xs uppercase tracking-wide" style="color: #B6B09F;">
                            {{ $post->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @endforeach

            {{-- No posts message --}}
            @if($mediaPosts->isEmpty())
                <div class="text-center py-12 animate-fade-in">
                    <svg class="w-16 h-16 mx-auto mb-4" style="color: #B6B09F;" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4 4h7v7H4V4zm0 9h7v7H4v-7zm9-9h7v7h-7V4zm0 9h7v7h-7v-7z"/>
                    </svg>
                    <h3 class="text-xl font-semibold mb-2" style="color: #B6B09F;">No Posts Yet</h3>
                    <p style="color: #B6B09F;">When {{ $user->name }} shares photos and videos, they'll appear here.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        // Scroll to specific post if postId is provided
        document.addEventListener('DOMContentLoaded', function() {
            @if(isset($scrollToIndex) && $scrollToIndex !== false && $scrollToIndex >= 0)
                const targetPost = document.querySelector('[data-post-index="{{ $scrollToIndex }}"]');
                if (targetPost) {
                    setTimeout(function() {
                        targetPost.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        targetPost.style.boxShadow = '0 0 0 3px #000000';
                        setTimeout(function() {
                            targetPost.style.boxShadow = '';
                        }, 2000);
                    }, 100);
                }
            @endif
        });

        // Like button functionality
        document.querySelectorAll('.like-btn').forEach(button => {
            button.addEventListener('click', function() {
                const postId = this.dataset.postId;
                const svg = this.querySelector('svg');
                const likesCount = this.closest('.px-4').querySelector('.likes-count');
                
                if (svg.getAttribute('fill') === 'none') {
                    svg.setAttribute('fill', 'currentColor');
                    svg.setAttribute('stroke', 'none');
                    this.style.color = '#EF4444';
                    
                    const currentCount = parseInt(likesCount.textContent.match(/\d+/)[0]);
                    likesCount.textContent = `${currentCount + 1} likes`;
                } else {
                    svg.setAttribute('fill', 'none');
                    svg.setAttribute('stroke', 'currentColor');
                    this.style.color = '#000000';
                    
                    const currentCount = parseInt(likesCount.textContent.match(/\d+/)[0]);
                    likesCount.textContent = `${Math.max(0, currentCount - 1)} likes`;
                }
            });
        });

        // Save button functionality
        document.querySelectorAll('.save-btn').forEach(button => {
            button.addEventListener('click', function() {
                const postId = this.dataset.postId;
                const svg = this.querySelector('svg');
                
                if (svg.getAttribute('fill') === 'none') {
                    svg.setAttribute('fill', 'currentColor');
                    svg.setAttribute('stroke', 'none');
                    this.style.color = '#EAB308';
                } else {
                    svg.setAttribute('fill', 'none');
                    svg.setAttribute('stroke', 'currentColor');
                    this.style.color = '#000000';
                }
            });
        });
    </script>
</x-app-layout>