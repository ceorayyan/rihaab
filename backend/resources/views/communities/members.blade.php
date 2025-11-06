<x-app-layout>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        .animate-slide-in {
            animation: slideIn 0.5s ease-out;
        }
        .member-item {
            animation: fadeIn 0.4s ease-out forwards;
            opacity: 0;
        }
        .member-item:nth-child(1) { animation-delay: 0.1s; }
        .member-item:nth-child(2) { animation-delay: 0.15s; }
        .member-item:nth-child(3) { animation-delay: 0.2s; }
        .member-item:nth-child(n+4) { animation-delay: 0.25s; }
    </style>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6" style="background-color: #F2F2F2; min-height: 100vh;">
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
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold" style="color: #000000;">Member Management</h1>
                    <p class="text-sm mt-1" style="color: #B6B09F;">Manage {{ $community->name }} members and permissions</p>
                </div>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg animate-slide-in" style="background-color: #D1FAE5; color: #065F46; border: 1px solid #6EE7B7;">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 rounded-lg animate-slide-in" style="background-color: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5;">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif

        {{-- Pending Requests Section --}}
        @if($pendingMembers->count() > 0)
            <div class="mb-6 rounded-lg p-6 animate-fade-in" style="background-color: #FEF3C7; border: 2px solid #F59E0B;">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 mr-2" style="color: #F59E0B;" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    <h2 class="text-xl font-bold" style="color: #92400E;">Pending Requests ({{ $pendingMembers->count() }})</h2>
                </div>

                <div class="space-y-3">
                    @foreach($pendingMembers as $member)
                        <div class="member-item rounded-lg p-4 flex items-center justify-between" 
                             style="background-color: #FFFFFF; border: 1px solid #F59E0B;">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('profile.public', ['user' => $member->user->username]) }}">
                                    <div class="w-12 h-12 rounded-full overflow-hidden transition-transform duration-300 hover:scale-110" 
                                         style="border: 2px solid #F59E0B;">
                                        @if($member->user->profile_picture)
                                            <img src="{{ asset('storage/'.$member->user->profile_picture) }}" 
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center font-bold" 
                                                 style="background: linear-gradient(135deg, #000000 0%, #B6B09F 100%); color: #F2F2F2;">
                                                {{ strtoupper(substr($member->user->name, 0, 2)) }}
                                            </div>
                                        @endif
                                    </div>
                                </a>
                                <div>
                                    <a href="{{ route('profile.public', ['user' => $member->user->username]) }}" 
                                       class="font-semibold hover:underline" 
                                       style="color: #000000;">
                                        {{ $member->user->username }}
                                    </a>
                                    <p class="text-sm" style="color: #B6B09F;">{{ $member->user->name }}</p>
                                    <p class="text-xs" style="color: #B6B09F;">Requested {{ $member->created_at->diffForHumans() }}</p>
                                </div>
                            </div>

                            <div class="flex space-x-2">
                                <form action="{{ route('communities.members.approve', [$community->slug, $member->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px]"
                                            style="background-color: #10B981; color: #FFFFFF;">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('communities.members.reject', [$community->slug, $member->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px]"
                                            style="background-color: #EF4444; color: #FFFFFF;">
                                        Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Members List --}}
        <div class="rounded-lg p-6 animate-fade-in" style="background-color: #FFFFFF; border: 1px solid #B6B09F;">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold" style="color: #000000;">
                    Members ({{ $approvedMembers->count() }})
                </h2>
            </div>

            @if($approvedMembers->count() > 0)
                <div class="space-y-3">
                    @foreach($approvedMembers as $member)
                        <div class="member-item rounded-lg p-4 transition-all duration-300 hover:shadow-lg" 
                             style="background-color: #F2F2F2; border: 1px solid #B6B09F;">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3 flex-1">
                                    <a href="{{ route('profile.public', ['user' => $member->user->username]) }}">
                                        <div class="w-12 h-12 rounded-full overflow-hidden transition-transform duration-300 hover:scale-110" 
                                             style="border: 2px solid #B6B09F;">
                                            @if($member->user->profile_picture)
                                                <img src="{{ asset('storage/'.$member->user->profile_picture) }}" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center font-bold" 
                                                     style="background: linear-gradient(135deg, #000000 0%, #B6B09F 100%); color: #F2F2F2;">
                                                    {{ strtoupper(substr($member->user->name, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('profile.public', ['user' => $member->user->username]) }}" 
                                               class="font-semibold hover:underline" 
                                               style="color: #000000;">
                                                {{ $member->user->username }}
                                            </a>
                                            @if($member->user_id === $community->creator_id)
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium" 
                                                      style="background-color: #DBEAFE; color: #1E40AF;">
                                                    Creator
                                                </span>
                                            @endif
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium capitalize"
                                                  style="background-color: {{ $member->role === 'admin' ? '#FEE2E2' : ($member->role === 'moderator' ? '#E0E7FF' : '#F3F4F6') }}; 
                                                         color: {{ $member->role === 'admin' ? '#991B1B' : ($member->role === 'moderator' ? '#3730A3' : '#1F2937') }};">
                                                {{ $member->role }}
                                            </span>
                                        </div>
                                        <p class="text-sm" style="color: #B6B09F;">{{ $member->user->name }}</p>
                                        <p class="text-xs" style="color: #B6B09F;">Joined {{ $member->joined_at->diffForHumans() }}</p>
                                    </div>
                                </div>

                                @if($member->user_id !== $community->creator_id)
                                    <div class="flex items-center space-x-2">
                                        <button onclick="openRoleModal({{ $member->id }}, '{{ $member->user->username }}', '{{ $member->role }}')" 
                                                class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px]"
                                                style="background-color: #EAE4D5; color: #000000; border: 1px solid #B6B09F;">
                                            Change Role
                                        </button>
                                        <form action="{{ route('communities.members.remove', [$community->slug, $member->id]) }}" 
                                              method="POST"
                                              onsubmit="return confirm('Are you sure you want to remove this member?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 hover:translate-y-[-2px]"
                                                    style="background-color: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5;">
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto mb-4" style="color: #B6B09F;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h3 class="text-xl font-semibold mb-2" style="color: #B6B09F;">No Members Yet</h3>
                    <p style="color: #B6B09F;">Members will appear here once they join.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Change Role Modal --}}
    <div id="roleModal" class="hidden fixed inset-0 z-50 overflow-y-auto" style="background-color: rgba(0, 0, 0, 0.5);">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="relative rounded-lg p-6 w-full max-w-md animate-fade-in" style="background-color: #FFFFFF; border: 2px solid #B6B09F;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold" style="color: #000000;">Change Member Role</h3>
                    <button onclick="closeRoleModal()" style="color: #B6B09F;">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form id="roleForm" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <p class="text-sm mb-4" style="color: #B6B09F;">
                            Change role for <span id="memberUsername" class="font-semibold" style="color: #000000;"></span>
                        </p>
                        <label class="block text-sm font-semibold mb-2" style="color: #000000;">Select Role</label>
                        <select name="role" 
                                id="roleSelect"
                                class="w-full px-4 py-2 rounded-lg"
                                style="border: 2px solid #B6B09F; background-color: #F2F2F2; color: #000000;">
                            <option value="member">Member - Can post and comment</option>
                            <option value="moderator">Moderator - Can moderate content</option>
                            <option value="admin">Admin - Full control</option>
                        </select>
                    </div>

                    <div class="flex space-x-3">
                        <button type="button" 
                                onclick="closeRoleModal()"
                                class="flex-1 px-4 py-2 rounded-lg font-medium transition-all duration-300"
                                style="background-color: #EAE4D5; color: #000000; border: 1px solid #B6B09F;">
                            Cancel
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 rounded-lg font-medium transition-all duration-300 hover:shadow-lg"
                                style="background-color: #000000; color: #F2F2F2;">
                            Update Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRoleModal(memberId, username, currentRole) {
            const modal = document.getElementById('roleModal');
            const form = document.getElementById('roleForm');
            const usernameSpan = document.getElementById('memberUsername');
            const roleSelect = document.getElementById('roleSelect');
            
            form.action = `/communities/{{ $community->slug }}/members/${memberId}/role`;
            usernameSpan.textContent = username;
            roleSelect.value = currentRole;
            modal.classList.remove('hidden');
        }

        function closeRoleModal() {
            document.getElementById('roleModal').classList.add('hidden');
        }
    </script>
</x-app-layout>