<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>
    <div class="navbar">
        <a href="{{ route('admin.dashboard') }}">Home</a>
        <a href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>


    <div class="grid-container">
        <div class="grid-item">
            <h2>Block 1</h2>
            <img src="path/to/image1.jpg" alt="Image 1">
            <p><a href="#">Go to Section 1</a></p>
        </div>
        <div class="grid-item">
            <h2>Block 2</h2>
            <img src="path/to/image2.jpg" alt="Image 2">
            <p><a href="#">Go to Section 2</a></p>
        </div>
        <!-- Add more blocks as needed -->
    </div>
</body>
</html>