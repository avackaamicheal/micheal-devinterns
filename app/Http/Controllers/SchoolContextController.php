<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SchoolContextController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'school_id' => 'nullable|exists:schools,id',
        ]);

        // If 'school_id' is null (or empty), we forget the session
        // to return to "Global View".
        if (empty($data['school_id'])) {
            session()->forget('active_school');
        } else {
            session(['active_school' => $data['school_id']]);
        }

        return back()->with('success', 'Context switched successfully.');
    }
}
