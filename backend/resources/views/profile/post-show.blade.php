<x-app-layout>
    <div class="max-w-4xl mx-auto">
        <!-- Container for vertical scroll -->
        <div class="h-screen overflow-y-scroll snap-y snap-mandatory">
            @foreach($posts as $post)
                <div class="h-screen flex items-center justify-center snap-start">
                    <div class="bg-white rounded-lg shadow p-4 w-full max-w-lg">
                        <!-- User info -->
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-10 h-10 rounded-full overflow-hidden">
                                <img src="{{ asset($post->user->profile_picture ?? 'default.jpg') }}" alt="" class="w-full h-full object-cover">
                            </div>
                            <span class="font-semibold">{{ $post->user->name }}</span>
                        </div>

                        <!-- Media -->
                        @if($post->media_type === 'image')
                            <img src="{{ asset('storage/'.$post->media_path) }}" class="rounded-lg mb-4 w-full object-contain max-h-[70vh]" />
                        @elseif($post->media_type === 'video')
                            <video controls autoplay loop muted class="rounded-lg mb-4 w-full max-h-[70vh]">
                                <source src="{{ asset('storage/'.$post->media_path) }}" type="video/mp4">
                            </video>
                        @endif

                        <!-- Caption -->
                        @if($post->content)
                            <p class="text-gray-800">{{ $post->content }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
