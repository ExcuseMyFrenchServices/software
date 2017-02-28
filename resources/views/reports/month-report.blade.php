@extends('layouts.main')
@section('content')
	<div class="col-xs-12 col-sm-4" style="position: fixed">
		<a href="{{ url('reports/month-report')}}" class="btn btn-primary">
			<span class="glyphicon glyphicon-backward"></span> Back to reports section
		</a>
	</div>
	<div class="container">
		<nav aria-label="...">
			<ul class="pager">

				@if($last_month == 0)
				<li class="previous"><a href="{{ url('reports/month-report/'.$last_year.'/12/'.$order) }}"><span aria-hidden="true">&larr;</span> Last Year</a></li>
				@else
				<li class="previous"><a href="{{ url('reports/month-report/'.$year.'/'.$last_month.'/'.$order) }}"><span aria-hidden="true">&larr;</span> Last Month</a></li>			
				@endif

				@if($next_month == 13)
				<li class="next"><a href="{{ url('reports/month-report/'.$next_year.'/01/'.$order) }}"> Next Year <span aria-hidden="true">&rarr;</span></a></li>
				@else
				<li class="next"><a href="{{ url('reports/month-report/'.$year.'/'.$next_month.'/'.$order) }}">Next Month <span aria-hidden="true">&rarr;</span></a></li>
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
						<th rowspan="2">Total</th>
						<th>Events</th>
						<th>Staff</th>
						<th>Days worked</th>
					</tr>
					@foreach($total_events as $total_event)
					<tr>
						<td>{{ $total_event->events_number }}</td>
						<td>{{ $total_event->staff_number }}</td>
						<td>{{ $total_event->days_worked }} / <b>{{ $days_numbers_in_month }}<b></td>
					</tr>
					@endforeach
				</table>
				<table class="col-xs-12 table table-striped table-bordered">
					<tr>
						<th>Client name</th>
						<th>
							@if($order != "events_number")
								<a href="{{ url('reports/month-report/'.$year.'/'.$month.'/events_number') }}"> 
									Number of events
								</a>
							@else
								Number of events <span class="pull-right glyphicon glyphicon-arrow-down"></span>
							@endif
						</th>
						<th>
							@if($order != "staff_number")
								<a href="{{ url('reports/month-report/'.$year.'/'.$month.'/staff_number') }}">
									Staff
								</a>
							@else
								Staff <span class="pull-right glyphicon glyphicon-arrow-down"></span>
							@endif
						</th>
						<th>
							@if($order != "days_worked")
							<a href="{{ url('reports/month-report/'.$year.'/'.$month.'/days_worked') }}">
								Days Worked
							</a>
							@else
								Days Worked <span class="pull-right glyphicon glyphicon-arrow-down"></span>
							@endif
						</th>
					</tr>
					@foreach($events as $event)
						<tr><td>{{ $event->name }}</td><td>{{ $event->events_number}}</td><td>{{ $event->staff_number }}</td><td>{{ $event->days_worked }}</td></tr>
					@endforeach
				</table>
			</div>
			<div class="panel-footer">
			</div>
		</div>
	</div>
@stop