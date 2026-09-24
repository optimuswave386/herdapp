<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::withCount('followers')->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }
}
