<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

use App\Models\Link;

class IngestController extends Controller
{
    //
    function handleUpload(Request $request) {
        // Validate the uploaded file as a required file with specific mime type and max length of 2MB
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ]);
        }

        $success_response = [];
        
        // Open and read the file
        $file = $request->file('csv_file');
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            // Get the header row
            $header = array_map('trim', fgetcsv($handle));

            // Check if the header row is valid
            // It must match the static header array in the Link model exactly
            // This will allow using array_combine safely below to fill the model
            if (count($header) !== count(Link::$header) || count(array_intersect(Link::$header, $header)) !== count(Link::$header)) {
                return response()->json([
                    'error' => 'Invalid CSV format.'
                ]);
            }

            // Loop through the remaining rows
            while (($row = fgetcsv($handle)) !== false) {
                // Create an associative array of the data using the header
                $data = array_combine($header, array_map('trim', $row));
                $link = Link::where('long_url', $data['long_url'])->first();
                $action = '';
                if (empty($link)) {
                    // Don't recreate existing long_urls
                    $link = Link::create($data);
                    $action = 'created';
                } else {
                    $action = 'existed';
                }

                $success_response[] = array_merge($data, [
                    'short_url' => env('SHORT_LINK_BASE_URL').'/'.$link->id,
                    'action' => $action,
                ]);
            }

            fclose($handle);

            return response()->json($success_response);
        }

        return response()->json([
            'error' => 'Could not read the CSV file.'
        ]);
    }
}
