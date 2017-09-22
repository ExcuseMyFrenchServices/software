<?php
    $needed     = $event->number_staff;
    $assigned   = $event->assignments->count();
    $confirmed  = $event->assignments->where('status', 'confirmed')->count();
?>

@if(Auth::user()->role_id == 1 || Auth::user()->role_id == 13)
	@if (!$assigned)
	    <span class="label label-danger {{ $agent->isMobile()?'pull-right':'' }}">{{ $confirmed .' / '. $assigned .' / '. $needed }}</span>
	@else
	    @if($needed == $confirmed)
	        <span class="label label-success {{ $agent->isMobile()?'pull-right':'' }}">{{ $confirmed .' / '. $assigned .' / '. $needed }}</span>
	    @else
	        <span class="label label-warning {{ $agent->isMobile()?'pull-right':'' }}">{{ $confirmed .' / '. $assigned .' / '. $needed }}</span>
	    @endif
	@endif
@else
	<span class="{{ $agent->isMobile()?'pull-right':'' }} label label-info">{{ $needed }} staff</span>
@endif