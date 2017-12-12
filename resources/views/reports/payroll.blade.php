@extends('layouts.main')

@section('content')
<div class="pull-left">
	<a style="font-size: 2em; margin-left: 10px" href="/payroll/{{ $user->id }}/{{ $lastWeek }}"><span class="glyphicon glyphicon-arrow-left"></span> Last Week</a>
</div>
<div class="pull-right">
	<a style="font-size: 2em;margin-right: 10px" href="/payroll/{{ $user->id }}/{{ $nextWeek }}">Next Week <span class="glyphicon glyphicon-arrow-right"></span></a>
</div>
<div class="container">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h1 style="text-align: center">{{ $user->profile->first_name }} Payroll - Week N°{{ $week }}</h1>
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
						<th>Total Hours</th>
						@if(Auth::user()->id == 119)
							<th>Total Pay</th>
						@endif
					</tr>
				</thead>
				<tbody>
					@php $total_pay = 0 @endphp
					@foreach($payrolls as $payroll)
						@if(date('W',strtotime($payroll->date)) == $week && date('Y', strtotime($payroll->date)) == $year)
							<tr>
								<td>{{ $payroll->isPublicHoliday ? 'Public Holiday':'' }}</td>
								<td>{{ date('l d/m/Y', strtotime($payroll->date)) }}</td>
								<td>{{ $payroll->event_name }}</td>
								<td>{{ $payroll->start }}</td>
								<td>{{ $payroll->break }} min</td>
								<td>{{ $payroll->travel_time ? $payroll->travel_time.' min' : 'No' }}</td>
								<td>{{ $payroll->end }}</td>
								<td>{{ $payroll->total_hour }}</td>
								@if(Auth::user()->id == 119)
									<td>${{ $payroll->pay }}*</td>
								@endif
							</tr>
							@php  $total_pay = $total_pay + $payroll->pay @endphp
						@endif
					@endforeach
				</tbody>
			</table>
		</div>
		<div class="panel-footer">
			@if(Auth::user()->role_id == 1)
				<a href="/reports/week-report/{{ $week }}" class="btn btn-success">See Week Report</a>
			@endif

			@if(Auth::user()->id == 119)
				<p>Total pay of the Week : ${{ $total_pay }}*</p>
			@endif
		</div>
	</div>
</div>
@stop