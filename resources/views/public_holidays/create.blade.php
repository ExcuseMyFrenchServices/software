@extends('layouts.main')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div style="margin-top: 70px;" class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ isset($public_holiday) && !is_null($public_holiday->id) ? 'Update' : 'Add new'}} public holiday</h3>
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
                        <form id="public-holiday-form" action="{{ isset($public_holiday) && !is_null($public_holiday->id) ? '/public-holidays/'.$public_holiday->id : url('public-holidays') }}" method="POST">
                        	<input type="hidden" name="_token" value="{{ csrf_token() }}">
                        	@if(isset($public_holiday) && !is_null($public_holiday))
                        		<input type="hidden" name="_method" value="PUT">
                        	@endif
                        	<div class="col-xs-12 col-md-6 form-group">
                        		<label>Public Holiday Name</label>
                        		@if(isset($public_holiday))
                        			<input type="text" name="public_holiday_name" value="{{ $public_holiday->public_holiday_name }}" class="form-control">
                        		@else
                        			<input type="text" name="public_holiday_name" class="form-control">
                        		@endif
                        	</div>
                        	<div class="form-group col-xs-12 col-md-6">
                                <label for="public_holiday_date">Date</label>
                                <div class="input-group date" id="public_holiday_date">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    @if(isset($public_holiday))
                                    	<input type='date' class="form-control" name="public_holiday_date" value="{{ date_format(date_create($public_holiday->public_holiday_date),'Y-m-d') }}" />
                                    @else
                                    	<input type='date' class="form-control" name="public_holiday_date"/>
                                    @endif	
                                </div>
                            </div>
                            <div class="checkbox col-xs-12 form-group">
                            	<label>
                            		@if(isset($public_holiday) && $public_holiday->year == 1)
                            			<input class="checked" type="checkbox" name="reccurent-date" value="yes">
                            		@else
                            			<input type="checkbox" name="reccurent-date" value="yes">
                            		@endif 
                            		This date is the same every year
                            	</label>
                            </div>
                        	<div class="col-xs-12">
                                <button class="btn btn-primary btn-sm" type="submit">
                                	@if(isset($public_holiday))
                                	Update public holiday
                                	@else
                                	Add new public holiday
                                	@endif
                                </button>
                                <a href="/public-holidays" class="btn btn-info btn-sm" role="button">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop