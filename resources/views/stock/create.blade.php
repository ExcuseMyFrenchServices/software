@extends('layouts.main')

@section('content')
	<div class="container">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h1>Stocks</h1>
			</div>
			<div class="panel-body">
				<div class="col-xs-12">

                    @if(count($errors) > 0)
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(Session::has('success'))
                        <div class="alert alert-success" role="alert">
                            {{ Session::get('success') }}
                        </div>
                    @endif
                </div>

				<form action="{{ isset($stock) && !is_null($stock->id) ? '/stocks/'.$stock->id : url('stocks') }}" method="POST" class="form">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					@if(isset($stock) && !is_null($stock->id))
                        <input type="hidden" name="_method" value="PUT">
                    @endif
					<div class="col-xs-4">
						<label for="name">Name</label>
						<input type="text" name="name" value="{{ $stock->name or old('name') }}">
					</div>
					<div class="col-xs-4">
						<label for="category">Category</label>
						<select name="category">
							@if(isset($stock))
								<option value="{{ $stock->category }}">{{ $stock->category }}</option>
							@else
								<option></option>
							@endif
							<option value="soft drink">soft drink</option>
							<option value="alcohol">alcohol</option>
							<option value="accessory">accessory</option>
							<option value="glass">glass</option>
						</select>
					</div>
					<div class="col-xs-4">
						<label for="quantity">Quantity</label>
						<input type="number" name="quantity" value="{{ $stock->quantity or old('quantity') }}">
					</div>
					<div class="col-xs-4" style="margin-top: 50px">
						<button type="submit" class="btn btn-primary btn-small">
							{{ isset($stock) ? "Edit" : "Add" }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
@stop