@extends('layouts.main')
 
@section('content')

    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-8 col-md-offset-2">
                <div style="margin-top: 70px;" class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Select Staff for {{ $client->name }}'s event for {{ $time }}</h3>
                    </div>
                    <div class="panel-body">
                        <form id="assign_staff" action="{{ url('event/' . $event->id . '/staff') }}" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="time" value="{{ $time }}">
                            <h6 class="label label-success">Available</h6>
                            @if(!$available->isEmpty())
                                <ul class="list-group">
                                    @foreach($available as $user)
                                        <li class="list-group-item">
                                            <div class="checkbox" style="margin: 0;">
                                                <label>
                                                    <input type="checkbox" id="user-{{ $user->id }}" name={{ $user->id }}> {{ $user->profile->first_name . ' ' . $user->profile->last_name . ' ('. $userMissions->getMissionsForClient($client->name,$user->id)[0]->time_worked_for . ' times)' }}

                                                    {{--<i style="font-size: 12px; margin-left: 15px">{{ ucwords($roles->where('id', $user->role_id)->first()->name) }} - Updated: {{ !is_null($user->availabilities->first()) ? date_format(date_create($user->availabilities->first()->updated_at), 'F d, Y') : null }}</i>--}}
                                                    @if($user->level_alert > 0) 
                                                        <i class="label label-info">Ready for level {{ $user->level_alert }}</i>
                                                    @endif
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
                            @if(count($temp_user) == 1)
                            <h6 class="label label-warning">Temporary User</h6>
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <div class="checkbox" style="margin: 0;">
                                            <label>
                                                <input type="checkbox" name={{ $temp_user->id }}> {{ $temp_user->profile->first_name .' '. $temp_user->profile->last_name  }}
                                            </label>
                                        </div>
                                    </li>
                                </ul>
                            @endif

                            <button type="button" id="showUnavailable" class="btn btn-danger"><span id="icon" class="glyphicon glyphicon-plus"></span> Unavailable Staff</button>
                            <div id="unavailableContent" class="hidden">
                            @if(count($unavailable) >= 1)
                                <h6 class="label label-danger"> Unavailable</h6>
                                <ul class="list-group">
                                    @foreach($unavailable as $user)
                                        <li class="list-group-item">
                                            <div class="checkbox" style="margin: 0;">
                                                <label>
                                                    <input type="checkbox" name={{ $user->id }} {{count($availableService->busyOn($event->id,$user->id,$time)) > 0 ? 'disabled' : '' }}> {{ $user->profile->first_name . ' ' . $user->profile->last_name .' ('. $userMissions->getMissionsForClient($client->name,$user->id)[0]->time_worked_for . ' times)'}}{{--<i style="font-size: 12px; margin-left: 15px">{{ ucwords($roles->where('id', $user->role_id)->first()->name) }} - Updated: {{ !is_null($user->availabilities->first()) ? date_format(date_create($user->availabilities->first()->updated_at), 'F d, Y') : null }}</i>--}}
                                                    @if($user->level_alert > 0) 
                                                        <i class="label label-info">Ready for level {{ $user->level_alert }}</i>
                                                    @endif
                                                

                                                <!-- Give info on the event attended by the user -->
                                                    @php 
                                                        $busyEvent = $availableService->busyOn($event->id,$user->id,$time)
                                                    @endphp 

                                                    @if($busyEvent !== null)
                                                        
                                                        @if($agent->isMobile())
                                                            <br>
                                                            <span class="label label-danger" style="margin-left: -30px">
                                                            {{ $busyEvent->time }}-{{ $busyEvent->event->finish_time }} 
                                                            -{{ $busyEvent->event->client->name }}-{{ $busyEvent->event->event_name }}
                                                            </span>
                                                        @else
                                                            <span class="label label-danger">
                                                                Busy from:
                                                                {{ $busyEvent->time }} to:
                                                                {{ $busyEvent->event->finish_time }} for {{ $busyEvent->event->client->name }} on {{ $busyEvent->event->event_name }}
                                                            </span>
                                                        @endif
                                                    @endif
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
                            </div>
                            <br>
                            <br>
                            <br>
                            <button class="btn btn-primary btn-sm" type="submit">Finish</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        $('#showUnavailable').click(function(){
            var content = $('#unavailableContent');
            if(content.hasClass('hidden')){
                content.attr('class','');
                $('#icon').attr('class','glyphicon glyphicon-minus');
            } else {
                content.attr('class','hidden');
                $('#icon').attr('class','glyphicon glyphicon-plus');
            }
        });
    </script>
@stop
