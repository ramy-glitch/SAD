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
            'criteria_names' => 'required|array|min:2',
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

        // Store the data in the session
        session([
            'criteriaNames' => $criteriaNames,
            'criteriaWeights' => $criteriaWeights
        ]);

        // Redirect to the specified view upon success
        return redirect()->route('criteria.tables');
    }

    public function criteriaTables()
    {
        $criteriaNames = session('criteriaNames', []);
        $criteriaWeights = session('criteriaWeights', []);
        return view('wsm.criteria_tables', compact('criteriaNames', 'criteriaWeights'));
    }

    
    public function storeAlternative(Request $request)
    {
        // Validate the request data
        $request->validate([
            'alternative_name' => 'required|string',
            'scores' => 'required|array|min:1',
            'scores.*' => 'required|numeric|min:0|max:10',
        ]);
    
        $alternativeName = $request->input('alternative_name');
        $scores = $request->input('scores');
    
        $alternatives = session('alternatives', []);
        $criteriaWeights = session('criteriaWeights', []);
    
        // Check if the alternative name is unique
        foreach ($alternatives as $alternative) {
            if ($alternative['name'] === $alternativeName) {
                return back()->withErrors(['alternative_name' => 'The alternative name must be unique.']);
            }
        }
    
        // Calculate WSM value
        $wsmValue = 0;
        foreach ($scores as $index => $score) {
            $wsmValue += $score * $criteriaWeights[$index];
        }
    
        $alternatives[] = [
            'name' => $alternativeName,
            'scores' => $scores,
            'wsm_value' => $wsmValue
        ];
    
        session(['alternatives' => $alternatives]);
    
        return redirect()->route('criteria.tables');
    }

    public function getBestAlternative()
    {
        $alternatives = session('alternatives', []);
        if (empty($alternatives)) {
            return null;
        }

        usort($alternatives, function ($a, $b) {
            return $b['wsm_value'] <=> $a['wsm_value'];
        });

        return $alternatives[0];
    }
}
