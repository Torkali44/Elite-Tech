<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CommunityController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->with(['cv', 'ideas'])
            ->latest();

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(function ($b) use ($q) {
                $b->where('name', 'like', "%{$q}%")
                    ->orWhere('title', 'like', "%{$q}%")
                    ->orWhere('bio', 'like', "%{$q}%");
            });
        }

        $members = $query->paginate(12)->withQueryString();

        return view('community.index', compact('members'));
    }

    public function show($id)
    {
        $user = User::with(['cv', 'ideas'])->findOrFail($id);

        return view('profile.show', compact('user'));
    }
}
