<!-- resources/views/partials/criteria_tables.blade.php -->

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<div class="container mt-5">
    <!-- First Table: Weights of Each Criterion -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    @foreach ($criteriaNames as $name)
                        <th>{{ $name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach ($criteriaWeights as $weight)
                        <td>{{ $weight }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Second Table: Alternatives and Scores -->
    <div class="table-responsive mt-5">
        <table class="table table-bordered" id="alternatives-table">
            <thead>
                <tr>
                    <th>Alternative</th>
                    @foreach ($criteriaNames as $name)
                        <th>{{ $name }}</th>
                    @endforeach
                    <th>WSM</th>
                </tr>
            </thead>
            <tbody>
                <!-- Example row, you can dynamically add rows using JavaScript -->
                <tr>
                    <td><input type="text" class="form-control" name="alternatives[]"></td>
                    @foreach ($criteriaNames as $name)
                        <td><input type="number" class="form-control" name="scores[{{ $name }}][]"></td>
                    @endforeach
                    <td><input type="number" class="form-control" name="wsm[]" readonly></td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-primary" id="add-alternative">Add Alternative</button>
    </div>
</div>

<!-- Modal for Adding Alternative -->
<div class="modal fade" id="addAlternativeModal" tabindex="-1" aria-labelledby="addAlternativeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAlternativeModalLabel">Add Alternative</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-alternative-form">
                    <div class="form-group">
                        <label for="alternative-name">Alternative Name</label>
                        <input type="text" class="form-control" id="alternative-name" name="alternative_name" required>
                    </div>
                    @foreach ($criteriaNames as $name)
                        <div class="form-group">
                            <label for="score-{{ $name }}">Score for {{ $name }}</label>
                            <input type="number" class="form-control" id="score-{{ $name }}" name="scores[{{ $name }}]" required>
                        </div>
                    @endforeach
                    <button type="submit" class="btn btn-primary">Add</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('add-alternative').addEventListener('click', function() {
            $('#addAlternativeModal').modal('show');
        });

        document.getElementById('add-alternative-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission

            let alternativeName = document.getElementById('alternative-name').value;
            let scores = {};
            @foreach ($criteriaNames as $name)
                scores['{{ $name }}'] = document.getElementById('score-{{ $name }}').value;
            @endforeach

            let table = document.getElementById('alternatives-table').getElementsByTagName('tbody')[0];
            let newRow = table.insertRow();
            let cell1 = newRow.insertCell(0);
            cell1.innerHTML = '<input type="text" class="form-control" name="alternatives[]" value="' + alternativeName + '">';
            @foreach ($criteriaNames as $name)
                let cell = newRow.insertCell();
                cell.innerHTML = '<input type="number" class="form-control" name="scores[{{ $name }}][]" value="' + scores['{{ $name }}'] + '">';
            @endforeach
            let cellLast = newRow.insertCell();
            cellLast.innerHTML = '<input type="number" class="form-control" name="wsm[]" readonly>';

            $('#addAlternativeModal').modal('hide'); // Hide the modal
        });
    });
</script>