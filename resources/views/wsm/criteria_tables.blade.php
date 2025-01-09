<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WSM Criteria Tables</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
@include('partials._navbar')
    <div class="container">

    <h2>Select Problem</h2>
        <form action="{{ route('criteria.tables.problem') }}" method="POST">
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
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        @if(isset($criteriaNames) && !empty($criteriaNames))
            <h2>Criteria and Weights</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Criteria</th>
                        <th>Weight</th>
                        <th>Intervals</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($criteriaNames as $index => $name)
                        <tr>
                            <td>{{ $name }}</td>
                            <td>{{ $criteriaWeights[$index] }}</td>
                            <td>
                                @foreach($intervals[$index] as $interval)
                                    [{{ $interval['min'] }}-{{ $interval['max'] }}]<br>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <h2>Alternatives</h2>
        <div class="mb-3">
            <button id="add-alternative-btn" class="btn btn-primary">Add Alternative</button>
            <button id="clear-table-btn" class="btn btn-danger ml-2">Clear Table</button>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Alternative</th>
                    @foreach($criteriaNames as $name)
                        <th>{{ $name }}</th>
                    @endforeach
                    <th>WSM Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach(session('alternatives', []) as $alternative)
                    <tr>
                        <td>{{ $alternative['name'] }}</td>
                        @foreach($alternative['scores'] as $score)
                            <td>{{ $score }}</td>
                        @endforeach
                        <td>{{ $alternative['wsm_value'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($bestAlternative = app('App\Http\Controllers\WsmController')->getBestAlternative())
            <h2>Best Alternative</h2>
            <p>{{ $bestAlternative['name'] }} with WSM Value: {{ $bestAlternative['wsm_value'] }}</p>
        @endif
    </div>

    <!-- Modal for adding alternative -->
    <div class="modal fade" id="add-alternative-modal" tabindex="-1" role="dialog" aria-labelledby="addAlternativeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="add-alternative-form" method="POST" action="{{ route('store.alternative') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAlternativeModalLabel">Add Alternative</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="alternative_name">Alternative Name</label>
                            <input type="text" class="form-control" id="alternative_name" name="alternative_name" required>
                        </div>
                        @foreach($criteriaNames as $index => $name)
                            <div class="form-group">
                                <label for="real_value_{{ $index }}">{{ $name }} Real Value</label>
                                <input type="number" class="form-control" id="real_value_{{ $index }}" name="real_values[]" required>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Alternative</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('add-alternative-btn').addEventListener('click', function() {
            $('#add-alternative-modal').modal('show');
        });

        document.getElementById('clear-table-btn').addEventListener('click', function() {
            if (confirm('Are you sure you want to clear all alternatives?')) {
                $.ajax({
                    type: 'POST',
                    url: '{{ route('clear.session.data') }}',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function() {
                        location.reload();
                    },
                    error: function() {
                        alert('Error clearing the table. Please try again.');
                    }
                });
            }
        });

    </script>
</body>
</html>