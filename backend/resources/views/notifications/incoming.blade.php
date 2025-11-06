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
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        .animate-slide-in {
            animation: slideIn 0.5s ease-out;
        }
        .notification-item {
            animation: fadeIn 0.4s ease-out forwards;
            opacity: 0;
        }
        .notification-item:nth-child(1) { animation-delay: 0.1s; }
        .notification-item:nth-child(2) { animation-delay: 0.2s; }
        .notification-item:nth-child(3) { animation-delay: 0.3s; }
        .notification-item:nth-child(4) { animation-delay: 0.4s; }
        .notification-item:nth-child(n+5) { animation-delay: 0.5s; }
        .pulse-dot {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6" style="background-color: #F2F2F2; min-height: 100vh;">
        {{-- Header --}}
        <div class="mb-6 animate-fade-in">
            <h1 class="text-2xl font-bold" style="color: #000000;">Notifications</h1>
            <p class="text-sm mt-1" style="color: #B6B09F;">Your activity updates</p>
        </div>

        {{-- Flash Messages --}}
        @if(session('error'))
            <div class="mb-4 p-4 rounded-lg animate-slide-in" style="background-color: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5;">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

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

        {{-- Notifications List --}}
        <div class="space-y-3">
            {{-- Combined Notifications --}}
            @php
                $allNotifications = collect();
                
                // Add key requests
                if(isset($requests) && $requests->isNotEmpty()) {
                    foreach($requests as $request) {
                        $allNotifications->push([
                            'type' => 'key_request',
                            'user' => $request->sender,
                            'data' => $request,
                            'created_at' => $request->created_at
                        ]);
                    }
                }
                
                // Add likes
                if(isset($likes) && $likes->isNotEmpty()) {
                    foreach($likes as $like) {
                        $allNotifications->push([
                            'type' => 'like',
                            'user' => $like->user,
                            'data' => $like,
                            'created_at' => $like->created_at
                        ]);
                    }
                }
                
                // Add comments
                if(isset($comments) && $comments->isNotEmpty()) {
                    foreach($comments as $comment) {
                        $allNotifications->push([
                            'type' => 'comment',
                            'user' => $comment->user,
                            'data' => $comment,
                            'created_at' => $comment->created_at
                        ]);
                    }
                }
                
                // Sort by date (newest first)
                $allNotifications = $allNotifications->sortByDesc('created_at');
            @endphp

            @if($allNotifications->isEmpty())
                <div class="text-center py-12 animate-fade-in">
                    <svg class="w-16 h-16 mx-auto mb-4" style="color: #B6B09F;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <h3 class="text-xl font-semibold mb-2" style="color: #B6B09F;">No Notifications</h3>
                    <p style="color: #B6B09F;">When someone interacts with your content, you'll see it here.</p>
                </div>
            @else
                @foreach($allNotifications as $notification)
                    <div class="notification-item rounded-lg p-4 transition-all duration-300 hover:shadow-lg" 
                         style="background-color: #FFFFFF; border: 1px solid #B6B09F;">
                        
                        {{-- Key Request Notification --}}
                        @if($notification['type'] === 'key_request')
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3 flex-1">
                                    {{-- Profile Picture --}}
                                    <a href="{{ route('profile.public', ['user' => $notification['user']->username]) }}" 
                                       class="shrink-0">
                                        <div class="w-12 h-12 rounded-full overflow-hidden transition-transform duration-300 hover:scale-110" 
                                             style="border: 2px solid #B6B09F;">
                                            @if($notification['user']->profile_picture)
                                                <img src="{{ asset('storage/'.$notification['user']->profile_picture) }}" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="flex items-center justify-center w-full h-full text-sm font-bold" 
                                                     style="background: linear-gradient(135deg, #000000 0%, #B6B09F 100%); color: #F2F2F2;">
                                                    {{ strtoupper(substr($notification['user']->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                    </a>

                                    {{-- Text Content --}}
                                    <div class="flex-1">
                                        <p style="color: #000000;">
                                            <a href="{{ route('profile.public', ['user' => $notification['user']->username]) }}" 
                                               class="font-semibold hover:underline">{{ $notification['user']->username }}</a>
                                            <span class="font-normal"> sent you a key request.</span>
                                        </p>
                                        <p class="text-xs mt-1" style="color: #B6B09F;">
                                            {{ $notification['created_at']->diffForHumans() }}
                                        </p>
                                    </div>

                                    {{-- Request Icon --}}
                                    <div class="shrink-0 mr-2">
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center" 
                                             style="background-color: #EAE4D5;">
                                            <svg class="w-4 h-4" style="color: #000000;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="flex space-x-2 ml-3">
                                    <form action="{{ route('keyrequest.accept', $notification['data']->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px] hover:shadow-lg"
                                                style="background-color: #000000; color: #F2F2F2;">
                                            Accept
                                        </button>
                                    </form>

                                    <form action="{{ route('keyrequest.reject', $notification['data']->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px] hover:shadow-lg"
                                                style="background-color: #EAE4D5; color: #000000; border: 1px solid #B6B09F;">
                                            Decline
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif

                        {{-- Like Notification --}}
                        @if($notification['type'] === 'like')
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3 flex-1">
                                    {{-- Profile Picture --}}
                                    <a href="{{ route('profile.public', ['user' => $notification['user']->username]) }}" 
                                       class="shrink-0">
                                        <div class="w-12 h-12 rounded-full overflow-hidden transition-transform duration-300 hover:scale-110" 
                                             style="border: 2px solid #B6B09F;">
                                            @if($notification['user']->profile_picture)
                                                <img src="{{ asset('storage/'.$notification['user']->profile_picture) }}" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="flex items-center justify-center w-full h-full text-sm font-bold" 
                                                     style="background: linear-gradient(135deg, #000000 0%, #B6B09F 100%); color: #F2F2F2;">
                                                    {{ strtoupper(substr($notification['user']->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                    </a>

                                    {{-- Text Content --}}
                                    <div class="flex-1">
                                        <p style="color: #000000;">
                                            <a href="{{ route('profile.public', ['user' => $notification['user']->username]) }}" 
                                               class="font-semibold hover:underline">{{ $notification['user']->username }}</a>
                                            <span class="font-normal"> liked your post.</span>
                                        </p>
                                        <p class="text-xs mt-1" style="color: #B6B09F;">
                                            {{ $notification['created_at']->diffForHumans() }}
                                        </p>
                                    </div>

                                    {{-- Like Icon --}}
                                    <div class="shrink-0 mr-2">
                                        <svg class="w-8 h-8" style="color: #EF4444;" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                        </svg>
                                    </div>
                                </div>

                                {{-- Post Thumbnail --}}
                                @if($notification['data']->post && $notification['data']->post->media_path)
                                    <a href="{{ route('profile.feed', ['id' => $notification['data']->post->id]) }}" 
                                       class="shrink-0 ml-3">
                                        <div class="w-12 h-12 rounded overflow-hidden transition-transform duration-300 hover:scale-110" 
                                             style="border: 1px solid #B6B09F;">
                                            @if($notification['data']->post->media_type === 'image')
                                                <img src="{{ asset('storage/'.$notification['data']->post->media_path) }}" 
                                                     class="w-full h-full object-cover">
                                            @elseif($notification['data']->post->media_type === 'video')
                                                <video class="w-full h-full object-cover">
                                                    <source src="{{ asset('storage/'.$notification['data']->post->media_path) }}" type="video/mp4">
                                                </video>
                                            @endif
                                        </div>
                                    </a>
                                @endif
                            </div>
                        @endif

                        {{-- Comment Notification --}}
                        @if($notification['type'] === 'comment')
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3 flex-1">
                                    {{-- Profile Picture --}}
                                    <a href="{{ route('profile.public', ['user' => $notification['user']->username]) }}" 
                                       class="shrink-0">
                                        <div class="w-12 h-12 rounded-full overflow-hidden transition-transform duration-300 hover:scale-110" 
                                             style="border: 2px solid #B6B09F;">
                                            @if($notification['user']->profile_picture)
                                                <img src="{{ asset('storage/'.$notification['user']->profile_picture) }}" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="flex items-center justify-center w-full h-full text-sm font-bold" 
                                                     style="background: linear-gradient(135deg, #000000 0%, #B6B09F 100%); color: #F2F2F2;">
                                                    {{ strtoupper(substr($notification['user']->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                    </a>

                                    {{-- Text Content --}}
                                    <div class="flex-1">
                                        <p style="color: #000000;">
                                            <a href="{{ route('profile.public', ['user' => $notification['user']->username]) }}" 
                                               class="font-semibold hover:underline">{{ $notification['user']->username }}</a>
                                            <span class="font-normal"> commented: </span>
                                            <span style="color: #B6B09F;">{{ Str::limit($notification['data']->content, 50) }}</span>
                                        </p>
                                        <p class="text-xs mt-1" style="color: #B6B09F;">
                                            {{ $notification['created_at']->diffForHumans() }}
                                        </p>
                                    </div>

                                    {{-- Comment Icon --}}
                                    <div class="shrink-0 mr-2">
                                        <svg class="w-8 h-8" style="color: #3B82F6;" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M21 6h-2l-1.27-1.27A2 2 0 0 0 16.32 4H15V2a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h2v2a2 2 0 0 0 2 2h8.32a2 2 0 0 0 1.41-.59L21 15.93A2 2 0 0 0 21 14V8a2 2 0 0 0-2-2z"/>
                                        </svg>
                                    </div>
                                </div>

                                {{-- Post Thumbnail --}}
                                @if($notification['data']->post && $notification['data']->post->media_path)
                                    <a href="{{ route('profile.feed', ['id' => $notification['data']->post->id]) }}" 
                                       class="shrink-0 ml-3">
                                        <div class="w-12 h-12 rounded overflow-hidden transition-transform duration-300 hover:scale-110" 
                                             style="border: 1px solid #B6B09F;">
                                            @if($notification['data']->post->media_type === 'image')
                                                <img src="{{ asset('storage/'.$notification['data']->post->media_path) }}" 
                                                     class="w-full h-full object-cover">
                                            @elseif($notification['data']->post->media_type === 'video')
                                                <video class="w-full h-full object-cover">
                                                    <source src="{{ asset('storage/'.$notification['data']->post->media_path) }}" type="video/mp4">
                                                </video>
                                            @endif
                                        </div>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</x-app-layout>