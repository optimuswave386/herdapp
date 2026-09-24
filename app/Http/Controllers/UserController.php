<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Posts;
use App\Models\Photos;
use App\Models\User;
use App\Models\User_is_following;
use App\Models\User_has_followers;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends Controller {
    
    public function index($username): View {

        //dd($username);
        
        if (Auth::user()->name == $username) {
            
            // Fetch all users from the database
            $userId = Auth::id();
            $followers = User_has_followers::where('user_id', $userId)->count();
            //if, check if the logged in user is following this profile user
            // user cannot follow themselves
            $isFollowing = false;

        } else {

            $user = User::where('name', $username)->first();
            if ($user) {
                
                $userId = $user->id;
                $followers = User_has_followers::where('user_id', $userId)->count();
                //if, check if the logged in user is following this profile user
                $isFollowing = User_has_followers::where('follower_id', Auth::id())
                    ->where('user_id', $userId)
                    ->exists();

            } else {
                abort(404); // User not found
            }
        }

        //dd($followers);

            // Based on the user ID, fetch related data on profile page
            $friends = User::all();
            $photos = Photos::where('idusers', $userId)->get();
            $username = User::where('id', $userId)->get();
            $subscription = "";
            $posts = Posts::where('user_id', $userId)->get();        
            $categories = Category::all();
            $user = User::find($userId);
            //$user->assignRole('admin-role');
            $roles = $user->getRoleNames();
            
        // Pass the data to the 'users.blade.php' view
        return view('user.profile', compact('userId', 'user', 'friends', 'photos', 'posts', 'categories', 'username', 'roles', 'subscription', 'followers', 'isFollowing'));
    }

    public function portal(): RedirectResponse
    {
        // User is not logged in, return the standard welcome view
        return Redirect::to('/');
    }

    public function settings($username='', $subscription=[]): View {
        $userId = Auth::id();
        $username = User::where('id', $userId)->get();
        $subscription = ['plan' => 'Free', 'status' => 'Active'];
        return view('user.partials.settings', compact('username', 'subscription'));
    }

    public function photos(Request $request): View {
    
        return view('user.partials.photos', [
            'user' => $request->user(), 
            'photos' => Photos::where('idusers', $request->user()->id)->get()
        ]);

    }

    public function redirectToProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleProviderCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Find or create a user in your database
        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'password' => bcrypt(Str::random(16)), // Generate a random password
            ]
        );

        // Log the user in
        Auth::login($user, true);

        // Redirect to intended page
        return redirect()->intended(route('products.index'));
    }

    public function followers($username): View {
        $user = User::where('name', $username)->firstOrFail();
        $followers = User_has_followers::where('user_id', $user->id)->get();
        return view('user.followers', compact('user', 'followers'));
    }

    public function toggleFollow(Request $request, $userId)
    {
        $userToFollow = User::findOrFail($userId);
        $currentUser = $request->user();
        $follow = $request->input('follow');
        if ($follow) {
            // Follow the user
            $result = User_has_followers::firstOrCreate([
                 'user_id' => $userToFollow->id,
                 'follower_id' => $currentUser->id,
            ]);
        } else {
            // Unfollow the user
            $result = User_has_followers::where('user_id', $userToFollow->id)
                ->where('follower_id', $currentUser->id)
                ->delete();
        }
        return response()->json(['status' => 'success']); // Adjust status message as needed
    }

}
