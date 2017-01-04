@extends('layouts.main')

@section('content')

    <div class="container">
        <div class="row" style="position: fixed;margin-top: 100px;">
            <form action="/user/search/" method="post" class="col-xs-12 col-md-2 col-md-offset-9">
                {{ csrf_field() }}
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search a user..." name="search">
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-search"></span></button>
                    </span>                        
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-xs-12 col-md-10 col-md-offset-1 table-responsive">
                <table id="users_list" class="table table-striped">
                    <thead>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th></th>
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
                            <td><a href="/user/{{ $user->id }}/edit" class="btn btn-info btn-xs" role="button">Profile</a> </td>
                            <td><a href="/user/{{ $user->id }}/credentials/edit" class="btn btn-primary btn-xs" role="button">Credentials</a> </td>
                            <td>
                                <button class="btn btn-danger delete_button btn-xs" data-toggle="modal" data-target="#confirm_delete">Delete</button>
                                <div class="hidden user_id" >{{ $user->id }}</div>
                                <div class="hidden username" >{{ $user->username }}</div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <a class="btn btn-primary btn-sm" href="{{ url('user/create') }}">Create User</a>

                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>

                <div id="confirm_delete" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="confirm_deleteLabel">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Delete Confirmation</h4>
                            </div>
                            <div class="modal-body row">
                                <div class="col-xs-12">
                                    <p id="confirmation_message"></p>
                                </div>
                                <div class="col-xs-4 col-xs-offset-8">
                                    <form id="form_delete" action="/user/" method="POST">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-danger btn-sm" id="button_confirm">Delete</button>
                                        <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
                                    </form>
                                </div>
                            </div>

                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->

            </div>
        </div>
    </div>
@stop