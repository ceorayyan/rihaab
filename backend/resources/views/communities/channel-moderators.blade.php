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

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6" style="background-color: #F2F2F2; min-height: 100vh;">
        {{-- Header --}}
        <div class="mb-6 animate-fade-in">
            <a href="{{ route('communities.channel', [$community->slug, $channel->slug]) }}" 
               class="inline-flex items-center text-sm mb-4 transition-colors duration-300 hover:underline" 
               style="color: #B6B09F;">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Back to Channel
            </a>
            <h1 class="text-3xl font-bold" style="color: #000000;">Channel Moderators</h1>
            <p class="text-sm mt-1" style="color: #B6B09F;">Manage moderators for #{{ $channel->name }}</p>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg animate-fade-in" style="background-color: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7;">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 rounded-lg animate-fade-in" style="background-color: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5;">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        {{-- Add Moderator Section --}}
        @if($availableMembers->count() > 0)
            <div class="mb-6 rounded-lg p-6 animate-fade-in" style="background-color: #FFFFFF; border: 1px solid #B6B09F;">
                <h2 class="text-xl font-bold mb-4" style="color: #000000;">Add Channel Moderator</h2>
                <form action="{{ route('communities.channels.add-moderator', [$community->slug, $channel->slug]) }}" method="POST">
                    @csrf
                    <div class="flex space-x-3">
                        <select name="user_id" 
                                required
                                class="flex-1 px-4 py-2 rounded-lg"
                                style="border: 2px solid #B6B09F; background-color: #F2F2F2; color: #000000;">
                            <option value="">Select a member...</option>
                            @foreach($availableMembers as $member)
                                <option value="{{ $member->user_id }}">
                                    {{ $member->user->username }} ({{ $member->user->name }})
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" 
                                class="px-6 py-2 rounded-lg font-medium transition-all duration-300 hover:translate-y-[-2px]"
                                style="background-color: #000000; color: #F2F2F2;">
                            Add Moderator
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Current Moderators --}}
        <div class="rounded-lg p-6 animate-fade-in" style="background-color: #FFFFFF; border: 1px solid #B6B09F;">
            <h2 class="text-xl font-bold mb-4" style="color: #000000;">
                Current Moderators ({{ $channelModerators->count() }})
            </h2>

            @if($channelModerators->count() > 0)
                <div class="space-y-3">
                    @foreach($channelModerators as $moderator)
                        <div class="rounded-lg p-4 flex items-center justify-between transition-all duration-300 hover:shadow-lg" 
                             style="background-color: #F2F2F2; border: 1px solid #B6B09F;">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('profile.public', ['user' => $moderator->user->username]) }}">
                                    <div class="w-12 h-12 rounded-full overflow-hidden transition-transform duration-300 hover:scale-110" 
                                         style="border: 2px solid #B6B09F;">
                                        @if($moderator->user->profile_picture)
                                            <img src="{{ asset('storage/'.$moderator->user->profile_picture) }}" 
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center font-bold" 
                                                 style="background: linear-gradient(135deg, #000000 0%, #B6B09F 100%); color: #F2F2F2;">
                                                {{ strtoupper(substr($moderator->user->name, 0, 2)) }}
                                            </div>
                                        @endif
                                    </div>
                                </a>
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('profile.public', ['user' => $moderator->user->username]) }}" 
                                           class="font-semibold hover:underline" 
                                           style="color: #000000;">
                                            {{ $moderator->user->username }}
                                        </a>
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium" 
                                              style="background-color: #E0E7FF; color: #3730A3;">
                                            Channel Moderator
                                        </span>
                                    </div>
                                    <p class="text-sm" style="color: #B6B09F;">{{ $moderator->user->name }}</p>
                                    <p class="text-xs" style="color: #B6B09F;">Added {{ $moderator->created_at->diffForHumans() }}</p>
                                </div>
                            </div>

                            <form action="{{ route('communities.channels.remove-moderator', [$community->slug, $channel->slug, $moderator->id]) }}" 
                                  method="POST"
                                  onsubmit="return confirm('Remove this channel moderator?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px]"
                                        style="background-color: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5;">
                                    Remove
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto mb-4" style="color: #B6B09F;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <h3 class="text-xl font-semibold mb-2" style="color: #B6B09F;">No Channel Moderators</h3>
                    <p style="color: #B6B09F;">Add moderators to help manage this channel.</p>
                </div>
            @endif
        </div>

        {{-- Info Box --}}
        <div class="mt-6 rounded-lg p-4" style="background-color: #E0E7FF; border: 1px solid #818CF8;">
            <h3 class="font-semibold mb-2" style="color: #3730A3;">Channel Moderator Permissions:</h3>
            <ul class="text-sm space-y-1" style="color: #3730A3;">
                <li>• Can post in announcement channels</li>
                <li>• Can edit and delete any post in this channel</li>
                <li>• Can pin/unpin posts in this channel</li>
                <li>• Cannot edit channel settings (admin only)</li>
                <li>• Cannot manage other moderators (admin only)</li>
            </ul>
        </div>
    </div>
</x-app-layout>