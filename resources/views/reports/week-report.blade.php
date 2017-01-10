@extends('layouts.main')
@section('content')
	<div class="container">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h1>Financial report from {{ date('D d F Y',strtotime($week_end)) }} to {{ date('D d F Y',strtotime($week_start)) }}</h1>
			</div>
			<div class="panel-body">
			<table class="col-xs-12">
				<tr><th>Event Date</th><th>Event Name</th><th>Staff</th><th>Level</th><th>Start Time</th><th>Finish Time</th><th>Hours spent</th><th>Cost</th></tr>
				<?php $cost = 0; ?>
				@foreach($assignments as $assignment)
						@if(!isset($event_date))
						<tr style="border-top:1px solid silver">
							<td>{{ $event_date = date('d/m/Y',strtotime($assignment->event_date)) }}</td>
						@else
							@if($event_date != date('d/m/Y',strtotime($assignment->event_date)))
						<tr style="border-top:1px solid silver">
							<td>{{ $event_date = date('d/m/Y',strtotime($assignment->event_date)) }}</td>
							@elseif(isset($event_name) && $event_name != $assignment->event_name)
						<tr style="border-top:1px solid silver">
							<td>{{ $event_date = date('d/m/Y',strtotime($assignment->event_date)) }}</td>														
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
						<td>{{ $start_time = str_replace('["','',str_replace('"]','',$assignment->start_time)) }}</td>
						<td>{{ $finish_time = $assignment->finish_time }}</td>
						<td>{{ $hours_spent = $finish_time - $start_time }}</td>
						@if($assignment->level == 1)
							<td>${{ $event_cost = $hours_spent*23 }}</td>
							<?php $cost += $event_cost ?>
						@elseif($assignment->level == 2)
							<td>${{ $event_cost = $hours_spent*24 }}</td>
							<?php $cost += $event_cost ?>
						@elseif($assignment->level == 3)
							<td>${{ $event_cost = $hours_spent*25 }}</td>
							<?php $cost += $event_cost ?>		
						@elseif($assignment->level == 4)
							<td>${{ $event_cost = $hours_spent*27 }}</td>
							<?php $cost += $event_cost ?>
						@endif
					</tr>
				@endforeach
			</table>
			</div>
			<div class="panel-footer">
				<p>Total cost :<span class="pull-right">${{ $cost }}</span></p>
			</div>
		</div>
	</div>
@stop