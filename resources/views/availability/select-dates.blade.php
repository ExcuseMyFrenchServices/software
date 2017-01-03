
@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div style="margin-top: 70px;" class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Select dates</h3>
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
                        <form action="/availability" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="form-group col-xs-12 {{ $errors->has('dates.0.date') ? 'has-error' : '' }}">
                                <div class="row">
                                    <div class="col-xs-12 col-md-6 col-md-offset-3 dates-repeater">
                                        <div data-repeater-list="dates">
                                            <div data-repeater-item>
                                                <div class="input-group date" id="date">
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                        <input type="text" class="form-control" name="date"/>
                                                    <span data-repeater-delete class="input-group-addon">
                                                        <span class="glyphicon glyphicon-remove"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <span data-repeater-create class="btn btn-success btn-sm">
                                            <span class="glyphicon glyphicon-plus"></span> Add
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <button class="btn btn-primary btn-sm" type="submit">Create</button>
                                <a href="/availability" class="btn btn-info btn-sm" role="button">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop






