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
                                                <div class="col-sm-4 container">
                                                    <label>Start Time</label>
                                                    <div class="col-xs-12">
                                                        <div class="form-group">
                                                            <div class="input-group date" id="time">
                                                                <input type="text" name="{{ $assignment->id }}-start-time" class="form-control" value="{{ empty($assignment->start_time) ? $assignment->time : $assignment->start_time }}">
                                                                <span class="input-group-addon">
                                                                    <span class="glyphicon glyphicon-time"></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 container">
                                                    <label>Break</label>
                                                    <div class="col-xs-12">
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <input type="text" name="{{$assignment->id}}-break" class="form-control" value="{{ $assignment->break }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4 container">
                                                    <label>Finish time</label>
                                                    <div class="col-xs-12">
                                                        <div class="form-group">
                                                            <div class="input-group date" id="time">
                                                                <input type="text" name="{{$assignment->id}}-hours" class="form-control" value="{{ $assignment->hours or $assignment->event->finish_time }}">
                                                                <span class="input-group-addon">
                                                                    <span class="glyphicon glyphicon-time"></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
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
