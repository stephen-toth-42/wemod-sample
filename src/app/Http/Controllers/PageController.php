<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Link;

class PageController extends Controller
{
    //
    function handleRequest(Request $request) {
        // Validate the requested short link
        $validator = Validator::make($request->route()->parameters(), [
            'id' => 'required|exists:links',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ]);
        }

        // Find the link, increment hits, and redirect
        $link = Link::where('id', $request->id)->first();
        if (!empty($link)) {
            $link->incrementHits()->save();
            return redirect($link->long_url);
        }

        return response()->json([
            'error' => 'Could not find URL.'
        ]);
    }
}
