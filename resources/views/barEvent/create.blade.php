@extends('layouts.main')

@section('styles')
	<style type="text/css">
		.panel 
		{
			margin-bottom: 20px;
		}
		.showButton
		{
			color: black;
			font-size: 1.5em;
		}
		.showButton:hover
		{
			color: gray;
		}
		.btn-success
		{
			margin-top: 25px;
			margin-bottom: 25px;
		}
		label
		{
			margin-top: 30px;
		}
		input
		{
			margin-top: 30px;
		}
		textarea
		{
			border: 2px solid silver;
			border-radius: 5px;
			padding: 5px;
			padding-left: 10px;		
		}
		textarea:focus
		{
			border-color: lightblue;
			box-shadow: 0px 0px 5px 1px lightblue inset;
		}
		input[type=text]
		{
			border: none;
			border-bottom: 2px solid silver;
			border-radius: 3px;
			padding: 5px;
			padding-left: 10px;
		}
		input[type=text]:focus
		{
			color: darkgray;
			border-color: lightblue;
		}
		input[type=number]
		{
			border: none;
			border-bottom: 2px solid silver;
			border-radius: 3px;
			padding: 5px;
			padding-left: 10px;
		}
		input[type=number]:focus
		{
			color: darkgray;
			border-color: lightblue;
		}
		#event_info
		{
			position: fixed;
		}
		@media (max-width: 768px)
		{
			#event_info
			{
				position: initial;
				margin-bottom: 0px;
			}	
		}
	</style>
@stop

