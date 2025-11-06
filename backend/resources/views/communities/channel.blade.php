<x-app-layout>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        .post-item {
            animation: fadeIn 0.4s ease-out forwards;
            opacity: 0;
        }
        .post-item:nth-child(1) { animation-delay: 0.1s; }
        .post-item:nth-child(2) { animation-delay: 0.15s; }
        .post-item:nth-child(3) { animation-delay: 0.2s; }
        .post-item:nth-child(n+4) { animation-delay: 0.25s; }
    </style>

    <div class="flex" style="background-color: #F2F2F2; min-height: 100vh;">
        {{-- Channels Sidebar --}}
        <div class="w-64 shrink-0" style="background-color: #FFFFFF; border-right: 2px solid #B6B09F;">
            {{-- Community Header --}}
          {{-- Channel Header --}}
<div class="px-6 py-4 animate-fade-in" style="background-color: #FFFFFF; border-bottom: 2px solid #B6B09F;">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold flex items-center space-x-2" style="color: #000000;">
                @if($channel->type === 'announcement')
                    <svg class="w-6 h-6" style="color: #B6B09F;" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                    </svg>
                @endif
                <span>{{ $channel->name }}</span>
                @if($channel->is_private)
                    <svg class="w-5 h-5" style="color: #B6B09F;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                @endif
            </h1>
            @if($channel->description)
                <p class="text-sm mt-1" style="color: #B6B09F;">{{ $channel->description }}</p>
            @endif
        </div>

        <div class="flex items-center space-x-2">
            @if($userRole === 'admin' || $isChannelModerator)
                {{-- Access Requests Button (with badge if there are pending requests) --}}
                @if($channel->is_private && isset($pendingAccessCount) && $pendingAccessCount > 0)
                    <a href="{{ route('communities.channels.access-requests', [$community->slug, $channel->slug]) }}"
                       class="relative px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px]"
                       style="background-color: #FEF3C7; color: #92400E; border: 1px solid #F59E0B;">
                        Access Requests
                        <span class="absolute -top-2 -right-2 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold"
                              style="background-color: #EF4444; color: #FFFFFF;">
                            {{ $pendingAccessCount }}
                        </span>
                    </a>
                @endif

                @if($userRole === 'admin')
                    <a href="{{ route('communities.channels.moderators', [$community->slug, $channel->slug]) }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px]"
                       style="background-color: #E0E7FF; color: #3730A3; border: 1px solid #818CF8;">
                        Manage Moderators
                    </a>
                    <button onclick="openEditChannelModal()" 
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px]"
                            style="background-color: #EAE4D5; color: #000000; border: 1px solid #B6B09F;">
                        Edit Channel
                    </button>
                @endif
            @endif
        </div>
    </div>
