@extends('layouts.main')

@section('content')

    <?php $pastEvents = str_contains(Route::getCurrentRoute()->getPath(), 'past/events'); ?>

    <div id="users_container" class="container">
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
                            <tr>
                                <td> <a href="{{ url('event/' . $event->id) }}">{{ $event->client->name }}</a></td>
                                <td>{{ date_format(date_create($event->event_date), 'l F d, Y') }}</td>
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
    <!-- Calendar -->
    @if (!$pastEvents)
        @include('event.calendar')
    @endif
@stop
