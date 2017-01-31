@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-8 col-md-offset-2">
                <div class="panel panel-default event_detail">
                    <div class="panel-heading">
                        @if(isset($event))
                            <h3 class="panel-title">{{date_format(date_create($event->event_date), 'l jS F')}}</h3>
                        @else
                            <h3 class="panel-title">Event Detail</h3>
                        @endif
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <p><b>Client:</b> {{ $event->client->name }}</p>
                                <br>

                                @if(!empty($event->address))
                                    <p><b>Address:</b> <a target="_blank" href="https://www.google.com.au/maps/search/{{ urlencode($event->address) }}">{{ $event->address }}</a></p>
                                    <br>
                                @endif

                                @if(!empty($event->uniform))
                                    <p><b>Uniform:</b> {{ $event->uniform }}</p>
                                    <br>
                                @endif

                                @if(!empty($event->glasses) || !empty($event->soft_drinks) || !empty($event->bar))
                                    <p>
                                        <b>Extras:</b>
                                        <ul>
                                            @if(!empty($event->glasses))
                                                <li>Glasses</li>
                                            @endif
                                            @if(!empty($event->soft_drinks))
                                                <li>Soft drinks</li>
                                            @endif
                                            @if(!empty($event->bar))
                                                <li>Bar</li>
                                            @endif
                                        </ul>
                                    </p>
                                    <br>
                                @endif

                                @if(!empty($event->notes))
                                    <p><b>Notes:</b> {{ $event->notes }}</p>
                                    <br>
                                @endif

                                @foreach($event->start_time as $time)
                                    <p><b>Team starting at {{ $time }}</b></p>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            @if(!$event->assignments->where('time', $time)->isEmpty())
                                                <ul class="list-group">
                                                @foreach($event->assignments->where('time', $time) as $assignment)
                                                    @include('event.staff-row', ['$assignment' => $assignment])
                                                @endforeach
                                                @if(Auth::user()->role_id == 1)
                                                    <li class="list-group-item">
                                                        <b>Admin Report</b>:<br>
                                                        {{ $assignment->event->report }}
                                                    </li>
                                                @endif
                                                </ul>
                                            @endif

                                            @if(Auth::user()->role_id == 1)
                                                @if($assignment->event->event_date >= date('Y-m-d'))
                                                    <a href="/assignment/add/{{ $event->id .'/'.$time }}" class="btn btn-primary btn-xs">
                                                        <span class="glyphicon glyphicon-plus"></span> Add staff
                                                    </a>
                                                    <br>
                                                    <br>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                @endforeach

                                @if(!empty($event->finish_time))
                                    <p><b>Approximate finish time:</b> {{ $event->finish_time }}</p>
                                    <br>
                                @endif

                                @if ($event->event_date >= date('Y-m-d'))
                                    <a class="btn btn-info btn-sm back_btn" href="{{ url('events/' . Auth::user()->id) }}">Back</a>
                                @else
                                    <a class="btn btn-info btn-sm back_btn" href="{{ url('past/events/') }}">Back</a>
                                @endif

                                @if(!$event->assignments->where('user_id', Auth::user()->id)->where('status', 'pending')->isEmpty())
                                    <a class="btn btn-success btn-sm back_btn" href="{{ url('event/' . $event->id . '/confirm') }}">
                                        <span class="glyphicon glyphicon-check" aria-hidden="true"></span>
                                        Confirm
                                    </a>
                                @endif

                                @include('event.actions', ['event' => $event])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop