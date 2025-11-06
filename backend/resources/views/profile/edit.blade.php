<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl" style="color: #000000;">
            {{ __('Edit Profile') }}
        </h2>
    </x-slot>

    <style>
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-down {
            animation: slideDown 0.5s ease-out;
        }
    </style>

    <div class="max-w-3xl mx-auto py-8" style="background-color: #F2F2F2; min-height: 100vh;">
        <div class="shadow rounded-xl p-6 animate-slide-down" style="background-color: #EAE4D5; border: 2px solid #B6B09F;">
            <h2 class="text-lg font-semibold mb-6" style="color: #000000;">Update Your Profile</h2>

            <!-- Profile Update Form -->
            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('patch')

                <!-- Profile Picture -->
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <img id="profilePreview" 
                             src="{{ $user->profile_picture ? asset($user->profile_picture) : 'https://via.placeholder.com/100' }}" 
                             class="w-24 h-24 rounded-full object-cover shadow-sm transition-all duration-300 hover:scale-110" 
                             style="border: 3px solid #B6B09F;">
                        <label for="profile_picture" 
                            class="absolute bottom-0 right-0 p-1.5 rounded-full shadow cursor-pointer transition-all duration-300 hover:scale-110" 
                            style="background-color: #000000;">
                            <svg class="w-5 h-5" style="color: #F2F2F2;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M12 4v16m8-8H4"/>
                            </svg>
                        </label>
                    </div>
                    <input type="file" id="profile_picture" name="profile_picture" class="hidden" accept="image/*">
                    <div>
                        <p class="font-medium" style="color: #000000;">{{ $user->name }}</p>
                        <p class="text-sm" style="color: #B6B09F;">{{ $user->email }}</p>
                    </div>
                </div>

                <!-- Bio -->
                <div>
                    <label for="bio" class="block text-sm font-medium mb-1" style="color: #000000;">Bio</label>
                    <textarea id="bio" name="bio" rows="2"
                              class="w-full rounded-lg px-3 py-2 text-sm transition-all duration-300 focus:scale-[1.02]"
                              style="border: 2px solid #B6B09F; background-color: #F2F2F2; color: #000000;"
                              placeholder="Tell us about yourself...">{{ old('bio', $user->bio ?? '') }}</textarea>
                </div>

                <!-- DOB + Marital Status -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="dob" class="block text-sm font-medium mb-1" style="color: #000000;">Date of Birth</label>
                        <input type="date" id="dob" name="dob"
                               value="{{ old('dob', $user->dob ?? '') }}"
                               class="w-full rounded-lg px-3 py-2 text-sm transition-all duration-300 focus:scale-[1.02]"
                               style="border: 2px solid #B6B09F; background-color: #F2F2F2; color: #000000;">
                    </div>
                    <div>
                        <label for="marital_status" class="block text-sm font-medium mb-1" style="color: #000000;">Marital Status</label>
                        <select id="marital_status" name="marital_status"
                                class="w-full rounded-lg px-3 py-2 text-sm transition-all duration-300 focus:scale-[1.02]"
                                style="border: 2px solid #B6B09F; background-color: #F2F2F2; color: #000000;">
                            <option value="">Select</option>
                            <option value="Single" {{ $user->marital_status=='Single'?'selected':'' }}>Single</option>
                            <option value="Married" {{ $user->marital_status=='Married'?'selected':'' }}>Married</option>
                            <option value="Divorced" {{ $user->marital_status=='Divorced'?'selected':'' }}>Divorced</option>
                            <option value="Widowed" {{ $user->marital_status=='Widowed'?'selected':'' }}>Widowed</option>
                        </select>
                    </div>
                </div>

                <!-- Education -->
                <div>
                    <label for="education" class="block text-sm font-medium mb-1" style="color: #000000;">Education</label>
                    <input type="text" id="education" name="education"
                           value="{{ old('education', $user->education ?? '') }}"
                           placeholder="e.g., Bachelor's in CS"
                           class="w-full rounded-lg px-3 py-2 text-sm transition-all duration-300 focus:scale-[1.02]"
                           style="border: 2px solid #B6B09F; background-color: #F2F2F2; color: #000000;">
                </div>

                <!-- Occupation -->
                <div>
                    <label for="occupation" class="block text-sm font-medium mb-1" style="color: #000000;">Occupation</label>
                    <input type="text" id="occupation" name="occupation"
                           value="{{ old('occupation', $user->occupation ?? '') }}"
                           placeholder="e.g., Software Engineer"
                           class="w-full rounded-lg px-3 py-2 text-sm transition-all duration-300 focus:scale-[1.02]"
                           style="border: 2px solid #B6B09F; background-color: #F2F2F2; color: #000000;">
                </div>

                <!-- Save Button -->
                <div class="flex justify-end pt-4">
                    <a href="{{ route('profile') }}" 
                       class="px-5 py-2 rounded-lg text-sm font-medium shadow-sm transition-all duration-300 hover:translate-y-[-2px] hover:shadow-lg mr-2" 
                       style="background-color: #B6B09F; color: #000000;">
                        Cancel
                    </a>
                    <button type="submit" 
                        class="px-5 py-2 rounded-lg text-sm font-medium shadow-sm transition-all duration-300 hover:translate-y-[-2px] hover:shadow-lg"
                        style="background-color: #000000; color: #F2F2F2;">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.getElementById("profile_picture")?.addEventListener("change", function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                document.getElementById("profilePreview").src = ev.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
    </script>
</x-app-layout>

