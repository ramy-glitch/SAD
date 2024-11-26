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
        <h2>Criteria and Weights</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Criteria</th>
                    <th>Weight</th>
                </tr>
            </thead>
            <tbody>
                @foreach($criteriaNames as $index => $name)
                    <tr>
                        <td>{{ $name }}</td>
                        <td>{{ $criteriaWeights[$index] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <h2>Alternatives</h2>
        <button id="add-alternative-btn" class="btn btn-primary">Add Alternative</button>
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
                                <label for="score_{{ $index }}">{{ $name }} Score</label>
                                <input type="number" class="form-control" id="score_{{ $index }}" name="scores[]" min="0" max="10" required>
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

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('add-alternative-btn').addEventListener('click', function() {
            $('#add-alternative-modal').modal('show');
        });
    </script>
</body>
</html>