<div>
    <h3 class="panel-title">{{ date_format(date_create($event['event_date']), 'l jS F') }}</h3>

    <h4 style="color:blue">You will be in charge of the team during this event. Please do not forget to confirm start time, breaks and finish time.</h4>
    <p><b>client:</b> {{ $event->client['name'] }}</p>
    @if($event['address'])
        <p><b>Address:</b> {{ $event['address'] }}</p>
    @endif

    @if($event['uniform'])
        <p><b>Uniform:</b> {{ $event['uniform'] }}</p>
    @endif

    <p><b>Job start:</b> {{ $assignment['time'] }}</p>

    @if(!empty($event->finish_time))
        <p><b>Approximate finish time:</b> {{ $event->finish_time }}</p>
    @endif

    @if($event['notes'])
        <p><b>Notes:</b> {{ $event['notes'] }}</p>
    @endif
</div>