
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WSM Page</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/wsmforms.css') }}">

</head>
<body>
@include('partials._navbar')
    <h1>Welcome to the WSM Page</h1>

    <div class="container">
        <h2>Select a Problem or Create a New One</h2>
        <div class="problem-list">
            <h3>Choose a Problem</h3>
            @include('partials._chooseProblem')
        </div>
        <div class="create-problem">
            <h3>Create a New Problem</h3>

            @include('partials._problemNameNumCriteria')

        </div>
    </div>

    <script>

        document.getElementById('list-problems-form').addEventListener('submit', function(event) {
            event.preventDefault();
            var problem = document.getElementById('problem').value;
            window.location.href = '/problems/' + problem;
        });

        document.getElementById('problem-form').addEventListener('submit', function(event) {
            event.preventDefault();
            var problemName = document.getElementById('problem-name').value;
            var criteria = document.getElementById('criteria').value;

            fetch('/problems/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    problem_name: problemName,
                    criteria: criteria
                })
            })
            .then(response => response.text())
            .then(data => {
                document.querySelector('.create-problem').innerHTML = data;
            })
            .catch(error => console.error('Error:', error));
        });

</body>
</html>