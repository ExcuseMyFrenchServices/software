@extends('layouts.main')
@section('content')
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-5">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3>Number of events by months - {{ $last_year }}</h3>
					</div>
					<div class="panel-body">
						<div class="list-group">
							@for($i=1;$i<=12;$i++)
								<a type="button" class="list-group-item" href="{{ url('reports/month-report/'.$last_year.'/'.$i) }}">
										Month {{ $i }} : {{ $last_year_report[$i] }} events
								</a>
							@endfor
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-2">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3>Differences</h3>
					</div>
					<div class="panel-body">
						<ul class="list-group">
							@for($i=1;$i<=12;$i++)
								@if($i > $month)
									<li class="list-group-item list-group-item-info">
										Goal :
										{{ $last_year_report[$i] - $this_year_report[$i] }}
									</li>									
								@else		
									@if($this_year_report[$i] - $last_year_report[$i] > 0)
										<li class="list-group-item list-group-item-success">
											<span class="glyphicon glyphicon-arrow-up"></span>
											{{ $this_year_report[$i] - $last_year_report[$i] }}
										</li>
									@elseif($this_year_report[$i] - $last_year_report[$i] == 0)
										<li class="list-group-item list-group-item-warning">
											<span class="glyphicon glyphicon-resize-horizontal"></span>
											{{ $this_year_report[$i] - $last_year_report[$i] }}
										</li>								
									@else
										<li class="list-group-item list-group-item-danger">
											<span class="glyphicon glyphicon-arrow-down"></span>
											{{ $this_year_report[$i] - $last_year_report[$i] }}
										</li>									
									@endif
								@endif
							@endfor
						</ul>
					</div>
				</div>
			</div>				
			<div class="col-xs-12 col-sm-5">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3>Number of events by months - {{ $year }}</h3>
					</div>
					<div class="panel-body">
						<div class="list-group">
							@for($i=1;$i<=12;$i++)
								<a type="button" class="list-group-item" href="{{ url('reports/month-report/'.$year.'/'.$i) }}">
										Month {{ $i }} : {{ $this_year_report[$i] }} events
								</a>
							@endfor
						</div>
					</div>
				</div>
			</div>				
		</div>
	</div>

	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-5">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3>Number of events by months - {{ $last_last_year }}</h3>
					</div>
					<div class="panel-body">
						<div class="list-group">
							@for($i=1;$i<=12;$i++)
								<a type="button" class="list-group-item" href="{{ url('reports/month-report/'.$last_last_year.'/'.$i) }}">
										Month {{ $i }} : {{ $last_last_year_report[$i] }} events
								</a>
							@endfor
						</div>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-sm-2">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3>Differences</h3>
					</div>
					<div class="panel-body">
						<ul class="list-group">
							@for($i=1;$i<=12;$i++)		
								@if($last_year_report[$i] - $last_last_year_report[$i] > 0)
									<li class="list-group-item list-group-item-success">
										<span class="glyphicon glyphicon-arrow-up"></span>
										{{ $last_year_report[$i] - $last_last_year_report[$i] }}
									</li>
								@elseif($last_year_report[$i] - $last_last_year_report[$i] == 0)
									<li class="list-group-item list-group-item-warning">
										<span class="glyphicon glyphicon-resize-horizontal"></span>
										{{ $last_year_report[$i] - $last_last_year_report[$i] }}
									</li>								
								@else
									<li class="list-group-item list-group-item-danger">
										<span class="glyphicon glyphicon-arrow-down"></span>
										{{ $last_year_report[$i] - $last_last_year_report[$i] }}
									</li>									
								@endif
							@endfor
						</ul>
					</div>
				</div>
			</div>				
			<div class="col-xs-12 col-sm-5">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3>Number of events by months - {{ $last_year }}</h3>
					</div>
					<div class="panel-body">
						<div class="list-group">
							@for($i=1;$i<=12;$i++)
								<a type="button" class="list-group-item" href="{{ url('reports/month-report/'.$last_year.'/'.$i) }}">
										Month {{ $i }} : {{ $last_year_report[$i] }} events
								</a>
							@endfor
						</div>
					</div>
				</div>
			</div>				
		</div>
	</div>	
@stop