<!-- resources/views/wsm/index.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WSM Page</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/wsmforms.css') }}">
</head>
<body>
@include('partials._navbar')
    <h1>Welcome to the WSM Page</h1>
    <div id="dynamic-content">
        @include('partials._numCriteria')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('criteria-form').addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent default submission
                let criteria = document.getElementById('criteria').value;

                // Validate the criteria input
                if (!criteria || isNaN(criteria) || criteria <= 1) {
                    alert('Please enter a valid number of criteria.');
                    return;
                }

                // Show loading feedback
                document.getElementById('dynamic-content').innerHTML = '<p>Loading...</p>';

                // Send the request
                fetch('{{ route("criteria.submit") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // CSRF token
                    },
                    body: JSON.stringify({ criteria: criteria })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.html) {
                        document.getElementById('dynamic-content').innerHTML = data.html; // Update content

/***********************  criteria names, weights, and intervals  *********************/

document.getElementById('criteria-names-weights-form').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default submission

    let criteriaNames = [];
    let criteriaWeights = [];
    let intervals = [];
    let isValid = true;

    // Collect and validate criteria names
    document.querySelectorAll('input[name="criteria_names[]"]').forEach(function(input) {
        let name = input.value.trim();
        if (!name) {
            alert('Criterion names cannot be empty.');
            isValid = false;
            return;
        }
        if (criteriaNames.includes(name)) {
            alert('Criterion names must not be repeated.');
            isValid = false;
            return;
        }
        criteriaNames.push(name);
    });

    if (!isValid) return;

    // Collect and validate criteria weights
    document.querySelectorAll('input[name="criteria_weights[]"]').forEach(function(input) {
        let weight = parseFloat(input.value);
        if (isNaN(weight) || weight < 0.01 || weight > 1) {
            alert('Criterion weights must be between 0.01 and 1.');
            isValid = false;
            return;
        }
        criteriaWeights.push(weight);
    });

    if (!isValid) return;

    // Validate the sum of weights
    let totalWeight = criteriaWeights.reduce((sum, weight) => sum + weight, 0);
    if (totalWeight !== 1) {
        alert('The sum of all weights must be equal to 1.');
        return;
    }

    // Collect intervals
    document.querySelectorAll('[id^="intervals_"]').forEach(function(intervalGroup) {
        let intervalArray = [];
        let min = null;
        intervalGroup.querySelectorAll('input').forEach(function(input, index) {
            let value = parseFloat(input.value);
            if (isNaN(value)) {
                alert('Interval values must be numeric.');
                isValid = false;
                return;
            }
            if (index % 2 === 0) {
                // Min value
                min = value;
            } else {
                // Max value
                intervalArray.push({ min: min, max: value });
                min = value; // Set the next min to the current max
            }
        });
        intervals.push(intervalArray);
    });

    if (!isValid) return;

    // If all validations pass, send AJAX request
    fetch('{{ route("criteria.storeNamesWeights") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}' // CSRF token
        },
        body: JSON.stringify({
            criteria_names: criteriaNames,
            criteria_weights: criteriaWeights,
            intervals: intervals
        })
    })
    .then(response => {
        if (response.redirected) {
            window.location.href = response.url;
        } else {
            return response.text();
        }
    })
    .then(html => {
        if (html) {
            document.open();
            document.write(html);
            document.close();
        }
    })
    .catch(error => {
        alert('An error occurred. Please try again.');
    });
});

/*************************  criteria names, weights, and intervals  ***********************/





                    } else {
                        document.getElementById('dynamic-content').innerHTML = '<p>No content available.</p>'; // Fallback
                    }
                })
                .catch(error => {
                    document.getElementById('dynamic-content').innerHTML = '<p>An error occurred. Please try again later.</p>';
                });
            });
        });
    </script>
</body>
</html>