<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


class WsmController extends Controller
{
    public function index()
    {
        return view('wsm.index');
    }

    public function criteriaNum(Request $request)
    {
            $criteria = $request->input('criteria');

            // Validate input
            if (!$criteria || !is_numeric($criteria) || $criteria <= 1) {
                return response()->json(['error' => 'Invalid criteria value'], 400);
            }

            Session::put('criteria_count', $criteria);

            $html = view('partials.criteria_names_weights_form', compact('criteria'))->render();

            return response()->json(['html' => $html]);
    }


    public function storeCriteriaNamesWeights(Request $request)
    {
        // Validate the request data
        $request->validate([
            'criteria_names' => 'required|array|min:1',
            'criteria_names.*' => 'required|string|distinct',
            'criteria_weights' => 'required|array|min:1',
            'criteria_weights.*' => 'required|numeric|min:0.01|max:1',
        ]);
    
        $criteriaNames = $request->input('criteria_names'); // Get the criteria names from the request
        $criteriaWeights = $request->input('criteria_weights'); // Get the criteria weights from the request
    
        // Validate the sum of weights
        $totalWeight = array_sum($criteriaWeights);
        if ($totalWeight !== 1.0) {
            return back()->withErrors(['criteria_weights' => 'The sum of all weights must be equal to 1.']);
        }
    
        // If validation passes, proceed with storing or processing the data
        // For example, you can store the data in the session or database
    
        // Redirect to the specified view upon success
        return redirect()->route('criteria.tables')->with([
            'criteriaNames' => $criteriaNames,
            'criteriaWeights' => $criteriaWeights
        ]);
    }
}
