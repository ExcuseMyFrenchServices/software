
@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-1">
                <div style="margin-top: 70px;" class="panel panel-default">
                    <div class="panel-heading">
                        @if(isset($profile))
                            <h3 class="panel-title">Update user <b>{{ $user->username }}</b></h3>
                        @else
                            <h3 class="panel-title">Create new user</h3>
                        @endif
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

                        @if(isset($profile))
                            <form id="create-form" action="/user/{{ $user->id }}" method="POST">
                                <input type="hidden" name="_method" value="PUT">
                        @else
                            <form id="create-form" action="{{ url('user') }}" method="POST">
                        @endif
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            @include('user.personal-info')

                            @include('user.documents')

                            <div class="col-xs-12 col-sm-6 col-md-6 form-group">
                                <label for="role">User Role</label>
                                <select name="role">
                                    @foreach($roles as $role)
                                        @if(isset($user))
                                            <option {{ $user->role_id == $role->id ? 'selected' : '' }} value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                        @else
                                            <option {{ old('role') == $role->id ? 'selected' : '' }} value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xs-12">
                                <br>
                                @if(isset($profile))
                                    <button  class="btn btn-primary btn-sm" type="submit">Update</button>
                                @else
                                    <button  class="btn btn-primary btn-sm" type="submit">Register</button>
                                @endif
                                <a href="/user" class="btn btn-info btn-sm" role="button">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @if(isset($profile))
            <div class="col-xs-12 col-md-5">
                <div style="margin-top: 70px;" class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Number of time working for</h3>
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
                        <div class="col-xs-12">
                        <table class="col-xs-12">
                            <tr><th>Client</th><th></th></tr>
                            @if(!empty($userClientMission))
                                @foreach($userClientMission as $userMission)
                                <tr><td>{{ $userMission->name }}</td><td style="text-align: right">{{ $userMission->time_worked_for }}</td></tr>
                                @endforeach
                            @else
                                <tr><td>No missions yet</td></tr>
                            @endif
                        </table>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <p><b>TOTAL MISSIONS : <span style="float:right">{{ $userTotalMissions }}</span></b></p>
                    </div>
                </div> 
                @if(!empty($userBestClient))
                <div style="margin-top: 70px;" class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Best Client</h3>
                    </div>
                    <div class="panel-body">
                        <p>{{ $userBestClient->name }} <span style="float:right"><b>{{ $userBestClient->time_worked_for }}</b></span></p>
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>

@stop