</div>

            {{-- Channels List --}}
            <div class="p-4">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold uppercase tracking-wide" style="color: #B6B09F;">Channels</h3>
                    @if($userRole === 'admin' || $userRole === 'moderator')
                        <button onclick="openCreateChannelModal()" 
                                class="text-xs transition-colors duration-300 hover:text-black" 
                                style="color: #B6B09F;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    @endif
                </div>

                <div class="space-y-1">
                    @foreach($community->channels as $ch)
                        <a href="{{ route('communities.channel', [$community->slug, $ch->slug]) }}" 
                           class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all duration-300 hover:translate-x-1 {{ $channel->id === $ch->id ? 'active-channel' : '' }}"
                           style="color: #000000;">
                            @if($ch->type === 'announcement')
                                <svg class="w-4 h-4 shrink-0" style="color: #B6B09F;" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                </svg>
                            @else
                                <svg class="w-4 h-4 shrink-0" style="color: #B6B09F;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zm-3 0h-2v2h2V9z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                            <span class="text-sm truncate">{{ $ch->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col max-h-screen">
            {{-- Channel Header --}}
            <div class="px-6 py-4 animate-fade-in" style="background-color: #FFFFFF; border-bottom: 2px solid #B6B09F;">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold flex items-center space-x-2" style="color: #000000;">
                            @if($channel->type === 'announcement')
                                <svg class="w-6 h-6" style="color: #B6B09F;" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                </svg>
                            @endif
                            <span>{{ $channel->name }}</span>
                        </h1>
                        @if($channel->description)
                            <p class="text-sm mt-1" style="color: #B6B09F;">{{ $channel->description }}</p>
                        @endif
                    </div>

                    <div class="flex items-center space-x-2">
                        @if($userRole === 'admin')
                            <a href="{{ route('communities.channels.moderators', [$community->slug, $channel->slug]) }}"
                               class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px]"
                               style="background-color: #E0E7FF; color: #3730A3; border: 1px solid #818CF8;">
                                Manage Moderators
                            </a>
                            <button onclick="openEditChannelModal()" 
                                    class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px]"
                                    style="background-color: #EAE4D5; color: #000000; border: 1px solid #B6B09F;">
                                Edit Channel
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Posts Area --}}
            <div class="flex-1 overflow-y-auto p-6">
                <div class="max-w-3xl mx-auto space-y-4">
                    {{-- Create Post Form --}}
                    @if($canPost)
                        <div class="rounded-lg p-4 animate-fade-in" style="background-color: #FFFFFF; border: 1px solid #B6B09F;">
                            <form action="{{ route('communities.posts.store', [$community->slug, $channel->slug]) }}" 
                                  method="POST" 
                                  enctype="multipart/form-data">
                                @csrf
                                
                                <div class="flex space-x-3">
                                    <div class="w-10 h-10 rounded-full overflow-hidden shrink-0" style="background-color: #EAE4D5;">
                                        @if(auth()->user()->profile_picture)
                                            <img src="{{ asset('storage/'.auth()->user()->profile_picture) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center font-bold text-sm" style="color: #000000;">
                                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-1">
                                        <textarea name="content" 
                                                  rows="3" 
                                                  required
                                                  placeholder="Share something with the community..."
                                                  class="w-full px-4 py-2 rounded-lg mb-2 resize-none"
                                                  style="border: 2px solid #B6B09F; background-color: #F2F2F2; color: #000000;"></textarea>
                                        
                                        <div class="flex items-center justify-between">
                                            <label class="cursor-pointer text-sm flex items-center space-x-2 transition-colors duration-300 hover:text-black" style="color: #B6B09F;">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                </svg>
                                                <span>Attach file</span>
                                                <input type="file" name="media" class="hidden" accept="image/*,video/*">
                                            </label>

                                            <button type="submit" 
                                                    class="px-6 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px]"
                                                    style="background-color: #000000; color: #F2F2F2;">
                                                Post
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="rounded-lg p-4 text-center animate-fade-in" style="background-color: #EAE4D5; border: 1px solid #B6B09F;">
                            <p style="color: #000000;">
                                @if($channel->type === 'announcement')
                                    Only admins can post in announcement channels.
                                @else
                                    You don't have permission to post in this channel.
                                @endif
                            </p>
                        </div>
                    @endif

                    {{-- Posts List --}}
                    @forelse($posts as $post)
                        <div class="post-item rounded-lg p-4" style="background-color: #FFFFFF; border: 1px solid #B6B09F;">
                            <div class="flex items-start space-x-3">
                                {{-- User Avatar --}}
                                <a href="{{ route('profile.public', ['user' => $post->user->username]) }}" class="shrink-0">
                                    <div class="w-10 h-10 rounded-full overflow-hidden transition-transform duration-300 hover:scale-110" style="background-color: #EAE4D5;">
                                        @if($post->user->profile_picture)
                                            <img src="{{ asset('storage/'.$post->user->profile_picture) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center font-bold text-sm" style="color: #000000;">
                                                {{ strtoupper(substr($post->user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                </a>

                                <div class="flex-1 min-w-0">
                                    {{-- Post Header --}}
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('profile.public', ['user' => $post->user->username]) }}" 
                                               class="font-semibold hover:underline" 
                                               style="color: #000000;">
                                                {{ $post->user->username }}
                                            </a>
                                            <span class="text-xs" style="color: #B6B09F;">{{ $post->created_at->diffForHumans() }}</span>
                                            @if($post->is_pinned)
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium" 
                                                      style="background-color: #EAE4D5; color: #000000;">
                                                    📌 Pinned
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Post Actions Dropdown --}}
                                        @if($post->canEdit(auth()->id()))
                                            <div class="relative" x-data="{ open: false }">
                                                <button @click="open = !open" style="color: #B6B09F;">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                                    </svg>
                                                </button>
                                                <div x-show="open" 
                                                     @click.away="open = false"
                                                     class="absolute right-0 mt-2 w-48 rounded-lg shadow-lg z-10"
                                                     style="background-color: #FFFFFF; border: 1px solid #B6B09F;">
                                                    <div class="py-1">
                                                        @if($userRole === 'admin' || $userRole === 'moderator')
                                                            <form action="{{ route('communities.posts.toggle-pin', $post->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50" style="color: #000000;">
                                                                    {{ $post->is_pinned ? 'Unpin Post' : 'Pin Post' }}
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <button onclick="openEditPostModal({{ $post->id }}, '{{ addslashes($post->content) }}')" 
                                                                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50" 
                                                                style="color: #000000;">
                                                            Edit Post
                                                        </button>
                                                        <form action="{{ route('communities.posts.destroy', $post->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" 
                                                                    onclick="return confirm('Are you sure you want to delete this post?')"
                                                                    class="w-full text-left px-4 py-2 text-sm hover:bg-gray-50" 
                                                                    style="color: #EF4444;">
                                                                Delete Post
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Post Content --}}
                                    <p class="whitespace-pre-line mb-3" style="color: #000000;">{{ $post->content }}</p>

                                    {{-- Post Media --}}
                                    @if($post->media_path)
                                        <div class="mb-3 rounded-lg overflow-hidden" style="border: 1px solid #B6B09F;">
                                            @if($post->media_type === 'image')
                                                <img src="{{ asset('storage/'.$post->media_path) }}" class="w-full">
                                            @elseif($post->media_type === 'video')
                                                <video controls class="w-full">
                                                    <source src="{{ asset('storage/'.$post->media_path) }}" type="video/mp4">
                                                </video>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Post Reactions --}}
                                    <div class="flex items-center space-x-4 text-sm" style="color: #B6B09F;">
                                        <button class="flex items-center space-x-1 transition-colors duration-300 hover:text-red-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                            </svg>
                                            <span>{{ $post->reactions->count() }}</span>
                                        </button>
                                        <button class="flex items-center space-x-1 transition-colors duration-300 hover:text-blue-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                            <span>Reply</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 rounded-lg" style="background-color: #FFFFFF; border: 1px solid #B6B09F;">
                            <svg class="w-16 h-16 mx-auto mb-4" style="color: #B6B09F;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <h3 class="text-xl font-semibold mb-2" style="color: #B6B09F;">No Posts Yet</h3>
                            <p style="color: #B6B09F;">Be the first to start a conversation!</p>
                        </div>
                    @endforelse

                    {{-- Pagination --}}
                    @if($posts->hasPages())
                        <div class="mt-4">
                            {{ $posts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Post Modal --}}
    <div id="editPostModal" class="hidden fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="relative rounded-lg p-6 w-full max-w-md animate-fade-in" style="background-color: #FFFFFF; border: 2px solid #B6B09F;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold" style="color: #000000;">Edit Post</h3>
                    <button onclick="closeEditPostModal()" style="color: #B6B09F;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form id="editPostForm" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2" style="color: #000000;">Content</label>
                        <textarea id="editPostContent" 
                                  name="content" 
                                  rows="5"
                                  required
                                  class="w-full px-4 py-2 rounded-lg"
                                  style="border: 2px solid #B6B09F; background-color: #F2F2F2; color: #000000;"></textarea>
                    </div>

                    <div class="flex space-x-3">
                        <button type="button" 
                                onclick="closeEditPostModal()"
                                class="flex-1 px-4 py-2 rounded-lg font-medium transition-all duration-300"
                                style="background-color: #EAE4D5; color: #000000; border: 1px solid #B6B09F;">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 rounded-lg font-medium transition-all duration-300 hover:shadow-lg"
                                style="background-color: #000000; color: #F2F2F2;">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Channel Modal --}}
    @if($userRole === 'admin' || $userRole === 'moderator')
    <div id="editChannelModal" class="hidden fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="relative rounded-lg p-6 w-full max-w-md animate-fade-in" style="background-color: #FFFFFF; border: 2px solid #B6B09F;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold" style="color: #000000;">Edit Channel</h3>
                    <button onclick="closeEditChannelModal()" style="color: #B6B09F;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('communities.channels.update', [$community->slug, $channel->slug]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2" style="color: #000000;">Channel Name</label>
                        <input type="text" 
                               name="name" 
                               value="{{ $channel->name }}"
                               required
                               class="w-full px-4 py-2 rounded-lg"
                               style="border: 2px solid #B6B09F; background-color: #F2F2F2; color: #000000;">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2" style="color: #000000;">Description</label>
                        <textarea name="description" 
                                  rows="3"
                                  class="w-full px-4 py-2 rounded-lg"
                                  style="border: 2px solid #B6B09F; background-color: #F2F2F2; color: #000000;">{{ $channel->description }}</textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2" style="color: #000000;">Channel Type</label>
                        <select name="type" 
                                class="w-full px-4 py-2 rounded-lg"
                                style="border: 2px solid #B6B09F; background-color: #F2F2F2; color: #000000;">
                            <option value="general" {{ $channel->type === 'general' ? 'selected' : '' }}>General - All members can post</option>
                            <option value="announcement" {{ $channel->type === 'announcement' ? 'selected' : '' }}>Announcement - Only admins can post</option>
                            <option value="restricted" {{ $channel->type === 'restricted' ? 'selected' : '' }}>Restricted - Controlled posting</option>
                        </select>
                    </div>

                    <div class="flex space-x-3">
                        <button type="button" 
                                onclick="closeEditChannelModal()"
                                class="flex-1 px-4 py-2 rounded-lg font-medium transition-all duration-300"
                                style="background-color: #EAE4D5; color: #000000; border: 1px solid #B6B09F;">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 rounded-lg font-medium transition-all duration-300 hover:shadow-lg"
                                style="background-color: #000000; color: #F2F2F2;">
                            Update
                        </button>
                    </div>
                </form>

                @if($userRole === 'admin')
                    <form action="{{ route('communities.channels.destroy', [$community->slug, $channel->slug]) }}" 
                          method="POST" 
                          class="mt-4 pt-4" 
                          style="border-top: 2px solid #B6B09F;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Are you sure you want to delete this channel? All posts will be deleted.')"
                                class="w-full px-4 py-2 rounded-lg font-medium transition-all duration-300"
                                style="background-color: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5;">
                            Delete Channel
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Create Channel Modal --}}
    <div id="createChannelModal" class="hidden fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="relative rounded-lg p-6 w-full max-w-md animate-fade-in" style="background-color: #FFFFFF; border: 2px solid #B6B09F;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold" style="color: #000000;">Create Channel</h3>
                    <button onclick="closeCreateChannelModal()" style="color: #B6B09F;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('communities.channels.store', $community->slug) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2" style="color: #000000;">Channel Name</label>
                        <input type="text" 
                               name="name" 
                               required
                               class="w-full px-4 py-2 rounded-lg"
                               style="border: 2px solid #B6B09F; background-color: #F2F2F2; color: #000000;"
                               placeholder="e.g., General Chat">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2" style="color: #000000;">Description</label>
                        <textarea name="description" 
                                  rows="3"
                                  class="w-full px-4 py-2 rounded-lg"
                                  style="border: 2px solid #B6B09F; background-color: #F2F2F2; color: #000000;"
                                  placeholder="What is this channel about?"></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2" style="color: #000000;">Channel Type</label>
                        <select name="type" 
                                class="w-full px-4 py-2 rounded-lg"
                                style="border: 2px solid #B6B09F; background-color: #F2F2F2; color: #000000;">
                            <option value="general">General - All members can post</option>
                            <option value="announcement">Announcement - Only admins can post</option>
                            <option value="restricted">Restricted - Controlled posting</option>
                        </select>
                    </div>

                    <div class="flex space-x-3">
                        <button type="button" 
                                onclick="closeCreateChannelModal()"
                                class="flex-1 px-4 py-2 rounded-lg font-medium transition-all duration-300"
                                style="background-color: #EAE4D5; color: #000000; border: 1px solid #B6B09F;">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 rounded-lg font-medium transition-all duration-300 hover:shadow-lg"
                                style="background-color: #000000; color: #F2F2F2;">
                            Create
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <script>
        function openEditPostModal(postId, content) {
            const modal = document.getElementById('editPostModal');
            const form = document.getElementById('editPostForm');
            const textarea = document.getElementById('editPostContent');
            
            form.action = `/communities/posts/${postId}`;
            textarea.value = content;
            modal.classList.remove('hidden');
        }

        function closeEditPostModal() {
            document.getElementById('editPostModal').classList.add('hidden');
        }

        function openEditChannelModal() {
            document.getElementById('editChannelModal').classList.remove('hidden');
        }

        function closeEditChannelModal() {
            document.getElementById('editChannelModal').classList.add('hidden');
        }

        function openCreateChannelModal() {
            document.getElementById('createChannelModal').classList.remove('hidden');
        }

        function closeCreateChannelModal() {
            document.getElementById('createChannelModal').classList.add('hidden');
        }

        // Add active state styling
        const style = document.createElement('style');
        style.textContent = `
            .active-channel {
                background-color: #EAE4D5 !important;
                font-weight: 600;
            }
        `;
        document.head.appendChild(style);
    </script>
</x-app-layout>