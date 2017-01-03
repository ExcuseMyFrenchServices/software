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

    @if ($assignment->event->event_date >= date('Y-m-d'))
        @if($assignment->status == 'confirmed')
            <span class="label label-success">Confirmed</span>
        @elseif($assignment->status == 'pending')
            <span class="label label-warning">Pending</span>
        @else
            <span class="label label-danger">Cancelled</span>
        @endif
    @elseif(!empty($assignment->hours))
            <span class="label label-success">{{ $assignment->hours }} Hours</span>
    @endif
</li>