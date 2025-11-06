<script>

function addOption() {
    const container = document.getElementById('poll-options');
    const input = document.createElement('input');
    input.type = "text";
    input.name = "poll_options[]";
    input.placeholder = "Another option";
    input.className = "w-full border rounded-lg p-2 mt-2";
    container.appendChild(input);
}

function votePoll(pollId, option) {
    fetch(`/polls/${pollId}/vote`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Content-Type": "application/json",
            "Accept": "application/json"
        },
        body: JSON.stringify({ option })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const resultsDiv = document.getElementById(`poll-results-${pollId}`);
            resultsDiv.innerHTML = data.votes.map(v => 
                `<div>${v.option}: <b>${v.count}</b> votes</div>`
            ).join("");
        }
    })
    .catch(err => console.error(err));
}

document.addEventListener("DOMContentLoaded", function () {
    console.log("✅ Enhanced post.js loaded");

    // ===============================
    // CSRF Token
    // ===============================
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    // ===============================
    // PREVIEW BEFORE UPLOAD
    // ===============================
    const mediaInput = document.getElementById("media");
    const preview = document.getElementById("preview");

    if (mediaInput && preview) {
        mediaInput.addEventListener("change", e => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = ev => {
                    preview.src = ev.target.result;
                    preview.classList.remove("hidden");
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.add("hidden");
                preview.src = "";
            }
        });
    }

    // ===============================
    // LIKE AJAX (GLOBAL FUNCTION)
    // ===============================
    window.likePost = function (postId) {
        fetch(`/posts/${postId}/like`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrf,
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({})
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Update like counter on main feed
                const likeCount = document.querySelector(`#like-count-${postId}`);
                if (likeCount) {
                    likeCount.innerText = data.likes_count;
                }
                
                // Update like counter in modal if open
                const modalLikeCount = document.getElementById('modalLikeCount');
                const modal = document.getElementById('postModal');
                if (modal && !modal.classList.contains('hidden')) {
                    const currentPostId = document.getElementById('modalLikeBtn').dataset.postId;
                    if (currentPostId == postId) {
                        modalLikeCount.innerText = data.likes_count;
                    }
                }
                
                // Add visual feedback
                const likeBtn = document.querySelector(`#like-btn-${postId}`);
                if (likeBtn) {
                    likeBtn.classList.add('text-red-500');
                    setTimeout(() => {
                        likeBtn.classList.remove('text-red-500');
                    }, 300);
                }
            }
        })
        .catch(err => console.error("Like error:", err));
    };

    // ===============================
    // POST MODAL FUNCTIONS
    // ===============================
    window.openPostModal = function(postId) {
        const modal = document.getElementById('postModal');
        const postData = document.getElementById(`post-data-${postId}`);
        
        if (!postData) {
            console.error('Post data not found');
            return;
        }

        // Get data attributes
        const userName = postData.dataset.userName;
        const userPicture = postData.dataset.userPicture;
        const createdAt = postData.dataset.createdAt;
        const content = postData.dataset.content;
        const mediaPath = postData.dataset.mediaPath;
        const mediaType = postData.dataset.mediaType;
        const likesCount = postData.dataset.likesCount;
        const comments = JSON.parse(postData.dataset.comments);

        // Set user info
        document.getElementById('modalUserName').textContent = userName;
        document.getElementById('modalCreatedAt').textContent = createdAt;
        
        // Set user avatar
        const modalAvatar = document.getElementById('modalUserAvatar');
        if (userPicture) {
            modalAvatar.innerHTML = `<img src="/storage/${userPicture}" alt="${userName}" class="w-full h-full rounded-full object-cover border-2" style="border-color: #B6B09F;">`;
        } else {
            const initial = userName.charAt(0).toUpperCase();
            modalAvatar.innerHTML = `<div class="w-full h-full avatar-placeholder rounded-full flex items-center justify-center">
                <span class="text-white font-semibold text-sm">${initial}</span>
            </div>`;
        }

        // Set content
        document.getElementById('modalContent').textContent = content;

        // Set media
        const modalImage = document.getElementById('modalPostImage');
        const modalVideo = document.getElementById('modalPostVideo');
        const mediaSection = document.getElementById('modalMediaSection');
        
        if (mediaPath && mediaType) {
            mediaSection.classList.remove('hidden');
            if (mediaType === 'image') {
                modalImage.src = `/storage/${mediaPath}`;
                modalImage.classList.remove('hidden');
                modalVideo.classList.add('hidden');
                modalVideo.pause();
                modalVideo.src = "";
            } else if (mediaType === 'video') {
                modalVideo.src = `/storage/${mediaPath}`;
                modalVideo.classList.remove('hidden');
                modalImage.classList.add('hidden');
                modalImage.src = "";
            }
        } else {
            mediaSection.classList.add('hidden');
            modalImage.classList.add('hidden');
            modalVideo.classList.add('hidden');
        }

        // Set likes
        document.getElementById('modalLikeCount').textContent = likesCount;
        const modalLikeBtn = document.getElementById('modalLikeBtn');
        modalLikeBtn.dataset.postId = postId;
        modalLikeBtn.onclick = () => likePost(postId);

        // Set comments
        const commentsList = document.getElementById('modalCommentsList');
        commentsList.innerHTML = '';
        
        if (comments && comments.length > 0) {
            comments.forEach(comment => {
                const commentHTML = createCommentHTML(comment);
                commentsList.innerHTML += commentHTML;
            });
        } else {
            commentsList.innerHTML = '<p class="text-sm text-center" style="color: #B6B09F;">No comments yet. Be the first to comment!</p>';
        }

        // Set up comment form
        const commentForm = document.getElementById('modalCommentForm');
        commentForm.onsubmit = (e) => submitModalComment(e, postId);

        // Show modal
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    };

    window.closePostModal = function() {
        const modal = document.getElementById('postModal');
        const modalVideo = document.getElementById('modalPostVideo');
        
        // Pause video if playing
        if (modalVideo) {
            modalVideo.pause();
        }
        
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    };

    // Helper function to create comment HTML
    function createCommentHTML(comment) {
        const initial = comment.user.name.charAt(0).toUpperCase();
        const avatarHTML = comment.user.profile_picture 
            ? `<img src="/storage/${comment.user.profile_picture}" alt="${comment.user.name}" class="w-8 h-8 rounded-full object-cover border-2" style="border-color: #B6B09F;">`
            : `<div class="w-8 h-8 avatar-placeholder rounded-full flex items-center justify-center">
                 <span class="text-white font-semibold text-xs">${initial}</span>
               </div>`;

        return `
            <div class="flex space-x-3">
                <div class="relative flex-shrink-0">
                    ${avatarHTML}
                </div>
                <div class="flex-1">
                    <div class="comment-bubble rounded-lg px-4 py-2">
                        <p class="text-sm font-semibold" style="color: #000000;">${comment.user.name}</p>
                        <p class="text-sm" style="color: #000000;">${comment.content}</p>
                    </div>
                </div>
            </div>
        `;
    }

    // Submit comment from modal
    function submitModalComment(e, postId) {
        e.preventDefault();
        const form = e.target;
        const input = form.querySelector('input[name="content"]');
        const content = input.value.trim();

        if (!content) return;

        fetch(`/posts/${postId}/comment`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrf,
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({ content: content })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success || data.user) {
                const commentsList = document.getElementById('modalCommentsList');
                
                // Remove "no comments" message if exists
                const noCommentsMsg = commentsList.querySelector('p');
                if (noCommentsMsg && noCommentsMsg.textContent.includes('No comments yet')) {
                    commentsList.innerHTML = '';
                }
                
                // Add new comment
                const newComment = {
                    user: {
                        name: data.user,
                        profile_picture: data.user_profile_picture
                    },
                    content: data.content
                };
                
                commentsList.innerHTML += createCommentHTML(newComment);
                
                // Clear input
                input.value = '';
                
                // Scroll to bottom
                commentsList.scrollTop = commentsList.scrollHeight;
            }
        })
        .catch(err => console.error("Comment error:", err));
    }

    // Close modal on escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closePostModal();
        }
    });

    // Close modal when clicking outside
    const modal = document.getElementById('postModal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closePostModal();
            }
        });
    }

    // ===============================
    // SIDEBAR MENU FUNCTIONALITY
    // ===============================
    
    // Toggle submenu
    window.toggleSubmenu = function(menuId) {
        const submenu = document.getElementById(menuId);
        const parentItem = submenu.previousElementSibling;
        const chevron = parentItem.querySelector('.chevron-icon');
        
        submenu.classList.toggle('open');
        if (chevron) {
            chevron.classList.toggle('rotated');
        }
    };

    // Toggle mobile sidebar
    window.toggleMobileSidebar = function() {
        const sidebar = document.getElementById('mainSidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        
        sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('active');
    };

    // Auto-expand sidebar on hover for desktop
    const sidebar = document.getElementById('mainSidebar');
    let expandTimeout;

    if (sidebar) {
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
    }

    // Handle window resize
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            const sidebar = document.getElementById('mainSidebar');
            const overlay = document.querySelector('.sidebar-overlay');
            
            if (sidebar.classList.contains('mobile-open')) {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            }
        }
    });
});
</script>