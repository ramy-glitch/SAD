<!-- resources/views/partials/criteria_names_weights_form.blade.php -->

<form id="criteria-names-weights-form" >
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
            <label for="intervals_{{ $i }}">Enter intervals for criterion {{ $i }} (comma-separated, e.g., 50-100,100-170,...):</label>
            <input type="text" id="intervals_{{ $i }}" name="intervals[]" required>
        </div>
    @endfor
    <button type="submit">Submit</button>
</form>