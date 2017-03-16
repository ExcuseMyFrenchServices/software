@extends('layouts.main')
@section('content')
	<div class="container">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h1>Uniforms</h1>
			</div>
			<div class="panel-body">
				<div class="list-group">
					<div class="row">
						<div class="list-group-item col-xs-10">
							<div class="col-xs-4" style="text-align: center">
								Set name :
							</div>
							<div class="col-xs-2" style="text-align: center">
								Top :
							</div>
							<div class="col-xs-2" style="text-align: center">
								Shirt :
							</div>
							<div class="col-xs-2" style="text-align: center">
								Pant :
							</div>
							<div class="col-xs-2" style="text-align: center">
								Shoes :
							</div>
						</div>
					</div>
					@foreach($uniforms as $uniform)
					<div class="row">
						<div class="list-group-item col-xs-10">
							<div class="col-xs-4" style="text-align: center">
								{{ $uniform->set_name }}
							</div>
							<div class="col-xs-2" style="text-align: center">
								{{ $uniform->jacket_color }} {{ $uniform->jacket }}
							</div>
							<div class="col-xs-2" style="text-align: center">
								{{ $uniform->shirt_color }} {{ $uniform->shirt }}
							</div>
							<div class="col-xs-2" style="text-align: center">
								{{ $uniform->pant_color }} {{ $uniform->pant }}
							</div>
							<div class="col-xs-2" style="text-align: center">
								{{ $uniform->shoes_color }} {{ $uniform->shoes }}
							</div>
						</div>
						<div class="col-xs-2">
							<div style="margin-top: 10px; text-align: center">
								<a href="{{ url('uniforms/'.$uniform->id.'/edit') }}" class="btn btn-info btn-xs">Update</a>
								<a href="{{ url('uniforms/'.$uniform->id.'/delete') }}" class="btn btn-danger btn-xs">Delete</a>
							</div>
						</div>
					</div>
					@endforeach
				</div>
			</div>
		</div>
		<div>
			<a href="{{ url('uniforms/create') }}" class="btn btn-primary"><span class="glyphicon glyphicon-plus"> </span> Add an uniform</a>
		</div>
	</div>
@stop