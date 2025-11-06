<x-app-layout>
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Sidebar Styles */
        .main-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 60px;
            background-color: #000000;
            transition: width 0.3s ease;
            z-index: 1000;
            overflow: hidden;
        }

        .main-sidebar:hover,
        .main-sidebar.expanded {
            width: 260px;
        }

        .sidebar-menu {
            padding-top: 80px;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: #EAE4D5;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .sidebar-item:hover {
            background-color: #1a1a1a;
        }

        .sidebar-item.active {
            background-color: #1a1a1a;
            border-right: 3px solid #EAE4D5;
        }

        .sidebar-icon {
            min-width: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sidebar-text {
            margin-left: 16px;
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .main-sidebar:hover .sidebar-text,
        .main-sidebar.expanded .sidebar-text {
            opacity: 1;
        }

        .sidebar-section-title {
            padding: 16px 16px 8px;
            color: #B6B09F;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .main-sidebar:hover .sidebar-section-title,
        .main-sidebar.expanded .sidebar-section-title {
            opacity: 1;
        }

        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background-color: #0a0a0a;
        }

        .submenu.open {
            max-height: 500px;
        }

        .submenu-item {
            padding: 10px 16px 10px 60px;
            color: #B6B09F;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .submenu-item:hover {
            background-color: #1a1a1a;
            color: #EAE4D5;
        }

        .chevron-icon {
            margin-left: auto;
            transition: transform 0.3s ease;
        }

        .chevron-icon.rotated {
            transform: rotate(180deg);
        }

        /* Main Content Area */
        .main-content {
            margin-left: 60px;
            transition: margin-left 0.3s ease;
        }

        .post-card {
            background-color: #EAE4D5;
            border: 2px solid #B6B09F;
            animation: fadeInUp 0.5s ease-out;
        }

        .post-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .community-card {
            background-color: #FFFFFF;
            border: 1px solid #B6B09F;
            transition: all 0.3s ease;
        }

        .community-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .action-button {
            transition: all 0.2s ease;
        }

        .action-button:hover {
            background-color: #F2F2F2;
            color: #000000;
        }

        .action-button:active {
            animation: pulse 0.3s ease;
        }

        .create-post-card {
            background-color: #EAE4D5;
            border: 2px solid #B6B09F;
        }

        .textarea-custom {
            background-color: #F2F2F2;
            border: 2px solid #B6B09F;
            color: #000000;
        }

        .textarea-custom:focus {
            border-color: #000000;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
        }

        .btn-primary {
            background-color: #000000;
            color: #EAE4D5;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #1a1a1a;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .btn-secondary {
            background-color: #F2F2F2;
            color: #000000;
            border: 2px solid #B6B09F;
        }

        .btn-secondary:hover {
            background-color: #EAE4D5;
            border-color: #000000;
        }

        .comment-bubble {
            background-color: #F2F2F2;
            border: 1px solid #B6B09F;
        }

        .comment-input {
            background-color: #F2F2F2;
            border: 2px solid #B6B09F;
            color: #000000;
        }

        .comment-input:focus {
            border-color: #000000;
            outline: none;
        }

        .avatar-placeholder {
            background: linear-gradient(135deg, #B6B09F 0%, #000000 100%);
        }

        .sidebar-sticky {
            position: sticky;
            top: 80px;
        }

        /* CONSISTENT POST MEDIA SIZING */
        .post-media-container {
            width: 100%;
            height: 0;
            padding-bottom: 100%;
            position: relative;
            overflow: hidden;
            border-radius: 0.5rem;
            border: 2px solid #B6B09F;
            background-color: #000000;
            cursor: pointer;
        }

        .post-media-container img,
        .post-media-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .post-media-container:hover img,
        .post-media-container:hover video {
            transform: scale(1.05);
        }

        /* MODAL STYLES */
        .modal-overlay {
            background-color: rgba(0, 0, 0, 0.95);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            animation: slideIn 0.3s ease;
            max-height: 90vh;
            max-width: 90vw;
        }

        .modal-post-card {
            background-color: #EAE4D5;
            border: 2px solid #B6B09F;
            border-radius: 1rem;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            max-height: 90vh;
        }

        .modal-media {
            width: 100%;
            max-height: 60vh;
            object-fit: contain;
            background-color: #000000;
        }

        .modal-comments-section {
            max-height: 300px;
            overflow-y: auto;
        }

        .modal-comments-section::-webkit-scrollbar {
            width: 8px;
        }

        .modal-comments-section::-webkit-scrollbar-track {
            background: #F2F2F2;
            border-radius: 4px;
        }

        .modal-comments-section::-webkit-scrollbar-thumb {
            background: #B6B09F;
            border-radius: 4px;
        }

        .modal-comments-section::-webkit-scrollbar-thumb:hover {
            background: #000000;
        }

        #preview {
            max-height: 200px;
            border-radius: 0.5rem;
            border: 2px solid #B6B09F;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .main-sidebar {
                width: 0;
            }

            .main-sidebar.mobile-open {
                width: 260px;
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-btn {
                display: flex !important;
            }
        }

        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background-color: #000000;
            color: #EAE4D5;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .sidebar-overlay.active {
            display: block;
        }
    </style>

    {{-- Mobile Menu Button --}}
    <button class="mobile-menu-btn" onclick="toggleMobileSidebar()">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>

    {{-- Sidebar Overlay for Mobile --}}
    <div class="sidebar-overlay" onclick="toggleMobileSidebar()"></div>

    {{-- Left Sidebar --}}
    <aside class="main-sidebar" id="mainSidebar">
        <nav class="sidebar-menu">
            {{-- Main Navigation --}}
            <a href="{{ route('posts.index') }}" class="sidebar-item active">
                <span class="sidebar-icon">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                </span>
                <span class="sidebar-text">Home</span>
            </a>

            <a href="#" class="sidebar-item">
                <span class="sidebar-icon">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                    </svg>
                </span>
                <span class="sidebar-text">Popular</span>
            </a>

            <a href="#" class="sidebar-item">
                <span class="sidebar-icon">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                    </svg>
                </span>
                <span class="sidebar-text">Explore</span>
            </a>

            <a href="#" class="sidebar-item">
                <span class="sidebar-icon">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"></path>
                    </svg>
                </span>
                <span class="sidebar-text">All</span>
            </a>

            {{-- Communities Section --}}
            <div class="sidebar-section-title">Communities</div>

            <div class="sidebar-item" onclick="toggleSubmenu('communitiesMenu')">
                <span class="sidebar-icon">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                    </svg>
                </span>
                <span class="sidebar-text">My Communities</span>
                <svg class="w-4 h-4 chevron-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="submenu" id="communitiesMenu">
                @if(isset($myCommunities) && $myCommunities->count() > 0)
                    @foreach($myCommunities as $community)
                        <a href="{{ route('communities.show', $community->slug) }}" class="submenu-item">
                            {{ $community->name }}
                        </a>
                    @endforeach
                @else
                    <div class="submenu-item">No communities yet</div>
                @endif
                <a href="{{ route('communities.create') }}" class="submenu-item" style="color: #EAE4D5;">
                    + Create Community
                </a>
            </div>

            {{-- Resources Section --}}
            <div class="sidebar-section-title">Resources</div>

            <div class="sidebar-item" onclick="toggleSubmenu('resourcesMenu')">
                <span class="sidebar-icon">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                </span>
                <span class="sidebar-text">Resources</span>
                <svg class="w-4 h-4 chevron-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="submenu" id="resourcesMenu">
                <a href="#" class="submenu-item">About</a>
                <a href="#" class="submenu-item">Help</a>
                <a href="#" class="submenu-item">Privacy Policy</a>
                <a href="#" class="submenu-item">Terms of Service</a>
            </div>

            {{-- Settings Section --}}
            <div class="sidebar-item" onclick="toggleSubmenu('settingsMenu')">
                <span class="sidebar-icon">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                    </svg>
                </span>
                <span class="sidebar-text">Settings</span>
                <svg class="w-4 h-4 chevron-icon" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="submenu" id="settingsMenu">
                <a href="#" class="submenu-item">Profile Settings</a>
                <a href="#" class="submenu-item">Privacy</a>
                <a href="#" class="submenu-item">Notifications</a>
                <a href="#" class="submenu-item">Theme</a>
            </div>
        </nav>
    </aside>

    {{-- Main Content Area --}}
    <div class="main-content">
        <div class="container mx-auto px-4 py-6">
            @include('stories.index')
        </div>

        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
            <div class="flex gap-4 lg:gap-6">
                {{-- Main Feed --}}
                <div class="flex-1 max-w-2xl mx-auto w-full">
 {{-- 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path> --}}
                                            </svg>
                                            <span class="text-sm font-medium">Photo/Video</span>
                                        </label>
                                        <input type="file" name="media" id="media" class="hidden" accept="image/*,video/*">
                                        
                                        <button type="submit" class="btn-primary px-6 py-2 rounded-lg font-medium">
                                            Post
                                        </button>
                                    </div>
                                    
                                    <img id="preview" class="mt-4 hidden mx-auto shadow-sm"/>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Posts --}}
                    <div class="space-y-4 sm:space-y-6 pb-8">
                        @foreach ($posts as $post)
                            <article class="post-card rounded-xl shadow-md overflow-hidden">
                                {{-- Post Header --}}
                                <div class="p-4 sm:p-6 pb-3 sm:pb-4">
                                    <div class="flex items-center space-x-3 mb-4">
                                        <div class="relative">
                                            @if($post->user->profile_picture)
                                                <img src="{{ asset('' . $post->user->profile_picture) }}" 
                                                     alt="{{ $post->user->name }}"
                                                     class="w-10 h-10 rounded-full object-cover border-2" style="border-color: #B6B09F;">
                                            @else
                                                <div class="w-10 h-10 avatar-placeholder rounded-full flex items-center justify-center">
                                                    <span class="text-white font-semibold text-sm">
                                                        {{ strtoupper(substr($post->user->name, 0, 2)) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-sm sm:text-base" style="color: #000000;">{{ $post->user->name }}</h3>
                                            <p class="text-xs sm:text-sm" style="color: #B6B09F;">{{ $post->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    
                                    {{-- Post Content --}}
                                    <p class="leading-relaxed mb-4 text-sm sm:text-base" style="color: #000000;">{{ $post->content }}</p>
                                </div>

                                {{-- Media with Consistent Sizing --}}
                                @if ($post->media_path)
                                    <div class="px-4 sm:px-6 pb-4">
                                        <div class="post-media-container" onclick="openPostModal({{ $post->id }})">
                                            @if ($post->media_type === 'image')
                                                <img src="{{ asset('storage/' . $post->media_path) }}"
                                                     alt="Post image">
                                            @elseif ($post->media_type === 'video')
                                                <video>
                                                    <source src="{{ asset('storage/' . $post->media_path) }}" type="video/mp4">
                                                </video>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                {{-- Post Actions --}}
                                <div class="px-4 sm:px-6 py-3 sm:py-4" style="border-top: 2px solid #B6B09F;">
                                    <div class="flex items-center space-x-4 sm:space-x-6">
                                        <button onclick="likePost({{ $post->id }})" 
                                                id="like-btn-{{ $post->id }}"
                                                class="action-button flex items-center space-x-2 px-2 sm:px-3 py-2 rounded-lg" 
                                                style="color: #000000;">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                            </svg>
                                            <span class="text-xs sm:text-sm font-medium">Like</span>
                                            <span id="like-count-{{ $post->id }}" class="text-xs sm:text-sm px-2 py-1 rounded-full" style="background-color: #F2F2F2; color: #000000;">
                                                {{ $post->likes()->count() }}
                                            </span>
                                        </button>

                                        <button class="action-button flex items-center space-x-2 px-2 sm:px-3 py-2 rounded-lg" 
                                                style="color: #000000;"
                                                onclick="openPostModal({{ $post->id }})">
                                            <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                            </svg>
                                            <span class="text-xs sm:text-sm font-medium">Comment</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Hidden data for modal --}}
                                <div class="hidden" id="post-data-{{ $post->id }}" 
                                     data-user-name="{{ $post->user->name }}"
                                     data-user-picture="{{ $post->user->profile_picture ?? '' }}"
                                     data-created-at="{{ $post->created_at->diffForHumans() }}"
                                     data-content="{{ $post->content }}"
                                     data-media-path="{{ $post->media_path ?? '' }}"
                                     data-media-type="{{ $post->media_type ?? '' }}"
                                     data-likes-count="{{ $post->likes()->count() }}"
                                     data-comments='@json($post->comments)'>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                {{-- Right Sidebar (Optional) --}}
                <div class="hidden xl:block w-80 shrink-0">
                    <div class="sidebar-sticky">
                        {{-- Suggested Communities --}}
                        @if(isset($suggestedCommunities) && $suggestedCommunities->count() > 0)
                            <div class="community-card rounded-xl shadow-md p-4 mb-4">
                                <h3 class="font-bold text-lg mb-4" style="color: #000000;">Discover Communities</h3>
                                <div class="space-y-3">
                                    @foreach($suggestedCommunities as $community)
                                        <div class="p-3 rounded-lg" style="background-color: #F2F2F2;">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <div class="w-12 h-12 rounded-lg overflow-hidden shrink-0" 
                                                     style="background-color: #EAE4D5;">
                                                    @if($community->icon)
                                                        <img src="{{ asset('storage/'.$community->icon) }}" 
                                                             class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center font-bold" 
                                                             style="color: #000000;">
                                                            {{ strtoupper(substr($community->name, 0, 2)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold truncate" style="color: #000000;">
                                                        {{ $community->name }}
                                                    </p>
                                                    <p class="text-xs" style="color: #B6B09F;">
                                                        {{ $community->members_count }} members
                                                    </p>
                                                </div>
                                            </div>
                                            <a href="{{ route('communities.show', $community->slug) }}" 
                                               class="block text-center px-4 py-2 rounded-lg text-sm font-medium transition-all hover:translate-y-[-2px]"
                                               style="background-color: #000000; color: #F2F2F2;">
                                                View
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Post Detail Modal (Instagram/Facebook Style) --}}
    <div id="postModal" class="fixed inset-0 modal-overlay hidden items-center justify-center z-50 p-4">
        <button onclick="closePostModal()" class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300 transition-colors z-10 w-10 h-10 flex items-center justify-center">
            &times;
        </button>
        
        <div class="modal-content w-full max-w-4xl">
            <div class="modal-post-card">
                {{-- Media Section --}}
                <div id="modalMediaSection" class="bg-black flex items-center justify-center">
                    <img id="modalPostImage" class="modal-media hidden">
                    <video id="modalPostVideo" controls class="modal-media hidden"></video>
                </div>

                {{-- Post Info Section --}}
                <div class="flex flex-col bg-white" style="background-color: #EAE4D5;">
                    {{-- Header --}}
                    <div class="p-4 border-b-2" style="border-color: #B6B09F;">
                        <div class="flex items-center space-x-3">
                            <div id="modalUserAvatar" class="w-10 h-10 rounded-full"></div>
                            <div>
                                <h3 id="modalUserName" class="font-semibold text-sm sm:text-base" style="color: #000000;"></h3>
                                <p id="modalCreatedAt" class="text-xs sm:text-sm" style="color: #B6B09F;"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-4 border-b-2" style="border-color: #B6B09F;">
                        <p id="modalContent" class="text-sm sm:text-base leading-relaxed" style="color: #000000;"></p>
                    </div>

                    {{-- Actions --}}
                    <div class="px-4 py-3 border-b-2" style="border-color: #B6B09F;">
                        <div class="flex items-center space-x-6">
                            <button id="modalLikeBtn" class="action-button flex items-center space-x-2 px-3 py-2 rounded-lg" style="color: #000000;">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span class="text-sm font-medium">Like</span>
                                <span id="modalLikeCount" class="text-sm px-2 py-1 rounded-full" style="background-color: #F2F2F2; color: #000000;"></span>
                            </button>
                        </div>
                    </div>

                    {{-- Comments --}}
                    <div class="modal-comments-section p-4 space-y-3" id="modalCommentsList">
                        <!-- Comments will be inserted here -->
                    </div>

                    {{-- Add Comment --}}
                    <div class="p-4 border-t-2" style="border-color: #B6B09F;">
                        <form id="modalCommentForm" class="flex space-x-3">
                            @csrf
                            <input type="text" 
                                   name="content" 
                                   class="comment-input flex-1 rounded-full px-4 py-2 text-sm" 
                                   placeholder="Write a comment..." 
                                   required>
                            <button type="submit" class="btn-primary px-4 py-2 rounded-lg text-sm font-medium">
                                Post
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle submenu
        function toggleSubmenu(menuId) {
            const submenu = document.getElementById(menuId);
            const parentItem = submenu.previousElementSibling;
            const chevron = parentItem.querySelector('.chevron-icon');
            
            submenu.classList.toggle('open');
            chevron.classList.toggle('rotated');
        }

        // Toggle mobile sidebar
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mainSidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
        }

        // Redirect to create page with specific tab
        function goToCreatePage(type = 'standard') {
            window.location.href = `{{ route('posts.create') }}?type=${type}`;
        }

        // Check if user wants advanced features
        function checkForAdvancedPost() {
            const content = document.getElementById('quickPostContent').value;
            if (content.length > 500) {
                if (confirm('Your post is quite long. Would you like to use the advanced editor for better formatting options?')) {
                    goToCreatePage('standard');
                }
            }
        }

        // Auto-expand sidebar on hover for desktop
        const sidebar = document.getElementById('mainSidebar');
        let expandTimeout;

        sidebar.addEventListener('mouseenter', () => {
            if (window.innerWidth > 768) {
                clearTimeout(expandTimeout);
                sidebar.classList.add('expanded');
            }
        });

        sidebar.addEventListener('mouseleave', () => {
            if (window.innerWidth > 768) {
                expandTimeout = setTimeout(() => {
                    sidebar.classList.remove('expanded');
                }, 300);
            }
        });
    </script>

</x-app-layout>

@include('posts.partials.index')