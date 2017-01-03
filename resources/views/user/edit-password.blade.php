@extends('layouts.main')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="panel panel-default edit_pass">
                    <div class="panel-heading">
                        <h3 class="panel-title">Change Password</h3>
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
                        <form id="login-form" action="{{ url('user/'. $id . '/password') }}" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>

                            <div class="form-group">
                                <label for="confirm">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm" name="confirm">
                            </div>
                            <button class="btn btn-primary" type="submit">Save</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop






