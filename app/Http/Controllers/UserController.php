<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
public function index(Request $request)
{
    $search = $request->input('search');
    $per_page = $request->input('per_page', 5);

    $users = User::query()
        ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%")
                                      ->orWhere('email', 'like', "%{$search}%"))
        ->select('id','name','email')
        ->paginate($per_page)
        ->withQueryString();

    return Inertia::render('Users/Index', [
        'users' => $users,
        'filters' => [
            'search' => $search,
            'per_page' => $per_page,
        ],
    ]);
}

}
