<!-- resources/views/wsm/index.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WSM Page</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/wsmforms.css') }}">
    <script>
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}',
            routes: {
                criteriaSubmit: '{{ route("criteria.submit") }}',
                storeCriteriaNamesWeights: '{{ route("store.criteria.names.weights") }}'
            }
        };
    </script>
</head>
<body>
@include('partials._navbar')
    <h1>Welcome to the WSM Page</h1>
    <div id="dynamic-content">
        @include('partials._numCriteria')
    </div>

    <script src="{{ asset('js/validation.js') }}"></script>
    <script src="{{ asset('js/api.js') }}"></script>
    <script src="{{ asset('js/eventHandlers.js') }}"></script>
</body>
</html>