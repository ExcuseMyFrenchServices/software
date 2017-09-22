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

        @if($agent->isMobile())
            <div class="collapse navbar-collapse" id="menu">
                @if(Auth::user()->role_id == 1)
                    <div class="row">
                        <div class="col-xs-4 col-xs-offset-4">
                            <form action="/event/changeUserRole" method="post">
                                {{ csrf_field() }}   
                                <button type="submit" class="btn btn-success btn-block" style="margin-top: 8px"><span class="glyphicon glyphicon-eye-close"></span></button>
                            </form>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-6">
                            <a class="btn btn-info btn-block" href="{{ url('event') }}">Events</a>
                        </div>
                        <div class="col-xs-6">
                            <a class="btn btn-warning btn-block" href="{{ url('past/events/') }}">Past Events</a>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-6 col-xs-offset-3">
                            <a class="btn btn-primary btn-block" href="{{ url('reports')}}">Reports</a>
                        </div>
                    </div>
                    <br>
                    <hr>
                    <div class="row">
                        <div class="col-xs-6">
                            <a class="btn btn-default btn-block" href="{{ url('stocks') }}">Stocks</a>
                        </div>
                        <div class="col-xs-6">
                            <a class="btn btn-default btn-block" href="{{ url('uniforms') }}">Uniforms</a>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-6">
                            <a class="btn btn-default btn-block" href="{{ url('client') }}">Clients</a>
                        </div>
                        <div class="col-xs-6">
                            <a class="btn btn-default btn-block" href="{{ url('user') }}">Users</a>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-6">
                            <a class="btn btn-default btn-block" href="{{ url('archive')}}">Archived Users</a>
                        </div>
                        <div class="col-xs-6">
                            <a class="btn btn-default btn-block" href="{{ url('public-holidays') }}">Public Holidays</a>
                        </div>
                    </div>
                @else
                    @if(Auth::user()->role_id == 11)
                    <div class="col-xs-4 col-xs-offset-4">
                        <form action="/event/changeUserRole" method="post">
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-info" style="margin-top: 8px"><span class="glyphicon glyphicon-eye-open"></span></button>
                        </form>
                    </div>
                    <br>
                    @endif
                    <br>
                    <div class="row" style='text-align: center'>
                        <div class="col-xs-6 col-xs-offset-3">
                            <a class="btn btn-primary btn-block" href="{{ url('timesheets/') }}"><span class="glyphicon glyphicon-time"> </span> Timesheets</a>
                        </div>
                    </div>
                    <br>
                    <div class="row" style='text-align: center'>
                        <div class="col-xs-6 col-xs-offset-3">
                            <a class="btn btn-success bnt-block" href="{{ url('availability') }}"><span class="glyphicon glyphicon-calendar"> </span> Availability</a>
                        </div>
                    </div>
                    <br>
                    <div class="row" style='text-align: center'>
                        <div class="col-xs-6 col-xs-offset-3">
                            <a class="btn btn-info bnt-block" href="{{ url('user/' . Auth::user()->id . '/edit') }}"><span class="glyphicon glyphicon-user"> </span> Profile</a>
                        </div>
                    </div>
                @endif
                <br>
                <hr>
                <div class="row">
                    <div class="col-xs-6">
                        <a class="btn btn-info btn-block" href="{{ url('user/' . Auth::user()->id . '/password') }}"><span class="glyphicon glyphicon-console"> </span> Password</a>
                    </div>
                    <div class="col-xs-6">
                        <a class="btn btn-danger btn-block" href="{{ url('logout') }}"><span class="glyphicon glyphicon-off"> </span> Logout</a>
                    </div>
                </div>
            </div> 
        @else
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="menu">
                <ul class="nav navbar-nav navbar-right">

                    @if(Auth::user()->role_id == 1)
                        <li>
                            <form action="/event/changeUserRole" method="post">
                            {{ csrf_field() }}   
                                <button type="submit" class="btn btn-success" style="margin-top: 8px"><span class="glyphicon glyphicon-eye-close"></span></button>
                            </form>
                        </li>
                        <li><a href="{{ url('event') }}">Home</a></li>
                        <li><a href="{{ url('event') }}">Events</a></li>
                        <li><a href="{{ url('past/events/') }}">Past Events</a></li>
                        <li>
                            <a type="button" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Manage <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ url('stocks') }}">Stocks</a></li>
                                <li><a href="{{ url('uniforms') }}">Uniforms</a></li>
                                <li><a href="{{ url('client') }}">Clients</a></li>

                                <li role="separator" class="divider"></li>

                                <li><a href="{{ url('user') }}">Users</a></li>
                                <li><a href="{{ url('archive')}}">Archived Users</a></li>

                                <li role="separator" class="divider"></li>

                                <li><a href="{{ url('public-holidays') }}">Public Holidays</a></li>
                            </ul>
                        </li>
                        <li><a href="{{ url('reports')}}">Reports</a></li>
                        <li><a href="{{ url('user/' . Auth::user()->id . '/password') }}">Password</a></li>
                        <li><a href="{{ url('logout') }}">Logout</a></li>
                    @else
                        @if(Auth::user()->role_id == 11)
                        <li>
                            <form action="/event/changeUserRole" method="post">
                            {{ csrf_field() }}
                                <button type="submit" class="btn btn-info" style="margin-top: 8px"><span class="glyphicon glyphicon-eye-open"></span></button>
                            </form>
                        </li>
                        @endif
                        <li><a href="{{ url('timesheets/') }}">Timesheets</a></li>
                        <li><a href="{{ url('events/' . Auth::user()->id) }}">Events</a></li>
                        <li><a href="{{ url('availability') }}">Availability</a></li>
                        <li><a href="{{ url('user/' . Auth::user()->id . '/edit') }}">Profile</a></li>
                        <li><a href="{{ url('user/' . Auth::user()->id . '/password') }}">Password</a></li>
                        <li><a href="{{ url('logout') }}">Logout</a></li>
                    @endif
                </ul>
            </div>
        @endif
        </div>
    </nav>
</header>
@endif