<x-app-layout>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
    </style>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6" style="background-color: #F2F2F2; min-height: 100vh;">
        {{-- Header --}}
        <div class="mb-6 animate-fade-in">
            <a href="{{ route('communities.show', $community->slug) }}" 
               class="inline-flex items-center text-sm mb-4 transition-colors duration-300 hover:underline" 
               style="color: #B6B09F;">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                </svg>
                Back to Community
            </a>
            <h1 class="text-3xl font-bold" style="color: #000000;">Community Settings</h1>
            <p class="text-sm mt-1" style="color: #B6B09F;">Manage your community preferences</p>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg animate-fade-in" style="background-color: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7;">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 rounded-lg animate-fade-in" style="background-color: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5;">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        {{-- Settings Form --}}
        <div class="rounded-lg p-6 animate-fade-in" style="background-color: #FFFFFF; border: 1px solid #B6B09F;">
            <form action="{{ route('communities.update', $community->slug) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Basic Information Section --}}
                <div class="mb-8">
                    <h2 class="text-xl font-bold mb-4 pb-2" style="color: #000000; border-bottom: 2px solid #B6B09F;">
                        Basic Information
                    </h2>

                    {{-- Community Name --}}
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-semibold mb-2" style="color: #000000;">
                            Community Name <span style="color: #EF4444;">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', $community->name) }}"
                               required
                               class="w-full px-4 py-3 rounded-lg transition-all duration-300 focus:ring-2 focus:outline-none @error('name') ring-2 @enderror"
                               style="border: 2px solid #B6B09F; background-color: #F2F2F2; color: #000000; @error('name') border-color: #EF4444; @enderror">
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
                                  style="border: 2px solid #B6B09F; background-color: #F2F2F2; color: #000000;">{{ old('description', $community->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm" style="color: #EF4444;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Privacy Setting --}}
                    <div class="mb-6">
                        <label class="flex items-start cursor-pointer">
                            <input type="checkbox" 
                                   name="is_private" 
                                   value="1"
                                   {{ old('is_private', $community->is_private) ? 'checked' : '' }}
                                   class="w-5 h-5 rounded mt-1 transition-all duration-300"
                                   style="border: 2px solid #B6B09F; color: #000000;">
                            <span class="ml-3">
                                <span class="block font-semibold" style="color: #000000;">Private Community</span>
                                <span class="text-sm" style="color: #B6B09F;">Only approved members can join and view content</span>
                            </span>
                        </label>
                    </div>
                </div>

                {{-- Visual Branding Section --}}
                <div class="mb-8">
                    <h2 class="text-xl font-bold mb-4 pb-2" style="color: #000000; border-bottom: 2px solid #B6B09F;">
                        Visual Branding
                    </h2>

                    {{-- Current Icon --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2" style="color: #000000;">
                            Community Icon
                        </label>
                        <div class="flex items-center space-x-4">
                            <div id="iconPreview" class="w-24 h-24 rounded-lg overflow-hidden flex items-center justify-center" 
                                 style="background-color: #EAE4D5; border: 2px solid #B6B09F;">
                                @if($community->icon)
                                    <img src="{{ asset('storage/'.$community->icon) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center font-bold text-xl" style="color: #000000;">
                                        {{ strtoupper(substr($community->name, 0, 2)) }}
                                    </div>
                                @endif
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
                                    Change Icon
                                </label>
                                <p class="text-xs mt-2" style="color: #B6B09F;">Recommended: Square image, max 2MB</p>
                            </div>
                        </div>
                        @error('icon')
                            <p class="mt-1 text-sm" style="color: #EF4444;">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Current Banner --}}
                    <div class="mb-6">
                        <label class="block text-sm font-semibold mb-2" style="color: #000000;">
                            Community Banner
                        </label>
                        <div class="space-y-3">
                            <div id="bannerPreview" class="w-full h-32 rounded-lg overflow-hidden flex items-center justify-center" 
                                 style="border: 2px solid #B6B09F; {{ $community->banner ? '' : 'background: linear-gradient(135deg, #000000 0%, #B6B09F 100%);' }}">
                                @if($community->banner)
                                    <img src="{{ asset('storage/'.$community->banner) }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-12 h-12" style="color: #F2F2F2;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                @endif
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
                                Change Banner
                            </label>
                            <p class="text-xs" style="color: #B6B09F;">Recommended: 1200x300px, max 5MB</p>
                        </div>
                        @error('banner')
                            <p class="mt-1 text-sm" style="color: #EF4444;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-between pt-6" style="border-top: 2px solid #B6B09F;">
                    <a href="{{ route('communities.show', $community->slug) }}"
                       class="px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:translate-y-[-2px]"
                       style="background-color: #EAE4D5; color: #000000; border: 1px solid #B6B09F;">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-3 rounded-lg font-medium transition-all duration-300 hover:translate-y-[-2px] hover:shadow-xl"
                            style="background-color: #000000; color: #F2F2F2;">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- Danger Zone --}}
        <div class="mt-6 rounded-lg p-6 animate-fade-in" style="background-color: #FEE2E2; border: 2px solid #FCA5A5;">
            <h2 class="text-xl font-bold mb-2" style="color: #991B1B;">Danger Zone</h2>
            <p class="text-sm mb-4" style="color: #991B1B;">Irreversible actions that will permanently affect this community</p>
            
            <div class="flex items-center justify-between p-4 rounded-lg" style="background-color: #FFFFFF;">
                <div>
                    <h3 class="font-semibold" style="color: #000000;">Delete Community</h3>
                    <p class="text-sm" style="color: #B6B09F;">Once deleted, all channels, posts, and members will be removed.</p>
                </div>
                <button onclick="openDeleteModal()"
                        class="px-6 py-2 rounded-lg font-medium transition-all duration-300 hover:shadow-lg"
                        style="background-color: #991B1B; color: #FFFFFF;">
                    Delete
                </button>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="relative rounded-lg p-6 w-full max-w-md animate-fade-in" style="background-color: #FFFFFF; border: 2px solid #FCA5A5;">
                <div class="mb-4">
                    <h3 class="text-xl font-bold mb-2" style="color: #991B1B;">Delete Community</h3>
                    <p style="color: #000000;">Are you sure you want to delete <strong>{{ $community->name }}</strong>? This action cannot be undone.</p>
                    <p class="mt-2 text-sm" style="color: #B6B09F;">All channels, posts, and member data will be permanently deleted.</p>
                </div>

                <div class="flex space-x-3">
                    <button type="button" 
                            onclick="closeDeleteModal()"
                            class="flex-1 px-4 py-2 rounded-lg font-medium transition-all duration-300"
                            style="background-color: #EAE4D5; color: #000000; border: 1px solid #B6B09F;">
                        Cancel
                    </button>
                    <form action="{{ route('communities.destroy', $community->slug) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full px-4 py-2 rounded-lg font-medium transition-all duration-300 hover:shadow-lg"
                                style="background-color: #991B1B; color: #FFFFFF;">
                            Delete Forever
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewIcon(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('iconPreview');
                    preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
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
                    preview.innerHTML = '<img src="' + e.target.result + '" class="w-full h-full object-cover">';
                }
                reader.readAsDataURL(file);
            }
        }

        function openDeleteModal() {
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</x-app-layout>