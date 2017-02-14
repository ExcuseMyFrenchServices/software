<li class="list-group-item user_list assignments">
    @if(Auth::user()->role_id == 1 && $assignment->event->event_date >= date('Y-m-d'))
        <a href="{{ url('assignment/delete/' . $assignment->id) }}">
            <span class="label label-danger assign_delete">X</span>
        </a>

        <a href="{{ url('event/notify/' . $assignment->id) }}">
            <span class="label label-{{ $assignment->notification ? 'success' : 'warning' }} assign_delete">Notify</span>
        </a>
    @endif
    @if(Auth::user()->role_id == 1)
        <a href="{{ url('event/' . $assignment->event->id . '/admin/' . $assignment->user->id) }}">
            <span class="label label-{{ $assignment->event->admin_id == $assignment->user->id ? 'success' : 'warning' }} assign_delete">Admin</span>
        </a>
    @endif

    {{$assignment->user->profile->first_name . " " . $assignment->user->profile->last_name }}<b style="margin-left: 15px"><a href="tel: +61{{ $assignment->user->profile->phone_number }}">{{ $assignment->user->profile->phone_number }}</a></b>
    
    @if(Auth::user()->role_id == 1)
    <form id='confirm_start_time' action="{{ url('event/'.$event->id.'/start-time-confirm') }}" method="POST">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
            @if(!$assignment->start_time_confirmation)  
                <div class="form-group form-inline"> 
                    <label>Start Time :</label>
                    <input type="text" name="confirmed_start_time" value="{{$assignment->time}}" class="form-control" style='width: 60px'>
                    <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">
                    <button type="submit" class="btn btn-info btn-sm">Confirm</button>
                </div>
            @else
                <div class="form-group form-inline">
                    <p>Start Time: {{ $assignment->time }}</p>
                </div>
            @endif
    </form>
    @endif

    @if(Auth::user()->role_id == 1)
        @if ($assignment->event->event_date >= date('Y-m-d'))
            @if($assignment->status == 'confirmed')
                <span style="margin-top:-50px" class="label label-success">Confirmed</span>
            @elseif($assignment->status == 'pending')
                <span style="margin-top:-50px" class="label label-warning">Pending</span>
            @else
                <span style="margin-top:-50px" class="label label-danger">Cancelled</span>
            @endif
        @elseif(!empty($assignment->hours))
                <span style="margin-top:-50px" class="label label-success"> {{ $assignment->hours }} Hours </span>
                <span style="margin-top:-50px" class="label label-warning"> {{ $assignment->break }} Breaks </span>
        @endif
    @endif
</li>