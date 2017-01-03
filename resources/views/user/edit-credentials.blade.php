@extends('layouts.main')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default edit_pass">
                    <div class="panel-heading">
                        <h3 class="panel-title">Update credentials</h3>
                    </div>
                    <div class="panel-body">
                        @if(Session::has('success'))
                            <div class="alert alert-success" role="alert">
                                {{ Session::get('success') }}
                            </div>
                        @endif

                        @if(count($errors) > 0)
                            <div class="alert alert-danger" role="alert">
                                <ul>
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form id="login-form" action="{{ url('user/'. $user->id . '/credentials/edit') }}" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="form-group {{ $errors->has('username') ? 'has-error' : '' }}">
                                <label for="username">Username</label>
                                <input type="text" name="username" id="username" class="form-control" value="{{ $user->username or old('username') }}">
                            </div>

                            <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>

                            <div class="form-group {{ $errors->has('confirm') ? 'has-error' : '' }}">
                                <label for="confirm">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm" name="confirm">
                            </div>
                            <button class="btn btn-success" type="submit">Save</button>
                            <a href="/user" class="btn btn-primary" style="margin-top: 20px;" role="button">Back</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop






