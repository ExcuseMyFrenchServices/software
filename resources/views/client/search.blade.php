@extends('layouts.main')

@section('content')

<div class="container">
        <div class="row" style="position: fixed;margin-top: 100px;">
            <form action="/user/search/" method="post" class="col-xs-12 col-md-2 col-md-offset-9">
                    {{ csrf_field() }}
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search another client..." name="search">
                        <span class="input-group-btn">
                            <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-search"></span></button>
                        </span>                        
                    </div>
                </form>
            </div>
        </div>
        <div class="row">    
            <div class="col-xs-12 col-md-6 col-md-offset-3 table-responsive">
                <table id="users_list" class="table table-striped">
                    <thead>
                        <th>First Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    </thead>
                    <tbody>
                    	@foreach($clients as $client)
                    		<tr>
                    			<td>{{$client->name}}</td>
                    			<td>{{$client->email}}</td>
                                <td>{{$client->phone_number}}</td>
                    			<td><a href="/client/{{ $client->id }}/edit" class="btn btn-info btn-xs" role="button">Profile</a> </td>
                            	<td><a href="/client/{{ $client->id }}/credentials/edit" class="btn btn-primary btn-xs" role="button">Credentials</a> </td>
                            	<td>
                                	<button class="btn btn-danger delete_button btn-xs" data-toggle="modal" data-target="#confirm_delete">Delete</button>
                                	<div class="hidden id" >{{ $client->id }}</div>
                                	<div class="hidden name" >{{ $client->name }}</div>
                            	</td>
                    		</tr>
                    	@endforeach
                    </tbody>
                </table>
                <div class="col-xs-12 col-md-4 col-md-offset-4">
                    <a class="btn btn-warning btn-lg" href="{{ url('client/') }}"><span class="glyphicon glyphicon-backward"></span> Back to full Clients list</a>
                </div>

</div>
@stop