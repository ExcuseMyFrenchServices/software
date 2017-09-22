@extends('layouts.main')
@section('content')
	<div class="container-fluid">
	@if($agent->isMobile())
		<div class="container">
			<a href="/reports/week-report/{{ $previous }}" class="pull-left"> <span class="glyphicon glyphicon-arrow-left"></span> Previous Week </a>
			
			<a href="/reports/week-report/{{ $next }}" class="pull-right"><span class="glyphicon glyphicon-arrow-right"></span> Next Week</a>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h1 class="panel-title">Financial report from 
					<br>
					{{ date('D d F Y',strtotime($week_end)) }} 
					<br>
					to {{ date('D d F Y',strtotime($week_start)) }}</h1>
			</div>
			<div class="panel-body">
				@foreach($assignments as $assignment)
				<div class="panel panel-info">
					<div class="panel-heading">
						<h2 class="panel-title">
							<!-- Date -->
							<span class="badge">
									{{ $public_holiday[$assignment->event_id]}}
							</span> 
							{{ $event_date = date('l d/m/Y',strtotime($assignment->event_date)) }}
							-
							
							<!-- Event Name -->
							<a href="{{ url('event/'.$assignment->event_id) }}">{{ $event_name = $assignment->event_name }}</a>
						</h2>
					</div>
					<div class="panel-body">
						<p>
							<a href="{{url('user/'.$assignment->user_id.'/edit')}}">	
								{{ $assignment->first_name }} {{ $assignment->last_name }}
							</a>
							-
							Level {{ $assignment->level }}
						</p>
						<div class="row">
							<span class="col-xs-4">
								Start Time : {{str_replace('["','',str_replace('"]','',$assignment->start_time)) }}
							</span> 
							<span class='col-xs-4'>
								Finish Time : {{ $assignment->hours }}
							</span>
							<span class="col-xs-4">
								Break : {{ $assignment->break }}
							</span>
						</div>
						<br>
						<div class="row">
							@if($hours[$assignment->event_id.'-'.$assignment->user_id]['saturday'] == 0 && $hours[$assignment->event_id.'-'.$assignment->user_id]['sunday'] == 0 && $hours[$assignment->event_id.'-'.$assignment->user_id]['publicHoliday'] == 0)
								<span class="col-xs-4">
									Day Hours : {{$hours[$assignment->event_id.'-'.$assignment->user_id]['low']}}
								</span>
								<span class="col-xs-4">
									Evening Hours : {{$hours[$assignment->event_id.'-'.$assignment->user_id]['mid']}}
								</span>
								<span class="col-xs-4">
									Night Hours : {{$hours[$assignment->event_id.'-'.$assignment->user_id]['hight']}}
								</span>
							@elseif($hours[$assignment->event_id.'-'.$assignment->user_id]['saturday'] > 0)
								<span class="col-xs-4">
									Saturday Hours : {{$hours[$assignment->event_id.'-'.$assignment->user_id]['saturday']}}
								</span>
							@elseif($hours[$assignment->event_id.'-'.$assignment->user_id]['sunday'] > 0)
								<span class="col-xs-4">
									Saturday Hours : {{$hours[$assignment->event_id.'-'.$assignment->user_id]['sunday']}}
								</span>
							@else
								<span class="col-xs-4">
									Saturday Hours : {{$hours[$assignment->event_id.'-'.$assignment->user_id]['publicHoliday']}}
								</span>
							@endif
						</div>
					</div>
					<div class="panel-footer">
						<p>Bonus : {{$hours[$assignment->event_id.'-'.$assignment->user_id]['bonus']}}</p>
						<p>Total : {{$hours[$assignment->event_id.'-'.$assignment->user_id]['total']}}</p>
					</div>
				</div>
				@endforeach
			</div>
		</div>
	@else	
		<div class="container" style="font-size: 2em">
			<a href="/reports/week-report/{{ $previous }}" class="pull-left"> <span class="glyphicon glyphicon-arrow-left"></span> Previous Week </a>
			
			<a href="/reports/week-report/{{ $next }}" class="pull-right"><span class="glyphicon glyphicon-arrow-right"></span> Next Week</a>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h1>Financial report from {{ date('D d F Y',strtotime($week_end)) }} to {{ date('D d F Y',strtotime($week_start)) }}</h1>
			</div>
			<div class="panel-body">
			<table class="col-xs-12">
				<tr><th>Event Date</th><th>Event Name</th><th>Staff</th><th>Level</th><th>Start Time</th><th>Finish Time</th><th>< 4 hours</th><th>Break</th><th>7am - 7pm</th><th>7pm - 00am</th><th>00am - 7am</th><th>Saturday Hours</th><th>Sunday Hours</th><th>Public Holiday Hours</th><th>Bonus</th><th>Total</th></tr>
				<?php $total_cost = 0; ?>
				@foreach($assignments as $assignment)
						@if(!isset($event_date))
						<tr style="border-top:1px solid silver">
							<td><span class="badge">{{ $public_holiday[$assignment->event_id]}}</span> {{ $event_date = date('l d/m/Y',strtotime($assignment->event_date)) }}</td>
						@else
							@if($event_date != date('l d/m/Y',strtotime($assignment->event_date)))
						<tr style="border-top:1px solid silver">
							<td><span class="badge">{{ $public_holiday[$assignment->event_id]}}</span> {{ $event_date = date('l d/m/Y',strtotime($assignment->event_date)) }}</td>
							@elseif(isset($event_name) && $event_name != $assignment->event_name)
						<tr style="border-top:1px solid silver">
							<td><span class="badge">{{ $public_holiday[$assignment->event_id]}}</span> {{ $event_date = date('l d/m/Y',strtotime($assignment->event_date)) }}</td>														
							@else
						<tr>
							<td></td>
							@endif
						@endif
						@if(!isset($event_name)	)
								<td><a href="{{ url('event/'.$assignment->event_id) }}">{{ $event_name = $assignment->event_name }}</a></td>
						@else
							@if($event_name != $assignment->event_name)
								<td><a href="{{ url('event/'.$assignment->event_id) }}">{{ $event_name = $assignment->event_name }}</a></td>
							@else
								<td></td>
							@endif
						@endif
						<td><a href="{{url('user/'.$assignment->user_id.'/edit')}}">{{ $assignment->first_name }} {{ $assignment->last_name }}</a></td>
						<td>{{ $assignment->level }}</td>
						<td>{{str_replace('["','',str_replace('"]','',$assignment->start_time)) }}</td>
						<td>{{ $assignment->hours }}</td>
						<td>{{ $hours[$assignment->event_id.'-'.$assignment->user_id]['bonus'] > 0 ? 'Yes' : ''  }}</td>
						<td>{{ $assignment->break }}</td>
						<td>{{$hours[$assignment->event_id.'-'.$assignment->user_id]['low']}}</td>
						<td>{{$hours[$assignment->event_id.'-'.$assignment->user_id]['mid']}}</td>
						<td>{{$hours[$assignment->event_id.'-'.$assignment->user_id]['hight']}}</td>
						<td>{{$hours[$assignment->event_id.'-'.$assignment->user_id]['saturday']}}</td>
						<td>{{$hours[$assignment->event_id.'-'.$assignment->user_id]['sunday']}}</td>
						<td>{{$hours[$assignment->event_id.'-'.$assignment->user_id]['publicHoliday']}}</td>
						<td>{{$hours[$assignment->event_id.'-'.$assignment->user_id]['bonus']}}</td>
						<td>{{$hours[$assignment->event_id.'-'.$assignment->user_id]['total']}}</td>
					</tr>
				@endforeach
			</table>
			</div>
			<div class="panel-footer">
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