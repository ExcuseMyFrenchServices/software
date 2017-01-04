
@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div style="margin-top: 70px;" class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ isset($event) && !is_null($event->id) ? 'Update' : 'Create new' }} event</h3>
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


                        <form id="create-form" action="{{ isset($event) && !is_null($event->id) ? '/event/'.$event->id : url('event') }}" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            @if(isset($event) && !is_null($event->id))
                                <input type="hidden" name="_method" value="PUT">
                            @endif

                            <div class="col-xs-8 form-group {{ $errors->has('client') ? 'has-error' : '' }}">
                                <label for="client">Client</label>
                                <select name="client" data-live-search="true" data-size="8" data-width="100%">
                                    @foreach($clients as $client)
                                        @if(isset($event))
                                            <option {{ $client->id == $event->client_id ? 'selected' : '' }} value="{{ $client->id }}">{{ ucfirst($client->name) }}</option>
                                        @else
                                            <option {{ old('client') == $client->id ? 'selected' : '' }} value="{{ $client->id }}">{{ ucfirst($client->name) }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-xs-4 form-group" style="text-align: center">
                                <label style="display: block;">&nbsp;</label>
                                <a href="/client/create" class="btn btn-success btn-sm">
                                    <span class="glyphicon glyphicon-plus"></span> Add
                                </a>
                            </div>

                            <div class="col-xs-12 form-group {{ $errors->has('event_name') ? 'has-error' : '' }}">
                                <label for="event_name">Event Name</label>
                                @if(isset($event))
                                    <input  type="text" name="event_name" id="event_name" class="form-control"  value="{{ $event->event_name }}">
                                @else
                                    <input type="text" name="event_name" id="event_name" class="form-control" value="{{ old('event_name') }}">
                                @endif
                            </div>

                            <div class="form-group col-xs-12 col-md-6 {{ $errors->has('booking_date') ? 'has-error' : '' }}">
                                <label for="booking_date">Booking Date</label>
                                <div class="input-group date" id="booking_date">
                                    <input type="text" class="form-control" name="booking_date" value="{{ $event->booking_date or old('booking_date') }}"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group col-xs-12 col-md-6 {{ $errors->has('event_date') ? 'has-error' : '' }}">
                                <label for="event_date">Event Date</label>
                                <div class="input-group date" id="event_date">
                                    <input type='text' class="form-control" name="event_date" value="{{ $event->event_date or old('event_date') }}"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group col-xs-12 {{ $errors->has('start_times.0.time') ? 'has-error' : '' }}">
                                <label for="start_time">Start time</label>
                                <div class="row">
                                    <div class="col-xs-12 col-md-6 start-time-repeater">
                                        <div data-repeater-list="start_times">
                                        @if(isset($event) && !is_null($event->id))
                                            @foreach($event->start_time as $key => $time)
                                                <div data-repeater-item>
                                                    <div class="input-group date" id="time">
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                        </span>
                                                        <input type="text" class="form-control" name="time" value="{{ $time  }}"/>
                                                        <span data-repeater-delete class="input-group-addon">
                                                            <span class="glyphicon glyphicon-remove"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div data-repeater-item>
                                                <div class="input-group date" id="time">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-time"></span>
                                                    </span>
                                                    <input type="text" class="form-control" name="time"/>
                                                    <span data-repeater-delete class="input-group-addon">
                                                        <span class="glyphicon glyphicon-remove"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                        </div>
                                        <span data-repeater-create class="btn btn-success btn-sm">
                                            <span class="glyphicon glyphicon-plus"></span> Add
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-xs-12 col-md-6">
                                <label for="finish_time">Finish time</label>
                                <div class="input-group date" id="time">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                    <input type="text" class="form-control" name="finish_time" value="{{ $event->finish_time or old('finish_time') }}"/>
                                </div>
                            </div>

                            
                            <div class="col-xs-12 form-group {{ $errors->has('number_staff') ? 'has-error' : '' }}">
                                <label for="number_staff">Staff required</label>
                                <div class="staff-added">
                                    
                                </div>

                            </div>

                            <div class="col-xs-12 form-group {{ $errors->has('number_staff') ? 'has-error' : '' }}">
                                <label for="number_staff">Add staff role</label>
                                <div class="row">
                                    @foreach($roles as $role)
                                        <div class="staffing col-md-6" style="margin-bottom: 5px;">
                                            <a style="text-align: left;word-spacing: -10px;" class="btn btn-info btn-block staff-adding">
                                                <span class="glyphicon glyphicon-plus"> 
                                                    {{ $role->name }}
                                                </span>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-xs-12 form-group">
                                <label for="address">Address</label>
                                <textarea rows="2"  name="address" id="address" class="form-control">{{ $event->address or old('address') }}</textarea>
                            </div>

                            <div class="col-xs-12 form-group">
                                <label for="uniform">Uniform</label>
                                <textarea rows="2"  name="uniform" id="uniform" class="form-control">{{ $event->uniform or old('uniform') }}</textarea>
                            </div>

                            <div class="col-xs-12 col-sm-4 form-group services">
                                <label class="">Extras</label>
                                <div class="checkbox">
                                    <label>
                                        @if(isset($event))
                                            <input type="checkbox" name="glasses" id="glasses" {{ $event->glasses ? 'checked' : '' }}> Glasses
                                        @else
                                            <input type="checkbox" name="glasses" id="glasses" {{ old('glasses') == '0' ? 'checked' : '' }}> Glasses
                                        @endif
                                    </label>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-4 form-group services">
                                <label class="hidden-xs">&nbsp;</label>
                                <div class="checkbox">
                                    <label>
                                        @if(isset($event))
                                            <input type="checkbox" name="soft_drinks" id="soft_drinks" {{ $event->soft_drinks ? 'checked' : '' }}> Soft drinks
                                        @else
                                            <input type="checkbox" name="soft_drinks" id="soft_drinks" {{ old('soft_drinks') == '0' ? 'checked' : '' }}> Soft drinks
                                        @endif
                                    </label>
                                </div>
                            </div>

                            <div class="col-xs-12 col-sm-4 form-group services">
                                <label class="hidden-xs">&nbsp;</label>
                                <div class="checkbox">
                                    <label>
                                        @if(isset($event))
                                            <input type="checkbox" name="bar" id="bar" {{ $event->bar ? 'checked' : '' }}> Bar
                                        @else
                                            <input type="checkbox" name="bar" id="bar" {{ old('bar') == '0' ? 'checked' : '' }}> Bar
                                        @endif
                                    </label>
                                </div>
                            </div>


                            <div class="col-xs-12 form-group">
                                <label for="notes">Notes</label>
                                <textarea rows="2"  name="notes" id="notes" class="form-control">{{ $event->notes or old('notes') }}</textarea>
                            </div>

                            <div class="col-xs-12">
                                <button class="create-event btn btn-primary btn-sm" type="submit">{{ isset($event) && !is_null($event->id) ? 'Update' : 'Create' }}</button>
                                <a href="/event" class="btn btn-info btn-sm" role="button">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function(){

            var tempStaff;
            $('.staff-adding').on('click', function(){
                var value = $(this).text().trim().split(' ').join('-');
                tempStaff = $('.staff-added').append('<div class="staff input-group col-md-6"><span class="input-group-addon"><span class="glyphicon glyphicon-user"></span></span><input class="form-control input-success" value='+value+'></div>');
            });
            $('.create-event').on('click', function(){
                var numberOfStaff = $('.staff').length;
                $('.staff-added').append('<input type="hidden" name="number_staff" id="number_staff" class="form-control" value="'+numberOfStaff+'">');
            });
        });   
    </script>
@stop






