<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight" style="color: #000000;">
            {{ __('Create New Story') }}
        </h2>
    </x-slot>

    <style>
        .story-card {
            background-color: #EAE4D5;
            border: 2px solid #B6B09F;
        }
        
        .type-selector {
            border: 2px solid #B6B09F;
            transition: all 0.3s ease;
        }
        
        .type-selector:hover {
            border-color: #000000;
            background-color: #F2F2F2;
        }
        
        .type-selector.active {
            border-color: #000000;
            background-color: #F2F2F2;
        }
        
        .upload-zone {
            border: 2px dashed #B6B09F;
            background-color: #F2F2F2;
            transition: all 0.3s ease;
        }
        
        .upload-zone:hover {
            border-color: #000000;
            background-color: #EAE4D5;
        }
        
        .form-textarea {
            background-color: #F2F2F2;
            border: 2px solid #B6B09F;
            color: #000000;
        }
        
        .form-textarea:focus {
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
        }
        
        .btn-secondary {
            background-color: #B6B09F;
            color: #000000;
        }
        
        .btn-secondary:hover {
            background-color: #a09a89;
        }
        
        .info-alert {
            background-color: #F2F2F2;
            border: 2px solid #B6B09F;
        }
        
        .preview-container {
            background-color: #F2F2F2;
            border: 2px solid #B6B09F;
        }
    </style>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="story-card overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('stories.store') }}" method="POST" enctype="multipart/form-data" 
                          x-data="storyForm()" x-init="init()">
                        @csrf

                        <!-- Story Type Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium mb-3" style="color: #000000;">Story Type</label>
                            <div class="flex space-x-4">
                                <label class="flex-1">
                                    <input type="radio" name="type" value="image" class="sr-only" x-model="selectedType">
                                    <div class="type-selector rounded-lg p-4 cursor-pointer text-center"
                                         :class="selectedType === 'image' ? 'active' : ''">
                                        <svg class="w-8 h-8 mx-auto mb-2" style="color: #000000;" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                                        </svg>
                                        <span class="font-medium" style="color: #000000;">Image</span>
                                    </div>
                                </label>
                                
                                <label class="flex-1">
                                    <input type="radio" name="type" value="video" class="sr-only" x-model="selectedType">
                                    <div class="type-selector rounded-lg p-4 cursor-pointer text-center"
                                         :class="selectedType === 'video' ? 'active' : ''">
                                        <svg class="w-8 h-8 mx-auto mb-2" style="color: #000000;" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                                        </svg>
                                        <span class="font-medium" style="color: #000000;">Video</span>
                                    </div>
                                </label>
                                
                                <label class="flex-1">
                                    <input type="radio" name="type" value="text" class="sr-only" x-model="selectedType">
                                    <div class="type-selector rounded-lg p-4 cursor-pointer text-center"
                                         :class="selectedType === 'text' ? 'active' : ''">
                                        <svg class="w-8 h-8 mx-auto mb-2" style="color: #000000;" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm0 2h12v8H4V6z"/>
                                        </svg>
                                        <span class="font-medium" style="color: #000000;">Text</span>
                                    </div>
                                </label>
                            </div>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- File Upload -->
                        <div x-show="selectedType === 'image' || selectedType === 'video'" class="mb-6">
                            <label class="block text-sm font-medium mb-2" style="color: #000000;">Upload File</label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 upload-zone rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12" style="color: #B6B09F;" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <div class="flex text-sm" style="color: #000000;">
                                        <label class="relative cursor-pointer rounded-md font-medium hover:underline">
                                            <span>Upload a file</span>
                                            <input type="file" name="file" class="sr-only" 
                                                   x-ref="fileInput"
                                                   @change="handleFileSelect"
                                                   :accept="selectedType === 'image' ? 'image/*' : 'video/*'">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs" style="color: #B6B09F;" x-text="selectedType === 'image' ? 'PNG, JPG, GIF up to 10MB' : 'MP4, WebM up to 10MB'"></p>
                                </div>
                            </div>
                            @error('file')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Text Content -->
                        <div x-show="selectedType === 'text'" class="mb-6">
                            <label for="content" class="block text-sm font-medium mb-2" style="color: #000000;">Story Content</label>
                            <textarea name="content" id="content" rows="6" 
                                      class="form-textarea block w-full rounded-md shadow-sm"
                                      placeholder="Write your story here..."
                                      x-model="textContent"
                                      @input="updateTextPreview"></textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Caption -->
                        <div class="mb-6">
                            <label for="caption" class="block text-sm font-medium mb-2" style="color: #000000;">Caption (Optional)</label>
                            <textarea name="caption" id="caption" rows="2" 
                                      class="form-textarea block w-full rounded-md shadow-sm"
                                      placeholder="Add a caption to your story..."
                                      maxlength="500"></textarea>
                            <p class="mt-1 text-sm" style="color: #B6B09F;">Maximum 500 characters</p>
                            @error('caption')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Preview -->
                        <div x-show="showPreview" class="mb-6">
                            <label class="block text-sm font-medium mb-2" style="color: #000000;">Preview</label>
                            <div class="preview-container rounded-lg p-4">
                                <div x-show="selectedType === 'image' && imagePreview">
                                    <img :src="imagePreview" class="max-h-64 mx-auto rounded">
                                </div>
                                <div x-show="selectedType === 'video' && videoPreview">
                                    <video controls class="max-h-64 mx-auto rounded">
                                        <source :src="videoPreview">
                                    </video>
                                </div>
                                <div x-show="selectedType === 'text' && textContent" class="text-center">
                                    <p x-text="textContent" style="color: #000000;"></p>
                                </div>
                            </div>
                        </div>

                        <!-- Info Alert -->
                        <div class="mb-6 p-4 info-alert rounded-lg">
                            <div class="flex">
                                <svg class="flex-shrink-0 w-5 h-5" style="color: #000000;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <div class="ml-3">
                                    <p class="text-sm" style="color: #000000;">
                                        Your story will be visible for 24 hours and only to users who have accepted key requests with you.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-between">
                            <a href="{{ route('stories.index') }}" 
                               class="btn-secondary font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="btn-primary font-bold py-2 px-4 rounded">
                                Share Story
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function storyForm() {
            return {
                selectedType: '',
                textContent: '',
                imagePreview: null,
                videoPreview: null,
                showPreview: false,

                init() {
                    // Initialize component
                },

                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            if (this.selectedType === 'image') {
                                this.imagePreview = e.target.result;
                                this.videoPreview = null;
                            } else if (this.selectedType === 'video') {
                                this.videoPreview = e.target.result;
                                this.imagePreview = null;
                            }
                            this.showPreview = true;
                        };
                        reader.readAsDataURL(file);
                    }
                },

                updateTextPreview() {
                    this.showPreview = this.textContent.trim().length > 0;
                    this.imagePreview = null;
                    this.videoPreview = null;
                }
            }
        }
    </script>
</x-app-layout>