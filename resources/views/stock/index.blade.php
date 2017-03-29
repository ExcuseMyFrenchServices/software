@extends('layouts.main')

@section('content')
	<div class="container">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h1>Stocks{{ isset($category) ? "- ".ucfirst($category) : "" }}</h1>
			</div>
			<div class="panel-body">
				<form action="{{ url('/stocks/sort') }}" method="POST" class="form" style="margin-bottom: 25px">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<label for="category">Category</label>
					<select name="category">
						<option value="all">All</option>
						<option value="soft drink">Soft Drinks</option>
						<option value="alcohol">Alcohols</option>
						<option value="accessory">Accessories</option>
						<option value="glass">Glasses</option>
					</select>
					<input type="submit" class="btn btn-sm btn-info" value="sort">
				</form>
				<div class="list-group">
					<div class="row">
						<div class="list-group-item col-xs-12">
							<div class="col-xs-5">
								Name
							</div>
							<div class="col-xs-5">
								Quantity
							</div>
						</div>
					</div>
					@foreach($stocks as $stock)
					<div class="row">
						<div class="list-group-item col-xs-12">
							<div class="col-xs-5">
								{{ $stock->name }}
							</div>
							<div class="col-xs-5">
								{{ $stock->quantity }}
							</div>
							
							<div class="col-xs-2">
								<a href="{{ url('/stocks/'.$stock->id.'/edit') }}" class="btn btn-info btn-small">edit</a>
								<a href="{{ url('/stocks/'.$stock->id.'/delete	') }}" class="btn btn-danger btn-small">X</a>
							</div>
						</div>
					</div>
					@endforeach
				</div>
				<a href="{{ url('/stocks/create') }}" class="btn btn-primary btn-small">Add</a>
			</div>
		</div>
	</div>
@stop