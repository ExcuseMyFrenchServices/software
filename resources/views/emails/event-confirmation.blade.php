<div>
    <h3 class="panel-title">{{ date_format(date_create($event['event_date']), 'l jS F') }}</h3>

    <p><b>Client:</b> {{ $event->client['name'] }}</p>
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
    <h4>To confirm your assistance, please click the link below:</h4>
    <p>http://staff.excusemyfrenchservices.com/confirm/{{ $assignment['hash'] }}</p>
</div>
