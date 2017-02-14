<?php
    $needed     = $event->number_staff;
    $assigned   = $event->assignments->count();
    $confirmed  = $event->assignments->where('status', 'confirmed')->count();
?>

@if(Auth::user()->role_id == 1)
	@if (!$assigned)
	    <span class="label label-danger">{{ $confirmed .' / '. $assigned .' / '. $needed }}</span>
	@else
	    @if($needed == $confirmed)
	        <span class="label label-success">{{ $confirmed .' / '. $assigned .' / '. $needed }}</span>
	    @else
	        <span class="label label-warning">{{ $confirmed .' / '. $assigned .' / '. $needed }}</span>
	    @endif
	@endif
@else
	<span class="label label-info">{{ $needed }}</span>
@endif