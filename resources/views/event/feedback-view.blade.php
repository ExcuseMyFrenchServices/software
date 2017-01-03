
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

                        <div class="col-xs-12 form-group">
                            <label for="rating">Rating</label>
                            <select name="rating" id="rating-read">
                                <option value=""></option>
                                @foreach(range(1,5) as $i)
                                    @if($i == $feedback->rating)
                                        <option selected value="{{ $i }}">{{ $i }}</option>
                                    @else
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="col-xs-12 form-group">
                            <label for="comment">Comments</label>
                            <p>
                                {{ $feedback->comment }}
                            </p>
                        </div>

                        <div class="col-xs-12">
                            <a href="{{ back()->getTargetUrl() }}">
                                <button class="btn btn-primary btn-sm" type="submit">Back</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop






