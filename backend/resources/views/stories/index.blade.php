<!-- Stories Section - Instagram Style with 24hr Filter -->
<style>
    .stories-container {
        background-color: #EAE4D5;
        border: 2px solid #B6B09F;
    }
    
    .story-ring-unviewed {
        background: linear-gradient(135deg, #000000 0%, #B6B09F 100%);
    }
    
    .story-ring-viewed {
        background-color: #B6B09F;
    }
    
    .add-story-btn {
        border: 2px solid #B6B09F;
        background-color: #F2F2F2;
        transition: all 0.3s ease;
    }
    
    .add-story-btn:hover {
        border-color: #000000;
        background-color: #EAE4D5;
    }
    
    .story-modal-bg {
        background-color: #000000;
    }
    
    .story-management {
        background-color: #F2F2F2;
        border-top: 2px solid #B6B09F;
    }
    
    .story-grid-item {
        background-color: #EAE4D5;
        border: 2px solid #B6B09F;
        transition: all 0.3s ease;
    }
    
    .story-grid-item:hover {
        border-color: #000000;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .story-action-btn {
        background-color: #000000;
        color: #EAE4D5;
        transition: all 0.2s ease;
    }
    
    .story-action-btn:hover {
        background-color: #1a1a1a;
    }
    
    .story-delete-btn {
        background-color: #dc3545;
    }
    
    .story-delete-btn:hover {
        background-color: #c82333;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    
    .progress-bar {
        height: 2px;
        background-color: rgba(182, 176, 159, 0.3);
        border-radius: 2px;
        overflow: hidden;
        flex: 1;
    }
    
    .progress-bar-fill {
        height: 100%;
        background-color: #EAE4D5;
        width: 0%;
        transition: width 0.1s linear;
    }
</style>

<div class="stories-container rounded-lg p-4 mb-4">
    @php
        // Get only stories that are less than 24 hours old
        $twentyFourHoursAgo = now()->subHours(24);
        
        // Filter my active stories (less than 24 hours old)
        $myActiveStories = auth()->user()->stories()
            ->where('created_at', '>=', $twentyFourHoursAgo)
            ->latest()
            ->get();
        
        // Filter all stories to only include those less than 24 hours old
        $activeStories = $stories->map(function($userStories) use ($twentyFourHoursAgo) {
            return $userStories->filter(function($story) use ($twentyFourHoursAgo) {
                return $story->created_at >= $twentyFourHoursAgo;
            });
        })->filter(function($userStories) {
            return $userStories->isNotEmpty();
        });
    @endphp

    @if($activeStories->isEmpty() && $myActiveStories->isEmpty())
        <!-- No Stories State -->
        <div class="flex items-center space-x-4">
            <!-- Add Story Button -->
            <div class="flex-shrink-0">
                <button onclick="window.location.href='{{ route('stories.create') }}'" 
                        class="relative group">
                    <div class="add-story-btn w-14 h-14 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6" style="color: #000000;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v12m6-6H6"></path>
                        </svg>
                    </div>
                    <p class="text-xs mt-1 text-center" style="color: #000000;">Your story</p>
                </button>
            </div>
            
            <!-- Empty State Message -->
            <div class="flex-1">
                <p class="text-sm" style="color: #B6B09F;">No stories to show right now</p>
                <a href="{{ route('keyrequest.incoming') }}" class="text-xs hover:underline" style="color: #000000;">
                    Find people to follow
                </a>
            </div>
        </div>
    @else
        <!-- Stories Container -->
        <div class="flex space-x-3 overflow-x-auto scrollbar-hide">
            <!-- Add Story / Your Story - Always First -->
            <div class="flex-shrink-0">
                <div class="cursor-pointer" onclick="handleMyStory()">
                    @if($myActiveStories->isNotEmpty())
                        <!-- User has active stories -->
                        <div class="relative">
                            <div class="w-14 h-14 rounded-full story-ring-unviewed p-0.5">
                                <div class="w-full h-full rounded-full p-0.5" style="background-color: #EAE4D5;">
                                    <div class="w-full h-full rounded-full overflow-hidden" style="background-color: #F2F2F2;">
                                        @php $latestMyStory = $myActiveStories->first(); @endphp
                                        @if($latestMyStory->type === 'image')
                                            <img src="{{ asset('storage/' . $latestMyStory->content) }}" 
                                                 class="w-full h-full object-cover">
                                        @elseif($latestMyStory->type === 'video')
                                            <video class="w-full h-full object-cover">
                                                <source src="{{ asset('storage/' . $latestMyStory->content) }}" type="video/mp4">
                                            </video>
                                        @else
                                            <div class="w-full h-full flex items-center justify-center" style="background: linear-gradient(135deg, #B6B09F 0%, #000000 100%);">
                                                <span class="text-white font-semibold text-xs">
                                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- Add button overlay -->
                            <button onclick="event.stopPropagation(); window.location.href='{{ route('stories.create') }}'" 
                                    class="absolute -bottom-0.5 -right-0.5 text-white rounded-full w-5 h-5 flex items-center justify-center border-2 story-action-btn" style="border-color: #EAE4D5;">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                    @else
                        <!-- No stories - Show add button -->
                        <div class="relative">
                            <div class="w-14 h-14 rounded-full add-story-btn flex items-center justify-center">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" 
                                         class="w-full h-full rounded-full object-cover">
                                @else
                                    <div class="w-full h-full rounded-full flex items-center justify-center" style="background-color: #F2F2F2;">
                                        <span class="font-semibold text-xs" style="color: #000000;">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="absolute -bottom-0.5 -right-0.5 text-white rounded-full w-5 h-5 flex items-center justify-center border-2 story-action-btn" style="border-color: #EAE4D5;">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                        </div>
                    @endif
                    <p class="text-xs mt-1 text-center truncate w-14" style="color: #000000;">
                        Your story
                    </p>
                </div>
            </div>

            <!-- Other Users' Stories (filtered to less than 24 hours) -->
            @php
                $sortedStories = $activeStories->filter(function($userStories, $userId) {
                    return $userId != auth()->id();
                })->map(function($userStories, $userId) {
                    return [
                        'userId' => $userId,
                        'stories' => $userStories,
                        'latest_time' => $userStories->first()->created_at
                    ];
                })->sortByDesc('latest_time');
            @endphp
            
            @foreach($sortedStories as $userStoryData)
                @php 
                    $userId = $userStoryData['userId'];
                    $userStories = $userStoryData['stories'];
                    $user = $userStories->first()->user;
                    $hasUnviewedStories = $userStories->some(function($story) {
                        return !$story->hasBeenViewedBy(auth()->id());
                    });
                    $latestStory = $userStories->first();
                @endphp
                <div class="flex-shrink-0">
                    <div class="cursor-pointer" onclick="openStoryViewer({{ $userId }})">
                        <div class="relative">
                            <div class="w-14 h-14 rounded-full {{ $hasUnviewedStories ? 'story-ring-unviewed' : 'story-ring-viewed' }} p-0.5">
                                <div class="w-full h-full rounded-full p-0.5" style="background-color: #EAE4D5;">
                                    <div class="w-full h-full rounded-full overflow-hidden" style="background-color: #F2F2F2;">
                                        @if($latestStory->type === 'image')
                                            <img src="{{ asset('storage/' . $latestStory->content) }}" 
                                                 class="w-full h-full object-cover">
                                        @elseif($latestStory->type === 'video')
                                            <video class="w-full h-full object-cover">
                                                <source src="{{ asset('storage/' . $latestStory->content) }}" type="video/mp4">
                                            </video>
                                        @else
                                            <div class="w-full h-full flex items-center justify-center" style="background: linear-gradient(135deg, #B6B09F 0%, #000000 100%);">
                                                <span class="text-white font-semibold text-xs">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs mt-1 text-center truncate w-14" style="color: #000000;">
                            {{ $user->name }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Your Active Stories Management -->
        @if($myActiveStories->isNotEmpty())
            <div id="myStoriesManagement" class="hidden story-management mt-4 pt-4">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-sm font-semibold" style="color: #000000;">Your Active Stories</h4>
                    <button onclick="document.getElementById('myStoriesManagement').classList.add('hidden')" 
                            style="color: #B6B09F;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    @foreach($myActiveStories->take(6) as $story)
                        <div class="relative group story-grid-item">
                            <div class="aspect-square rounded-lg overflow-hidden" style="background-color: #F2F2F2;">
                                @if($story->type === 'image')
                                    <img src="{{ asset('storage/' . $story->content) }}" 
                                         class="w-full h-full object-cover">
                                @elseif($story->type === 'video')
                                    <video class="w-full h-full object-cover">
                                        <source src="{{ asset('storage/' . $story->content) }}" type="video/mp4">
                                    </video>
                                @else
                                    <div class="w-full h-full flex items-center justify-center p-2">
                                        <p class="text-xs text-center" style="color: #000000;">{{ Str::limit($story->content, 30) }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all rounded-lg flex items-center justify-center">
                                <div class="hidden group-hover:flex space-x-2">
                                    <button onclick="openStoryViewer({{ auth()->id() }}, {{ $loop->index }})" 
                                            class="story-action-btn p-1.5 rounded-full">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <button onclick="deleteStory({{ $story->id }})" 
                                            class="story-delete-btn text-white p-1.5 rounded-full">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="absolute top-1 right-1">
                                <span class="bg-black bg-opacity-60 text-white text-xs px-1 py-0.5 rounded">
                                    {{ $story->getViewersCount() }}
                                </span>
                            </div>
                            <!-- Time remaining indicator -->
                            <div class="absolute bottom-1 left-1">
                                @php
                                    $hoursRemaining = 24 - $story->created_at->diffInHours(now());
                                @endphp
                                <span class="bg-black bg-opacity-60 text-white text-xs px-1 py-0.5 rounded">
                                    {{ $hoursRemaining }}h left
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($myActiveStories->count() > 6)
                    <p class="text-xs text-center mt-2" style="color: #B6B09F;">+{{ $myActiveStories->count() - 6 }} more stories</p>
                @endif
            </div>
        @endif
    @endif
</div>

<!-- Story Viewer Modal -->
<div id="storyViewerModal" class="fixed inset-0 story-modal-bg z-50 hidden">
    <div class="h-full w-full flex items-center justify-center">
        <!-- Close button -->
        <button onclick="closeStoryViewer()" 
                class="absolute top-4 right-4 z-10" style="color: #EAE4D5;">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        
        <!-- Story container -->
        <div class="relative max-w-md w-full h-full md:h-auto md:max-h-[90vh]">
            <!-- Progress bars -->
            <div id="progressBars" class="absolute top-2 left-2 right-2 flex space-x-1 z-10">
                <!-- Progress bars will be dynamically added here -->
            </div>
            
            <!-- User info header -->
            <div class="absolute top-6 left-4 right-4 flex items-center z-10">
                <div class="flex items-center flex-1">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center mr-2" style="background: linear-gradient(135deg, #B6B09F 0%, #000000 100%);">
                        <span class="text-white text-sm font-bold" id="storyUserInitial"></span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold" style="color: #EAE4D5;" id="storyUserName"></p>
                        <p class="text-xs" style="color: #B6B09F;" id="storyTime"></p>
                    </div>
                </div>
                
                <!-- Actions for own stories -->
                <div id="myStoryActions" class="hidden space-x-2">
                    <button onclick="showStoryStats()" style="color: #EAE4D5;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                    <button onclick="deleteCurrentStory()" style="color: #EAE4D5;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Story content -->
            <div class="w-full h-screen md:h-[90vh] md:rounded-lg overflow-hidden" style="background-color: #000000;">
                <div id="storyContent" class="w-full h-full flex items-center justify-center">
                    <!-- Story content will be loaded here -->
                </div>
            </div>
            
            <!-- Caption overlay -->
            <div id="storyCaption" class="absolute bottom-4 left-4 right-4 bg-black bg-opacity-50 text-sm p-2 rounded hidden" style="color: #EAE4D5;">
                <!-- Caption will be shown here -->
            </div>
            
            <!-- Navigation areas -->
            <div class="absolute inset-0 flex pointer-events-none">
                <div class="w-1/3 h-full cursor-pointer pointer-events-auto" onclick="previousStory()"></div>
                <div class="w-1/3 h-full"></div>
                <div class="w-1/3 h-full cursor-pointer pointer-events-auto" onclick="nextStory()"></div>
            </div>
        </div>
    </div>
</div>

<!-- Story Stats Modal -->
<div id="storyStatsModal" class="fixed inset-0 bg-black bg-opacity-75 z-60 hidden">
    <div class="h-full flex items-end md:items-center md:justify-center">
        <div class="rounded-t-xl md:rounded-xl w-full md:max-w-sm max-h-[70vh] overflow-hidden" style="background-color: #EAE4D5;">
            <div class="p-4 flex justify-between items-center" style="border-bottom: 2px solid #B6B09F;">
                <h3 class="text-base font-semibold" style="color: #000000;">Views</h3>
                <button onclick="closeStoryStats()" style="color: #B6B09F;">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="storyStatsContent" class="p-4 overflow-y-auto max-h-[calc(70vh-4rem)]">
                <!-- Stats content will be loaded here -->
            </div>
        </div>
    </div>
</div>

@include('stories.partials.index')