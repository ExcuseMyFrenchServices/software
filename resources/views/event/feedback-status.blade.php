 @if (is_null($event->feedback))
    <a class="btn btn-warning btn-xs" href="{{ url('feedback/request/'.$event->id) }}">Request</a>
@elseif (!is_null($event->feedback->hash))
    <span class="label label-primary">Requested</span>
@else
    <a class="btn btn-success btn-xs" href="{{ url('feedback/view/' . $event->id) }}">View</a>
@endif
