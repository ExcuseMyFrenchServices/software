@extends('layouts.main')

@section('content')
    <div id="availabilities_container" class="container">
        <div class="row">
            <div class="col-xs-12 col-md-8 col-md-offset-2">
                @if(count($availabilities) >= 1)
                    <table id="availabilities_list" class="table table-striped">
                        <thead>
                            <th>Date</th>
                            <th>Hours available</th>
                            <th></th>
                            <th></th>
                        </thead>
                        <tbody>
                        @foreach($availabilities as $availability)
                            <tr>
                                <td>{{ date_format(date_create($availability->date), 'F d, Y') }}</td>
                                <td>{{ count($availability->times) }}</td>
                                <td><a href="/availability/{{ $availability->id }}" class="btn btn-info btn-xs" role="button">Update</a> </td>
                                <td>
                                    <button class="btn btn-danger a_delete_btn btn-xs" data-toggle="modal" data-target="#availability_delete">Delete</button>
                                    <div class="hidden availability_id">{{ $availability->id }}</div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty_event">
                        <h3>No availabilities</h3>
                        @if(Session::has('notAvailable'))
                            <p class="alert alert-danger">{{ Session::get('notAvailable') }}</p>
                        @endif
                    </div>
                @endif

                <a class="btn btn-primary btn-sm" href="{{ url('availability/create/dates') }}">Add availability</a>
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
                <div id="availability_delete" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="availability_delete">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Delete Confirmation</h4>
                            </div>
                            <div class="modal-body row">
                                <div class="col-xs-12">
                                    <p id="a_confirm_message"></p>
                                </div>
                                <div class="col-xs-4 col-xs-offset-8">
                                    <form id="availability_form_delete" action="/availability/" method="POST">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-danger btn-sm" id="e_btn_confirm">Delete</button>
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
