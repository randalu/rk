<?php

namespace App\Http\Controllers;

use App\Models\ActionLog;
use Illuminate\Http\Request;

class ActionLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActionLog::with('user')->latest('created_at');

        if ($request->filled('model')) {
            $query->where('model', $request->model);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        $logs   = $query->paginate(20)->withQueryString();
        $models = ActionLog::distinct()->pluck('model')->sort()->values();
        $users  = \App\Models\User::orderBy('name')->get();

        return view('action-log.index', compact('logs', 'models', 'users'));
    }
}