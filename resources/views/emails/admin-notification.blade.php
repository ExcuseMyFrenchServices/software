<div>
    <h3 class="panel-title">{{ date_format(date_create($event['event_date']), 'l jS F') }}</h3>

    <h4 style="color:blue">You will be in charge of the team during this event. Please do not forget to confirm start time, breaks and finish time.</h4>
    <p><b>client:</b> {{ $event->client['name'] }}</p>
    @if($event['address'])
        <p><b>Address:</b> {{ $event['address'] }}</p>
    @endif

    @if($event['details'])
        <p><b>Details:</b> {{ $event['details'] }}</p>
    @endif    
    
    @if(!empty($uniform))
        <p>
            <b>Uniform: </b>
            <ul style="list-style: none; padding: 0">
                    <li><b>{{ $uniform->set_name }}</b></li>
                @if(!empty($uniform->jacket))
                    <li>
                    <span class="pull-left" style="width:20px;height:20px;border:1px solid white;background-color: {{ $uniform->jacket_color }}; margin-right: 5px"> </span>
                     {{ $uniform->jacket_color }} {{ $uniform->jacket }} 
                    </li>
                @endif
                @if(!empty($uniform->shirt))
                    <li>
                    <span class="pull-left" style="width:20px;height:20px;border:1px solid white;background-color: {{ $uniform->shirt_color }}; margin-right: 5px"> </span>  
                     {{ $uniform->shirt_color }} {{ $uniform->shirt }}  
                    </li>
                @endif
                @if(!empty($uniform->pant))
                    <li>
                    <span class="pull-left" style="width:20px;height:20px;border:1px solid white;background-color: {{ $uniform->pant_color }}; margin-right: 5px"> </span>
                     {{ $uniform->pant_color }} {{ $uniform->pant }}
                    </li>
                @endif
                @if(!empty($uniform->shoes))
                    <li>
                    <span class="pull-left" style="width:20px;height:20px;border:1px solid white;background-color: {{ $uniform->shoes_color }}; margin-right: 5px"> </span>
                     {{ $uniform->shoes_color }} {{ $uniform->shoes }}
                    </li>
                @endif
            </ul>
        </p>
        <br>
    @endif

    <p><b>Job start:</b> {{ $assignment['time'] }}</p>

    @if(!empty($event->finish_time))
        <p><b>Approximate finish time:</b> {{ $event->finish_time }}</p>
    @endif

    @if($event['notes'])
        <p><b>Notes:</b> {{ $event['notes'] }}</p>
    @endif
    <h4>Add this event in :</h4>
    <a href="http://staff.excusemyfrenchservices.com/create_calendar_event/{{ $assignment['hash'] }}">
        <img src="{{ asset('img/google-calendar-api.png') }}">
    </a>
    <h4>Access to the function on this link:</h4>
    <p>http://staff.excusemyfrenchservices.com/confirm/{{ $assignment['hash'] }}</p>
    <p style="color:red">Please confirm your assistance by clicking on the "confirm" button once your logged in and on the function details.</p>
</div>