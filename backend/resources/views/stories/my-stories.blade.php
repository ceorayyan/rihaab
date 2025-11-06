<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl leading-tight" style="color: #000000;">
                {{ __('My Stories') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('stories.create') }}" class="btn-primary font-bold py-2 px-4 rounded text-sm">
                    Add New Story
                </a>
                <a href="{{ route('stories.index') }}" class="btn-secondary font-bold py-2 px-4 rounded text-sm">
                    All Stories
                </a>
            </div>
        </div>
    </x-slot>

    <style>
        .btn-primary {
            background-color: #000000;
            color: #EAE4D5;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: #1a1a1a;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background-color: #B6B09F;
            color: #000000;
        }
        
        .btn-secondary:hover {
            background-color: #a09a89;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .main-card {
            background-color: #EAE4D5;
            border: 2px solid #B6B09F;
        }
        
        .story-card {
            background-color: #EAE4D5;
            border: 2px solid #B6B09F;
            transition: all 0.3s ease;
        }
        
        .story-card:hover {
            border-color: #000000;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            transform: translateY(-4px);
        }
        
        .status-badge-active {
            background-color: #28a745;
        }
        
        .status-badge-expired {
            background-color: #dc3545;
        }
        
        .view-badge {
            background-color: #000000;
            color: #EAE4D5;
        }
        
        .empty-state {
            color: #B6B09F;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="main-card overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-6">
                    @if($stories->isEmpty())
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto mb-4 empty-state" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                            <h3 class="text-lg font-medium mb-2" style="color: #000000;">No stories yet</h3>
                            <p class="mb-6 empty-state">Create your first story to share with your connections!</p>
                            <a href="{{ route('stories.create') }}" class="btn-primary font-bold py-2 px-4 rounded">
                                Create Story
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($stories as $story)
                                <div class="story-card rounded-lg shadow-md overflow-hidden">
                                    <div class="relative">
                                        @if($story->type === 'image')
                                            <img src="{{ asset('storage/' . $story->content) }}" 
                                                 class="w-full h-48 object-cover">
                                        @elseif($story->type === 'video')
                                            <video class="w-full h-48 object-cover" controls>
                                                <source src="{{ asset('storage/' . $story->content) }}" type="video/mp4">
                                            </video>
                                        @else
                                            <div class="w-full h-48 flex items-center justify-center" style="background-color: #F2F2F2;">
                                                <div class="text-center p-4">
                                                    <svg class="w-8 h-8 mb-2 mx-auto" style="color: #B6B09F;" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z"/>
                                                    </svg>
                                                    <p class="text-sm" style="color: #000000;">{{ Str::limit($story->content, 100) }}</p>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <!-- Status indicators -->
                                        <div class="absolute top-2 right-2">
                                            @if($story->isExpired())
                                                <span class="status-badge-expired text-white text-xs px-2 py-1 rounded">Expired</span>
                                            @else
                                                <span class="status-badge-active text-white text-xs px-2 py-1 rounded">{{ $story->getTimeRemaining() }} left</span>
                                            @endif
                                        </div>
                                        
                                        <div class="absolute top-2 left-2">
                                            <span class="view-badge text-xs px-2 py-1 rounded">
                                                {{ $story->getViewersCount() }} {{ Str::plural('view', $story->getViewersCount()) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="p-4">
                                        @if($story->caption)
                                            <p class="text-sm mb-3" style="color: #000000;">{{ Str::limit($story->caption, 60) }}</p>
                                        @endif
                                        
                                        <div class="flex justify-between items-center text-xs mb-3" style="color: #B6B09F;">
                                            <span>{{ $story->created_at->format('M j, g:i A') }}</span>
                                            <span class="capitalize">{{ $story->type }}</span>
                                        </div>

                                        <div class="flex justify-between items-center">
                                            <a href="{{ route('stories.viewers', $story) }}" 
                                               class="text-sm hover:underline" style="color: #000000;">
                                                View Stats
                                            </a>
                                            <div class="space-x-2">
                                                <a href="{{ route('stories.show', $story) }}" 
                                                   class="btn-primary font-bold py-1 px-3 rounded text-sm">
                                                    View
                                                </a>
                                                <form action="{{ route('stories.destroy', $story) }}" method="POST" class="inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this story?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn-danger font-bold py-1 px-3 rounded text-sm">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>