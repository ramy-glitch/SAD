<!-- resources/views/partials/criteria_names_weights_form.blade.php -->

<form id="criteria-names-weights-form">
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
            <label>Enter intervals for criterion {{ $i }}:</label>
            <div id="intervals_{{ $i }}">
                <div class="interval-group">
                    <input type="text" name="intervals[{{ $i }}][]" placeholder="Min" Value="10" required>
                    <input type="text" name="intervals[{{ $i }}][]" placeholder="Max" Value="20" required>
                </div>
                @for ($j = 2; $j <= 9; $j++)
                    <div class="interval-group">
                        <input type="text" name="intervals[{{ $i }}][]" placeholder="Max" Value="{{$j}}00" required>
                    </div>
                @endfor
            </div>
        </div>
    @endfor
    <button type="submit">Submit</button>
</form>