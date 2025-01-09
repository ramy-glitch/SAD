
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WSM Page</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/problemParam.css') }}">
</head>
<body>
@include('partials._navbar')
    <div class="container">

    <h2>Select Problem</h2>
        <form action="{{ route('show.Selected.problem') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="problem_name">Problem Name</label>
                <select name="problem_name" id="problem_name" class="form-control">
                    <option value="">Select a problem</option>
                    @foreach($problemNames as $name)
                        <option value="{{ $name }}" {{ isset($selectedProblemName) && $selectedProblemName === $name ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Select</button>
        </form>

        @if(isset($criteriaNames) && !empty($criteriaNames))

            <form id="edit-problem-form" action="{{ route('problem.update') }}" method="POST">
                @csrf
                @foreach($criteriaNames as $index => $name)
                    <div class="form-group">
                        <label for="criteria_name_{{ $index }}">Edit name for criterion {{ $index+1 }}:</label>
                        <input type="text" id="criteria_name_{{ $index }}" name="criteria_names[]" value="{{ $name }}">
                    </div>
                    <div class="form-group">
                        <label for="criteria_weight_{{ $index }}">Edit weight for criterion {{ $index+1 }}:</label>
                        <input type="text" id="criteria_weight_{{ $index }}" name="criteria_weights[]" value="{{ $criteriaWeights[$index] }}">
                    </div>
                    <div class="form-group">
                        <label for="intervals_{{ $index }}">Edit intervals for criterion {{ $index+1 }}:</label>
                        <div id="intervals_{{ $index }}">
                            <div class="interval-group">
                                <input type="text" name="intervals[{{ $index }}][]" value="{{ $intervals[$index][0]['min'] }}" placeholder="1">
                                @foreach($intervals[$index] as $interval)
                                    <input type="text" name="intervals[{{ $index }}][]" value="{{ $interval['max'] }}" placeholder="{{ $loop->iteration + 1 }}">
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
                <input type="hidden" name="problem_name" value="{{ $selectedProblemName }}">
                <button type="submit">Update</button>
            </form>

            <h2>Delete Problem</h2>
                <form id="delete-problem-form" action="{{ route('problem.delete') }}" method="POST" onsubmit="return confirmDelete()">
                    @csrf
                    <input type="hidden" name="problem_name" value="{{ $selectedProblemName }}">
                    <button type="submit" class="btn btn-danger" style="background-color: red;">Delete Problem</button>
                </form>
        @endif

    </div>

    <script>
        function confirmDelete() {
            return confirm('Are you sure you want to delete this problem?');
        }
    </script>
</body>

</html>