<?php

namespace App\Http\Controllers;

use App\Models\Photos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PhotosController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all photos from the database
        $photos = Photos::all();

        // Fetch single record based on id
        // $item = DB::table('photos')->where('idphotos', $id)->first();
        // $item->photoblob

        // Pass the data to the 'photos.blade.php' view in user's folder
        return view('user.partials.photos', compact('photos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Photos $photos)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Photos $photos)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Photos $photos)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Photos $photos)
    {
        //
    }
}
