@extends('layouts.main')

@section('content')
<div class="pull-left">
	<a style="font-size: 2em; margin-left: 10px" href="/payroll/{{ $user->id }}/{{ $lastWeek }}"><span class="glyphicon glyphicon-arrow-left"></span> Last Week</a>
</div>
<div class="pull-right">
	<a style="font-size: 2em;margin-right: 10px" href="/payroll/{{ $user->id }}/{{ $nextWeek }}">Next Week <span class="glyphicon glyphicon-arrow-right"></span></a>
</div>
<div class="col-sm-10 col-sm-offset-1" style="{{ $agent->isMobile() ? 'margin-top: 80px;' : ''  }}">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h1 style="text-align: center">{{ $user->profile->first_name }} Roster - Week N°{{ $week }}</h1>
		</div>
		<div class="panel-body">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th></th>
						<th>Date</th>
						<th>Event</th>
						<th>Start Time</th>
						<th>Break</th>
						<th>Travel Time</th>
						<th>Finish Time</th>
						<th>7am-7pm</th>
						<th>7pm-00am</th>
						<th>00am-7am</th>
						<th>Saturday</th>
						<th>Sunday</th>
						<th>Public Holiday</th>
						<th>Total Hours</th>
					</tr>
				</thead>
				<tbody>
					@php
						$low = 0;
						$high = 0;
						$very_high = 0;
						$saturday = 0;
						$sunday = 0;
						$public_holiday = 0; 
						$total = 0 
					@endphp
					@foreach($payrolls as $payroll)
						@if(date('W',strtotime($payroll->date)) == $week && date('Y', strtotime($payroll->date)) == $year)
							<tr>
								<td>{{ $payroll->isPublicHoliday ? 'Public Holiday':'' }}</td>
								<td>{{ date('l d/m/Y', strtotime($payroll->date)) }}</td>
								<td><a href="/event/{{ $payroll->event_id }}" >{{ $payroll->event_name }}</a></td>
								<td>{{ $payroll->start }}</td>
								<td>{{ $payroll->break ? $payroll->break.'min' : 'No' }}</td>
								<td>{{ $payroll->travel_time ? $payroll->travel_time.' min' : 'No' }}</td>
								<td>{{ $payroll->end }}</td>
								<td>{{ $payroll->hours->low }}</td>
								<td>{{ $payroll->hours->high }}</td>
								<td>{{ $payroll->hours->very_high }}</td>
								<td>{{ $payroll->hours->saturday }}</td>
								<td>{{ $payroll->hours->sunday }}</td>
								<td>{{ $payroll->hours->public_holiday }}</td>
								<td>{{ $payroll->total_hour }}</td>
							</tr>
							@php  
								$low += $payroll->hours->low;
								$high += $payroll->hours->high;
								$very_high += $payroll->hours->very_high;;
								$saturday += $payroll->hours->saturday;
								$sunday += $payroll->hours->sunday;
								$public_holiday += $payroll->hours->public_holiday; 
								$total += $payroll->total_hour;
							@endphp
						@endif
					@endforeach
				</tbody>
			</table>
		</div>
		<div class="panel-footer">
			<p>Total <b>day</b> rate (7am-7pm): {{ $low }}</p>
			<p>Total <b>evening</b> rate (7pm-00am): {{ $high }}</p>
			<p>Total <b>night</b> rate (00am-7am): {{ $very_high }}</p>
			<p>Total <b>saturday</b> rate: {{ $saturday }}</p>
			<p>Total <b>sunday</b> rate: {{ $sunday }}</p>
				<p>Total <b>public holiday</b> rate: {{ $public_holiday }}</p>
			<p><b>Total Hours</b> : {{ $total }}</p>
			@if(Auth::user()->role_id == 1)
				<a href="/reports/week-report/{{ $week }}" class="btn btn-success">See Week Report</a>
			@endif
		</div>
	</div>
</div>
@stop