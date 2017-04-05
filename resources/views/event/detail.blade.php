@extends('layouts.main')

@section('content')
    <div id="page" class="container">
        <div class="row">
            <div class="col-xs-12 col-md-8 col-md-offset-2">
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
                                <p><b>Client:</b> {{ $event->client->name }}</p>
                                <br>

                                @if(!empty($event->address))
                                    <p><b>Address:</b> <a target="_blank" href="https://www.google.com.au/maps/search/{{ urlencode($event->address) }}">{{ $event->address }}</a></p>
                                    <br>
                                @endif

                                @if(!empty($event->details))
                                    <p><b>Address Details:</b> {{ $event->details }}</p>
                                    <br>
                                @endif

                                @if(file_exists('files/'.$event->id.'.pdf'))
                                    <div id="file" class="col-xs-12" style="margin-bottom: 25px">
                                        <a class="col-xs-8 col-sm-4 btn btn-primary" href="{{ asset('files/'.$event->id.'.pdf') }}" style="border: 1px solid rgba(100,100,100,0.2);border-radius:5px;padding: 15px;text-align: center;text-decoration: none;color: white"><span class="glyphicon glyphicon-file"> </span> Attached File</a>
                                    </div>
                                @elseif(file_exists('files/'.$event->id.'.jpg'))
                                    <div id="file" style="margin-bottom: 25px">
                                        <a class="col-xs-8 col-sm-4 btn btn-primary" href="{{ asset('files/'.$event->id.'.jpg') }}" style="border: 1px solid rgba(100,100,100,0.2);border-radius:5px;padding: 15px;text-align: center;text-decoration: none;color: white"><span class="glyphicon glyphicon-file"> </span> Attached File</a>
                                    </div>
                                @endif

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
  

                                @if(count($softs) != 0 || count($glasses) != 0 || count($accessories) != 0 || count($alcohols) != 0)
                                    <p>
                                        <b>Extras:</b>
                                        <ul class="list-group">
                                            @if(count($glasses) != 0)
                                                <li class="list-group-item"><b>Glasses</b></li>
                                                <li class="list-group-item">
                                                    <ul>
                                                    @foreach($glasses as $glass)
                                                        <li style="min-height: 40px">
                                                        {{ $glass->quantity.' '.$glass->name }}
                                                        <a href="{{ url('/outstocks/'.$glass->id.'/destroy/'.$event->id) }}" class='pull-right btn btn-danger btn-sm'>X</a>
                                                        </li>
                                                    @endforeach
                                                    </ul>
                                                </li>
                                            @endif
                                            @if(count($softs) != 0)
                                                <li class="list-group-item"><b>Soft drinks</b></li>
                                                <li class="list-group-item">
                                                    <ul>
                                                    @foreach($softs as $soft)
                                                        <li style="min-height: 40px">
                                                        {{ $soft->quantity.' '.$soft->name }}
                                                        <a href="{{ url('/outstocks/'.$soft->id.'/destroy/'.$event->id) }}" class='pull-right btn btn-danger btn-sm'>X</a>
                                                        </li>
                                                    @endforeach
                                                    </ul>
                                                </li>
                                            @endif
                                            @if(count($alcohols) != 0)
                                                <li class="list-group-item"><b>Alcohols</b></li>
                                                <li class="list-group-item">
                                                    <ul>
                                                    @foreach($alcohols as $alcohol)
                                                        <li style="min-height: 40px">
                                                        {{ $alcohol->quantity.' '.$alcohol->name }}
                                                        <a href="{{ url('/outstocks/'.$alcohol->id.'/destroy/'.$event->id) }}" class='pull-right btn btn-danger btn-sm'>X</a>
                                                        </li>
                                                    @endforeach
                                                    </ul>
                                                </li>
                                            @endif
                                            @if(count($accessories) != 0)
                                                <li class="list-group-item"><b>Accessories</b></li>
                                                <li class="list-group-item">
                                                    <ul>
                                                    @foreach($accessories as $accessory)
                                                        <li style="min-height: 40px">
                                                        {{ $accessory->quantity.' '.$accessory->name }}
                                                        <a href="{{ url('/outstocks/'.$accessory->id.'/destroy/'.$event->id) }}" class='pull-right btn btn-danger btn-sm'>X</a>
                                                        </li>
                                                    @endforeach
                                                    </ul>
                                                </li>
                                            @endif
                                        </ul>
                                    </p>
                                    <br>
                                @endif

                                @if(!empty($event->notes))
                                    <p><b>Notes:</b> {{ $event->notes }}</p>
                                    <br>
                                @endif

                                @if(!empty($event->outStock))
                                    <ul>
                                        @foreach($event->outStock as $item)
                                        <li>{{ $item->category }} | {{ $item->name }} - {{ $item->quantity }}</li>
                                        @endforeach
                                    </ul>
                                @endif

                                @foreach($event->start_time as $time)
                                    <p><b>Team starting at {{ $time }}</b></p>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            @if(!$event->assignments->where('time', $time)->isEmpty())
                                                <ul class="list-group">
                                                @foreach($event->assignments->where('time', $time) as $assignment)
                                                    @include('event.staff-row', ['$assignment' => $assignment])
                                                @endforeach
                                                @if(Auth::user()->role_id == 1)
                                                    <li class="list-group-item">
                                                        <a href="{{ url('event/notify-all/'.$event->id) }}" class="btn btn-success btn-sm">Notify all</a>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <b>Admin Report</b>:<br>
                                                        {{ $event->report }}
                                                    </li>
                                                @endif
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

                                @if(!empty($event->finish_time))
                                    <p><b>Approximate finish time:</b> {{ $event->finish_time }}</p>
                                    <br>
                                @endif

                                @if(Auth::user()->role_id == 1)
                                    @if(!$event->assignments->where('status','pending')->isEmpty())
                                    <div class="row">
                                        <p><b>Force confirmation :</b></p>
                                        <form action="{{ url('event/' . $event->id . '/confirm') }}" method="POST" class="form">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <div class="form-group col-xs-12">
                                                <select name="user_id" data-live-search="true" data-size="8" data-width="100%">
                                                    @foreach($event->assignments->where('status','pending') as $assignment)
                                                        <option value="{{ $assignment->user_id }}">{{$assignment->user->profile->first_name . " " . $assignment->user->profile->last_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-xs-4">
                                                <button role="submit" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-warning-sign"> </span> Force confirmation for this staff</button>
                                            </div>
                                        </form>
                                    </div>
                                    @endif
                                @endif

                                @if ($event->event_date >= date('Y-m-d'))
                                    <a class="col-xs-1 btn btn-info btn-sm back_btn" href="{{ url('events/' . Auth::user()->id) }}">Back</a>
                                @else
                                    <a class="col-xs-1 btn btn-info btn-sm back_btn" href="{{ url('past/events/') }}">Back</a>
                                @endif

                                @if(!$event->assignments->where('user_id', Auth::user()->id)->where('status', 'pending')->isEmpty())
                                    <a class="col-xs-2 btn btn-success btn-sm back_btn" href="{{ url('event/' . $event->id . '/confirm') }}">
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
        </div>
    </div>
    <!-- Hidden content -->
    <div id="popUp" class="hidden" style="position: absolute; width: 100vw; height: 150vh; background-color: rgba(0,0,0,0.8); top: 0; left: 0">
            <div id="formContainer" class="col-sm-6 col-sm-offset-3" style="height: 50vh;margin-top:15%;padding:150px 0px;background-color: white">
                <form action="{{ url('event/notify/'.$event->id.'/client') }}" method="POST" class="form">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group col-xs-10 col-xs-offset-1">
                        <label for="client-email">Choose Client Email</label>
                        <select name="client-email" id="client-email">
                            
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