<?php

namespace App\Http\Controllers;

// Import necessary classes
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\WsmData;


// Define the controller class
class WsmController extends Controller
{
    // Display the main index view
    public function index()
    {
        return view('wsm.index');
    }


    public function criteriaNum(Request $request)
    {
        // Get the 'problem_name' and 'criteria' input values from the request
        $problemName = $request->input('problem_name');
        $criteria = $request->input('criteria');
    
        // Validate the input
        if (!$criteria || !is_numeric($criteria) || $criteria <= 1) {
            // If invalid, return an error response with status code 400
            return response()->json(['error' => 'Invalid criteria value'], 400);
        }
    
        // Store the problem name and criteria count in the session
        Session::put('problem_name', $problemName);
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
            $validator = \Validator::make($request->all(), [
                'criteria_names' => 'required|array|min:2',
                'criteria_names.*' => 'required|string|distinct',
                'criteria_weights' => 'required|array',
                'criteria_weights.*' => 'required|numeric',
                'intervals' => 'required|array',
                'intervals.*' => 'required|array|size:10', 
                'intervals.*.*' => 'required|numeric'
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
    
            // Retrieve input data from the request
            $criteriaNames = $request->input('criteria_names');
            $criteriaWeights = $request->input('criteria_weights');
            $intervalsInput = $request->input('intervals');
    
            // Retrieve the problem name from the session
            $problemName = Session::get('problem_name');
    
            // Parse the intervals input into a structured array
            $intervals = array_map(function($intervalGroup) {
                $parsedIntervals = [];
                for ($i = 0; $i < count($intervalGroup) - 1; $i++) {
                    $parsedIntervals[] = [
                        'min' => (float)$intervalGroup[$i],
                        'max' => (float)$intervalGroup[$i + 1]
                    ];
                }
                return $parsedIntervals;
            }, $intervalsInput);
    
            // Prepare the data to be stored in JSON format
            $data = [
                'problem_name' => $problemName,
                'criteria_names' => $criteriaNames,
                'criteria_weights' => $criteriaWeights,
                'intervals' => $intervals
            ];
    
            // Store the data in the database
            WsmData::create([
                'user_id' => auth()->id(), 
                'criteria_data' => $data
            ]);
    
            // Redirect to the criteria tables route
            return redirect()->route('criteria.tables');
        }
    


// Display the criteria tables view
public function showCriteriaTables(Request $request)
{
    // Retrieve all criteria data for the authenticated user
    $wsmData = WsmData::where('user_id', auth()->id())->get();

    // Extract the problem names from the criteria data
    $problemNames = $wsmData->map(function ($data) {
        return $data->criteria_data['problem_name'];
    })->unique();

    // if there is no criteria data in the session 
    if (!session('criteriaNames')) {
        
        
        $criteriaNames = [];
        $criteriaWeights = [];
        $intervals = [];
        
    } else {
        // Retrieve the criteria data from the session
        $criteriaNames = session('criteriaNames');
        $criteriaWeights = session('criteriaWeights');
        $intervals = session('intervals');
    }


    return view('wsm.criteria_tables', compact('problemNames', 'criteriaNames', 'criteriaWeights', 'intervals'));
}

// Display criteria tables for a selected problem
public function showCriteriaTablesProblem(Request $request)
{
    // Retrieve all criteria data for the authenticated user
    $wsmData = WsmData::where('user_id', auth()->id())->get();

    // Extract the problem names from the criteria data
    $problemNames = $wsmData->map(function ($data) {
        return $data->criteria_data['problem_name'];
    })->unique();

    // Get the selected problem name from the request
    $selectedProblemName = $request->input('problem_name');

    // Retrieve the criteria data for the selected problem name
    $selectedData = $wsmData->first(function ($data) use ($selectedProblemName) {
        return $data->criteria_data['problem_name'] === $selectedProblemName;
    });

    if ($selectedData) {
        $criteriaData = $selectedData->criteria_data;
        $criteriaNames = $criteriaData['criteria_names'];
        $criteriaWeights = $criteriaData['criteria_weights'];
        $intervals = $criteriaData['intervals'];

        // forget the criteria data in the session
        session()->forget(['criteriaNames', 'criteriaWeights', 'intervals']);
        // Store the criteria data in the session
        session([
            'criteriaNames' => $criteriaNames,
            'criteriaWeights' => $criteriaWeights,
            'intervals' => $intervals
        ]);

    } else {
        $criteriaNames = [];
        $criteriaWeights = [];
        $intervals = [];
    }

    // Return the criteria tables view with the data
    return view('wsm.criteria_tables', compact('problemNames', 'criteriaNames', 'criteriaWeights', 'intervals', 'selectedProblemName'));
}



    // Clear session data related to alternatives
    public function clearSessionData()
    { 
        // Forget the 'alternatives' data in the session
        Session::forget(['alternatives', 'criteriaNames', 'criteriaWeights', 'intervals']);
    
        // Return a JSON response indicating success
        return response()->json(['status' => 'Session data cleared']);
    }

    // Store an alternative and calculate its WSM value
    public function storeAlternative(Request $request)
    {
        // Validate the request data
        $request->validate([
            'alternative_name' => 'required|string',
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


    public function showProblemParams(Request $request)
{
    // Retrieve all criteria data for the authenticated user
    $wsmData = WsmData::where('user_id', auth()->id())->get();

    // Extract the problem names from the criteria data
    $problemNames = $wsmData->map(function ($data) {
        return $data->criteria_data['problem_name'];
    })->unique();

    // if there is no criteria data in the session 
    if (!session('criteriaNames')) {
        
        
        $criteriaNames = [];
        $criteriaWeights = [];
        $intervals = [];
        
    } else {
        // Retrieve the criteria data from the session
        $criteriaNames = session('criteriaNames');
        $criteriaWeights = session('criteriaWeights');
        $intervals = session('intervals');
    }


    return view('problemsPram.index', compact('problemNames', 'criteriaNames', 'criteriaWeights', 'intervals'));
}
}