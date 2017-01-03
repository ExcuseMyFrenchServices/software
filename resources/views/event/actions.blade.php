@if(Auth::user()->role_id == 1)
    @if($event->event_date >= date('Y-m-d'))
        <a class="btn btn-{{ $event->client_notification ? 'success' : 'warning' }} btn-sm back_btn" href="{{ url('event/notify/'.$event->id.'/client') }}">
            <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
            Client
        </a>
    @else
        @if(is_null($event->feedback) || !is_null($event->feedback->hash))
            <a class="btn btn-warning btn-sm back_btn" href="{{ url('feedback/request/'.$event->id) }}">
                <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
                Request feedback
            </a>
        @else
            <a class="btn btn-success btn-sm back_btn" href="{{ url('feedback/view/'.$event->id) }}">
                <span class="glyphicon glyphicon-thumbs-up" aria-hidden="true"></span>
                View feedback
            </a>
        @endif
    @endif
@endif

@if((Auth::user()->role_id == 1 || $event->admin_id == Auth::user()->id))
    <a class="btn btn-primary btn-sm back_btn" href="{{ url('event/'.$event->id.'/timesheet') }}">
        <span class="glyphicon glyphicon-time" aria-hidden="true"></span>
        Timesheet
    </a>
@endif