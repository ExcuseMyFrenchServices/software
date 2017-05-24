    
@extends('layouts.main')

@section('styles')
    <style type="text/css">
        .showButton
        {
            color: black;
            font-size: 1.5em;
            padding-left: 0;
        }
        .showButton:hover
        {
            color: gray;
        }
    </style>
@stop

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div style="margin-top: 70px;" class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ isset($event) && !is_null($event->id) ? 'Update' : 'Create new' }} event</h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-xs-12">
                            @if(count($errors) > 0)
                                <div class="alert alert-danger" role="alert">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(Session::has('success'))
                                <div class="alert alert-success" role="alert">
                                    {{ Session::get('success') }}
                                </div>
                            @endif
                            @if(Session::has('danger'))
                                <div class="alert alert-danger" role="alert">
                                    {{ Session::get('danger') }}
                                </div>
                            @endif
                        </div>


                        <form id="create-form" action="{{ isset($event) && !is_null($event->id) ? '/event/'.$event->id : url('event') }}" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            @if(isset($event) && !is_null($event->id))
                                <input type="hidden" name="_method" value="PUT">
                            @endif

                            <div class="col-xs-8 form-group {{ $errors->has('client') ? 'has-error' : '' }}">
                                <label for="client">Client</label>
                                <select name="client" data-live-search="true" data-size="8" data-width="100%">
                                    <option value="{{ $event->client_id or old('client')}}"></option>
                                    @foreach($clients as $client)
                                        @if(isset($event))
                                            <option {{ $client->id == $event->client_id ? 'selected' : '' }} value="{{ $client->id }}">{{ ucfirst($client->name) }}</option>
                                        @else
                                            <option {{ old('client') == $client->id ? 'selected' : '' }} value="{{ $client->id }}">{{ ucfirst($client->name) }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-xs-4 form-group" style="text-align: center">
                                <label style="display: block;">&nbsp;</label>
                                <a href="/client/create" class="btn btn-success btn-sm">
                                    <span class="glyphicon glyphicon-plus"></span> Add
                                </a>
                            </div>

                            <div class="col-xs-6 form-group {{ $errors->has('event_name') ? 'has-error' : '' }}">
                                <label for="event_name">Event Name</label>
                                @if(isset($event))
                                    <input  type="text" name="event_name" id="event_name" class="form-control"  value="{{ $event->event_name }}">
                                @else
                                    <input type="text" name="event_name" id="event_name" class="form-control" value="{{ old('event_name') }}">
                                @endif
                            </div>

                            <div class="col-xs-6 form-group {{ $errors->has('event_name') ? 'has-error' : '' }}">
                                <label for="event_type">Event Type</label>
                                @if(isset($event))
                                    <input type="text" name="event_type" id="event_type" class="form-control" value="{{ $event->event_type }}">
                                @else
                                    <input type="text" name="event_type" id="event_type" class="form-control" value="{{ old('event_type') }}">
                                @endif
                            </div>

                            <div class="form-group col-xs-12 col-md-6 {{ $errors->has('booking_date') ? 'has-error' : '' }}">
                                <label for="booking_date">Booking Date</label>
                                <div class="input-group date" id="booking_date">
                                    <input type="text" class="form-control" name="booking_date" value="{{ $event->booking_date or old('booking_date') }}"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group col-xs-12 col-md-6 {{ $errors->has('event_date') ? 'has-error' : '' }}">
                                <label for="event_date">Event Date</label>
                                <div class="input-group date" id="event_date">
                                    <input type='text' class="form-control" name="event_date" value="{{ $event->event_date or old('event_date') }}"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group col-xs-12 col-md-6">
                                <label for="guest_arrival_time">Guest arrival time</label>
                                <div class="input-group date" id="time">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                    <input type="text" class="form-control" name="guest_arrival_time" value="{{ $event->guest_arrival_time or old('guest_arrival_time') }}"/>
                                </div>
                            </div>

                            <div class="form-group col-xs-12 col-md-6">
                                <label for="guest_number">Number of guest</label>
                                <input type="number" name="guest_number" id="guest_number" class="form-control" value="{{ $event->guest_number or old('guest_number') }}">
                            </div>

                            

                            <div class="col-xs-12 form-group">
                                <label for="address">Address</label>
                                <textarea rows="2"  name="address" id="address" class="form-control">{{ $event->address or old('address') }}</textarea>
                            </div>

                            <div class="col-xs-12 form-group">
                                <label for="details">Address Details</label>
                                <textarea rows="2"  name="details" id="details" class="form-control">{{ $event->details or old('details')}}</textarea>
                            </div>

                            <div class="col-xs-12 form-group">
                                <label for="notes">Notes</label>
                                <textarea rows="2"  name="notes" id="notes" class="form-control">{{ $event->notes or old('notes') }}</textarea>
                            </div>

                            <div id="barNoteNeeds" style="margin-bottom: 50px" class="col-xs-12">
                                <label for="barNotes">Bar Notes</label>
                                <textarea rows="2" id="barNotes" name="barNotes" class="form-control">{{ $event->barEvent->notes or old('barNotes')}}</textarea>
                            </div>

                            <div class="col-xs-12 form-group">
                                <label for="event_file">Add a file to event</label>
                                <input type="file" name="event_file">
                            </div>

                            <div class="col-xs-12 col-sm-10 form-group services">
                                <label class="">Bar Service</label>
                                <div class="checkbox">
                                    <label>
                                        @if(isset($event) && $event->id != null)
                                            <input type="checkbox" name="bar" id="glasses" {{ $event->bar ? 'checked' : '' }}> Bar Service required
                                        @else
                                            <input type="checkbox" name="bar" id="glasses"> Bar Service required
                                        @endif
                                    </label>
                                </div>
                            </div>

                            <!-- Bar Section -->

                            <div class="col-xs-12">
                                <a id="showbarFunction" class="showButton col-xs-12"><span id="barFunctionarrow" class="glyphicon glyphicon-triangle-right"> Bar</span></a>
                            </div>

                            <div id="barFunctionNeeds" class="hidden">
                                <a id="showstaff" class="showButton col-xs-12">
                                    @if(isset($event) && $event->barEvent !== null)
                                    <span id="staffarrow" class="glyphicon glyphicon-triangle-{{ isset($event) && $event->barEvent !== null && $event->barEvent->private > 0 || $event->barEvent->bar_back > 0 || $event->barEvent->bar_runner > 0 || $event->barEvent->classic_bartender > 0 || $event->barEvent->cocktail_bartender > 0 || $event->barEvent->flair_bartender > 0 || $event->barEvent->mixologist > 0 ? 'bottom':'right'}}"> 
                                        Bar Staff 
                                    </span>
                                    @else
                                    <span id="staffarrow" class="glyphicon glyphicon-triangle-right"> 
                                        Bar Staff 
                                    </span>
                                    @endif
                                </a>

                                @if(isset($event) && $event->barEvent !== null )
                                <div id="staffNeeds" class="{{ $event->barEvent->private > 0 || $event->barEvent->bar_back > 0 || $event->barEvent->bar_runner > 0 || $event->barEvent->classic_bartender > 0 || $event->barEvent->cocktail_bartender > 0 || $event->barEvent->flair_bartender > 0 || $event->barEvent->mixologist > 0 ? '':'hidden'}}">
                                @else
                                <div id="staffNeeds" class="hidden">
                                @endif

                                    <div class="row">
                                    <hr>
                                    <div id="staffMessage">
                                        
                                    </div>
                                    <div class="col-xs-10 col-xs-offset-1">
                                            <div class="col-xs-12">
                                                <div class="col-xs-6">
                                                    <label for="private">Private</label>
                                                </div>
                                                <input id="privateNumber" type="number" name="privateNumber" value="{{ $event->barEvent->private or old('private')}}">
                                            </div>

                                            <div class="col-xs-12">
                                                <div class="col-xs-6">
                                                    <label for="barBack">Bar Back</label>
                                                </div>
                                                <input id="barBackNumber" type="number" name="barBackNumber" value="{{ $event->barEvent->bar_back or old('bar_back')}}">
                                            </div>

                                            <div class="col-xs-12">
                                                <div class="col-xs-6">
                                                    <label for="barRunner">Bar Runner</label>
                                                </div>
                                                <input id="barRunnerNumber" type="number" name="barRunnerNumber" value="{{ $event->barEvent->bar_runner or old('bar_runner')}}">
                                            </div>

                                            <div class="col-xs-12">
                                                <div class="col-xs-6">
                                                    <label for="classicBartender">Classic Bartender</label>
                                                </div>
                                                <input id="classicBartenderNumber" type="number" name="classicBartenderNumber" value="{{ $event->barEvent->classic_bartender or old('classic_bartender')}}">
                                            </div>

                                            <div class="col-xs-12">
                                                <div class="col-xs-6">
                                                    <label for="cocktailBartender">Cocktail Bartender</label>
                                                </div>
                                                <input id="cocktailBartenderNumber" type="number" name="cocktailBartenderNumber" value="{{ $event->barEvent->cocktail_bartender or old('cocktail_bartender')}}">
                                            </div>

                                            <div class="col-xs-12">
                                                <div class="col-xs-6">
                                                    <label for="flairBartender">Flair Bartender</label>
                                                </div>
                                                <input id="flairBartenderNumber" type="number" name="flairBartenderNumber" value="{{ $event->barEvent->flair_bartender or old('flair_bartender')}}">
                                            </div>

                                            <div class="col-xs-12">
                                                <div class="col-xs-6">
                                                    <label for="mixologist">Mixologist</label>
                                                </div>
                                                <input id="mixologistNumber" type="number" name="mixologistNumber" value="{{ $event->barEvent->mixologist or old('mixologist')}}"> 
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <a id="showalcohol" class="showButton col-xs-12"><span id="alcoholarrow" class="glyphicon glyphicon-triangle-{{count($beers)>=1 || count($whines)>=1 || count($spirits)>=1 || count($shots)>=1 || count($cocktails)>=1 ? 'bottom':'right'}}"> Alcohol </span></a>

                                <div id="alcoholNeeds" class="{{ count($beers)>=1 || count($whines)>=1 || count($spirits)>=1 || count($shots)>=1 || count($cocktails)>=1 ? '':'hidden'}}">
                                    <div class="row">
                                        <hr>
                                        <div class="col-xs-10 col-xs-offset-1">
                                            
                                            <div class="col-xs-12">
                                                <input type="checkbox" name="beers" id="beers" {{ count($beers)>=1? 'checked':'' }}>
                                                <label for="beers">Beers</label>
                                            </div>

                                                <div id="beersNeeds" class="{{ count($beers)>=1? '':'hidden'}}">
                                                    <div class="row">
                                                        
                                                        <div class="col-xs-12">
                                                            <label>Beer Name</label>    
                                                        </div>

                                                    @if(count($beers)>=1)
                                                        <div id="beersContent" class="col-xs-10">
                                                            @foreach($beers as $beer)
                                                            <input type="text" name="beersName0" value="{{ $beer->name }}">
                                                            <input type="number" name="beersNumber0" value="{{ $beer->quantity }}">
                                                            @endforeach
                                                            <input id="beerscounter" type="hidden" name="beerscounter" value="{{ count($beers) }}">
                                                        </div>
                                                    @else
                                                        <div id="beersContent" class="col-xs-10">
                                                            <input type="text" name="beersName0">
                                                            <input type="number" name="beersNumber0">
                                                            <input id="beerscounter" type="hidden" name="beerscounter" value="1">
                                                        </div>
                                                    @endif

                                                        <div class="col-xs-12">
                                                            <button id="addbeers" type="button" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Add another beer</button>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                </div>

                                            <div class="col-xs-12"> 
                                                <input type="checkbox" name="whine" id="whine" {{ count($whines)>=1? 'checked':'' }}>
                                                <label for="whine">Whine</label>
                                            </div>

                                                <div id="whineNeeds" class="{{ count($whines)>=1? '':'hidden'}}">
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <div class="col-xs-4" style="padding-left: 0">
                                                                <label>Whine Name</label>
                                                            </div>
                                                            <div class="col-xs-4" style="padding-left: 0">
                                                                <label>Whine Type</label>   
                                                            </div>
                                                        </div>

                                                        @if(count($whines)>=1)
                                                            <div id="whineContent" class="col-xs-12">
                                                                @foreach($whines as $whine)
                                                                    <input type="text" name="whineName0" value="{{ $whine->name }}">
                                                                    <input type="text" name="whineList0" value="{{ $whine->brand }}">
                                                                    <input type="number" name="whineNumber0" value="{{ $whine->quantity }}">
                                                                @endforeach
                                                                <input id="whinecounter" type="hidden" name="whinecounter" value="{{ count($whines) }}">
                                                            </div>
                                                        @else
                                                            <div id="whineContent" class="col-xs-12">
                                                                <input type="text" name="whineName0">
                                                                <input type="text" name="whineList0">
                                                                <input type="number" name="whineNumber0">
                                                                <input id="whinecounter" type="hidden" name="whinecounter" value="1">
                                                            </div>
                                                        @endif

                                                        <div class="col-xs-12">
                                                            <button id="addwhine" type="button" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Add another whine</button>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                </div>

                                            <div class="col-xs-12">
                                                <input type="checkbox" name="spirits" id="spirits" {{ count($spirits)>=1? 'checked':'' }}>
                                                <label for="spirits">Spirits</label>
                                            </div>

                                                <div id="spiritsNeeds" class="{{ count($spirits)>=1 ? '':'hidden'}}">
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <div class="col-xs-4" style="padding-left: 0">
                                                                <label>Spirit Name</label>
                                                            </div>
                                                            <div class="col-xs-4" style="padding-left: 0">
                                                                <label>Spirit Brand</label> 
                                                            </div>
                                                        </div>

                                                        @if(count($spirits)>=1)
                                                        <div id="spiritsContent" class="col-xs-12">
                                                            @foreach($spirits as $spirit)
                                                                <input type="text" name="spiritsName0" value="{{ $spirit->name }}">
                                                                <input type="text" name="spiritsList0" value="{{ $spirit->brand }}">
                                                                <input type="number" name="spiritsNumber0" value="{{$spirit->quantity}}">
                                                            @endforeach
                                                            <input id="spiritscounter" type="hidden" name="spiritscounter" value="1">
                                                        </div>
                                                        @else
                                                        <div id="spiritsContent" class="col-xs-12">
                                                            <input type="text" name="spiritsName0">
                                                            <input type="text" name="spiritsList0">
                                                            <input type="number" name="spiritsNumber0">
                                                            <input id="spiritscounter" type="hidden" name="spiritscounter" value="1">
                                                        </div>
                                                        @endif
                                                    

                                                        <div class="col-xs-12">
                                                            <button id="addspirits" type="button" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Add another spirit</button>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                </div>

                                            <div class="col-xs-10">
                                                <input type="checkbox" name="cocktails" id="cocktails" {{ count($cocktails)>=1? 'checked':'' }}>
                                                <label for="cocktails">Cocktails</label>
                                            </div>

                                                <div id="cocktailsNeeds" class="{{ count($cocktails)>=1? '':'hidden' }}">
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <label>Cocktail Name</label>    
                                                        </div>

                                                        @if(count($cocktails)>=1)
                                                        <div id="cocktailsContent" class="col-xs-10">
                                                            @foreach($cocktails as $cocktail)
                                                            <input type="text" name="cocktailsName0" value="{{ $cocktail->name }}">
                                                            <input type="number" name="cocktailsNumber0" value="{{ $cocktail->quantity }}">
                                                            @endforeach
                                                            <input id="cocktailscounter" type="hidden" name="cocktailscounter" value="{{ count($cocktails) }}">
                                                        </div>
                                                        @else
                                                        <div id="cocktailsContent" class="col-xs-10">
                                                            <input type="text" name="cocktailsName0">
                                                            <input type="number" name="cocktailsNumber0">
                                                            <input id="cocktailscounter" type="hidden" name="cocktailscounter" value="1">
                                                        </div>
                                                        @endif

                                                        <div class="col-xs-12">
                                                            <button id="addcocktails" type="button" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Add another cocktail</button>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                </div>

                                            <div class="col-xs-12">
                                                <input type="checkbox" name="shots" id="shots" {{ count($shots)>=1? 'checked':'' }}>
                                                <label for="shots">Shots</label>
                                            </div>

                                                <div id="shotsNeeds" class="{{ count($shots)>=1? '':'hidden'}}">
                                                    <div class="row">
                                                        <div class="col-xs-12">
                                                            <label>Shot Name</label>    
                                                        </div>

                                                        @if(count($shots)>=1)
                                                        <div id="shotsContent" class="col-xs-10">
                                                            @foreach($shots as $shot)
                                                            <input type="text" name="shotsName0" value="{{ $shot->name }}">
                                                            <input type="number" name="shotsNumber0" value="{{ $shot->quantity }}">
                                                            @endforeach
                                                            <input id="shotscounter" type="hidden" name="shotscounter" value="{{ count($shots) }}">
                                                        </div>
                                                        @else
                                                        <div id="shotsContent" class="col-xs-10">
                                                            <input type="text" name="shotsName0">
                                                            <input type="number" name="shotsNumber0">
                                                            <input id="shotscounter" type="hidden" name="shotscounter" value="1">
                                                        </div>
                                                        @endif

                                                        <div class="col-xs-12">
                                                            <button id="addshots" type="button" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Add another shot</button>
                                                        </div>
                                                    </div>
                                                </div>
                                        </div>
                                    </div>
                                </div>

                                <a id="showsupplies" class="showButton col-xs-12"><span id="suppliesarrow" class="glyphicon glyphicon-triangle-{{ count($softs)>=1 || count($ingredients)>=1 || isset($event) &&  $event->barEvent !== null && $event->barEvent->ice == 1? 'bottom':'right'}}"> Supplies</span></a>

                                <div id="suppliesNeeds" class="{{ count($softs)>=1 || count($ingredients)>=1 || isset($event) && $event->barEvent !== null && $event->barEvent->ice == 1? '':'hidden'}}">
                                    <div class="row">
                                        <hr>
                                        <div class="col-xs-10 col-xs-offset-1">

                                            <div class="col-xs-12">
                                                <input type="checkbox" name="ice" id="ice" {{ isset($event) && $event->barEvent !== null && $event->barEvent->ice == 1 ? 'checked':'' }}>
                                                <label for="ice">Ice</label>
                                            </div>

                                            <div class="col-xs-12">
                                                <input type="checkbox" name="softs" id="softs" {{ count($softs)>=1 ? 'checked':'' }}>
                                                <label for="softs">Soft</label>
                                            </div>

                                            <div id="softsNeeds" class="{{ count($softs)>=1 ? '':'hidden' }}">
                                                <div class="row">
                                                    
                                                    <div class="col-xs-12">
                                                        <label>Soft Name</label>    
                                                    </div>

                                                    @if(count($softs)>=1)
                                                    <div id="softsContent" class="col-xs-10">
                                                        @foreach($softs as $soft)
                                                        <input type="text" name="softsName0" value="{{ $soft->name }}">
                                                        <input type="number" name="softsNumber0" value="{{ $soft->quantity }}">
                                                        @endforeach
                                                        <input id="softscounter" type="hidden" name="softscounter" value="{{ count($softs) }}">
                                                    </div>
                                                    @else
                                                    <div id="softsContent" class="col-xs-10">
                                                        <input type="text" name="softsName0">
                                                        <input type="number" name="softsNumber0">
                                                        <input id="softscounter" type="hidden" name="softscounter" value="1">
                                                    </div>
                                                    @endif

                                                    <div class="col-xs-12">
                                                        <button id="addsofts" type="button" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Add another soft</button>
                                                    </div>
                                                </div>
                                                <hr>
                                            </div>

                                            <div class="col-xs-12">
                                                <input type="checkbox" name="ingredients" id="ingredients" {{ count($ingredients)>=1 ? 'checked':'' }}>
                                                <label for="ingredients">Ingredient</label>
                                            </div>

                                            <div id="ingredientsNeeds" class="{{ count($ingredients)>=1 ? '':'hidden' }}">
                                                <div class="row">
                                                    
                                                    <div class="col-xs-12">
                                                        <label>Ingredient Name</label>  
                                                    </div>

                                                    @if(count($ingredients)>=1)
                                                    <div id="ingredientsContent" class="col-xs-10">
                                                        @foreach($ingredients as $ingredient)
                                                        <input type="text" name="ingredientsName0" value="{{ $ingredient->name }}">
                                                        <input type="number" name="ingredientsNumber0" value="{{ $ingredient->quantity }}">
                                                        @endforeach
                                                        <input id="ingredientscounter" type="hidden" name="ingredientscounter" value="{{ count($ingredients) }}">
                                                    </div>
                                                    @else
                                                    <div id="ingredientsContent" class="col-xs-10">
                                                        <input type="text" name="ingredientsName0">
                                                        <input type="number" name="ingredientsNumber0">
                                                        <input id="ingredientscounter" type="hidden" name="ingredientscounter" value="1">
                                                    </div>
                                                    @endif

                                                    <div class="col-xs-12">
                                                        <button id="addingredients" type="button" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Add another ingredient</button>
                                                    </div>
                                                </div>
                                                <hr>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <a id="showglasses" class="showButton col-xs-12"><span id="glassesarrow" class="glyphicon glyphicon-triangle-{{ isset($event) && $event->barEvent !== null  && $event->barEvent->glass_type != '' && $event->barEvent->glass_type != 'none' ? 'bottom':'right'}}"> Glasses </span></a>

                                <div id="glassesNeeds" class="{{ isset($event) && $event->barEvent !== null && $event->barEvent->glass_type != '' && $event->barEvent->glass_type != 'none' ? '':'hidden' }}">
                                    <div class="row">
                                    <hr>
                                        <div class="col-xs-10 col-xs-offset-1">
                                            
                                            <input type="radio" name="glassChoice" value="none" id="noGlasses" {{ isset($event) && $event->barEvent !== null && $event->barEvent->glass_type !== null && $event->barEvent->glass_type == 'none' ? 'checked':'' }}>
                                            <label for="noGlasses">None</label>                                     
                                            <input type="radio" name="glassChoice" value="glass" id="glassGlasses" {{ isset($event) && $event->barEvent !== null  && $event->barEvent->glass_type !== null && $event->barEvent->glass_type == 'glass' ? 'checked':'' }}>
                                            <label for="glassGlasses">Glass</label>
                                            
                                            <input type="radio" name="glassChoice" value="plastic" id="plasticGlasses" {{ isset($event) && $event->barEvent !== null  && $event->barEvent->glass_type !== null && $event->barEvent->glass_type == 'plastic' ? 'checked':'' }}>
                                            <label for="plasticGlasses">Plastic</label>

                                        </div>
                                    </div>
                                </div>

                                <a id="showequipment" class="showButton col-xs-12"><span id="equipmentarrow" class="glyphicon glyphicon-triangle-{{ count($bars)>=1 || count($furnitures)>=1 || isset($event) && $event->barEvent !== null && $event->barEvent->bar_number != 0 ? 'bottom':'right'}}"> Equipment </span></a>

                                <div id="equipmentNeeds" class="{{ count($bars)>=1 || count($furnitures)>=1 || isset($event) && $event->barEvent !== null && $event->barEvent->bar_number != 0 ? '':'hidden' }}">
                                    <div class="row">
                                    <hr>
                                        <div class="col-xs-10 col-xs-offset-1">
                                            
                                            <div class="col-xs-12">
                                                <input type="checkbox" name="bars" id="bars" {{ isset($event) && $event->barEvent !== null  && $event->barEvent->bar_number >= 1 ? 'checked':'' }}>
                                                <label for="bars">Bar</label>
                                            </div>

                                            <div id="barsNeeds" class="{{ isset($event) && $event->barEvent !== null && $event->barEvent->bar_number >= 1 ? '':'hidden' }}">
                                                <div class="row">

                                                    <div class="col-xs-12">
                                                        <label for="barNumber">Bar number</label>
                                                        <input type="number" name="barNumber" id="barNumber" value="{{ isset($event) && $event->barEvent !== null  && $event->barEvent->bar_number >0 ? $event->barEvent->bar_number : '' }}">
                                                    </div>

                                                    <div class="col-xs-12">
                                                        <label>Bar Type</label> 
                                                    </div>

                                                    @if(count($bars)>=1) 
                                                    <div id="barsContent" class="col-xs-10">
                                                            @foreach($bars as $bar)
                                                            <input type="text" name="barsName0" value="{{$bar->name}}">
                                                            <input type="number" name="barsNumber0" value="{{ $bar->quantity }}">
                                                            @endforeach
                                                        <input id="barscounter" type="hidden" name="barscounter" value="{{ count($bars) }}">
                                                    </div>
                                                    @else
                                                    <div id="barsContent" class="col-xs-10">
                                                        <input type="text" name="barsName0">
                                                        <input type="number" name="barsNumber0">
                                                        <input id="barscounter" type="hidden" name="barscounter" value="1">
                                                    </div>
                                                    @endif

                                                    <div class="col-xs-12">
                                                        <button id="addbars" type="button" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Add another bar</button>
                                                    </div>
                                                </div>
                                                <hr>
                                            </div>

                                            <div class="col-xs-12">
                                                <input type="checkbox" name="furnitures" id="furnitures" {{ count($furnitures)>=1 ? 'checked':'' }}>
                                                <label for="furnitures">Furniture</label>
                                            </div>

                                            <div id="furnituresNeeds" class="{{ count($furnitures)>=1 ? '':'hidden' }}">
                                                <div class="row">

                                                    <div class="col-xs-12">
                                                        <label>Furniture Type</label>   
                                                    </div>

                                                    @if(count($furnitures)>=1)
                                                    <div id="furnituresContent" class="col-xs-10">
                                                        @foreach($furnitures as $furniture)
                                                        <input type="text" name="furnituresName0" value="{{ $furniture->name }}">
                                                        <input type="number" name="furnituresNumber0" value="{{ $furniture->quantity }}">
                                                        @endforeach
                                                        <input id="furniturescounter" type="hidden" name="furniturescounter" value="{{ count($furnitures) }}">
                                                    </div>
                                                    @else
                                                    <div id="furnituresContent" class="col-xs-10">
                                                        <input type="text" name="furnituresName0">
                                                        <input type="number" name="furnituresNumber0">
                                                        <input id="furniturescounter" type="hidden" name="furniturescounter" value="1">
                                                    </div>
                                                    @endif

                                                    <div class="col-xs-12">
                                                        <button id="addfurnitures" type="button" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> Add another furniture</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Staff Section -->

                            <div class="col-xs-12">
                                <a id="showstaffFunction" class="showButton col-xs-12"><span id="staffFunctionarrow" class="glyphicon glyphicon-triangle-right"> Staff</span></a>
                            </div>

                            <div id="staffFunctionNeeds" class="hidden">

                                <div class="form-group col-xs-12 {{ $errors->has('start_times.0.time') ? 'has-error' : '' }}">
                                    <label for="start_time">Start time</label>
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6 start-time-repeater">
                                            <div data-repeater-list="start_times">
                                            @if(isset($event) && !is_null($event->id) && !empty($event->start_time))
                                                @foreach($event->start_time as $key => $time)
                                                    <div data-repeater-item>
                                                        <div class="input-group date" id="time">
                                                            <span class="input-group-addon">
                                                                <span class="glyphicon glyphicon-time"></span>
                                                            </span>
                                                            <input type="text" class="form-control" name="time" value="{{ $time  }}"/>
                                                            <span data-repeater-delete class="input-group-addon">
                                                                <span class="glyphicon glyphicon-remove"></span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div data-repeater-item>
                                                    <div class="input-group date" id="time">
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                        </span>
                                                        <input type="text" class="form-control" name="time"/>
                                                        <span data-repeater-delete class="input-group-addon">
                                                            <span class="glyphicon glyphicon-remove"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            @endif
                                            </div>
                                            <span data-repeater-create class="btn btn-success btn-sm">
                                                <span class="glyphicon glyphicon-plus"></span> Add
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-xs-12 col-md-6">
                                    <label for="finish_time">Finish time</label>
                                    <div class="input-group date" id="time">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                        <input type="text" class="form-control" name="finish_time" value="{{ $event->finish_time or old('finish_time') }}"/>
                                    </div>
                                </div>

                                <!-- Piece of code that will be remplaced --> 

                                <div class="col-xs-12 col-md-6 form-group {{ $errors->has('number_staff') ? 'has-error' : '' }}">
                                    <label for="number_staff">Staff required</label>
                                    <input type="number" name="number_staff" id="number_staff" class="form-control" value="{{ $event->number_staff or old('number_staff') }}">
                                </div>

                                <!-- Piece of code that need to be updated instead -->

                                <!-- 
                                <div class="col-xs-12 form-group {{ $errors->has('number_staff') ? 'has-error' : '' }}">
                                    <label for="number_staff">Staff required</label>
                                    <div class="staff-added">
                                        
                                    </div>

                                </div>

                                @if(isset($roles))
                                <div class="col-xs-12 form-group {{ $errors->has('number_staff') ? 'has-error' : '' }}">
                                    <label for="number_staff">Add staff role</label>
                                    <div class="row">
                                        @foreach($roles as $role)
                                            <div class="staffing col-md-6" style="margin-bottom: 5px;">
                                                <a style="text-align: left;word-spacing: -10px;" class="btn btn-info btn-block staff-adding">
                                                    <span class="glyphicon glyphicon-plus"> 
                                                        {{ $role->name }}
                                                    </span>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                
                                -->
                                <!-- End of piece of code that need to be updated -->

                                <div class="col-xs-12 form-group">
                                <label for="uniform">Uniform</label>
                                <select name="uniform" id="uniform" class="form-control">
                                    <option value="{{ $event->uniform or old('uniform') }}"></option>
                                    @foreach($uniforms as $uniform)
                                        @if(isset($event))
                                            <option {{ $uniform->id == $event->uniform ? 'selected' : '' }} value="{{ $uniform->id }}">{{ $uniform->set_name }}</option>
                                        @else
                                            <option {{ old('uniform') == $uniform->id ? 'selected' : '' }} value="{{ $uniform->id }}">{{ $uniform->set_name }}</option>
                                        @endif
                                    @endforeach
                                </select>

                            </div>
                                <!--END OF STAFF SECTION -->
                            </div>

                            @if(isset($event) && !is_null($event->id))
                            <div class="col-xs-12">    
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="notify-all"> Notify staff about the update
                                    </label>
                                </div>
                            </div>
                            @endif

                            <div class="col-xs-12">
                                <button class="create-event btn btn-primary btn-sm" type="submit">{{ isset($event) && !is_null($event->id) ? 'Update' : 'Create' }}</button>
                                <a href="{{ url('/event/') }}" class="btn btn-info btn-sm" role="button">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')

    <script type="text/javascript">
        var staffElt = document.getElementById('staff');
        
        //Make a div with more inputs to appear on screen
        function appearElt(element,arrow){
            document.getElementById(element).addEventListener('change', function(e){
                if(e.target.checked){
                    document.getElementById(element+"Needs").className = '';
                    if(arrow){
                        document.getElementById(element + 'arrow').className = "glyphicon glyphicon-triangle-bottom";
                    }
                } else {
                    document.getElementById(element+"Needs").className = 'hidden';
                    if(arrow){
                        document.getElementById(element + 'arrow').className = "glyphicon glyphicon-triangle-right";
                    }
                }   
            });
        }

        function showElt(element){
            var showLink = document.getElementById('show'+ element);
            var arrow = document.getElementById(element + 'arrow');

            showLink.addEventListener('click', function(){
                if(document.getElementById(element+"Needs").className == 'hidden'){
                    arrow.className = "glyphicon glyphicon-triangle-bottom";
                    document.getElementById(element+"Needs").className = '';
                    if(document.getElementById(element+"Needs").id == "barFunctionNeeds"){
                        var barservice = document.getElementById('glasses');
                        var checked = document.createAttribute('checked');
                        barservice.setAttributeNode(checked);
                    }
                }
                else if(document.getElementById(element+"Needs").className == '') {
                    arrow.className = "glyphicon glyphicon-triangle-right";
                    document.getElementById(element+"Needs").className = 'hidden';
                }
            });
        }

        //Add an input with text and number for adding product to list
        // If list = true, add a list that correspond to PHP list variable
        function addProduct(product,list){
            var counter = document.getElementById(product + 'counter');
            var ask = counter.value;

            document.getElementById('add'+product).addEventListener('click', function(){
                var productContent = document.getElementById(product+'Content');

                var newproductName = document.createElement('input');
                newproductName.type = "text";
                newproductName.name = product+"Name"+ask;
    
                var newproductNumber = document.createElement('input');
                newproductNumber.type = "number";
                newproductNumber.name = product+"Number"+ask;

                productContent.appendChild(newproductName);
                if(list){
                    var newproductList = document.createElement('input');
                    newproductList.type = "text";
                    newproductList.name = product+"List"+ask;
                    productContent.appendChild(newproductList); 
                }
                productContent.appendChild(newproductNumber);
                ask++;
                counter.setAttribute('value',ask);
            });
        }

        showElt('staffFunction');
        showElt('barFunction');

        showElt('staff');
        showElt('alcohol');
        showElt('supplies');
        showElt('glasses');
        showElt('equipment');

        appearElt('beers');
        appearElt('whine');
        appearElt('spirits');
        appearElt('cocktails');
        appearElt('shots');
        appearElt('softs');
        appearElt('ingredients');
        appearElt('bars');
        appearElt('furnitures');

        addProduct('beers');
        addProduct('cocktails');
        addProduct('whine',true);   
        addProduct('spirits',true);
        addProduct('shots');
        addProduct('softs');
        addProduct('ingredients');
        addProduct('bars');
        addProduct('furnitures');

    </script>

    <script type="text/javascript">
        $(document).ready(function(){

            var tempStaff;
            $('.staff-adding').on('click', function(){
                var value = $(this).text().trim().split(' ').join('-');
                tempStaff = $('.staff-added').append('<div class="staff input-group col-md-6"><span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span><input class="form-control input-success" value='+value+'></div>');
            });
            $('.create-event').on('click', function(){
                var numberOfStaff = $('.staff').length;
                $('.staff-added').append('<input type="hidden" name="number_staff" id="number_staff" class="form-control" value="'+numberOfStaff+'">');
            });
        });   
    </script>
@stop






