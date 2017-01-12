@extends('layouts.main')
@section('content')
	<div class="container">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h1>Public Holidays of {{ $year }}</h1>
			</div>
			<div class="panel-body">
				<div class="list-group">
					@foreach($public_holidays as $public_holiday)
					<div class="row">
						<div class="list-group-item col-xs-6 col-xs-offset-3">
							<div class="col-xs-5" style="text-align: center">
								{{ date_format(date_create($public_holiday->public_holiday_date), 'l, d F Y') }}
							</div>
							<div class="col-xs-2">
								@if($public_holiday->year != 1)
								<span class="badge">{{ $year }} only</span>
								@endif
							</div>
							<div class="col-xs-5" style="text-align: center"> 
								{{ $public_holiday->public_holiday_name }}
							</div>
						</div>
						<div class="col-xs-3">
							<div style="margin-top: 10px; text-align: center">
								<a href="{{ url('public-holidays/'.$public_holiday->id.'/edit') }}" class="btn btn-info btn-xs">Update</a>
								<a href="{{ url('public-holidays/'.$public_holiday->id.'/delete') }}" class="btn btn-danger btn-xs">Delete</a>
							</div>
						</div>
					</div>
					@endforeach
				</div>
			</div>
		</div>
		<div>
			<a href="{{ url('public-holidays/create') }}" class="btn btn-primary"><span class="glyphicon glyphicon-plus"></span> Add a public holiday</a>
		</div>
	</div>
@stop