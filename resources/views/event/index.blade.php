@extends('layouts.main')

@section('content')
    @if($agent->isMobile())
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h1 style="text-align: center">Events</h1>
                    @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 13)
                    <div class="row">
                        <div class="col-xs-4 col-xs-offset-4">
                            <a class="btn btn-success btn-sm" href="{{ url('event/create') }}">Create Event</a>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="panel-body">
                    @if(count($events) >= 1)
                        @foreach($events as $event)
                        @if( 
                            !isset($week) ||
                            date('W',strtotime($event->event_date)) != $week
                            )
                            <br>
                            <br>
                            <h3>{{ isset($week) ? 'Next Week' : 'This Week' }}</h3>
                            <br>
                            <br>
                            <br>
                            @php $week = date('W', strtotime($event->event_date)) @endphp
                        @endif
                        @if(Auth::user()->role_id == 1 && Auth::user()->role_id == 13)
                            <div class="panel {{ $event->type == 'pre-booking event' ? 'panel-info':'panel-primary' }}">
                        @else
                            @if($event->type == "pre-booking event")
                            <div class="panel panel-info">
                            @elseif(isset($user) && $user->assignments->where('event_id', $event->id)->where('user_id', $user->id)->first()->status == 'confirmed')
                            <div class="panel panel-success">
                            @else
                            <div class="panel panel-primary">
                            @endif
                        @endif
                            <div class="panel-heading">
                                <h2 class="panel-title">
                                    {{ $event->client->name }}
                                </h2>
                                <h3 class="panel-title">    
                                    {{ date_format(date_create($event->event_date), 'l F d, Y') }}
                                </h3>
                            </div>
                            <div class="panel-body">
                                <p>
                                    <span class="label label-default">
                                        @if(isset($user))
                                            <td>Starts at {{ $assignments->where('event_id', $event->id)->first()->time }}</td>
                                        @else
                                            @if(count($event->start_time)>0)
                                                <td>Starts at {{ $event->start_time[0] }}</td>
                                            @else
                                                <td>Unknown</td>
                                            @endif
                                        @endif
                                    </span>

                                    <span>
                                        @include('event.staff-counter', ['event' => $event])
                                    </span>
                                

                                    <br>
                                    <br>
                            
                                    @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 13)

                                        @include('event.notifications-status', ['event' => $event])
    
                                    @endif
                                </p>
                            </div>
                            <div class="panel-footer">
                                @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 13)
                                    @if($event->bar != 0 && $event->barEvent !== null)
                                    <p style="text-align: center">
                                        @if($event->barEvent->status == 1)
                                            <a href="{{ url('bar-event/create/'.$event->barEvent->id) }}" class="btn btn-warning btn-lg">pending</a>
                                        @elseif($event->barEvent->status == 2)
                                            <a href="{{ url('bar-event/create/'.$event->barEvent->id) }}" class="btn btn-success btn-lg">confirmed</a>
                                        @else
                                            <a href="{{ url('bar-event/create/'.$event->barEvent->id) }}" class="btn btn-default btn-lg">new bar function</a>

                                        @endif
                                    </p>
                                    <br>
                                    @endif 
                                    <a href="/event/create"></a>
                                    <a href="/event/{{ $event->id }}" class="btn btn-info" >More Info</a>
                                    <button class="btn btn-danger e_delete_btn pull-right" data-toggle="modal" data-target="#event_delete"><span class="glyphicon glyphicon-remove"></span></button>
                                    <div class="hidden event_id" >
                                        {{ $event->id }}
                                    </div>
                                    <div class="hidden client_name" >
                                        {{ $event->client->name}}
                                    </div>
                                @elseif(isset($user))
                                <p style="text-align: center">
                                    <a class="btn btn-info"  href="{{ url('event/'.$event->id)}}">More Info</a>
                                </p>
                                <p style="text-align: center">
                                    @if($user->assignments->where('event_id', $event->id)->where('user_id', $user->id)->first()->status == 'confirmed')
                                        <span class="label label-success">Confirmed</span>
                                    @else
                                        <a class="btn btn-warning btn-xs" href="{{ url('event/' . $event->id . '/confirm') }}">
                                            <span class="glyphicon glyphicon-check" aria-hidden="true"></span>
                                            Confirm
                                        </a>
                                    @endif
                                </p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p>No Event</p>
                    @endif
                </div>
            </div>
        </div>

    <!-- Desktop version -->
    @else
        <?php $pastEvents = str_contains(Route::getCurrentRoute()->getPath(), 'past/events'); ?>

        <div id="users_container" class="col-sm-8 col-sm-offset-2">
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
                                @if (!$pastEvents)
                                    <th>Start time</th>
                                @endif
                                @if(Auth::user()->role_id == 1 ||Auth::user()->role_id ==13) 
                                    <th>Staff</th>                           
                                    @if($pastEvents)
                                        <th>Feedback</th>
                                    @else
                                        <th>Client notification</th>
                                    @endif
                                    <th>Bar</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                @else
                                    <th>Team</th>    
                                @endif
                                <th></th>
                            </thead>

                            <tbody>
                            @foreach($events as $event)
                                @if( 
                                    !isset($date) || 
                                    date('Y-m-d',strtotime($event->event_date)) != date('Y-m-d',strtotime($date))
                                    )
                                    <tr></tr>
                                    <tr style="background-color: lightsteelblue">
                                        <td colspan="9">
                                            {{ date('l F d, Y', strtotime($event->event_date)) }}
                                        </td>

                                    </tr>
                                    @php $date = $event->event_date @endphp
                                @endif
                                <tr>
                                    <td> <a href="{{ url('event/' . $event->id) }}">{{ $event->client->name }}</a></td>
                                    @if (!$pastEvents)
                                        @if(isset($user))
                                            <td>{{ $assignments->where('event_id', $event->id)->first()->time }}</td>
                                        @else
                                            @if(count($event->start_time)>0)
                                                <td>{{ $event->start_time[0] }}</td>
                                            @else
                                                <td>Unknown</td>
                                            @endif
                                        @endif
                                    @endif
                                    
                                        <td>@include('event.staff-counter', ['event' => $event])</td>
                                        @if(Auth::user()->role_id != 1 && $event->event_type == "pre-booking event")
                                        <td><span class="label label-info">Pre Booking Event</span></td>
                                        @elseif(Auth::user()->role_id != 1 && $event->event_type != "pre-booking event")
                                        <td></td>
                                        @endif
                                    @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 13)
                                        @if ($pastEvents)
                                            <td>@include('event.feedback-status', ['event' => $event])</td>
                                        @elseif($event->event_type == "pre-booking event")
                                            <td><span class="label label-info">Pre Booking Event</span></td>
                                        @else
                                            <td>@include('event.notifications-status', ['event' => $event])</td>
                                        @endif

                                        @if($event->bar != 0 && $event->barEvent !== null)
                                        <td>
                                            @if($event->barEvent->status == 1)
                                                <a href="{{ url('bar-event/create/'.$event->barEvent->id) }}" class="btn btn-warning btn-xs">pending</a>
                                            @elseif($event->barEvent->status == 2)
                                                <a href="{{ url('bar-event/create/'.$event->barEvent->id) }}" class="btn btn-success btn-xs">confirmed</a>
                                            @else
                                                <a href="{{ url('bar-event/create/'.$event->barEvent->id) }}" class="btn btn-default btn-xs">new function</a>
                                            @endif
                                        </td>
                                        @else
                                        <td></td>
                                        @endif     

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
                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#preBookingModal">Create Prebooking</button>
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

                    <!-- PreBooking Modal -->
                    <div id="preBookingModal" class="modal fade" role="dialog">
                      <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                          <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Prebooking Event</h4>
                          </div>
                          <div class="modal-body">
                            <div class="container-fluid">
                                <form action="{{url('/preBooking')}}" method="post" id="preBookForm">
                                    <div class="col-xs-4 col-xs-offset-4">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <label>Event Date</label>
                                        <div class="input-group date" id="event_date">
                                            <input type='text' class="form-control" name="event_date"/>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                        <div class="form-group">
                                            <label for="staff-needed">Staff Needed</label>
                                            <input id="staff-needed" type="number" name="staffNeeded" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="start_time">Start Time</label>
                                            <div class="input-group date" id="time">
                                                <input type="text" class="form-control" name="start_time">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-time"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="finish_time">Finish Time</label>
                                            <div class="input-group date" id="time">
                                                <input type="text" class="form-control" name="finish_time">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-time"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="submit" form="preBookForm" class="btn btn-success">Pre Book an Event</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                          </div>
                        </div>

                      </div>
                    </div>
                </div>
            </div>
        </div>
        @if(Auth::user()->role_id == 1 && isset($events_of_the_day))
        <div class="col-sm-2" style="position: fixed; right: 0;">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Busy Staff - {{ date('d/m/Y', strtotime('+11 hour')) }} </h3>
                </div>
                <div class="panel-body" style="overflow-y: scroll; height: 90vh;">
                    <ul class="list-group">
                        @if(count($events_of_the_day) >= 1)
                            @foreach($events_of_the_day as $event)
                                <p>
                                    <span style="text-decoration: underline;">
                                        <a href="/event/{{ $event->id }}"> {{ $event->client->name }}</a>
                                    </span>
                                    <br>
                                    ({{ $event->start_time[0] }} to {{ $event->finish_time }})
                                </p>
                                @if(count($event->assignments) >= 1)
                                    @foreach($event->assignments as $assignment)
                                        <a href="/event/{{ $event->id }}">
                                            <li class="list-group-item">    
                                                {{ $assignment->user->profile->first_name }}
                                                {{ $assignment->user->profile->last_name }}
                                                - 
                                                {{ $assignment->time }}
                                                
                                                @if(date('H:m', strtotime('+14 hour')) <= $assignment->time)
                                                    <span style="position:absolute; right: 10px;background-color: green;border: 1px solid black; padding: 3px; border-radius: 100px; width: 3px;height:3px; margin-top: 5px;"></span>
                                                @else
                                                    <span style="position:absolute; right: 10px;background-color: red;border: 1px solid black; padding: 3px; border-radius: 100px; width: 3px;height:3px; margin-top: 5px;"></span>
                                                @endif
                                            </li>
                                        </a>
                                    @endforeach
                                @else
                                    <p>
                                        No staff for <em>{{ $event->client->name }}</em> today's event. 
                                    </p>
                                    @foreach($event->start_time as $start)
                                        <a href="/assignment/add/{{ $event->id }}/{{ $start }}" class="btn btn-primary">Assign for {{ $start }}</a>
                                    @endforeach
                                @endif
                                <hr>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        @endif
            <!-- Calendar -->
            @if (!$pastEvents)
                @include('event.calendar')
            @endif
    @endif
@stop