@section('content')
	<div class="container">
		<div class="row">
			<div class="panel panel-default" style="margin-top: 50px">
				<div class="panel-heading">
					<h3>Bar Event Creation</h3>
				</div>
				<div class="panel-body">
					<div id="event_info" class="col-xs-12 col-sm-2 panel panel-default">
						<div class="panel-heading row">
							<h4>Event Informations</h4>
						</div>
						<div class="panel-body">
							<ul class="list-group list-unstyled">
								<li><b>Client Name :</b> {{ $event->client->name }}</li>
								<li><b>Phone :</b> {{ $event->client->phone_number }}</li>
								<li><b>Email :</b> {{ $event->client->email }}</li>
								<li><b>Event Date :</b> {{ date_format(date_create($event->event_date), 'l jS F') }}</li>
								<li><b>Start Time :</b> {{ $event->start_time[0] }}</li>
								<li><b>Finish Time :</b> {{ $event->finish_time }} </li>
								<li><b>Staff Required:</b> {{ $event->number_staff }}</li>
								<li><b>Number of Guests : {{ $event->guest_number }}</b></li>
								<li><b>Notes :</b> {{ $event->notes }}</li>
							</ul>
						</div>
					</div>
					<div class="col-xs-12 col-sm-8 col-sm-offset-4">
						<form action="{{ url('/bar-event/create/'.$barEvent->id) }}" method="POST">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<div id="needs" class="panel panel-default">
								<div class="panel-heading">
									<h4>Client Needs</h4>
								</div>
								<div class="panel-body">
									<input type="checkbox" name="staff" id="staff" {{ $barEvent->private > 0 || $barEvent->bar_back > 0 || $barEvent->bar_runner > 0 || $barEvent->classic_bartender > 0 || $barEvent->cocktail_bartender > 0 || $barEvent->flair_bartender > 0 || $barEvent->mixologist > 0 ? 'checked':''}}>
									<label for="staff">Staff</label>

									<input type="checkbox" name="alcohol" id="alcohol" {{ count($beers)>=1 || count($whines)>=1 || count($spirits)>=1 || count($shots)>=1 || count($cocktails)>=1 ? 'checked':''}}>
									<label for="alcohol">Alcohol</label>

									<input type="checkbox" name="supplies" id="supplies" {{ count($softs)>=1 || count($ingredients)>=1 || $barEvent->ice == 1? 'checked':''}}>
									<label for="supplies">Supplies</label>

									<input type="checkbox" name="glasses" id="glasses" {{ $barEvent->glass_type != '' && $barEvent->glass_type != 'none' ? 'checked':''}}>
									<label for="glasses">Glasses</label>

									<input type="checkbox" name="equipment" id="equipment" {{ count($bars)>=1 || count($furnitures)>=1 || $barEvent->bar_number != 0 ? 'checked':''}}>
									<label for="equipment">Equipment</label>
								</div>
							</div>

							<div class="panel panel-default">
								<div class="panel-heading">
									<h4>Bar Function</h4>
								</div>
								<div class="panel-body">
									<a id="showstaff" class="showButton col-xs-12"><span id="staffarrow" class="glyphicon glyphicon-triangle-{{ $barEvent->private > 0 || $barEvent->bar_back > 0 || $barEvent->bar_runner > 0 || $barEvent->classic_bartender > 0 || $barEvent->cocktail_bartender > 0 || $barEvent->flair_bartender > 0 || $barEvent->mixologist > 0 ? 'bottom':'right'}}"> Staff </span></a>

									<div id="staffNeeds" class="{{ $barEvent->private > 0 || $barEvent->bar_back > 0 || $barEvent->bar_runner > 0 || $barEvent->classic_bartender > 0 || $barEvent->cocktail_bartender > 0 || $barEvent->flair_bartender > 0 || $barEvent->mixologist > 0 ? '':'hidden'}}">
										<div class="row">
										<hr>
										<div id="staffMessage">
											
										</div>
										<div class="col-xs-10 col-xs-offset-1">
												<div class="col-xs-12">
													<div class="col-xs-6">
														<label for="private">Private</label>
													</div>
													<input id="privateNumber" type="number" name="privateNumber" value="{{ $barEvent->private }}">
												</div>

												<div class="col-xs-12">
													<div class="col-xs-6">
														<label for="barBack">Bar Back</label>
													</div>
													<input id="barBackNumber" type="number" name="barBackNumber" value="{{ $barEvent->bar_back }}">
												</div>

												<div class="col-xs-12">
													<div class="col-xs-6">
														<label for="barRunner">Bar Runner</label>
													</div>
													<input id="barRunnerNumber" type="number" name="barRunnerNumber" value="{{ $barEvent->bar_runner }}">
												</div>

												<div class="col-xs-12">
													<div class="col-xs-6">
														<label for="classicBartender">Classic Bartender</label>
													</div>
													<input id="classicBartenderNumber" type="number" name="classicBartenderNumber" value="{{ $barEvent->classic_bartender }}">
												</div>

												<div class="col-xs-12">
													<div class="col-xs-6">
														<label for="cocktailBartender">Cocktail Bartender</label>
													</div>
													<input id="cocktailBartenderNumber" type="number" name="cocktailBartenderNumber" value="{{ $barEvent->cocktail_bartender }}">
												</div>

												<div class="col-xs-12">
													<div class="col-xs-6">
														<label for="flairBartender">Flair Bartender</label>
													</div>
													<input id="flairBartenderNumber" type="number" name="flairBartenderNumber" value="{{ $barEvent->flair_bartender }}">
												</div>

												<div class="col-xs-12">
													<div class="col-xs-6">
														<label for="mixologist">Mixologist</label>
													</div>
													<input id="mixologistNumber" type="number" name="mixologistNumber" value="{{ $barEvent->mixologist }}">	
												</div>
											</div>
										</div>
									</div>

									<a id="showalcohol" class="showButton col-xs-12"><span id="alcoholarrow" class="glyphicon glyphicon-triangle-{{ count($beers)>=1 || count($whines)>=1 || count($spirits)>=1 || count($shots)>=1 || count($cocktails)>=1 ? 'bottom':'right'}}"> Alcohol </span></a>

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

									<a id="showsupplies" class="showButton col-xs-12"><span id="suppliesarrow" class="glyphicon glyphicon-triangle-{{ count($softs)>=1 || count($ingredients)>=1 || $barEvent->ice == 1? 'bottom':'right'}}"> Supplies</span></a>

									<div id="suppliesNeeds" class="{{ count($softs)>=1 || count($ingredients)>=1 || $barEvent->ice == 1? '':'hidden'}}">
										<div class="row">
											<hr>
											<div class="col-xs-10 col-xs-offset-1">

												<div class="col-xs-12">
													<input type="checkbox" name="ice" id="ice" {{ $barEvent->ice == 1 ? 'checked':'' }}>
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

									<a id="showglasses" class="showButton col-xs-12"><span id="glassesarrow" class="glyphicon glyphicon-triangle-{{ $barEvent->glass_type != '' && $barEvent->glass_type != 'none' ? 'bottom':'right'}}"> Glasses </span></a>

									<div id="glassesNeeds" class="{{ $barEvent->glass_type != '' && $barEvent->glass_type != 'none' ? '':'hidden' }}">
										<div class="row">
										<hr>
											<div class="col-xs-10 col-xs-offset-1">
												
												<input type="radio" name="glassChoice" value="none" id="noGlasses" {{ $barEvent->glass_type !== null && $barEvent->glass_type == 'none' ? 'checked':'' }}>
												<label for="noGlasses">None</label>										
												<input type="radio" name="glassChoice" value="glass" id="glassGlasses" {{ $barEvent->glass_type !== null && $barEvent->glass_type == 'glass' ? 'checked':'' }}>
												<label for="glassGlasses">Glass</label>
												
												<input type="radio" name="glassChoice" value="plastic" id="plasticGlasses" {{ $barEvent->glass_type !== null && $barEvent->glass_type == 'plastic' ? 'checked':'' }}>
												<label for="plasticGlasses">Plastic</label>

											</div>
										</div>
									</div>

									<a id="showequipment" class="showButton col-xs-12"><span id="equipmentarrow" class="glyphicon glyphicon-triangle-{{ count($bars)>=1 || count($furnitures)>=1 || $barEvent->bar_number != 0 ? 'bottom':'right'}}"> Equipment </span></a>

									<div id="equipmentNeeds" class="{{ count($bars)>=1 || count($furnitures)>=1 || $barEvent->bar_number != 0 ? '':'hidden' }}">
										<div class="row">
										<hr>
											<div class="col-xs-10 col-xs-offset-1">
												
												<div class="col-xs-12">
													<input type="checkbox" name="bars" id="bars" {{ $barEvent->bar_number >= 1 ? 'checked':'' }}>
													<label for="bars">Bar</label>
												</div>

												<div id="barsNeeds" class="{{ $barEvent->bar_number >= 1 ? '':'hidden' }}">
													<div class="row">

														<div class="col-xs-12">
															<label for="barNumber">Bar number</label>
															<input type="number" name="barNumber" id="barNumber" value="{{ $barEvent->bar_number >0 ? $barEvent->bar_number : '' }}">
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
							</div>

							<div id="noteNeeds" style="margin-bottom: 50px" class="col-xs-12">
								<label for="notes">Specific Requirements</label>
								<textarea rows="5" id="notes" name="notes" class="form-control">{{ $barEvent->notes or old('barNotes')}}</textarea>
							</div>

							<div class="col-xs-12">
								<a class="btn btn-info btn-sm" href="{{ url('/event') }}">Back</a>
								<button class="btn btn-primary btn-sm" type="submit">Update Draft</button>
								@if($barEvent->status == 1)
								<a class="btn btn-success btn-sm" href="{{ url('/bar-event/confirm/'.$barEvent->id) }}">Confirm Bar Event</a>
								@endif
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
					document.getElementById(element).checked = true;
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

		appearElt('staff',true);
		appearElt('alcohol',true);
		appearElt('supplies',true);
		appearElt('glasses',true);
		appearElt('equipment',true);

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
@stop