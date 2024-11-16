<!-- resources/views/swm/index.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SWM Page</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">

</head>
<body>
@include('partials._navbar')
    <h1>Welcome to the SWM Page</h1>
    <div id="dynamic-content">
        @include('partials._numCriteria')
    </div>

    <script>/*
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
                fetch('#', {
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
                    } else {
                        document.getElementById('dynamic-content').innerHTML = '<p>No content available.</p>'; // Fallback
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('dynamic-content').innerHTML = '<p>An error occurred. Please try again later.</p>';
                });
            });
        });*/
    </script>
</body>
</html>