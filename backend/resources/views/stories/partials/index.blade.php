
<script>
    // All the existing JavaScript functions remain the same
    let currentStories = [];
    let currentStoryIndex = 0;
    let currentUserId = null;
    let storyTimeout = null;
    let progressInterval = null;

    function handleMyStory() {
        @if($myActiveStories->isNotEmpty())
            openStoryViewer({{ auth()->id() }}, 0);
        @else
            window.location.href = "{{ route('stories.create') }}";
        @endif
    }

    function openStoryViewer(userId, startIndex = 0) {
        currentUserId = userId;
        currentStoryIndex = startIndex;
        
        document.getElementById('storyViewerModal').classList.remove('hidden');
        document.getElementById('storyContent').innerHTML = '<div class="text-white">Loading...</div>';
        
        fetch(`/api/stories/user/${userId}`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to fetch stories');
                return response.json();
            })
            .then(stories => {
                if (stories.length === 0) {
                    closeStoryViewer();
                    return;
                }
                currentStories = stories;
                setupProgressBars();
                showCurrentStory();
            })
            .catch(error => {
                console.error('Error fetching stories:', error);
                closeStoryViewer();
            });
    }

    function deleteStory(storyId) {
        const deleteButton = event.target.closest('button');
        deleteButton.disabled = true;

        fetch(`{{ url('/stories') }}/${storyId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok || response.status === 204) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error deleting story:', error);
            deleteButton.disabled = false;
        });
    }

    function setupProgressBars() {
        const progressContainer = document.getElementById('progressBars');
        progressContainer.innerHTML = '';
        
        currentStories.forEach((story, index) => {
            const progressBar = document.createElement('div');
            progressBar.className = 'progress-bar';
            progressBar.innerHTML = '<div class="progress-bar-fill"></div>';
            progressContainer.appendChild(progressBar);
        });
    }

    function showCurrentStory() {
        if (currentStoryIndex >= currentStories.length) {
            closeStoryViewer();
            return;
        }

        const story = currentStories[currentStoryIndex];
        const storyContent = document.getElementById('storyContent');
        const storyCaption = document.getElementById('storyCaption');
        const myStoryActions = document.getElementById('myStoryActions');
        
        document.getElementById('storyUserName').textContent = story.user.name;
        document.getElementById('storyUserInitial').textContent = story.user.name.charAt(0).toUpperCase();
        document.getElementById('storyTime').textContent = formatTime(story.created_at);
        
        if (story.user.id === {{ auth()->id() }}) {
            myStoryActions.classList.remove('hidden');
        } else {
            myStoryActions.classList.add('hidden');
        }
        
        storyContent.innerHTML = '';
        
        if (story.type === 'image') {
            const img = document.createElement('img');
            img.src = `/storage/${story.content}`;
            img.className = 'max-w-full max-h-full object-contain';
            storyContent.appendChild(img);
            img.onload = () => startStoryTimer(5000);
            img.onerror = () => {
                storyContent.innerHTML = '<div class="text-white">Failed to load image</div>';
                startStoryTimer(3000);
            };
        } else if (story.type === 'video') {
            const video = document.createElement('video');
            video.src = `/storage/${story.content}`;
            video.className = 'max-w-full max-h-full object-contain';
            video.controls = false;
            video.autoplay = true;
            video.muted = true;
            storyContent.appendChild(video);
            video.onloadedmetadata = () => {
                startStoryTimer(Math.max(video.duration * 1000, 3000));
            };
            video.onended = () => nextStory();
            video.onerror = () => {
                storyContent.innerHTML = '<div class="text-white">Failed to load video</div>';
                startStoryTimer(3000);
            };
        } else {
            const textDiv = document.createElement('div');
            textDiv.className = 'text-white text-center p-8';
            textDiv.innerHTML = `<p class="text-lg">${escapeHtml(story.content)}</p>`;
            storyContent.appendChild(textDiv);
            startStoryTimer(4000);
        }
        
        if (story.caption) {
            storyCaption.innerHTML = escapeHtml(story.caption);
            storyCaption.classList.remove('hidden');
        } else {
            storyCaption.classList.add('hidden');
        }
        
        updateProgressBars();
        markStoryAsViewed(story.id);
    }

    function showStoryStats() {
        const story = currentStories[currentStoryIndex];
        clearTimeout(storyTimeout);
        clearInterval(progressInterval);
        
        fetch(`/api/stories/${story.id}/viewers`)
            .then(response => response.json())
            .then(data => {
                const statsContent = document.getElementById('storyStatsContent');
                
                if (data.viewers && data.viewers.length > 0) {
                    statsContent.innerHTML = `
                        <div class="space-y-3">
                            ${data.viewers.map(viewer => `
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-600 text-sm font-semibold">${viewer.name.charAt(0).toUpperCase()}</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium">${viewer.name}</p>
                                        <p class="text-xs text-gray-500">${formatTime(viewer.viewed_at)}</p>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    `;
                } else {
                    statsContent.innerHTML = `
                        <div class="text-center py-8">
                            <p class="text-gray-500">No views yet</p>
                        </div>
                    `;
                }
                
                document.getElementById('storyStatsModal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error fetching story stats:', error);
            });
    }

    function closeStoryStats() {
        document.getElementById('storyStatsModal').classList.add('hidden');
        if (!document.getElementById('storyViewerModal').classList.contains('hidden')) {
            showCurrentStory();
        }
    }

    function deleteCurrentStory() {
        const story = currentStories[currentStoryIndex];
        
        if (!confirm('Delete this story?')) return;

        clearTimeout(storyTimeout);
        clearInterval(progressInterval);

        fetch(`/stories/${story.id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if (response.ok) {
                currentStories.splice(currentStoryIndex, 1);
                if (currentStories.length === 0) {
                    closeStoryViewer();
                    location.reload();
                    return;
                }
                if (currentStoryIndex >= currentStories.length) {
                    currentStoryIndex = currentStories.length - 1;
                }
                setupProgressBars();
                showCurrentStory();
            }
        })
        .catch(error => {
            console.error('Error deleting story:', error);
            showCurrentStory();
        });
    }

    function startStoryTimer(duration) {
        clearTimeout(storyTimeout);
        clearInterval(progressInterval);
        
        const progressBar = document.querySelectorAll('.progress-bar-fill')[currentStoryIndex];
        if (!progressBar) return;
        
        let progress = 0;
        const increment = 100 / (duration / 100);
        
        progressInterval = setInterval(() => {
            progress += increment;
            if (progress >= 100) {
                progress = 100;
                clearInterval(progressInterval);
                nextStory();
            }
            progressBar.style.width = progress + '%';
        }, 100);
    }

    function updateProgressBars() {
        const progressBars = document.querySelectorAll('.progress-bar-fill');
        progressBars.forEach((bar, index) => {
            if (index < currentStoryIndex) {
                bar.style.width = '100%';
            } else if (index === currentStoryIndex) {
                bar.style.width = '0%';
            } else {
                bar.style.width = '0%';
            }
        });
    }

    function nextStory() {
        clearTimeout(storyTimeout);
        clearInterval(progressInterval);
        currentStoryIndex++;
        showCurrentStory();
    }

    function previousStory() {
        clearTimeout(storyTimeout);
        clearInterval(progressInterval);
        if (currentStoryIndex > 0) {
            currentStoryIndex--;
            showCurrentStory();
        }
    }

    
        function closeStoryViewer() {
            clearTimeout(storyTimeout);
            clearInterval(progressInterval);
            document.getElementById('storyViewerModal').classList.add('hidden');
            document.getElementById('storyStatsModal').classList.add('hidden');
            
            // Refresh the page to update viewed status
            location.reload();
        }

        function formatTime(timestamp) {
            const now = new Date();
            const time = new Date(timestamp);
            const diff = Math.floor((now - time) / 1000);
            
            if (diff < 60) return `${diff}s ago`;
            if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
            if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
            return `${Math.floor(diff / 86400)}d ago`;
        }

        function markStoryAsViewed(storyId) {
            fetch(`/api/stories/${storyId}/view`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            }).catch(error => console.error('Error marking story as viewed:', error));
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (document.getElementById('storyViewerModal').classList.contains('hidden')) return;
            
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                previousStory();
            }
            if (e.key === 'ArrowRight' || e.key === ' ') {
                e.preventDefault();
                nextStory();
            }
            if (e.key === 'Escape') {
                e.preventDefault();
                closeStoryViewer();
            }
        });

        // Prevent default space bar scrolling when story viewer is open
        document.addEventListener('keydown', function(e) {
            if (!document.getElementById('storyViewerModal').classList.contains('hidden') && e.key === ' ') {
                e.preventDefault();
            }
        });
    </script>
