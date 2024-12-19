<!-- resources/views/partials/criteria_names_weights_form.blade.php -->

<form id="criteria-names-weights-form" method="POST" action="{{ route('store.criteria.names.weights') }}">
    @csrf
    @for ($i = 1; $i <= $criteria; $i++)
        <div class="form-group">
            <label for="criteria_name_{{ $i }}">Enter name for criterion {{ $i }}:</label>
            <input type="text" id="criteria_name_{{ $i }}" name="criteria_names[]" required>
        </div>
        <div class="form-group">
            <label for="criteria_weight_{{ $i }}">Enter weight for criterion {{ $i }}:</label>
            <input type="text" id="criteria_weight_{{ $i }}" name="criteria_weights[]" required>
        </div>
        <div class="form-group">
            <label for="intervals_{{ $i }}">Enter intervals for criterion {{ $i }}:</label>
            <div id="intervals_{{ $i }}">
                @for ($j = 1; $j <= 8; $j++)
                    <div class="interval-group">
                        <input type="text" name="intervals[{{ $i }}][]" placeholder="Min" required>
                        <input type="text" name="intervals[{{ $i }}][]" placeholder="Max" required>
                    </div>
                @endfor
            </div>
        </div>
    @endfor
    <button type="submit">Submit</button>
</form>