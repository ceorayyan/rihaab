<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Post; // <-- add this
// use App\Http\Controllers\KeyRequest;
use App\Models\KeyRequest; 

// use Illuminate\Support\Facades\Auth;/
class ProfileController extends Controller
{
    /**
     * Display a list of people with optional search.
     */

public function people(Request $request): View
{
    $search = $request->input('search');

    $users = User::query()
        ->where('id', '!=', Auth::id()) // exclude yourself
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        })
        ->paginate(10);

    return view('people.index', compact('users', 'search'));
}


    /**
     * Show a public profile by user.
     */
   public function publicProfile(User $user): View
{
    $user->load([
        'posts' => function ($q) {
            $q->latest();
        },
        'stories' => function ($q) {
            $q->where('is_active', 1)
                ->where('expires_at', '>', now());
        }
    ]);

    return view('profile.public', [
        'user' => $user,
        'postsCount' => $user->posts->count(),
        'storiesCount' => $user->stories->count(),
    ]);
}


    /**
     * Show the form for editing the user's profile.
     */
   
// app/Http/Controllers/ProfileController.php

// app/Http/Controllers/ProfileController.php

// app/Http/Controllers/ProfileController.php

public function profile(Request $request)
{
    $user = $request->user();

    $posts = Post::where('user_id', $user->id)->latest()->get();
    $keyRequests = KeyRequest::where('receiver_id', $user->id)
        ->where('status', 'pending')
        ->get();

    // 👉 This will show the main profile page (profile.blade.php)
    return view('profile.profile', compact('user', 'posts', 'keyRequests'));
}

public function edit(Request $request)
{
    $user = $request->user();

    $posts = Post::where('user_id', $user->id)->latest()->get();
    $keyRequests = KeyRequest::where('receiver_id', $user->id)
        ->where('status', 'pending')
        ->get();

    // 👉 This will show the edit form (edit.blade.php)
    return view('profile.edit', compact('user', 'posts', 'keyRequests'));
}



    /**
     * Update the user's profile.
     */
    public function update(Request $request): RedirectResponse
{
    $user = $request->user();

    $validated = $request->validate([
        'name'           => 'nullable|string|max:255',
        'bio'            => 'nullable|string|max:1000',
        'dob'            => 'nullable|date',
        'marital_status' => 'nullable|string|max:50',
        'education'      => 'nullable|string|max:255',
        'occupation'     => 'nullable|string|max:255',
        'profile_picture'=> 'nullable|image|mimes:jpg,jpeg,png|max:2048', // 2MB limit
    ]);

    // Handle profile picture upload
    if ($request->hasFile('profile_picture')) {
        $username = strtolower(preg_replace('/\s+/', '', $user->username)); // safe filename

        // Count existing files for this user in folder
        $files = glob(public_path("userprofilepicture/{$username}*"));
        $nextNumber = count($files); // first file = 0, next = 1, etc.

        // Get extension
        $extension = $request->file('profile_picture')->getClientOriginalExtension();

        // Build new filename
        $filename = "{$username}{$nextNumber}.{$extension}";

        // Store in public/userprofilepicture
        $request->file('profile_picture')->move(public_path('userprofilepicture'), $filename);

        // Save relative path in DB
        $validated['profile_picture'] = "userprofilepicture/{$filename}";
    }

    $user->update($validated);

    return redirect()->back()->with('success', 'Profile updated successfully!');
}


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Show the authenticated user's profile (read-only).
     */
    public function show(): View
    {
        return view('profile.show', [
            'user' => Auth::user(),
        ]);
    }
    public function feed($id)
{
    $user = Auth::user();
    $posts = Post::where('user_id', $user->id)
        ->with('user')
        ->latest()
        ->get();

    return view('profile.feed', [
        'posts' => $posts,
        'startId' => $id
    ]);
}
public function userFeed($user, $postId = null)
{
    // Find user by username or name
    $user = User::where('username', $user)
               ->orWhere('name', $user)
               ->with(['posts' => function($query) {
                   $query->latest()->with(['likes', 'comments']);
               }])
               ->firstOrFail();

    // Get all posts and filter them
    $posts = $user->posts;
    $mediaPosts = $posts->filter(fn($p) => in_array($p->media_type, ['image', 'video']))->values();
    $threadPosts = $posts->filter(fn($p) => !in_array($p->media_type, ['image', 'video']))->values();

    // Find the index of the specific post to scroll to
    $scrollToIndex = false;
    if ($postId) {
        foreach ($mediaPosts as $index => $post) {
            if ($post->id == $postId) {
                $scrollToIndex = $index;
                break;
            }
        }
    }

    return view('profile.user_feed', compact('user', 'mediaPosts', 'threadPosts', 'scrollToIndex'));
}
}