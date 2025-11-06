<x-app-layout>
    <style>
        .create-container {
            background-color: #EAE4D5;
            border: 2px solid #B6B09F;
        }

        .tab-button {
            padding: 12px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
            color: #000000;
            background-color: #F2F2F2;
            border: 2px solid #B6B09F;
        }

        .tab-button.active {
            background-color: #000000;
            color: #EAE4D5;
            border-color: #000000;
        }

        .tab-button:hover:not(.active) {
            background-color: #EAE4D5;
            border-color: #000000;
        }

        .input-field {
            background-color: #F2F2F2;
            border: 2px solid #B6B09F;
            color: #000000;
            border-radius: 8px;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .input-field:focus {
            border-color: #000000;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
        }

        .media-preview {
            background-color: #000000;
            border: 2px solid #B6B09F;
            border-radius: 8px;
            overflow: hidden;
        }

        .poll-option {
            background-color: #F2F2F2;
            border: 2px solid #B6B09F;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .remove-option-btn {
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .remove-option-btn:hover {
            background-color: #c82333;
            transform: scale(1.1);
        }

        .add-option-btn {
            background-color: #000000;
            color: #EAE4D5;
            border-radius: 8px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .add-option-btn:hover {
            background-color: #1a1a1a;
            transform: translateY(-2px);
        }

        .feature-card {
            background-color: #F2F2F2;
            border: 2px solid #B6B09F;
            border-radius: 12px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            border-color: #000000;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .feature-card.selected {
            background-color: #000000;
            color: #EAE4D5;
            border-color: #000000;
        }

        .submit-btn {
            background-color: #000000;
            color: #EAE4D5;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #1a1a1a;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
        }

        .cancel-btn {
            background-color: #F2F2F2;
            color: #000000;
            border: 2px solid #B6B09F;
            padding: 14px 32px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .cancel-btn:hover {
            background-color: #EAE4D5;
            border-color: #000000;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .emoji-picker {
            background-color: #F2F2F2;
            border: 2px solid #B6B09F;
            border-radius: 8px;
            padding: 8px;
            display: none;
            position: absolute;
            z-index: 10;
            max-width: 300px;
        }

        .emoji-picker.active {
            display: block;
        }

        .emoji-item {
            display: inline-block;
            font-size: 24px;
            padding: 4px;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .emoji-item:hover {
            transform: scale(1.2);
        }
    </style>

    <div class="main-content ml-0 md:ml-60">
        <div class="max-w-4xl mx-auto px-4 py-8">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold mb-2" style="color: #000000;">Create Post</h1>
                <p class="text-sm" style="color: #B6B09F;">Share your thoughts, media, polls, or questions with the community</p>
            </div>

            <!-- Post Type Tabs -->
            <div class="create-container rounded-xl shadow-lg p-6 mb-6">
                <div class="flex flex-wrap gap-3 mb-6">
                    <button class="tab-button active" onclick="switchTab('standard')" id="tab-standard">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Standard Post</span>
                        </div>
                    </button>
                    <button class="tab-button" onclick="switchTab('poll')" id="tab-poll">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span>Poll</span>
                        </div>
                    </button>
                    <button class="tab-button" onclick="switchTab('qa')" id="tab-qa">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Q&A</span>
                        </div>
                    </button>
                </div>

                <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data" id="postForm">
                    @csrf
                    <input type="hidden" name="post_type" id="post_type" value="standard">

                    <!-- Standard Post Content -->
                    <div class="tab-content active" id="content-standard">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold mb-2" style="color: #000000;">What's on your mind?</label>
                                <textarea 
                                    name="content" 
                                    id="standard_content"
                                    class="input-field w-full resize-none" 
                                    rows="6" 
                                    placeholder="Share your thoughts..."
                                ></textarea>
                            </div>

                            <!-- Media Upload -->
                            <div>
                                <label class="block text-sm font-semibold mb-2" style="color: #000000;">Add Media (Optional)</label>
                                <div class="border-2 border-dashed rounded-lg p-6 text-center" style="border-color: #B6B09F;">
                                    <input type="file" name="media" id="media" class="hidden" accept="image/*,video/*" onchange="previewMedia(event)">
                                    <label for="media" class="cursor-pointer">
                                        <svg class="w-12 h-12 mx-auto mb-2" style="color: #B6B09F;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                        </svg>
                                        <p class="text-sm font-medium" style="color: #000000;">Click to upload photo or video</p>
                                        <p class="text-xs mt-1" style="color: #B6B09F;">PNG, JPG, GIF, MP4 up to 10MB</p>
                                    </label>
                                </div>
                                <div id="mediaPreview" class="mt-4 hidden">
                                    <div class="media-preview relative">
                                        <img id="previewImage" class="w-full h-auto hidden">
                                        <video id="previewVideo" class="w-full h-auto hidden" controls></video>
                                        <button type="button" onclick="removeMedia()" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-2 hover:bg-red-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Options -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div class="feature-card text-center">
                                    <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <p class="text-xs font-medium">Location</p>
                                </div>
                                <div class="feature-card text-center">
                                    <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    <p class="text-xs font-medium">Tag People</p>
                                </div>
                                <div class="feature-card text-center" onclick="toggleEmojiPicker()">
                                    <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-xs font-medium">Emoji</p>
                                </div>
                                <div class="feature-card text-center">
                                    <svg class="w-6 h-6 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-xs font-medium">Schedule</p>
                                </div>
                            </div>

                            <!-- Emoji Picker -->
                            <div class="emoji-picker relative" id="emojiPicker">
                                <div class="grid grid-cols-8 gap-2">
                                    <span class="emoji-item" onclick="insertEmoji('😀')">😀</span>
                                    <span class="emoji-item" onclick="insertEmoji('😂')">😂</span>
                                    <span class="emoji-item" onclick="insertEmoji('😍')">😍</span>
                                    <span class="emoji-item" onclick="insertEmoji('🥰')">🥰</span>
                                    <span class="emoji-item" onclick="insertEmoji('😎')">😎</span>
                                    <span class="emoji-item" onclick="insertEmoji('🤔')">🤔</span>
                                    <span class="emoji-item" onclick="insertEmoji('😢')">😢</span>
                                    <span class="emoji-item" onclick="insertEmoji('😡')">😡</span>
                                    <span class="emoji-item" onclick="insertEmoji('👍')">👍</span>
                                    <span class="emoji-item" onclick="insertEmoji('👎')">👎</span>
                                    <span class="emoji-item" onclick="insertEmoji('❤️')">❤️</span>
                                    <span class="emoji-item" onclick="insertEmoji('🔥')">🔥</span>
                                    <span class="emoji-item" onclick="insertEmoji('✨')">✨</span>
                                    <span class="emoji-item" onclick="insertEmoji('🎉')">🎉</span>
                                    <span class="emoji-item" onclick="insertEmoji('💯')">💯</span>
                                    <span class="emoji-item" onclick="insertEmoji('🚀')">🚀</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Poll Content -->
                    <div class="tab-content" id="content-poll">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold mb-2" style="color: #000000;">Poll Question</label>
                                <input 
                                    type="text" 
                                    name="poll_question" 
                                    id="poll_question"
                                    class="input-field w-full" 
                                    placeholder="Ask a question..."
                                >
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-2" style="color: #000000;">Poll Options</label>
                                <div id="pollOptions">
                                    <div class="poll-option">
                                        <span class="text-sm font-semibold" style="color: #B6B09F;">1.</span>
                                        <input type="text" name="poll_options[]" class="input-field flex-1" placeholder="Option 1" required>
                                    </div>
                                    <div class="poll-option">
                                        <span class="text-sm font-semibold" style="color: #B6B09F;">2.</span>
                                        <input type="text" name="poll_options[]" class="input-field flex-1" placeholder="Option 2" required>
                                    </div>
                                </div>
                                <button type="button" onclick="addPollOption()" class="add-option-btn mt-3 text-sm font-medium">
                                    + Add Option
                                </button>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold mb-2" style="color: #000000;">Poll Duration</label>
                                    <select name="poll_duration" class="input-field w-full">
                                        <option value="1">1 Day</option>
                                        <option value="3">3 Days</option>
                                        <option value="7" selected>7 Days</option>
                                        <option value="14">14 Days</option>
                                        <option value="30">30 Days</option>
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <label class="flex items-center space-x-2 cursor-pointer">
                                        <input type="checkbox" name="allow_multiple" class="w-4 h-4">
                                        <span class="text-sm" style="color: #000000;">Allow multiple choices</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Q&A Content -->
                    <div class="tab-content" id="content-qa">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold mb-2" style="color: #000000;">Your Question</label>
                                <textarea 
                                    name="qa_question" 
                                    id="qa_question"
                                    class="input-field w-full resize-none" 
                                    rows="4" 
                                    placeholder="What would you like to know?"
                                ></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-2" style="color: #000000;">Details (Optional)</label>
                                <textarea 
                                    name="qa_details" 
                                    class="input-field w-full resize-none" 
                                    rows="4" 
                                    placeholder="Provide additional context or details..."
                                ></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold mb-2" style="color: #000000;">Category</label>
                                <select name="qa_category" class="input-field w-full">
                                    <option value="">Select a category</option>
                                    <option value="general">General</option>
                                    <option value="tech">Technology</option>
                                    <option value="health">Health & Fitness</option>
                                    <option value="education">Education</option>
                                    <option value="entertainment">Entertainment</option>
                                    <option value="lifestyle">Lifestyle</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div class="flex items-center space-x-2">
                                <input type="checkbox" name="qa_anonymous" id="qa_anonymous" class="w-4 h-4">
                                <label for="qa_anonymous" class="text-sm" style="color: #000000;">Post anonymously</label>
                            </div>
                        </div>
                    </div>

                    <!-- Privacy Settings -->
                    <div class="mt-6 p-4 rounded-lg" style="background-color: #F2F2F2; border: 2px solid #B6B09F;">
                        <label class="block text-sm font-semibold mb-2" style="color: #000000;">Who can see this post?</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="privacy" value="public" checked class="w-4 h-4">
                                <span class="text-sm" style="color: #000000;">Public</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="privacy" value="friends" class="w-4 h-4">
                                <span class="text-sm" style="color: #000000;">Friends Only</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="privacy" value="private" class="w-4 h-4">
                                <span class="text-sm" style="color: #000000;">Only Me</span>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 mt-6">
                        <button type="submit" class="submit-btn flex-1 sm:flex-initial">
                            Post
                        </button>
                        <button type="button" onclick="saveDraft()" class="cancel-btn flex-1 sm:flex-initial">
                            Save as Draft
                        </button>
                        <a href="{{ route('posts.index') }}" class="cancel-btn flex-1 sm:flex-initial text-center">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let pollOptionCount = 2;

        function switchTab(tab) {
            // Remove active class from all tabs
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            // Add active class to selected tab
            document.getElementById(`tab-${tab}`).classList.add('active');
            document.getElementById(`content-${tab}`).classList.add('active');
            document.getElementById('post_type').value = tab;
        }

        function previewMedia(event) {
            const file = event.target.files[0];
            if (!file) return;

            const preview = document.getElementById('mediaPreview');
            const image = document.getElementById('previewImage');
            const video = document.getElementById('previewVideo');

            preview.classList.remove('hidden');

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    image.src = e.target.result;
                    image.classList.remove('hidden');
                    video.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            } else if (file.type.startsWith('video/')) {
                const url = URL.createObjectURL(file);
                video.src = url;
                video.classList.remove('hidden');
                image.classList.add('hidden');
            }
        }

        function removeMedia() {
            document.getElementById('media').value = '';
            document.getElementById('mediaPreview').classList.add('hidden');
            document.getElementById('previewImage').src = '';
            document.getElementById('previewVideo').src = '';
        }

        function addPollOption() {
            pollOptionCount++;
            const container = document.getElementById('pollOptions');
            const optionDiv = document.createElement('div');
            optionDiv.className = 'poll-option';
            optionDiv.innerHTML = `
                <span class="text-sm font-semibold" style="color: #B6B09F;">${pollOptionCount}.</span>
                <input type="text" name="poll_options[]" class="input-field flex-1" placeholder="Option ${pollOptionCount}">
                <button type="button" onclick="removePollOption(this)" class="remove-option-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;
            container.appendChild(optionDiv);
        }

        function removePollOption(btn) {
            btn.parentElement.remove();
            pollOptionCount--;
            // Renumber options
            document.querySelectorAll('#pollOptions .poll-option').forEach((option, index) => {
                option.querySelector('span').textContent = `${index + 1}.`;
                option.querySelector('input').placeholder = `Option ${index + 1}`;
            });
        }

        function toggleEmojiPicker() {
            const picker = document.getElementById('emojiPicker');
            picker.classList.toggle('active');
        }

        function insertEmoji(emoji) {
            const textarea = document.getElementById('standard_content');
            const cursorPos = textarea.selectionStart;
            const textBefore = textarea.value.substring(0, cursorPos);
            const textAfter = textarea.value.substring(cursorPos);
            textarea.value = textBefore + emoji + textAfter;
            textarea.focus();
            textarea.selectionStart = textarea.selectionEnd = cursorPos + emoji.length;
            document.getElementById('emojiPicker').classList.remove('active');
        }

        function saveDraft() {
            alert('Draft saved! (Feature coming soon)');
        }

        // Close emoji picker when clicking outside
        document.addEventListener('click', (e) => {
            const picker = document.getElementById('emojiPicker');
            const targetElement = e.target.closest('.feature-card');
            if (!picker.contains(e.target) && !targetElement) {
                picker.classList.remove('active');
            }
        });
    </script>
</x-app-layout>