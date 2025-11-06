<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <div id="feed-container" class="snap-y snap-mandatory overflow-y-scroll h-screen no-scrollbar">
            @foreach($posts as $post)
                @if(in_array($post->media_type, ['image', 'video'])) {{-- ✅ Only media posts --}}
                <div id="post-{{ $post->id }}" class="h-screen flex items-center justify-center snap-start">
                    <article class="bg-white rounded-xl shadow-sm border border-gray-100 w-full max-w-lg mx-auto">
                        
                        {{-- Post Header --}}
                        <div class="p-4 flex items-center gap-3 border-b">
                            <div class="w-10 h-10 rounded-full overflow-hidden">
                                <img src="{{ asset($post->user->profile_picture ?? 'default.jpg') }}" 
                                     alt="{{ $post->user->name }}" 
                                     class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="font-semibold">{{ $post->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        {{-- Media --}}
                        <div class="bg-black flex items-center justify-center max-h-[70vh]">
                            @if($post->media_type === 'image')
                                <img src="{{ asset('storage/'.$post->media_path) }}" 
                                     class="max-h-[70vh] object-contain" />
                            @elseif($post->media_type === 'video')
                                <video controls autoplay loop muted 
                                       class="max-h-[70vh] w-full object-contain">
                                    <source src="{{ asset('storage/'.$post->media_path) }}" type="video/mp4">
                                </video>
                            @endif
                        </div>

                        {{-- Caption --}}
                        @if($post->content)
                            <div class="px-4 py-3">
                                <p class="text-gray-800 text-sm">{{ $post->content }}</p>
                            </div>
                        @endif

                        {{-- Post Actions --}}
                        <div class="px-4 py-2 border-t flex items-center gap-6">
                            <button onclick="likePost({{ $post->id }})" 
                                    id="like-btn-{{ $post->id }}"
                                    class="flex items-center gap-2 text-gray-600 hover:text-red-500">
                                ❤️ <span id="like-count-{{ $post->id }}">{{ $post->likes()->count() }}</span>
                            </button>
                            <button class="toggle-comments flex items-center gap-2 text-gray-600 hover:text-blue-500">
                                💬 <span>{{ $post->comments()->count() }}</span>
                            </button>
                        </div>

                        {{-- Comments Section --}}
                        <div class="comments-section hidden border-t px-4 py-3 space-y-3">
                            {{-- Add Comment --}}
                            <form class="comment-form flex gap-2" data-id="{{ $post->id }}">
                                @csrf
                                <input type="text" 
                                       name="content" 
                                       placeholder="Write a comment..." 
                                       class="flex-1 border rounded-full px-3 py-1 text-sm">
                            </form>

                            {{-- Comments List --}}
                            <div class="comments-list space-y-2">
                                @foreach ($post->comments as $comment)
                                    <div class="flex gap-2">
                                        <strong>{{ $comment->user->name }}</strong>
                                        <span class="text-gray-700 text-sm">{{ $comment->content }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </article>
                </div>
                @endif
            @endforeach
        </div>
    </div>

    {{-- Scroll to clicked post --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const targetId = "{{ $startId ?? '' }}";
            if (targetId) {
                const el = document.getElementById("post-" + targetId);
                if (el) el.scrollIntoView({ behavior: "instant", block: "start" });
            }

            // Toggle comments
            document.querySelectorAll(".toggle-comments").forEach(btn => {
                btn.addEventListener("click", () => {
                    btn.closest("article").querySelector(".comments-section").classList.toggle("hidden");
                });
            });
        });

        function likePost(postId) {
            fetch(`/posts/${postId}/like`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({})
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`like-count-${postId}`).innerText = data.likes_count;
                }
            });
        }
    </script>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</x-app-layout>
