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
                                @foreach($event->assignments as $assignment)
                                    <li class="list-group-item">
                                        <div>{{ $assignment->user->profile->first_name . ' ' . $assignment->user->profile->last_name }}</div>
                                        <input type="text" name="{{ $assignment->id }}" class="form-control" value="{{ $assignment->hours }}">
                                    </li>
                                @endforeach
                            </ul>
                            <button class="btn btn-primary btn-sm" type="submit">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
