<div class="row">
<aside class="col-md-4 col-lg-3">
    <ul class="nav nav-dashboard flex-column mb-3 mb-md-0" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('user.index') ? 'active' : '' }}"
               href="{{ route('user.index') }}">
                Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('user.orders') ? 'active' : '' }}"
               href="{{ route('user.orders') }}">
                Orders
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('wishlist.index') ? 'active' : '' }}"
               href="{{ route('wishlist.index') }}">
                Account Details
            </a>
        </li>

        <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf
                <a href="{{ route('logout') }}"
                   class="nav-link"
                   onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    Logout
                </a>
            </form>
        </li>
    </ul>
</aside>
</div>
