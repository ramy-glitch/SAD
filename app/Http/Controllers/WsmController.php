<?php

namespace App\Http\Controllers;

// Import necessary classes
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

// Define the controller class
class WsmController extends Controller
{
    // Display the main index view
    public function index()
    {
        return view('wsm.index');
    }

    // Handle the number of criteria input from the user
    public function criteriaNum(Request $request)
    {
        // Get the 'criteria' input value from the request
        $criteria = $request->input('criteria');

        // Validate the input
        if (!$criteria || !is_numeric($criteria) || $criteria <= 1) {
            // If invalid, return an error response with status code 400
            return response()->json(['error' => 'Invalid criteria value'], 400);
        }

        // Store the criteria count in the session
        Session::put('criteria_count', $criteria);

        // Render the criteria names and weights form view
        $html = view('partials.criteria_names_weights_form', compact('criteria'))->render();

        // Return the rendered HTML as a JSON response
        return response()->json(['html' => $html]);
    }

    // Store criteria names, weights, and intervals
    public function storeCriteriaNamesWeights(Request $request)
    {
        // Validate the request data
        $request->validate([
            'criteria_names' => 'required|array|min:2',
            'criteria_names.*' => 'required|string|distinct',
            'criteria_weights' => 'required|array',
            'criteria_weights.*' => 'required|numeric',
            'intervals' => 'required|array',
            'intervals.*' => 'required|array|size:9', // 1 min + 8 max values
            'intervals.*.*' => 'required|numeric'
        ]);

        // Retrieve input data from the request
        $criteriaNames = $request->input('criteria_names');
        $criteriaWeights = $request->input('criteria_weights');
        $intervalsInput = $request->input('intervals');

        Log::debug('Criteria Names:', $criteriaNames);
        Log::debug('Criteria Weights:', $criteriaWeights);
        Log::debug('Intervals Input:', $intervalsInput);

        // Parse the intervals input into a structured array
        $intervals = [];
        foreach ($intervalsInput as $intervalGroup) {
            $parsedIntervals = [];
            $min = (float)$intervalGroup[0]; // First min value
            for ($i = 1; $i < count($intervalGroup); $i++) {
                $max = (float)$intervalGroup[$i];
                $parsedIntervals[] = [
                    'min' => $min,
                    'max' => $max
                ];
                $min = $max; // Set the next min to the current max
            }
            $intervals[] = $parsedIntervals;
        }

        Log::debug('Parsed Intervals:', $intervals);

        // Store the criteria data in the session
        session([
            'criteriaNames' => $criteriaNames,
            'criteriaWeights' => $criteriaWeights,
            'intervals' => $intervals
        ]);

        // Redirect to the criteria tables route
        return redirect()->route('criteria.tables');
    }

    // Display the criteria tables view
    public function criteriaTables()
    {
        // Retrieve criteria data from the session
        $criteriaNames = session('criteriaNames', []);
        $criteriaWeights = session('criteriaWeights', []);
        $intervals = session('intervals', []);

        // Pass the data to the view
        return view('wsm.criteria_tables', compact('criteriaNames', 'criteriaWeights', 'intervals'));
    }

    // Clear session data related to alternatives
    public function clearSessionData()
    {
        // Forget the 'alternatives' data in the session
        Session::forget(['alternatives']);
    
        // Return a JSON response indicating success
        return response()->json(['status' => 'Session data cleared']);
    }

    // Store an alternative and calculate its WSM value
    public function storeAlternative(Request $request)
    {
        // Validate the request data
        $request->validate([
            'alternative_name' => 'required|string|unique:alternatives,name',
            'real_values' => 'required|array',
            'real_values.*' => 'required|numeric'
        ]);

        // Retrieve input data from the request
        $alternativeName = $request->input('alternative_name');
        $realValues = $request->input('real_values');

        // Retrieve data from the session
        $alternatives = session('alternatives', []);
        $criteriaWeights = session('criteriaWeights', []);
        $intervals = session('intervals', []);

        // Check if the alternative name already exists
        foreach ($alternatives as $alternative) {
            if ($alternative['name'] === $alternativeName) {
                // If it exists, return back with an error message
                return back()->withErrors(['alternative_name' => 'The alternative name must be unique.']);
            }
        }

        // Normalize the real-world values to scores based on intervals
        $normalizedScores = array_map(function ($value, $interval) {
            // Get the score corresponding to the value and interval
            return $this->getScoreFromValue($value, $interval);
        }, $realValues, $intervals);

        // Calculate the Weighted Sum Model (WSM) value
        $wsmValue = 0;
        foreach ($normalizedScores as $index => $score) {
            // Multiply each score by its corresponding weight and sum them up
            $wsmValue += $score * $criteriaWeights[$index];
        }

        // Add the new alternative to the alternatives array
        $alternatives[] = [
            'name' => $alternativeName,
            'scores' => $normalizedScores,
            'wsm_value' => $wsmValue
        ];

        // Store the updated alternatives in the session
        session(['alternatives' => $alternatives]);

        // Redirect to the criteria tables route
        return redirect()->route('criteria.tables');
    }

    // Helper function to get the score from a value based on intervals
    private function getScoreFromValue($value, $intervals)
    {
        // Loop through each interval
        foreach ($intervals as $index => $interval) {
            // Check if the value falls within the interval range
            if ($value >= $interval['min'] && $value <= $interval['max']) {
                // Return the score (index + 1) if it fits
                return $index + 1;
            }
        }
        // Return null if the value doesn't fit in any interval
        return null;
    }

    // Get the best alternative based on the highest WSM value
    public function getBestAlternative()
    {
        // Retrieve alternatives from the session
        $alternatives = session('alternatives', []);
        if (empty($alternatives)) {
            // Return null if there are no alternatives
            return null;
        }

        // Sort alternatives by WSM value in descending order and return the first one
        return collect($alternatives)->sortByDesc('wsm_value')->first();
    }
}