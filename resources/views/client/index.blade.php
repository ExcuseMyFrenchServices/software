@extends('layouts.main')

@section('content')

    <div id="clients_container" class="container">
        <div class="row" style="position: fixed;margin-top: 100px;">
            <form action="/client/search/" method="post" class="col-xs-12 col-md-2 col-md-offset-9">
                {{ csrf_field() }}
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search a client..." name="search">
                    <span class="input-group-btn">
                        <button class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-search"></span></button>
                    </span>                        
                </div>
            </form>
        </div>        
        <div class="row">
            <div class="col-xs-12 col-md-8 col-md-offset-2">
                @if(count($clients) >= 1)
                    <table id="clients_list" class="table table-striped">
                        <thead>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th></th>
                        <th></th>

                        </thead>
                        <tbody>
                        @foreach($clients as $client)
                            <tr>
                                <td>{{ $client->name }}</td>
                                <td>{{ $client->email }}</td>
                                <td>{{ $client->phone_number }}</td>
                                <td><a href="/client/{{ $client->id }}/edit" class="btn btn-info btn-xs" role="button">Update</a> </td>
                                <td>
                                    <button class="btn btn-danger c_delete_btn btn-xs" data-toggle="modal" data-target="#client_delete">Delete</button>
                                    <div class="hidden client_id" >{{ $client->id }}</div>
                                    <div class="hidden client_name" >{{ $client->name }}</div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty_event">
                        <h3>No clients</h3>
                    </div>
                @endif
                @if(isset($user))
                    &nbsp;
                @else
                    <a class="btn btn-primary btn-sm" href="{{ url('client/create') }}">Create new client</a>
                @endif
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <br>
                <!-- Modal Delete Confirm -->
                <div id="client_delete" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="client_deleteLabel">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Delete Confirmation</h4>
                            </div>
                            <div class="modal-body row">
                                <div class="col-xs-12">
                                    <p id="c_confirm_message"></p>
                                </div>
                                <div class="col-xs-4 col-xs-offset-8">
                                    <form id="client_form_delete" action="/client/" method="POST">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-danger btn-sm" id="c_btn_confirm">Delete</button>
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
