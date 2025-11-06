<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">People</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">
        <form method="GET" action="{{ route('people') }}" class="mb-4">
            <input type="text" name="search" placeholder="Search by name or username"
                   value="{{ $search }}"
                   class="border rounded px-3 py-2 w-full">
        </form>

        <div class="bg-white shadow rounded-lg p-4">
            @forelse ($users as $user)
                <div class="flex items-center justify-between py-2 border-b">
                    <div>
                        <a href="{{ route('profile.public', $user) }}" class="font-bold">
                            {{ $user->name }}
                        </a>
                        <div class="text-sm text-gray-500">{{ '@' . $user->username }}</div>
                    </div>

                    @if(auth()->id() !== $user->id)
                        @php
                            $existingRequest = \App\Models\KeyRequest::where(function ($q) use ($user) {
                                $q->where('sender_id', auth()->id())
                                  ->where('receiver_id', $user->id);
                            })->orWhere(function ($q) use ($user) {
                                $q->where('sender_id', $user->id)
                                  ->where('receiver_id', auth()->id());
                            })->first();
                        @endphp

                        @if(!$existingRequest)
                            <!-- No request yet -->
                            <form action="{{ route('keyrequest.send', $user->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="bg-blue-500 text-dark px-3 py-1 rounded">
                                    Send Key Request
                                </button>
                            </form>
                        @elseif($existingRequest->status === 'pending')
                            <!-- Request sent but pending -->
                            <button class="bg-gray-400 text-dark px-3 py-1 rounded" disabled>
                                Request Sent
                            </button>
                        @elseif($existingRequest->status === 'accepted')
                            <!-- Already connected -->
                            <button class="bg-green-500 text-dark px-3 py-1 rounded" disabled>
                                Connected
                            </button>
                        @endif
                    @endif
                </div>
            @empty
                <p>No users found.</p>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
