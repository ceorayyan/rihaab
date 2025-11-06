<x-app-layout>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        .active-channel {
            background-color: #EAE4D5 !important;
            font-weight: 600;
        }
    </style>

    <div class="flex" style="background-color: #F2F2F2; min-height: 100vh;">
        {{-- Channels Sidebar --}}
        <div class="w-64 shrink-0 animate-fade-in" style="background-color: #FFFFFF; border-right: 2px solid #B6B09F;">
            {{-- Community Header --}}
            <div class="p-4" style="border-bottom: 2px solid #B6B09F;">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-12 h-12 rounded-lg overflow-hidden" style="background-color: #EAE4D5;">
                        @if($community->icon)
                            <img src="{{ asset('storage/'.$community->icon) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center font-bold" style="color: #000000;">
                                {{ strtoupper(substr($community->name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h2 class="font-bold truncate" style="color: #000000;">{{ $community->name }}</h2>
                        <p class="text-xs" style="color: #B6B09F;">{{ $community->members->count() }} members</p>
                    </div>
                </div>

                @if(!$isMember)
                    @php
                        $hasPending = $community->hasPendingRequest(Auth::id());
                    @endphp
                    @if($hasPending)
                        <button disabled
                                class="w-full px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed opacity-75"
                                style="background-color: #B6B09F; color: #F2F2F2;">
                            Request Pending
                        </button>
                    @else
                        <form action="{{ route('communities.join', $community->slug) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px]"
                                    style="background-color: #000000; color: #F2F2F2;">
                                {{ $community->is_private ? 'Request to Join' : 'Join Community' }}
                            </button>
                        </form>
                    @endif
                @endif
            </div>

            {{-- Channels List --}}
            @if($isMember)
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
                        @foreach($community->channels as $channel)
                            <a href="{{ route('communities.channel', [$community->slug, $channel->slug]) }}" 
                               class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all duration-300 hover:translate-x-1 {{ $defaultChannel && $defaultChannel->id === $channel->id ? 'active-channel' : '' }}"
                               style="color: #000000;">
                                @if($channel->type === 'announcement')
                                    <svg class="w-4 h-4 shrink-0" style="color: #B6B09F;" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 shrink-0" style="color: #B6B09F;" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zm-3 0h-2v2h2V9z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                                <span class="text-sm truncate">{{ $channel->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Settings Link (Admin Only) --}}
                @if($userRole === 'admin')
                    <div class="p-4" style="border-top: 2px solid #B6B09F;">
                        <a href="{{ route('communities.members', $community->slug) }}" 
                           class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all duration-300 hover:translate-x-1 mb-2"
                           style="color: #000000;">
                            <svg class="w-4 h-4" style="color: #B6B09F;" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                            <span class="text-sm">Members</span>
                            @if($community->pendingMembers->count() > 0)
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold rounded-full" 
                                      style="background-color: #F59E0B; color: #FFFFFF;">
                                    {{ $community->pendingMembers->count() }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('communities.settings', $community->slug) }}" 
                           class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all duration-300 hover:translate-x-1"
                           style="color: #000000;">
                            <svg class="w-4 h-4" style="color: #B6B09F;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-sm">Settings</span>
                        </a>
                    </div>
                @endif
            @endif
        </div>

        {{-- Main Content Area --}}
        <div class="flex-1 p-6 animate-fade-in">
            @if(!$isMember)
                {{-- Join Community Prompt --}}
                <div class="max-w-3xl mx-auto">
                    {{-- Banner --}}
                    <div class="h-48 rounded-lg overflow-hidden mb-6" style="background: linear-gradient(135deg, #000000 0%, #B6B09F 100%);">
                        @if($community->banner)
                            <img src="{{ asset('storage/'.$community->banner) }}" class="w-full h-full object-cover">
                        @endif
                    </div>

                    <div class="text-center rounded-lg p-8" style="background-color: #FFFFFF; border: 1px solid #B6B09F;">
                        <h2 class="text-2xl font-bold mb-2" style="color: #000000;">{{ $community->name }}</h2>
                        <p class="mb-6" style="color: #B6B09F;">{{ $community->description }}</p>
                        
                        <div class="flex items-center justify-center space-x-6 mb-6 text-sm" style="color: #B6B09F;">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                </svg>
                                {{ $community->members->count() }} members
                            </span>
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zm-3 0h-2v2h2V9z" clip-rule="evenodd"/>
                                </svg>
                                {{ $community->channels->count() }} channels
                            </span>
                        </div>

                        <form action="{{ route('communities.join', $community->slug) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="px-8 py-3 rounded-lg font-medium transition-all duration-300 hover:translate-y-[-2px] hover:shadow-xl"
                                    style="background-color: #000000; color: #F2F2F2;">
                                Join Community
                            </button>
                        </form>
                    </div>
                </div>
            @else
                {{-- Welcome Message - Show first channel or instructions --}}
                <div class="max-w-4xl mx-auto">
                    @if($defaultChannel)
                        <div class="rounded-lg p-6 mb-4" style="background-color: #FFFFFF; border: 1px solid #B6B09F;">
                            <h2 class="text-2xl font-bold mb-2" style="color: #000000;">Welcome to {{ $community->name }}!</h2>
                            <p class="mb-4" style="color: #B6B09F;">{{ $community->description }}</p>
                            <a href="{{ route('communities.channel', [$community->slug, $defaultChannel->slug]) }}"
                               class="inline-block px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:translate-y-[-2px]"
                               style="background-color: #000000; color: #F2F2F2;">
                                Go to {{ $defaultChannel->name }}
                            </a>
                        </div>
                    @endif

                    {{-- Recent Activity or Info Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="rounded-lg p-6" style="background-color: #FFFFFF; border: 1px solid #B6B09F;">
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #EAE4D5;">
                                    <svg class="w-6 h-6" style="color: #000000;" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold" style="color: #000000;">Members</h3>
                                    <p class="text-2xl font-bold" style="color: #000000;">{{ $community->members->count() }}</p>
                                </div>
                            </div>
                            <p class="text-sm" style="color: #B6B09F;">Active community members</p>
                        </div>

                        <div class="rounded-lg p-6" style="background-color: #FFFFFF; border: 1px solid #B6B09F;">
                            <div class="flex items-center space-x-3 mb-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background-color: #EAE4D5;">
                                    <svg class="w-6 h-6" style="color: #000000;" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zm-3 0h-2v2h2V9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold" style="color: #000000;">Channels</h3>
                                    <p class="text-2xl font-bold" style="color: #000000;">{{ $community->channels->count() }}</p>
                                </div>
                            </div>
                            <p class="text-sm" style="color: #B6B09F;">Discussion channels</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Create Channel Modal (Admin/Moderator) --}}
    @if($isMember && ($userRole === 'admin' || $userRole === 'moderator'))
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
        function openCreateChannelModal() {
            document.getElementById('createChannelModal').classList.remove('hidden');
        }

        function closeCreateChannelModal() {
            document.getElementById('createChannelModal').classList.add('hidden');
        }
    </script>
</x-app-layout>