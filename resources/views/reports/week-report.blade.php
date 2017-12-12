@extends('layouts.main')
@section('content')
	<div class="container-fluid">
	@if($agent->isMobile())

	@else	
		<div class="pull-left">
			<a style="font-size: 2em; margin-left: 10px" href="/reports/week-report/{{ $last }}"><span class="glyphicon glyphicon-arrow-left"></span> Last Week</a>
		</div>
		<div class="pull-right">
			<a style="font-size: 2em;margin-right: 10px" href="/reports/week-report/{{ $next }}">Next Week <span class="glyphicon glyphicon-arrow-right"></span></a>
		</div>
		<div class="container">
			<div class="panel panel-default">
				<div class="panel-heading" style="text-align: center">
					<h2>{{ $start }} - {{ $end }}</h2>
					<h1>Week Report - Week N°{{ $week }}</h1>
				</div>
				<div class="panel-body">
					<table class="table table-hover">
						<thead>
							<tr>
								<th style="width: 625px">Staff</th>
								<th>Level</th>
								<th>Start</th>
								<th>Break</th>
								<th>End</th>
								<th>Worktime</th>
							</tr>
						</thead>
						@foreach($weekReports as $report)
						<tbody>
							@if( !isset($id) || $report->id != $id)
								<tr style="background-color: lightsteelblue">
									<td colspan="7" style="font-size: 1.5em">
										<a href="/event/{{ $report->id }}">
											<em>
												{{ $report->date }} - {{ $report->name }}
											</em>
										</a>
									</td>
									@php $id = $report->id @endphp
								</tr>
							@endif
							@foreach($report->staff as $staff)
							<tr>
								<td style="width: 625px">
									<a href="/payroll/{{ $staff->id }}/{{ $week }}">
										{{ $staff->first_name }} {{ $staff->last_name }}
									</a>
								</td>
								<td>{{ $staff->level }}</td>
								<td>{{ $staff->start_time }}</td>
								<td>{{ $staff->break }} min</td>
								<td>{{ $staff->end_time }}</td>
								<td>{{ $staff->worktime }} hours</td>
							</tr>
							@endforeach
						</tbody>
						@endforeach
					</table>
				</div>
			</div>
		</div>
	@endif
	</div>
@stop


@section('scripts')
	<script type="text/javascript">
        $(function () {
            $('#reportDate').datetimepicker({
            	format: "YYYY-MM-DD"
            });
        });
    </script>
@stop