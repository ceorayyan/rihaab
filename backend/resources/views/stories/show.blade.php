<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl leading-tight" style="color: #000000;">
                {{ $story->user->name }}'s Story
            </h2>
            <a href="{{ route('stories.index') }}" class="btn-secondary font-bold py-2 px-4 rounded text-sm">
                Back to Stories
            </a>
        </div>
    </x-slot>

    <style>
        .main-card {
            background-color: #EAE4D5;
            border: 2px solid #B6B09F;
        }
        
        .btn-secondary {
            background-color: #B6B09F;
            color: #000000;
            transition: all 0.3s ease;
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
        
        .user-avatar {
            background: linear-gradient(135deg, #B6B09F 0%, #000000 100%);
        }
        
        .type-badge {
            background-color: #F2F2F2;
            color: #000000;
            border: 1px solid #B6B09F;
        }
        
        .caption-box {
            background-color: #F2F2F2;
            border: 2px solid #B6B09F;
        }
        
        .stats-card {
            background-color: #F2F2F2;
            border: 2px solid #B6B09F;
        }
        
        .viewer-badge {
            background-color: #EAE4D5;
            border: 1px solid #B6B09F;
        }
        
        .viewer-avatar {
            background-color: #B6B09F;
        }
        
        .text-story-container {
            background-color: #F2F2F2;
            border: 2px solid #B6B09F;
        }
    </style>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="main-card overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-6">
                    <!-- Story Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 user-avatar rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">
                                {{ strtoupper(substr($story->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg" style="color: #000000;">{{ $story->user->name }}</h3>
                                <p class="text-sm" style="color: #B6B09F;">{{ $story->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="type-badge inline-block text-xs px-2 py-1 rounded-full mb-1">
                                {{ ucfirst($story->type) }}
                            </span>
                            <br>
                            <span class="text-xs" style="color: #B6B09F;">
                                @if($story->isExpired())
                                    <span class="text-red-600">Expired</span>
                                @else
                                    {{ $story->getTimeRemaining() }} left
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Story Content -->
                    <div class="mb-6">
                        @if($story->type === 'image')
                            <div class="text-center">
                                <img src="{{ asset('storage/' . $story->content) }}" 
                                     class="max-w-full max-h-96 mx-auto rounded-lg shadow-md" style="border: 2px solid #B6B09F;">
                            </div>
                        @elseif($story->type === 'video')
                            <div class="text-center">
                                <video controls class="max-w-full max-h-96 mx-auto rounded-lg shadow-md" style="border: 2px solid #B6B09F;">
                                    <source src="{{ asset('storage/' . $story->content) }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        @else
                            <div class="text-story-container rounded-lg p-8">
                                <div class="text-center">
                                    <svg class="w-12 h-12 mb-4 mx-auto" style="color: #B6B09F;" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z"/>
                                    </svg>
                                    <div class="prose max-w-none">
                                        <p class="text-lg leading-relaxed whitespace-pre-wrap" style="color: #000000;">{{ $story->content }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Story Caption -->
                    @if($story->caption)
                        <div class="mb-6">
                            <div class="caption-box rounded-lg p-4">
                                <p style="color: #000000;">{{ $story->caption }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Story Stats (only show to story owner) -->
                    @if($story->user_id === auth()->id())
                        <div class="pt-6" style="border-top: 2px solid #B6B09F;">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold" style="color: #000000;">Story Statistics</h4>
                                <a href="{{ route('stories.viewers', $story) }}" class="text-sm hover:underline" style="color: #000000;">
                                    View all viewers →
                                </a>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div class="stats-card rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold" style="color: #000000;">{{ $story->getViewersCount() }}</div>
                                    <div class="text-sm" style="color: #B6B09F;">{{ Str::plural('View', $story->getViewersCount()) }}</div>
                                </div>
                                <div class="stats-card rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold" style="color: #000000;">{{ $story->getTimeRemaining() }}</div>
                                    <div class="text-sm" style="color: #B6B09F;">Time Remaining</div>
                                </div>
                            </div>

                            <!-- Recent Viewers Preview -->
                            @if($story->views->count() > 0)
                                <div class="mt-4">
                                    <h5 class="font-medium mb-2" style="color: #000000;">Recent Viewers</h5>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($story->views->take(5) as $view)
                                            <div class="viewer-badge flex items-center space-x-1 rounded-full px-3 py-1 text-xs">
                                                <div class="viewer-avatar w-4 h-4 rounded-full flex items-center justify-center text-white text-xs">
                                                    {{ strtoupper(substr($view->viewer->name, 0, 1)) }}
                                                </div>
                                                <span style="color: #000000;">{{ $view->viewer->name }}</span>
                                            </div>
                                        @endforeach
                                        @if($story->views->count() > 5)
                                            <span class="text-xs" style="color: #B6B09F;">+{{ $story->views->count() - 5 }} more</span>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Delete Story Button -->
                            <div class="mt-6">
                                <form action="{{ route('stories.destroy', $story) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this story?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-danger font-bold py-2 px-4 rounded text-sm">
                                        Delete Story
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>