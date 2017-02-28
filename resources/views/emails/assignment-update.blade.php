<div>
    <h3 class="panel-title">{{ date_format(date_create($event['event_date']), 'l jS F') }}</h3>

    <p><b>Client:</b> {{ $event->client['name'] }}</p>
    @if(!empty($admin))
        <p><b>Admin :</b>{{ $admin->first_name." ".$admin->last_name }}</p>
    @endif
    @if($event['address'])
        <p><b>Address:</b> {{ $event['address'] }}</p>
    @endif
    
    @if($event['details'])
        <p><b>Details:</b> {{ $event['details'] }}</p>
    @endif    

    @if($event['uniform'])
        <p><b>Uniform:</b> {{ $event['uniform'] }}</p>
    @endif

    <p style="color:red"><b>New Job start:</b> {{ $assignment['time'] }}</p>

    @if(!empty($event->finish_time))
        <p><b>Approximate finish time:</b> {{ $event->finish_time }}</p>
    @endif

    @if($event['notes'])
        <p><b>Notes:</b> {{ $event['notes'] }}</p>
    @endif
    <h4>If the new event changes does not suits your agenda please contact JB ASAP at : 0410 125 994</h4>
</div>