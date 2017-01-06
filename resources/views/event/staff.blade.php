@extends('layouts.main')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-8 col-md-offset-2">
                <div style="margin-top: 70px;" class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Select Staff for {{ $client->name }}'s event </h3>
                    </div>
                    <div class="panel-body">
                        <form id="assign_staff" action="{{ url('event/' . $event->id . '/staff') }}" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="time" value="{{ $time }}">
                            <h6>Available</h6>
                            @if(!$available->isEmpty())
                                <ul class="list-group">
                                    @foreach($available as $user)
                                        <li class="list-group-item">
                                            <div class="checkbox" style="margin: 0;">
                                                <label>
                                                    <input type="checkbox" id="user-{{ $user->id }}" name={{ $user->id }}> {{ $user->profile->first_name . ' ' . $user->profile->last_name . ' ('. $userMissions->getMissionsForClient($client->name,$user->id)[0]->time_worked_for . ' times)' }}{{--<i style="font-size: 12px; margin-left: 15px">{{ ucwords($roles->where('id', $user->role_id)->first()->name) }} - Updated: {{ !is_null($user->availabilities->first()) ? date_format(date_create($user->availabilities->first()->updated_at), 'F d, Y') : null }}</i>--}}
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="empty_user">
                                    <p>No users available</p>
                                </div>
                            @endif
                            <h6>Unavailable</h6>
                            @if(count($unavailable) >= 1)
                                <ul class="list-group">
                                    @foreach($unavailable as $user)
                                        <li class="list-group-item">
                                            <div class="checkbox" style="margin: 0;">
                                                <label>
                                                    <input type="checkbox" name={{ $user->id }}> {{ $user->profile->first_name . ' ' . $user->profile->last_name .' ('. $userMissions->getMissionsForClient($client->name,$user->id)[0]->time_worked_for . ' times)'}}{{--<i style="font-size: 12px; margin-left: 15px">{{ ucwords($roles->where('id', $user->role_id)->first()->name) }} - Updated: {{ !is_null($user->availabilities->first()) ? date_format(date_create($user->availabilities->first()->updated_at), 'F d, Y') : null }}</i>--}}
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="empty_user">
                                    <p>No users unavailable</p>
                                </div>
                            @endif
                            <button class="btn btn-primary btn-sm" type="submit">Finish</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
