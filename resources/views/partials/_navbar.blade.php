<!-- resources/views/partials/_navbar.blade.php -->

<style>
    .navbar {
        background-color: #333;
        overflow: hidden;
    }

    .navbar a {
        float: right;
        display: block;
        color: white;
        text-align: center;
        padding: 14px 20px;
        text-decoration: none;
    }

    .navbar a:hover {
        background-color: #ddd;
        color: black;
    }
</style>

<div class="navbar">
    <a href="{{ route('admin.dashboard') }}">Home</a>
    <a href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
</div>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
    @csrf
</form>