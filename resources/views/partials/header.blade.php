@if(Auth::check())
<header>
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#menu" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a style="padding-top: 5px" class="navbar-brand" href="#">
                    <img style="width: 42px;" alt="Brand" src="{{ asset('img/logo_small.png') }}">
                </a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="menu">
                <ul class="nav navbar-nav navbar-right">

                    @if(Auth::user()->role_id == 1)
                        <li><a href="{{ url('events/' . Auth::user()->id) }}">Home</a></li>
                        <li><a href="{{ url('availability') }}">Availability</a></li>
                        <li><a href="{{ url('user') }}">Users</a></li>
                        <li><a href="{{ url('event') }}">Events</a></li>
                        <li><a href="{{ url('past/events/') }}">Past Events</a></li>
                        <li><a href="{{ url('client') }}">Clients</a></li>
                        <li><a href="{{ url('user/' . Auth::user()->id . '/password') }}">Password</a></li>
                        <li><a href="{{ url('logout') }}">Logout</a></li>
                    @else
                        <li><a href="{{ url('timesheets/') }}">Timesheets</a></li>
                        <li><a href="{{ url('events/' . Auth::user()->id) }}">Events</a></li>
                        <li><a href="{{ url('availability') }}">Availability</a></li>
                        <li><a href="{{ url('user/' . Auth::user()->id . '/password') }}">Password</a></li>
                        <li><a href="{{ url('logout') }}">Logout</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
</header>
@endif