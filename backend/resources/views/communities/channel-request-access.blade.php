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

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-6" style="background-color: #F2F2F2; min-height: 100vh;">
        {{-- Back Button --}}
        <div class="mb-6 animate-fade-in">
            <a href="{{ route('communities.show', $community->slug) }}" 
               class="inline-flex items-center text-sm transition-colors duration-300 hover:underline" 
               style="color: #B6B09F;">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Back to Community
            </a>
        </div>

        {{-- Main Card --}}
        <div class="rounded-xl p-8 text-center animate-fade-in" style="background-color: #FFFFFF; border: 2px solid #B6B09F;">
            {{-- Lock Icon --}}
            <div class="w-20 h-20 rounded-full mx-auto mb-6 flex items-center justify-center" style="background-color: #EAE4D5;">
                <svg class="w-10 h-10" style="color: #000000;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                </svg>
            </div>

            {{-- Channel Info --}}
            <h1 class="text-2xl font-bold mb-2" style="color: #000000;">
                # {{ $channel->name }}
            </h1>
            <p class="text-sm mb-2" style="color: #B6B09F;">
                Private Channel
            </p>
            @if($channel->description)
                <p class="mb-6" style="color: #000000;">{{ $channel->description }}</p>
            @endif

            {{-- Info Box --}}
            <div class="rounded-lg p-4 mb-6" style="background-color: #FEF3C7; border: 1px solid #F59E0B;">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 shrink-0 mt-0.5" style="color: #F59E0B;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-left">
                        <p class="text-sm font-semibold mb-1" style="color: #92400E;">Private Channel Access</p>
                        <p class="text-sm" style="color: #92400E;">
                            This is a private channel. You need approval from a channel moderator or admin to access it.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-6 p-4 rounded-lg" style="background-color: #D1FAE5; border: 1px solid #6EE7B7;">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" style="color: #065F46;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span style="color: #065F46;">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-lg" style="background-color: #FEE2E2; border: 1px solid #FCA5A5;">
                    <div class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" style="color: #991B1B;" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span style="color: #991B1B;">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            {{-- Check if already has pending request --}}
            @if($channel->hasPendingAccessRequest(Auth::id()))
                <div class="rounded-lg p-6 mb-6" style="background-color: #EAE4D5; border: 1px solid #B6B09F;">
                    <svg class="w-12 h-12 mx-auto mb-3" style="color: #B6B09F;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="text-lg font-semibold mb-2" style="color: #000000;">Request Pending</h3>
                    <p style="color: #B6B09F;">Your access request is waiting for approval.</p>
                </div>
            @else
                {{-- Request Access Form --}}
                <form action="{{ route('communities.channels.request-access', [$community->slug, $channel->slug]) }}" 
                      method="POST"
                      class="text-left">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2" style="color: #000000;">
                            Message (Optional)
                        </label>
                        <textarea name="message" 
                                  rows="4"
                                  class="w-full px-4 py-3 rounded-lg"
                                  style="border: 2px solid #B6B09F; background-color: #F2F2F2; color: #000000;"
                                  placeholder="Tell the moderators why you'd like to join this channel..."></textarea>
                        <p class="text-xs mt-1" style="color: #B6B09F;">Optional: Explain why you want access to this channel</p>
                    </div>

                    <div class="flex space-x-3">
                        <a href="{{ route('communities.show', $community->slug) }}"
                           class="flex-1 px-6 py-3 rounded-lg font-medium text-center transition-all duration-300 hover:translate-y-[-2px]"
                           style="background-color: #EAE4D5; color: #000000; border: 1px solid #B6B09F;">
                            Cancel
                        </a>
                        <button type="submit"
                                class="flex-1 px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:translate-y-[-2px] hover:shadow-xl"
                                style="background-color: #000000; color: #F2F2F2;">
                            Request Access
                        </button>
                    </div>
                </form>
            @endif
        </div>

        {{-- Channel Stats --}}
        <div class="mt-6 rounded-lg p-4" style="background-color: #FFFFFF; border: 1px solid #B6B09F;">
            <h3 class="text-sm font-semibold mb-3" style="color: #000000;">Channel Information</h3>
            <div class="space-y-2 text-sm">
                <div class="flex items-center justify-between">
                    <span style="color: #B6B09F;">Type</span>
                    <span class="font-medium capitalize" style="color: #000000;">{{ $channel->type }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span style="color: #B6B09F;">Privacy</span>
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium" 
                          style="background-color: #E0E7FF; color: #3730A3;">
                        🔒 Private
                    </span>
                </div>
                @if($channel->moderators()->count() > 0)
                    <div class="flex items-center justify-between">
                        <span style="color: #B6B09F;">Moderators</span>
                        <span class="font-medium" style="color: #000000;">{{ $channel->moderators()->count() }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>