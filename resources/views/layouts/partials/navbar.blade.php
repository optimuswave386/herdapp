
<div>
<x-application-logo class="w-9 h-9 text-gray-500" />
</div>

    <nav>
    <ul>
        <li><a href="/login">Login</a></li>
        <li><hr /></li>
        <li>New User? <a href="/register">Register</a></li>
    </ul>
    <br />
    <ul>
        <li class="nav-item @if(Request::is('/')) active @endif">
            <a href="{{ url('/') }}" class="nav-link">Welcome</a>
        </li>
        <li><a href="/about">About</a></li>
        <li><a href="/settings">Settings</a></li>
    </ul>
    <br />
    </nav>