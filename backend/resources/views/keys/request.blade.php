<x-app-layout>
    <h2 class="text-xl font-bold mb-4">Key Requests</h2>

    @forelse($requests as $request)
        <div class="p-4 border rounded mb-2 flex justify-between">
            <span>{{ $request->sender->name }} has sent you a key</span>
            <div class="space-x-2">
                <form method="POST" action="{{ route('keys.accept', $request->id) }}">
                    @csrf
                    <button class="bg-green-500 text-white px-2 py-1 rounded">Accept</button>
                </form>
                <form method="POST" action="{{ route('keys.decline', $request->id) }}">
                    @csrf
                    <button class="bg-red-500 text-white px-2 py-1 rounded">Decline</button>
                </form>
            </div>
        </div>
    @empty
        <p>No pending key requests.</p>
    @endforelse
</x-app-layout>
