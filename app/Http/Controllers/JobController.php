<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $talents = User::query()
            ->where('show_in_jobs_forum', true)
            ->where('kyc_status', 'approved')
            ->where('is_suspended', false)
            ->where('available_for_hire', true)
            ->where(function ($q) {
                $q->where('role', 'developer')
                    ->orWhereJsonContains('roles', 'developer');
            })
            ->with('cv')
            ->latest()
            ->paginate(12);

        return view('jobs.index', compact('talents'));
    }
}
