
@extends('layouts.main')

@section('header')
@stop


@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <img style="margin: 0 auto 20px auto" src="{{ asset('img/logo_large.png') }}" class="img-responsive">
        </div>

        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Login</h3>
                </div>
                <div class="panel-body">
                    @if(isset($error))
                    <div class="alert alert-danger" role="alert">
                        {{ $error }}
                    </div>
                    @endif

                    <form id="login-form" action="{{ url('login') }}" method="POST">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username">
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <button class="btn btn-primary" type="submit">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@stop






