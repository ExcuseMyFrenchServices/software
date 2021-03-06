@extends('layouts.main')

@section('styles')
    <style type="text/css">
        h3
        {
            text-align: center;
            margin-bottom: 25px;
        }
        #page 
        {
            margin-top: 50px;
        }
    </style>
@stop

@section('content')
    @if(Auth::user()->role_id == 1 && !empty($previous_event))
    <div id="arrow-left" class="col-xs-2" style="position: fixed; font-size: 2em">
        <a href="{{ url('/event/'.$previous_event->id) }}">
            <span class="glyphicon glyphicon-arrow-left"></span>
        </a>
    </div>
    @endif

    @if(Auth::user()->role_id == 1 && !empty($next_event))
    <div id="arrow-right" class="col-xs-2 col-xs-offset-10" style="position: fixed; font-size: 2em">
        <a href="{{ url('/event/'.$next_event->id) }}" class="pull-right">
            <span class="glyphicon glyphicon-arrow-right"></span>
        </a>
    </div>
    @endif

    <div id="page" class="container">
        <div class="row">
            <div class="col-xs-12 col-md-8 col-md-offset-1">
                <div class="panel panel-default event_detail">
                    <div class="panel-heading">
                        @if(isset($event))
                            <h3 class="panel-title">{{date_format(date_create($event->event_date), 'l jS F')}}</h3>
                        @else
                            <h3 class="panel-title">Event Detail</h3>
                        @endif
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12">
                                @if(Session::has('calendar'))
                                    <div class="alert alert-success" role="alert">
                                        {{ Session::get('calendar') }}
                                    </div>
                                @endif
                                @if($event->event_type == "pre-booking event")
                                    <div class="alert alert-info" role="alert">
                                        <h3>Pre Booking Event</h3>
                                        @if(Auth::user()->role_id == 1)
                                            <p style="text-align: center">Please remember to change client and event type to make it a regular event !</p>
                                        @endif
                                    </div>
                                @endif
                                <h3><b>Basic Informations</b></h3>
                                <p><b>Client:</b> <a href="{{ url('/client/'.$event->client->id.'/edit') }}">{{ $event->client->name }}</a></p>
                                <br>

                                @if(!empty($event->address))
                                    <p><b>Address:</b> <a target="_blank" href="https://www.google.com.au/maps/search/{{ urlencode($event->address) }}">{{ $event->address }}</a></p>
                                    <br>
                                @endif

                                @if(!empty($event->details))
                                    <p><b>Address Details:</b> {{ $event->details }}</p>
                                    <br>
                                @endif

                                @if(!empty($event->guest_number))
                                    <p><b>Guest Number:</b> {{ $event->guest_number }} </p>
                                    <br>
                                @endif

                                @if(!empty($event->guest_arrival_time))
                                    <p><b>Guest Arrival Time:</b> {{ $event->guest_arrival_time }} </p>
                                @endif

                                @if(file_exists(public_path().'/storage/events/'.$event->id.'.pdf'))
                                    <div id="file" class="col-xs-12" style="margin-bottom: 25px">
                                        <a class="col-xs-8 col-sm-4 btn btn-primary" href=" download/{{ $event->id }}.pdf" style="border: 1px solid rgba(100,100,100,0.2);border-radius:5px;padding: 15px;text-align: center;text-decoration: none;color: white"><span class="glyphicon glyphicon-file"> </span> Attached File</a>
                                    </div>
                                @elseif(file_exists(public_path().'/storage/events/'.$event->id.'.jpeg'))
                                    <div id="file" style="margin-bottom: 25px">
                                        <img class="col-xs-12" src="{{ asset('/storage/events/'.$event->id.'.jpeg') }}" style="border: 1px solid rgba(100,100,100,0.2);border-radius:5px;padding: 15px;">
                                    </div>
                                @endif

                                @if(!empty($event->notes))
                                    <p><b>Notes:</b> {{ $event->notes }}</p>
                                    <br>
                                @endif

                                @if($event->travel_paid)
                                    <p><b>Travel Paid:</b><em> {{ $event->travel_time }}min of travel is paid by client</em></p>
                                @endif

                                <hr>
                                <h3><b>Staff Function</b></h3>

                                @if(!empty($uniform))
                                    <p>
                                        <b>Uniform: </b>
                                        <ul style="list-style: none; padding: 0">
                                                <li><b>{{ $uniform->set_name }}</b></li>
                                            @if(!empty($uniform->jacket))
                                                <li>
                                                <span class="pull-left" style="width:20px;height:20px;border:1px solid black;background-color: {{ $uniform->jacket_color }}; margin-right: 5px"> </span>
                                                 {{ $uniform->jacket_color }} {{ $uniform->jacket }} 
                                                </li>
                                            @endif
                                            @if(!empty($uniform->shirt))
                                                <li>
                                                <span class="pull-left" style="width:20px;height:20px;border:1px solid black;background-color: {{ $uniform->shirt_color }}; margin-right: 5px"> </span>  
                                                 {{ $uniform->shirt_color }} {{ $uniform->shirt }}  
                                                </li>
                                            @endif
                                            @if(!empty($uniform->pant))
                                                <li>
                                                <span class="pull-left" style="width:20px;height:20px;border:1px solid black;background-color: {{ $uniform->pant_color }}; margin-right: 5px"> </span>
                                                 {{ $uniform->pant_color }} {{ $uniform->pant }}
                                                </li>
                                            @endif
                                            @if(!empty($uniform->shoes))
                                                <li>
                                                <span class="pull-left" style="width:20px;height:20px;border:1px solid black;background-color: {{ $uniform->shoes_color }}; margin-right: 5px"> </span>
                                                 {{ $uniform->shoes_color }} {{ $uniform->shoes }}
                                                </li>
                                            @endif
                                        </ul>
                                    </p>
                                    <br>
                                    <p><b>Accessories : </b>
                                        <ul style="list-style: none; padding: 0">
                                            <li>Tray</li>
                                            <li>Bottle Opener</li>
                                        </ul>
                                    </p>
                                    <br>
                                @endif

                                @if(!empty($event->start_time))
                                @foreach($event->start_time as $time)
                                    <p><b>Team starting at {{ $time }}</b></p>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            @if(!$event->assignments->where('time', $time)->isEmpty())
                                                <ul class="list-group">
                                                @foreach($event->assignments->where('time', $time) as $assignment)
                                                    @include('event.staff-row', ['$assignment' => $assignment])
                                                @endforeach
                                                <li class="list-group-item">
                                                    <b>Admin Report</b>:<br>
                                                    {{ $event->report }}
                                                </li>
                                                </ul>
                                            @endif

                                            @if(Auth::user()->role_id == 1)
                                                    <a href="/assignment/add/{{ $event->id .'/'.$time }}" class="btn btn-primary btn-xs">
                                                        <span class="glyphicon glyphicon-plus"></span> Add staff
                                                    </a>
                                                    <br>
                                                    <br>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                                @else
                                    <p>Please define a start time</p>
                                @endif

                                @if(!empty($event->finish_time))
                                    <p><b>Approximate finish time:</b> {{ $event->finish_time }}</p>
                                    <br>
                                @endif
                                
                                @if(Auth::user()->role_id == 1 && !empty($event->start_time))
                                    <a href="{{ url('event/notify-all/'.$event->id) }}" class="btn btn-success btn-sm">Notify all</a>
                                @endif

                                <br>
                                <br>

                                @if($notification)
                                    <p><em>{{ $notification->message }}</em></p>
                                @endif

                                @if($event->bar != 0 && $event->barEvent !== null && Auth::user()->role_id == 1)
                                    <hr>
                                    <h3><b>Bar Function</b></h3>
                                    <p><b>Bar Staff</b></p>
                                    <ul class="list-group list-unstyled">
                                        
                                        @if($event->barEvent->private != 0)
                                        <li  class="list-group-item">Private ({{ $event->barEvent->private }})</li>
                                        @endif

                                        @if($event->barEvent->bar_back != 0)
                                        <li  class="list-group-item">Bar Back ({{ $event->barEvent->bar_back }})</li>
                                        @endif 

                                        @if($event->barEvent->bar_runner != 0)
                                        <li  class="list-group-item">Bar Runner ({{ $event->barEvent->bar_runner }})</li>
                                        @endif  

                                        @if($event->barEvent->classic_bartender != 0)
                                        <li  class="list-group-item">Classic Bartender ({{ $event->barEvent->classic_bartender }})</li>
                                        @endif       

                                        @if($event->barEvent->cocktail_bartender != 0)
                                        <li  class="list-group-item">Cocktail Bartender ({{ $event->barEvent->cocktail_bartender }})</li>
                                        @endif     

                                        @if($event->barEvent->flair_bartender != 0)
                                        <li  class="list-group-item">Flair Bartender ({{ $event->barEvent->flair_bartender }})</li>
                                        @endif 

                                        @if($event->barEvent->mixologist != 0)
                                        <li  class="list-group-item">Mixologist ({{ $event->barEvent->mixologist }})</li>
                                        @endif 

                                    </ul>

                                    <p><b>Drinks</b></p>

                                    @if(count($beers)>=1)
                                    <p>Beers</p>    
                                        <ul class="list-group list-unstyled">
                                            @foreach($beers as $beer)
                                            <li class="list-group-item">{{ $beer->name }} ({{ $beer->quantity }})</li>
                                            @endforeach    
                                        </ul>
                                    @endif

                                    @if(count($whines)>=1)
                                    <p>Whines</p>    
                                        <ul class="list-group list-unstyled">
                                            @foreach($whines as $whine)
                                            <li class="list-group-item">{{ $whine->name }} ({{ $whine->quantity }})</li>
                                            @endforeach    
                                        </ul>
                                    @endif

                                    @if(count($cocktails)>=1)
                                    <p>Cocktails</p>    
                                        <ul class="list-group list-unstyled">
                                            @foreach($cocktails as $cocktail)
                                            <li  class="list-group-item">{{ $cocktail->name }} ({{ $cocktail->quantity }})</li>
                                            @endforeach    
                                        </ul>
                                    @endif

                                    @if(count($shots)>=1)
                                    <p>Shots</p>    
                                        <ul class="list-group list-unstyled">
                                            @foreach($shots as $shot)
                                            <li class="list-group-item">{{ $shot->name }} ({{ $shot->quantity }})</li>
                                            @endforeach    
                                        </ul>
                                    @endif

                                    <p><b>Supplies</b></p>

                                    <p>Ice : {{ $event->barEvent->ice == 0 ? 'No':'Yes' }}</p>

                                    @if(count($softs)>=1)
                                    <p>Softs</p>
                                        <ul class="list-group list-unstyled">
                                            @foreach($softs as $soft)
                                            <li  class="list-group-item">{{ $soft->name }} ({{ $soft->quantity}})</li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    @if(count($ingredients)>=1)
                                    <p>Ingredients</p>
                                        <ul class="list-group list-unstyled">
                                            @foreach($ingredients as $ingredient)
                                            <li  class="list-group-item">{{ $ingredient->name }} ({{ $ingredient->quantity}})</li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    <p><b>Equipment</b></p>

                                    @if($event->barEvent->bar_number != 0)
                                    <p>Bars : {{ $event->barEvent->bar_number }}</p>
                                    @endif

                                    @if(count($furnitures)>=1)
                                    <p>Furnitures</p>
                                        <ul class="list-group list-unstyled">
                                            @foreach($furnitures as $furniture)
                                            <li  class="list-group-item">{{ $furniture->name }} ({{ $furniture->quantity}})</li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    @if($event->barEvent->glass_type != 0)
                                    <p>Glasses</p>
                                        <ul class="list-group list-unstyled">
                                            <li class="list-group-item">{{ $event->barEvent->glass_type }}</li>
                                        </ul>
                                    @endif

                                    @if($event->barEvent->notes != "")
                                    <p>Notes</p>
                                        <p>{{ $event->barEvent->notes }}</p>
                                    @endif

                                @endif

                                @if ($event->event_date >= date('Y-m-d'))
                                    <a class="col-md-1 btn btn-info btn-sm back_btn" href="{{ url('events/' . Auth::user()->id) }}">Back</a>
                                @else
                                    <a class="col-md-1 btn btn-info btn-sm back_btn" href="{{ url('past/events/') }}">Back</a>
                                @endif

                                @if(!$event->assignments->where('user_id', Auth::user()->id)->where('status', 'pending')->isEmpty())
                                    <a class="col-md-2 btn btn-success btn-sm back_btn" href="{{ url('event/' . $event->id . '/confirm') }}">
                                        <span class="glyphicon glyphicon-check" aria-hidden="true"></span>
                                        Confirm
                                    </a>
                                @endif
                                
                                @include('event.actions', ['event' => $event])
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if(Auth::user()->role_id == 1)
            <div id="admin-notification" class="col-xs-12 col-md-3">

                <div id="buttons-shortcut" class="panel panel-default event_detail">
                    <div class="panel-heading">
                        <h4 class="panel-title" style="text-align: center">Actions</h4>
                    </div>
                    <div class="panel-body" style="text-align: center">
                        <a href="/event/{{ $event->id }}/edit" class="btn btn-info btn-xs" role="button">Update</a>
                        <a href="/event/{{ $event->id }}/copy" class="btn btn-primary btn-xs" role="button">Copy</a>
                    </div>
                </div>

                @if($event->bar != 0 && $event->barEvent !== null)
                <div id="bar-event-function" class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title" style="text-align: center">Bar Function Status</h4>
                    </div>
                    <div class="panel-body" style="text-align: center">
                        @if($event->barEvent->status == 1)
                            <a href="{{ url('bar-event/create/'.$event->barEvent->id) }}" class="btn btn-warning btn-xs">pending</a>
                        @elseif($event->barEvent->status == 2)
                            <a href="{{ url('bar-event/create/'.$event->barEvent->id) }}" class="btn btn-success btn-xs">confirmed</a>
                        @else
                            <a href="{{ url('bar-event/create/'.$event->barEvent->id) }}" class="btn btn-default btn-xs">new function</a>
                        @endif
                    </div>
                </div>
                @endif

                @if(!$event->assignments->where('status','pending')->isEmpty())
                <div id="force-confirm" class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title" style="text-align: center">Force Confirmation</h4>
                    </div>
                    <div class="panel-body" style="text-align: center">
                        <form action="{{ url('event/' . $event->id . '/confirm') }}" method="POST" class="form">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="form-group">
                                <select name="user_id" data-live-search="true" data-size="8" data-width="100%" class="form-control">
                                    @foreach($event->assignments->where('status','pending') as $assignment)
                                        <option value="{{ $assignment->user_id }}">{{$assignment->user->profile->first_name . " " . $assignment->user->profile->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <button role="submit" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-warning-sign"> </span> Force confirmation</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endif

                @if(count($modifications) > 0)
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Last Modification</h3>
                    </div>
                    <div class="panel-body">
                        <ul class="list-group">
                            <li class="list-group-item" style="margin-bottom: 5px">
                                <b>{{ date_format(date_add($modifications->created_at,date_interval_create_from_date_string('10hours')),'d/m/Y H:i')}}:</b>
                                <br>
                                {{ $modifications->role." ".$modifications->name." ".str_replace('_',' ',$modifications->modifications) }}
                            </li>
                            <a href="{{ url('event/modifications/'.$event->id) }}" class="btn btn-info btn-sm">Check all</a>
                        </ul>
                    </div>
                </div>
                @endif

            </div>
            @endif

        </div>
    </div>
    <!-- Hidden content -->
    <div id="popUp" class="hidden" style="position: absolute; width: 100vw; height: 150vh; background-color: rgba(0,0,0,0.8); top: 0; left: 0">
            <div id="formContainer" class="col-sm-6 col-sm-offset-3" style="height: 50vh;margin-top:15%;padding:150px 0px;background-color: white">
                <form action="{{ url('event/notify/'.$event->id.'/client') }}" method="POST" class="form">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group col-xs-10 col-xs-offset-1">
                        <label for="client-email">Choose Client Email</label>
                        <select name="client-email" id="client-email" class="form-control">
                            
                            <option value="{{$event->client->email}}">
                                {{ $event->client->email }}
                            </option>

                            <option value="{{$event->client->second_email}}">
                                {{ $event->client->second_email }}
                            </option>
                            
                            @if(!empty($event->client->third_email))
                            <option value="{{$event->client->second_email}}">
                                {{ $event->client->third_email }}
                            </option>
                            @endif
                            
                            <option value="to-all">To All</option>
                        </select>
                    </div>
                    <div class="col-xs-10 col-xs-offset-1">
                        <button role="submit" class="btn btn-info btn-sm">Send Email</button>
                    </div>
                </form>
            </div>
        </div>
@stop
@section('scripts')
    @if(!empty($event->client->second_email))
    <script type="text/javascript">
        document.getElementById('client-mail').addEventListener('click', function(e){
            var popUp = document.getElementById('popUp');
            popUp.className = "";

            popUp.addEventListener('click', function(e){
                popUp.className = "hidden";
            });

            document.getElementById('formContainer').addEventListener('click', function(e){
                e.stopPropagation();
            })
        });
    </script>
    @endif
@stop