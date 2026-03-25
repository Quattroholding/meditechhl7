<?php

namespace App\Http\Controllers;

use App\Models\HemoScreenStandaloneResult;

class HemoScreenStandaloneWebController extends Controller
{
    /**
     * Display the HemoScreen standalone dashboard
     */
    public function index()
    {
        return view('hemoscreen.dashboard');
    }

    /**
     * Display a specific result detail
     */
    public function show(string $resultId)
    {
        // Verify ownership and load result
        $result = HemoScreenStandaloneResult::where('id', $resultId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('hemoscreen.result-detail', compact('result'));
    }
}
