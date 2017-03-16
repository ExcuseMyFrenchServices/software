
@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div style="margin-top: 70px;" class="panel panel-default">
                    <div class="panel-heading">
                        @if(isset($client))
                            <h3 class="panel-title">Update client</h3>
                        @else
                            <h3 class="panel-title">Create new client</h3>
                        @endif
                    </div>
                    <div class="panel-body">
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

                        @if(isset($client))
                            <form id="create-form" action="/client/{{ $client->id }}" method="POST">
                            <input type="hidden" name="_method" value="PUT">
                        @else
                            <form id="create-form" action="{{ url('client') }}" method="POST">
                        @endif
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="col-xs-12 col-sm-6 col-md-6 form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                <label for="name">Name</label>
                                @if(isset($client))
                                    <input type="text" name="name" id="name" class="form-control"  value="{{ $client->name }}">
                                @else
                                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}">
                                @endif
                            </div>

                            <div class="col-xs-12 col-sm-6 col-md-6 form-group {{ $errors->has('phone_number') ? 'has-error' : '' }}">
                                <label for="phone_number">Phone Number</label>
                                <input type="text" name="phone_number" id="phone_number" class="form-control"  value="{{ $client->phone_number or old('phone_number') }}">
                            </div>

                            <div class="col-xs-12 form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control"  value="{{ $client->email or old('email') }}">
                            </div>

                            <div class="col-xs-12 form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                                <label for="email">Second Email</label>
                                <input type="email" name="second_email" id="second_email" class="form-control"  value="{{ $client->second_email or old('second_email') }}">
                            </div>

                            <div class="col-xs-12 form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                                <label for="email">Third Email</label>
                                <input type="email" name="third_email" id="third_email" class="form-control"  value="{{ $client->third_email or old('third_email') }}">
                            </div>

                            @if(isset($client))
                                <button  class="btn btn-primary btn-sm" type="submit">Update</button>
                            @else
                                <button  class="btn btn-primary btn-sm" type="submit">Create</button>
                            @endif
                            <a href="/client" class="btn btn-info btn-sm" role="button">Back</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop






