<x-app-layout>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        .preview-image {
            transition: all 0.3s ease;
        }
    </style>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6" style="background-color: #F2F2F2; min-height: 100vh;">
        {{-- Header --}}
        <div class="mb-6 animate-fade-in">
            <a href="{{ route('communities.index') }}" class="inline-flex items-center text-sm mb-4 transition-colors duration-300 hover:underline" style="color: #B6B09F;">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Back to Communities
            </a>
            <h1 class="text-3xl font-bold" style="color: #000000;">Create Community</h1>
            <p class="text-sm mt-1" style="color: #B6B09F;">Build your own community and connect with people</p>
        </div>

        {{-- Form --}}
        <div class="rounded-lg p-6 animate-fade-in" style="background-color: #FFFFFF; border: 1px solid #B6B09F;">
            <form action="{{ route('communities.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Community Name --}}
                <div class="mb-6">
                    <label for="name" class="block text-sm font-semibold mb-2" style="color: #000000;">
                        Community Name <span style="color: #EF4444;">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name') }}"
                           required
                           class="w-full px-4 py-3 rounded-lg transition-all duration-300 focus:ring-2 focus:outline-none @error('name') ring-2 @enderror"
                           style="border: 2px solid #B6B09F; background-color: #F2F2F2; color: #000000; @error('name') border-color: #EF4444; @enderror"
                           placeholder="e.g., Photography Enthusiasts">
                    @error('name')
                        <p class="mt-1 text-sm" style="color: #EF4444;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="mb-6">
                    <label for="description" class="block text-sm font-semibold mb-2" style="color: #000000;">
                        Description
                    </label>
                    <textarea name="description" 
                              id="description" 
                              rows="4"
                              class="w-full px-4 py-3 rounded-lg transition-all duration-300 focus:ring-2 focus:outline-none"
                              style="border: 2px solid #B6B09F; background-color: #F2F2F2; color: #000000;"
                              placeholder="Tell people what your community is about...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm" style="color: #EF4444;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Icon Upload --}}
                <div class="mb-6">
                    <label for="icon" class="block text-sm font-semibold mb-2" style="color: #000000;">
                        Community Icon
                    </label>
                    <div class="flex items-center space-x-4">
                        <div id="iconPreview" class="w-24 h-24 rounded-lg overflow-hidden flex items-center justify-center" 
                             style="background-color: #EAE4D5; border: 2px solid #B6B09F;">
                            <svg class="w-12 h-12" style="color: #B6B09F;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <input type="file" 
                                   name="icon" 
                                   id="icon" 
                                   accept="image/*"
                                   class="hidden"
                                   onchange="previewIcon(event)">
                            <label for="icon" 
                                   class="inline-block px-6 py-3 rounded-lg font-medium cursor-pointer transition-all duration-300 hover:translate-y-[-2px]"
                                   style="background-color: #EAE4D5; color: #000000; border: 1px solid #B6B09F;">
                                Choose Icon
                            </label>
                            <p class="text-xs mt-2" style="color: #B6B09F;">Recommended: Square image, max 2MB</p>
                        </div>
                    </div>
                    @error('icon')
                        <p class="mt-1 text-sm" style="color: #EF4444;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Banner Upload --}}
                <div class="mb-6">
                    <label for="banner" class="block text-sm font-semibold mb-2" style="color: #000000;">
                        Community Banner
                    </label>
                    <div class="space-y-3">
                        <div id="bannerPreview" class="w-full h-32 rounded-lg overflow-hidden flex items-center justify-center" 
                             style="background: linear-gradient(135deg, #000000 0%, #B6B09F 100%); border: 2px solid #B6B09F;">
                            <svg class="w-12 h-12" style="color: #F2F2F2;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input type="file" 
                               name="banner" 
                               id="banner" 
                               accept="image/*"
                               class="hidden"
                               onchange="previewBanner(event)">
                        <label for="banner" 
                               class="inline-block px-6 py-3 rounded-lg font-medium cursor-pointer transition-all duration-300 hover:translate-y-[-2px]"
                               style="background-color: #EAE4D5; color: #000000; border: 1px solid #B6B09F;">
                            Choose Banner
                        </label>
                        <p class="text-xs" style="color: #B6B09F;">Recommended: 1200x300px, max 5MB</p>
                    </div>
                    @error('banner')
                        <p class="mt-1 text-sm" style="color: #EF4444;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Privacy Setting --}}
                <div class="mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="is_private" 
                               value="1"
                               {{ old('is_private') ? 'checked' : '' }}
                               class="w-5 h-5 rounded transition-all duration-300"
                               style="border: 2px solid #B6B09F; color: #000000;">
                        <span class="ml-3">
                            <span class="block font-semibold" style="color: #000000;">Private Community</span>
                            <span class="text-sm" style="color: #B6B09F;">Only approved members can join and view content</span>
                        </span>
                    </label>
                </div>

                {{-- Submit Button --}}
                <div class="flex items-center justify-end space-x-3 pt-4" style="border-top: 2px solid #B6B09F;">
                    <a href="{{ route('communities.index') }}"
                       class="px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:translate-y-[-2px]"
                       style="background-color: #EAE4D5; color: #000000; border: 1px solid #B6B09F;">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:translate-y-[-2px] hover:shadow-xl"
                            style="background-color: #000000; color: #F2F2F2;">
                        Create Community
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewIcon(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('iconPreview');
                    preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover preview-image">';
                }
                reader.readAsDataURL(file);
            }
        }

        function previewBanner(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('bannerPreview');
                    preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover preview-image">';
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</x-app-layout>