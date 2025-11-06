<x-app-layout>
    <div class="bg-white shadow rounded p-6 max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">My Keys</h1>

        @if($keys->isEmpty())
            <p class="text-gray-500">You don’t have any keys yet.</p>
        @else
            <ul class="space-y-4">
                @foreach($keys as $key)
                    @php
                        $otherUser = $key->sender_id === auth()->id()
                            ? $key->receiver
                            : $key->sender;
                    @endphp

                    <li class="flex justify-between items-center p-4 border rounded bg-gray-50">
                        <span class="font-medium text-gray-800">
                            {{ $otherUser->name }}
                        </span>
                        <span class="text-green-600 font-semibold">
                            ✔ Connected
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</x-app-layout>
