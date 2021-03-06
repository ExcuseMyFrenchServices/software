
<div>
    <h3>Dear {{ $client['name'] }},</h3>

    <p>Your event has been confirmed!</p>

    <h4 class="panel-title">{{ date_format(date_create($event['event_date']), 'l jS F') }}</h4>
    @if($event['address'])
        <p><b>Address:</b> {{ $event['address'] }}</p>
    @endif
    @if($event['details'])
        <p><b>Address Details:</b> {{ $event['details'] }}</p>
    @endif    
    @if(count($event['start_time']) == 1)
        <p><b>Start Time:</b> 
                {{ $event['start_time'][0] }}
        </p>
    @else
        @for($i=0;$i < count($event['start_time']);$i++)
            <p><b>Start Time:</b> 
                    {{ $event['start_time'][$i] }}
            </p>
        @endfor
    @endif
    @if(!$event->assignments->isEmpty())
        <p><b>Team:</b></p>
        @if(!empty($admin))
            <p><b>Main Contact :</b>{{ $admin->first_name." ".$admin->last_name }}</p>
        @endif
        <div class="row">
            <div class="col-xs-12 col-sm-8">
                <ul class="list-group">
                    @foreach($assignments as $assignment)
                        <li class="list-group-item user_list assignments">
                            {{$assignment->user->profile->first_name . " " . $assignment->user->profile->last_name. " (" .$assignment->time.")"}}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
    @if($event->finish_time)
        <p><b>Approximated finish time :</b> {{ $event->finish_time }}</p>
    @endif
</div>
