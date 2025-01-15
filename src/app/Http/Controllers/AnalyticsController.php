<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Link;

class AnalyticsController extends Controller
{
    function handleRequest(Request $request) {
        $links_by_user = Link::where(function($query) use($request) {
            if ($request->input('user')) {
                $query->where('user', $request->input('user'));
            }
        })->get()->groupBy('user');

        return response()->json($links_by_user);
    }
}
