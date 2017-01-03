@extends('layouts.main')

@section('content')
    <div id="users_container" class="container">
        <div class="row">
            <div class="col-xs-12 col-md-10 col-md-offset-1">
                @if(count($events) >= 1)
                    <table id="events_list" class="table table-striped">
                        <thead>
                            <th>Client Name</th>
                            <th>Date</th>
                            <th></th>
                        </thead>
                        <tbody>
                        @foreach($events as $event)
                            <tr>
                                <td> <a href="{{ url('event/' . $event->id . '/timesheet') }}">{{ $event->client->name }}</a></td>
                                <td>{{ date_format(date_create($event->event_date), 'F d, Y') }}</td>
                                <td></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
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
            </div>
        </div>
    </div>
@stop
