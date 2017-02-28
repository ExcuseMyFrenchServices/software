@extends('layouts.main')
@section('content')
	<div class="container">
		<div class="row">
            <div class="col-xs-12 col-md-10 col-md-offset-1 table-responsive">
                <table id="users_list" class="table table-striped">
                    <thead>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Level</th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->profile->first_name }}</td>
                            <td>{{ $user->profile->last_name }}</td>
                            <td>{{ $user->profile->email }}</td>
                            <td>{{ $user->level }}</td>
                            <td><a href="/user/{{ $user->id }}/edit" class="btn btn-info btn-xs" role="button">Profile</a> </td>
                            <td><a href="/user/{{ $user->id }}/credentials/edit" class="btn btn-primary btn-xs" role="button">Credentials</a> </td>
                            <td>
                                @if(Auth::user()->id != $user->id)
                                    <a href="/user/unarchive/{{ $user->id }}" class="btn btn-success btn-xs" role="button"><span class="glyphicon glyphicon-level-up"> </span> Unarchive</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
	</div>	
@stop