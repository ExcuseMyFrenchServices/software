@extends('layouts.main')
@section('content')
	<div class="col-xs-12 col-sm-4" style="position: fixed">
		<a href="{{ url('reports')}}" class="btn btn-primary">
			<span class="glyphicon glyphicon-backward"></span> Back to reports section
		</a>
	</div>
	<div class="container">
		<nav aria-label="...">
			<ul class="pager">

				@if($last_month == 0)
				<li class="previous"><a href="{{ url('reports/month-report/'.$last_year.'/12') }}"><span aria-hidden="true">&larr;</span> Last Year</a></li>
				@else
				<li class="previous"><a href="{{ url('reports/month-report/'.$year.'/'.$last_month) }}"><span aria-hidden="true">&larr;</span> Last Month</a></li>			
				@endif

				@if($next_month == 13)
				<li class="next"><a href="{{ url('reports/month-report/'.$next_year.'/01') }}"> Next Year <span aria-hidden="true">&rarr;</span></a></li>
				@else
				<li class="next"><a href="{{ url('reports/month-report/'.$year.'/'.$next_month) }}">Next Month <span aria-hidden="true">&rarr;</span></a></li>
				@endif

			</ul>
		</nav>		
		<div class="panel panel-default">
			<div class="panel-heading">
				<h1>Number of events by clients on {{ $written_date }}</h1>
			</div>
			<div class="panel-body">
				<table class="col-xs-12 table table-striped table-bordered">
					<tr>
						<th>Client name</th>
						<th>Number of events</th>
					</tr>
					@foreach($events as $event)
						<tr><td>{{ $event->name }}</td><td>{{ $event->events_number}}</td></tr>
					@endforeach
				</table>
			</div>
			<div class="panel-footer">
				<p><b>Total : <span style="float:right; margin-right: 100px">{{ $total_events }}</span></b></p>	
			</div>
		</div>
	</div>
@stop