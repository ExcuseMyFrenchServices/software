@extends('layouts.main')

@section('content')

    <?php $pastEvents = str_contains(Route::getCurrentRoute()->getPath(), 'past/events'); ?>

    <div id="users_container" class="container">
        @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 11)
        <div class="col-xs-4 col-xs-offset-8">
            <form action="/event/changeUserRole" method="post">
            {{ csrf_field() }}
                @if(Auth::user()->role_id == 1)    
                    <button type="submit" class="btn btn-warning"><span class="glyphicon glyphicon-eye-close"></span></button>Switch to basic User view 
                @else
                    <button type="submit" class="btn btn-info"><span class="glyphicon glyphicon-eye-open"></span></button> Switch to Admin view
                @endif
            </form>
        </div>
        @endif
        <div class="row">

            @if ($pastEvents)
                <div class="form-group col-xs-2 col-xs-offset-5" style="text-align: center;">
                    <form action="/past/events/" method="GET">
                        <div class="input-group year_month" id="date-range">
                            <input type='text' class="form-control" name="date-range" value="{{ $range }}"/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                        <br>
                        <button class="btn btn-primary btn-sm" type="submit">Search</button>
                    </form>
                </div>
            @else
                <div class="col-xs-6 col-xs-offset-3" style="text-align: center;">
                    <button type="button" class="btn btn-primary btn-lg users_btn" aria-label="Left Align">
                        <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span>
                    </button>
                    <button type="button" class="btn btn-primary btn-lg calendar_btn" aria-label="Left Align">
                        <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                    </button>
                </div>
            @endif

            <div class="col-xs-12 col-md-10 col-md-offset-1">
                @if(count($events) >= 1)
                    <table id="events_list" class="table table-striped">
                        <thead>
                            <th>Client Name</th>
                            <th>Date</th>
                            @if (!$pastEvents)
                                <th>Start time</th>
                            @endif
                            @if(Auth::user()->role_id == 1) 
                                <th>Staff</th>                           
                                @if($pastEvents)
                                    <th>Feedback</th>
                                @else
                                    <th>Client notification</th>
                                @endif
                                <th></th>
                                <th></th>
                            @else
                                <th>Team</th>    
                            @endif
                            <th></th>
                        </thead>

                        <tbody>
                        @foreach($events as $event)
                            <tr>
                                <td> <a href="{{ url('event/' . $event->id) }}">{{ $event->client->name }}</a></td>
                                <td>{{ date_format(date_create($event->event_date), 'F d, Y') }}</td>
                                @if (!$pastEvents)
                                    @if(isset($user))
                                        <td>{{ $assignments->where('event_id', $event->id)->first()->time }}</td>
                                    @else
                                        <td>{{ $event->start_time[0] }}</td>
                                    @endif
                                @endif
                                
                                    <td>@include('event.staff-counter', ['event' => $event])</td>
                                @if(Auth::user()->role_id == 1)
                                    @if ($pastEvents)
                                        <td>@include('event.feedback-status', ['event' => $event])</td>
                                    @else
                                        <td>@include('event.notifications-status', ['event' => $event])</td>
                                    @endif
                                    <td><a href="/event/{{ $event->id }}/edit" class="btn btn-info btn-xs" role="button">Update</a></td>
                                    <td><a href="/event/{{ $event->id }}/copy" class="btn btn-primary btn-xs" role="button">Copy</a></td>
                                    <td>
                                        <button class="btn btn-danger e_delete_btn btn-xs" data-toggle="modal" data-target="#event_delete">Delete</button>
                                        <div class="hidden event_id" >{{ $event->id }}</div>
                                        <div class="hidden client_name" >{{ $event->client->name}}</div>
                                    </td>
                                @elseif(isset($user))
                                    <td>
                                        @if($user->assignments->where('event_id', $event->id)->where('user_id', $user->id)->first()->status == 'confirmed')
                                            <span class="label label-success">Confirmed</span>
                                        @else
                                            <a class="btn btn-warning btn-xs" href="{{ url('event/' . $event->id . '/confirm') }}">
                                                <span class="glyphicon glyphicon-check" aria-hidden="true"></span>
                                                Confirm
                                            </a>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty_event">
                        <h3>No events</h3>
                    </div>
                @endif
                @if(Auth::user()->role_id == 1 && !$pastEvents)
                    <a class="btn btn-primary btn-sm" href="{{ url('event/create') }}">Create Event</a>
                @else
                    &nbsp;
                @endif
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <!-- Modal Delete Confirm -->
                <div id="event_delete" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="event_deleteLabel">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Delete Confirmation</h4>
                            </div>
                            <div class="modal-body row">
                                <div class="col-xs-12">
                                    <p id="e_confirm_message"></p>
                                </div>
                                <div class="col-xs-4 col-xs-offset-8">
                                    <form id="event_form_delete" action="/event/" method="POST">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-danger btn-sm" id="e_btn_confirm">Delete</button>
                                        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
                                    </form>
                                </div>
                            </div>

                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->
            </div>
        </div>
    </div>
    <!-- Calendar -->
    @if (!$pastEvents)
        @include('event.calendar')
    @endif
@stop
