@extends('layouts.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div style="margin-top: 70px;" class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ isset($uniform) && !is_null($uniform->id) ? 'Update' : 'Add new'}} uniform</h3>
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
                        <form id="uniform-form" action="{{ isset($uniform) && !is_null($uniform->id) ? '/uniforms/'.$uniform->id : url('uniforms') }}" method="POST">
                        	<input type="hidden" name="_token" value="{{ csrf_token() }}">
                        	@if(isset($uniform) && !is_null($uniform))
                        		<input type="hidden" name="_method" value="PUT">
                        	@endif
                            <div class="col-xs-12 form-group">
                                <label>Set Name</label>
                                @if(isset($uniform))
                                    <input type="text" name="set_name" value="{{ $uniform->set_name }}" class="form-control">
                                @else
                                    <input type="text" name="set_name" class="form-control">
                                @endif
                            </div>
                        	<div class="col-xs-6 form-group">
                        		<label>Top</label>
                        		@if(isset($uniform))
                        			<input type="text" name="jacket" value="{{ $uniform->jacket }}" class="form-control">
                        		@else
                        			<input type="text" name="jacket" class="form-control">
                        		@endif
                        	</div>
                            <div class="col-xs-6 form-group">
                                <label>Top Color</label>
                                @if(isset($uniform))
                                    <input type="text" name="jacket_color" value="{{ $uniform->jacket_color }}" class="form-control">
                                @else
                                    <input type="text" name="jacket_color" class="form-control">
                                @endif
                            </div>
                            <div class="col-xs-6 form-group">
                                <label>Shirt</label>
                                @if(isset($uniform))
                                    <input type="text" name="shirt" value="{{ $uniform->shirt }}" class="form-control">
                                @else
                                    <input type="text" name="shirt" class="form-control">
                                @endif
                            </div>
                            <div class="col-xs-6 form-group">
                                <label>Shirt Color</label>
                                @if(isset($uniform))
                                    <input type="text" name="shirt_color" value="{{ $uniform->shirt_color }}" class="form-control">
                                @else
                                    <input type="text" name="shirt_color" class="form-control">
                                @endif
                            </div>
                            <div class="col-xs-6 form-group">
                                <label>Pant</label>
                                @if(isset($uniform))
                                    <input type="text" name="pant" value="{{ $uniform->pant }}" class="form-control">
                                @else
                                    <input type="text" name="pant" class="form-control">
                                @endif
                            </div>
                            <div class="col-xs-6 form-group">
                                <label>Pant Color</label>
                                @if(isset($uniform))
                                    <input type="text" name="pant_color" value="{{ $uniform->pant_color }}" class="form-control">
                                @else
                                    <input type="text" name="pant_color" class="form-control">
                                @endif
                            </div>
                            <div class="col-xs-6 form-group">
                                <label>Shoes</label>
                                @if(isset($uniform))
                                    <input type="text" name="shoes" value="{{ $uniform->shoes }}" class="form-control">
                                @else
                                    <input type="text" name="shoes" class="form-control">
                                @endif
                            </div>
                            <div class="col-xs-6 form-group">
                                <label>Shoes Color</label>
                                @if(isset($uniform))
                                    <input type="text" name="shoes_color" value="{{ $uniform->shoes_color }}" class="form-control">
                                @else
                                    <input type="text" name="shoes_color" class="form-control">
                                @endif
                            </div>
                        	<div class="col-xs-12">
                                <button class="btn btn-primary btn-sm" type="submit">
                                	@if(isset($uniform))
                                	Update uniform
                                	@else
                                	Add new uniform
                                	@endif
                                </button>
                                <a href="/uniforms" class="btn btn-info btn-sm" role="button">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop