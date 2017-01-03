
@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div style="margin-top: 70px;" class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Event Feedback</h3>
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

                        <form id="create-form" action="{{ url('feedback/' . $hash) }}" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="col-xs-12 form-group">
                                <label for="rating">How would you rate us?</label>
                                <select name="rating" id="rating">
                                    <option value=""></option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>

                            <div class="col-xs-12 form-group {{ $errors->has('comment') ? 'has-error' : '' }}">
                                <label for="comment">Comments</label>
                                <textarea rows="6" name="comment" id="feedback" class="form-control"  value="{{ old('comment') }}"></textarea>
                            </div>


                            <div class="col-xs-12">
                                <button class="btn btn-primary btn-sm" type="submit">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop






