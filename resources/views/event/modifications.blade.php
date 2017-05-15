@extends('layouts.main')
@section('content')

	<div id="page" class="container">
        <div class="row">
            <div class="col-xs-12 col-md-8 col-md-offset-2">
            	<div class="panel panel-default">
            		<div class="panel-heading">
            			<h3>Modifications on {{ $event->event_name }}</h3>
            		</div>
            		<div class="panel-body">
            			<div class="row">
            				<div class="col-xs-12">

            					<table class="table table-stripped">
            						<thead>
	            						<tr>
	            							<th>Date</th>
	            							<th>Role</th>
	            							<th>Name</th>
	            							<th>Action</th>
	            							<th>Old Value</th>
	            							<th>New Value</th>
	            							<th></th>
	            						</tr>
            						</thead>
            						<tbody>
            						@foreach($modifications as $modif)
	            						<tr>
	            							<td>{{ date_format(date_add($modif->created_at,date_interval_create_from_date_string('10hours')),'d/m/Y H:i')}}</td>
	            							<td>{{ $modif->role }}</td>
	            							<td>{{ $modif->name }}</td>
	            							<td>{{ str_replace('_',' ',$modif->modifications) }}</td>   
                                            <td>{{ $modif->old_value }}</td>
                                            <td>{{ $modif->new_value }}</td>
	            							<td><a href="{{ url('event/back-up/'.$event->id.'/'.$modif->id) }}" class="btn btn-danger btn-sm">Backup</a></td>
	            						</tr>
            						@endforeach
            						</tbody>
            					</table>

            					<a href="{{ url('event/'.$event->id) }}" class="btn btn-primary btn-sm">Back</a>

            				</div>
            			</div>
            		</div>
            	</div>
            </div>
        </div>
    </div>

@stop