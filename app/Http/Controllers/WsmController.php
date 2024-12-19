<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

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
            'criteria_weights' => 'required|array',
            'criteria_weights.*' => 'required|numeric',
            'intervals' => 'required|array',
            'intervals.*' => 'required|string|regex:/^(\d+-\d+,)*\d+-\d+$/'
        ]);

        $criteriaNames = $request->input('criteria_names');
        $criteriaWeights = $request->input('criteria_weights');
        $intervalsInput = $request->input('intervals');

        // Debugging statements
        Log::debug('Criteria Names:', $criteriaNames);
        Log::debug('Criteria Weights:', $criteriaWeights);
        Log::debug('Intervals Input:', $intervalsInput);

        $intervals = [];
        foreach ($intervalsInput as $intervalString) {
            $intervals[] = array_map(function ($interval) {
                list($min, $max) = explode('-', $interval);
                return ['min' => (float)$min, 'max' => (float)$max];
            }, explode(',', $intervalString));
        }

        // Debugging statement
        Log::debug('Parsed Intervals:', $intervals);

        // Store the data in the session
        session([
            'criteriaNames' => $criteriaNames,
            'criteriaWeights' => $criteriaWeights,
            'intervals' => $intervals
        ]);

        // Redirect to the specified view upon success
        return redirect()->route('criteria.tables');
    }

    public function criteriaTables()
    {
        $criteriaNames = session('criteriaNames', []);
        $criteriaWeights = session('criteriaWeights', []);
        $intervals = session('intervals', []);
        return view('wsm.criteria_tables', compact('criteriaNames', 'criteriaWeights', 'intervals'));
    }

    public function clearSessionData()
    {
        // Clear the session values related to criteria and alternatives
        Session::forget(['alternatives']);
    
        return response()->json(['status' => 'Session data cleared']);
    }

    public function storeAlternative(Request $request)
    {
        // Validate the request data
        $request->validate([
            'alternative_name' => 'required|string|unique:alternatives,name',
            'real_values' => 'required|array',
            'real_values.*' => 'required|numeric'
        ]);

        $alternativeName = $request->input('alternative_name');
        $realValues = $request->input('real_values');

        $alternatives = session('alternatives', []);
        $criteriaWeights = session('criteriaWeights', []);
        $intervals = session('intervals', []);

        // Check if the alternative name is unique
        foreach ($alternatives as $alternative) {
            if ($alternative['name'] === $alternativeName) {
                return back()->withErrors(['alternative_name' => 'The alternative name must be unique.']);
            }
        }

        // Normalize the real-world values
        $normalizedScores = array_map(function ($value, $interval) {
            return $this->getScoreFromValue($value, $interval);
        }, $realValues, $intervals);

        // Calculate WSM value
        $wsmValue = 0;
        foreach ($normalizedScores as $index => $score) {
            $wsmValue += $score * $criteriaWeights[$index];
        }

        $alternatives[] = [
            'name' => $alternativeName,
            'scores' => $normalizedScores,
            'wsm_value' => $wsmValue
        ];

        session(['alternatives' => $alternatives]);

        return redirect()->route('criteria.tables');
    }

    private function getScoreFromValue($value, $intervals)
    {
        foreach ($intervals as $index => $interval) {
            if ($value >= $interval['min'] && $value <= $interval['max']) {
                return $index + 1; // Score is index + 1
            }
        }
        return null; // If value doesn't fit in any interval
    }

    public function getBestAlternative()
    {
        $alternatives = session('alternatives', []);
        if (empty($alternatives)) {
            return null;
        }

        return collect($alternatives)->sortByDesc('wsm_value')->first();
    }
}