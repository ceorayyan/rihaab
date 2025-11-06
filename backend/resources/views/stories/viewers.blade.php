<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Story Viewers
            </h2>
            <div class="space-x-2">
                <a href="{{ route('stories.show', $story) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                    Back to Story
                </a>
                <a href="{{ route('stories.my-stories') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm">
                    My Stories
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Story Summary -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                    @if($story->type === 'image')
                                        <img src="{{ asset('storage/' . $story->content) }}" 
                                             class="w-full h-full object-cover rounded-lg">
                                    @elseif($story->type === 'video')
                                        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                        </svg>
                                    @else
                                        <svg class="w-8 h-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="font-semibold text-lg">Your {{ ucfirst($story->type) }} Story</h3>
                                    <p class="text-gray-500 text-sm">Created {{ $story->created_at->diffForHumans() }}</p>
                                    @if($story->caption)
                                        <p class="text-gray-600 text-sm mt-1">{{ Str::limit($story->caption, 50) }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-blue-600">{{ $viewers->count() }}</div>
                                <div class="text-sm text-gray-600">{{ Str::plural('View', $viewers->count()) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Viewers List -->
                    @if($viewers->isEmpty())
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No views yet</h3>
                            <p class="text-gray-500">Your story hasn't been viewed by anyone yet. Share it with your connections!</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            <h3 class="font-semibold text-lg text-gray-800 mb-4">
                                People who viewed your story ({{ $viewers->count() }})
                            </h3>
                            
                            @foreach($viewers as $view)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold mr-4">
                                            {{ strtoupper(substr($view->viewer->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $view->viewer->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $view->viewer->email }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-500">Viewed</div>
                                        <div class="text-xs text-gray-400">{{ $view->viewed_at->diffForHumans() }}</div>
                                        <div class="text-xs text-gray-400">{{ $view->viewed_at->format('M j, Y g:i A') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- View Statistics -->
                        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 rounded-lg p-4 text-center">
                                <div class="text-lg font-bold text-blue-600">{{ $viewers->count() }}</div>
                                <div class="text-sm text-gray-600">Total Views</div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4 text-center">
                                <div class="text-lg font-bold text-green-600">
                                    {{ $viewers->where('viewed_at', '>=', now()->subDay())->count() }}
                                </div>
                                <div class="text-sm text-gray-600">Views Today</div>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-4 text-center">
                                <div class="text-lg font-bold text-purple-600">
                                    @if($viewers->count() > 0)
                                        {{ $viewers->first()->viewed_at->diffForHumans() }}
                                    @else
                                        N/A
                                    @endif
                                </div>
                                <div class="text-sm text-gray-600">Latest View</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>