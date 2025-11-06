<x-app-layout>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        .request-item {
            animation: fadeIn 0.4s ease-out forwards;
            opacity: 0;
        }
        .request-item:nth-child(1) { animation-delay: 0.1s; }
        .request-item:nth-child(2) { animation-delay: 0.15s; }
        .request-item:nth-child(3) { animation-delay: 0.2s; }
        .request-item:nth-child(n+4) { animation-delay: 0.25s; }
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
            <h1 class="text-3xl font-bold" style="color: #000000;">Channel Access Requests</h1>
            <p class="text-sm mt-1" style="color: #B6B09F;">Manage access requests for #{{ $channel->name }}</p>
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

        {{-- Pending Requests --}}
        <div class="rounded-lg p-6 animate-fade-in" style="background-color: #FFFFFF; border: 1px solid #B6B09F;">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold" style="color: #000000;">
                    Pending Requests ({{ $pendingRequests->count() }})
                </h2>
            </div>

            @if($pendingRequests->count() > 0)
                <div class="space-y-4">
                    @foreach($pendingRequests as $request)
                        <div class="request-item rounded-lg p-4" style="background-color: #F2F2F2; border: 1px solid #B6B09F;">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-3 flex-1">
                                    {{-- User Avatar --}}
                                    <a href="{{ route('profile.public', ['user' => $request->user->username]) }}">
                                        <div class="w-12 h-12 rounded-full overflow-hidden transition-transform duration-300 hover:scale-110" 
                                             style="border: 2px solid #B6B09F;">
                                            @if($request->user->profile_picture)
                                                <img src="{{ asset('storage/'.$request->user->profile_picture) }}" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center font-bold" 
                                                     style="background: linear-gradient(135deg, #000000 0%, #B6B09F 100%); color: #F2F2F2;">
                                                    {{ strtoupper(substr($request->user->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                    </a>

                                    {{-- User Info --}}
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <a href="{{ route('profile.public', ['user' => $request->user->username]) }}" 
                                               class="font-semibold hover:underline" 
                                               style="color: #000000;">
                                                {{ $request->user->username }}
                                            </a>
                                            <span class="text-xs" style="color: #B6B09F;">
                                                {{ $request->user->name }}
                                            </span>
                                        </div>
                                        <p class="text-xs mb-2" style="color: #B6B09F;">
                                            Requested {{ $request->created_at->diffForHumans() }}
                                        </p>
                                        
                                        @if($request->message)
                                            <div class="rounded-lg p-3 mt-2" style="background-color: #FFFFFF; border: 1px solid #B6B09F;">
                                                <p class="text-sm font-semibold mb-1" style="color: #000000;">Message:</p>
                                                <p class="text-sm" style="color: #000000;">{{ $request->message }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="flex space-x-2 ml-3">
                                    <form action="{{ route('communities.channels.approve-access', [$community->slug, $channel->slug, $request->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px]"
                                                style="background-color: #10B981; color: #FFFFFF;">
                                            Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('communities.channels.reject-access', [$community->slug, $channel->slug, $request->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px]"
                                                style="background-color: #EF4444; color: #FFFFFF;">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto mb-4" style="color: #B6B09F;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-xl font-semibold mb-2" style="color: #B6B09F;">No Pending Requests</h3>
                    <p style="color: #B6B09F;">All access requests have been reviewed.</p>
                </div>
            @endif
        </div>

        {{-- Info Box --}}
        <div class="mt-6 rounded-lg p-4" style="background-color: #E0E7FF; border: 1px solid #818CF8;">
            <h3 class="font-semibold mb-2" style="color: #3730A3;">About Channel Access:</h3>
            <ul class="text-sm space-y-1" style="color: #3730A3;">
                <li>• Approved users can view and post in this private channel</li>
                <li>• You can revoke access anytime from the channel moderators page</li>
                <li>• Community admins always have access to all channels</li>
            </ul>
        </div>
    </div>
</x-app-layout>