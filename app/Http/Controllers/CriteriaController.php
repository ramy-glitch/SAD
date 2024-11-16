<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CriteriaController extends Controller
{
    public function submit(Request $request)
    {
        try {
            $criteria = $request->input('criteria');
    
            // Validate input
            if (!$criteria || !is_numeric($criteria) || $criteria <= 0) {
                return response()->json(['error' => 'Invalid criteria value'], 400);
            }
    
            Session::put('criteria_count', $criteria);
    
            $html = view('partials.criteria_names_weights_form', compact('criteria'))->render();
    
            return response()->json(['html' => $html]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500); // Ensure JSON format for errors
        }
    }
    
    public function storeCriteriaNamesWeights(Request $request)
    {
        $criteriaNames = $request->input('criteria_names'); // Get the criteria names from the request
        $criteriaWeights = $request->input('criteria_weights'); // Get the criteria weights from the request

        // Store the criteria names and weights in the session or database as needed
        Session::put('criteria_names', $criteriaNames);
        Session::put('criteria_weights', $criteriaWeights);

        // For demonstration, we'll just return a simple message
        $html = '<p>Criteria names and weights have been stored successfully.</p>';

        return response()->json(['html' => $html]); // Return the HTML as a JSON response
    }
}
