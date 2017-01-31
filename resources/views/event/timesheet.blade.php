@extends('layouts.main')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-8 col-md-offset-2">
                <div style="margin-top: 70px;" class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Timesheet - {{date_format(date_create($event->event_date), 'l jS F')}}</h3>
                    </div>
                    <div class="panel-body">
                        <form id='confirm_start_time' action="{{ url('event/'.$event->id.'/start-time-confirm') }}" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <label>Please confirm start time of event for :</label>
                            <ul class="list-group">
                            @foreach($event->assignments as $assignment)
                                @if(!$assignment->start_time_confirmation)
                                    <li class="list-group-item">    
                                        {{ $assignment->user->profile->first_name . ' ' . $assignment->user->profile->last_name }}
                                        <input type="text" name="confirmed_start_time" value="{{$assignment->time}}" class="form-control">
                                        <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">
                                        <button type="submit" class="btn btn-info btn-sm">Confirm</button>
                                    </li>
                                @endif
                            @endforeach
                                <button type="submit" name="confirm-all" value="confirm-all" class="btn btn-primary">Confirm All</button>
                            </ul>
                        </form>
                        <form id="staff_timesheet" action="{{ url('event/' . $event->id . '/timesheet') }}" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <ul class="list-group">
                                <?php $i=0; ?>
                                @foreach($event->assignments as $assignment)
                                    <li class="list-group-item">
                                        <div class="form-inline">
                                            <div class="col-sm-4 col-xs-12">
                                                {{ $assignment->user->profile->first_name . ' ' . $assignment->user->profile->last_name }}
                                            </div>
                                            <div class="form-group">
                                                <input type="hidden" name="id-{{$i}}" value="{{ $assignment->id }}">
                                                @if($assignment->start_time_confirmation)
                                                <div class="col-sm-4">
                                                    <label>Confirmed Start Time</label>
                                                    <p>{{$assignment->time}}</p>
                                                </div>
                                                @endif
                                                <div class="col-sm-4">
                                                    <label>Finish time</label>
                                                    <input type="text" name="{{ $assignment->id }}-hours" class="form-control" value="{{ $assignment->hours }}">
                                                </div>
                                                <div class="col-sm-4">
                                                    <label>Break</label>
                                                    <input type="text" name="{{$assignment->id}}-break" class=" form-control" value="{{ $assignment->break }}">
                                                </div>
                                                <?php $i++; ?>
                                                <input type="hidden" name="assignment_number" value="{{$i}}">
                                            </div>
                                        </div>
                                    </li>
                                @endforeach

                                    <li class="list-group-item">
                                        <div class="form-group">
                                            <label for="report">Report</label>
                                            <textarea name="report" class="form-control">
                                            {{$event->report}}
                                            </textarea>
                                        </div>
                                    </li>
                            </ul>
                            <button class="btn btn-primary btn-sm" type="submit">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
