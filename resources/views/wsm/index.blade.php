<!-- resources/views/swm/index.blade.php -->

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
                if (!criteria || isNaN(criteria) || criteria <= 0) {
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

                        // Add event listener for the new form
                        document.getElementById('criteria-names-weights-form').addEventListener('submit', function(event) {
                            event.preventDefault(); // Prevent default submission

                            let formData = new FormData(this);
                            let criteriaNames = [];
                            let criteriaWeights = [];
                            formData.forEach((value, key) => {
                                if (key.startsWith('criteria_names')) {
                                    criteriaNames.push(value);
                                } else if (key.startsWith('criteria_weights')) {
                                    criteriaWeights.push(value);
                                }
                            });

                            // Show loading feedback
                            document.getElementById('dynamic-content').innerHTML = '<p>Loading...</p>';

                            // Send the request
                            fetch('{{ route("criteria.storeNamesWeights") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // CSRF token
                                },
                                body: JSON.stringify({ criteria_names: criteriaNames, criteria_weights: criteriaWeights })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.html) {
                                    document.getElementById('dynamic-content').innerHTML = data.html; // Update content
                                } else {
                                    document.getElementById('dynamic-content').innerHTML = '<p>No content available.</p>'; // Fallback
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                document.getElementById('dynamic-content').innerHTML = '<p>An error occurred. Please try again later.</p>';
                            });
                        });
                    } else {
                        document.getElementById('dynamic-content').innerHTML = '<p>No content available.</p>'; // Fallback
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('dynamic-content').innerHTML = '<p>An error occurred. Please try again later.</p>';
                });
            });
        });
    </script>
</body>
</html>