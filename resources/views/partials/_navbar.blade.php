<!-- resources/views/partials/_navbar.blade.php -->

<div class="navbar">
    <a href="{{ route('admin.dashboard') }}">Home</a>
    <a href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
</div>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
    @csrf
</form>