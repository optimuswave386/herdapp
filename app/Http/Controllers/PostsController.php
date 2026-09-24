<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //$posts = Posts::all();
        $posts = Posts::paginate(10); // Paginate with 10 posts per page

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //fetch categories for the dropdown in the create post form
        //pass 'categories' to the view as an associative array of 'id' => 'name'
        $categories = Category::orderBy('name')->pluck('name', 'id');
        
        return view('posts.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        //$validated = $request->validate([
        $rules = [
            'title' => 'required|max:255',
            'content' => 'required',
            'author' => 'required',
            'excerpt' => 'required',
            'image' => 'nullable|max:255', // Consider changing to 'image' rule if file upload as nullable|image|max:255
            'slug' => 'nullable|max:255',
            'category_id' => 'nullable|integer',
            'user_id' => 'nullable|integer',
        ];
        //]);

        $rulesMessage = [
            'title.required' => 'The title field is required.',
            'content.required' => 'The content field is required.',
            'author.required' => 'The author field is required.',
            'excerpt.required' => 'The excerpt field is required.',
            'image.max' => 'The image must be a valid image file path not more than 255 characters.',
            'slug.max' => 'The slug may not be greater than 255 characters.',
            'category_id.integer' => 'The category ID must be an integer.',
            'user_id.integer' => 'The user ID must be an integer.',
        ];

        $validator = Validator::make($request->all(), $rules, $rulesMessage);
        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        //dd($request->all());

        // The $post variable now holds the created model instance.
        $post = Posts::create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'author' => $request->input('author'),
            'excerpt' => $request->input('excerpt'),
            'image' => $request->hasFile('image') ? $request->file('image')->getClientOriginalName() : $request->input('image'), // Store only the original file name
            'slug' => $request->input('slug'),
            'category_id' => $request->input('category_id'),
            'user_id' => $request->input('user_id'),
            'created_at' => now(),
            'modified_at' => now(),
        ]);

        if ($request->hasFile('image')) {
            $post->addMediaFromRequest('image')->toMediaCollection('images'); // 'images' is the collection name
        }
        
        // if ($request->hasFile('images')) {
        //     foreach ($request->file('images') as $image) {
        //         $post->addMedia($image)->toMediaCollection('images');
        //     }
        // }

        return Redirect::route('posts.index')->with('status', 'profile-updated');
    }

    /**
     * Display the specified resource.
     */
    public function show(String $id, Posts $posts)
    {
        // Find a post by its primary key
        $post = Posts::find($id);
        return view('posts.view', compact('post'));
    }

    /**
     * Display a listing of the posts filtered by user.
     */
    public function showbyUser($userId)
    {
        $posts = Posts::where('user_id', $userId)->paginate(10); // Paginate with 10 posts per page

        return view('posts.index', compact('posts'));
    }

    /**
     * Display a listing of the posts filtered by category.
     */
    public function showByCategory($categoryId)
    {
        $posts = Posts::where('category_id', $categoryId)->paginate(10); // Paginate with 10 posts per page

        //$posts = Posts::with('category')->get();

        // $posts = DB::table('posts')
        // ->join('categories', 'posts.category_id', '=', 'categories.id')
        // ->select('posts.*', 'categories.name as category_name') // Select the name and alias it
        // ->get();

        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(String $id, Posts $posts)
    {

        // if (!auth()->user()->hasAnyRole(['admin', 'editor'])) {
        //     abort(403, 'Unauthorized action.');
        // }

        // Find a post by an attribute using a where clause
        $post = Posts::where('id', $id)
                            ->orderBy('created_at', 'desc')
                            ->first();

        return view('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(String $id, Request $request, Posts $posts)
    {
        $post = Posts::find($id);
        if ($post) {
            $post->title = $request->title;
            $post->content = $request->content;
            $post->author = $request->author;
            $post->save(); // Updates the existing record
        }

        return Redirect::route('posts.index')->with('status', 'post-updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $id, Posts $posts)
    {
        $post = Posts::find($id);
        if ($post) {
            $post->delete();
        }
        
        return Redirect::route('posts.index')->with('status', 'post-deleted');

        // Or delete multiple records by query
        // Posts::where('idusers', 1)->delete();
    }
}
