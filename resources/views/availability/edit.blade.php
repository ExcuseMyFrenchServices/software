@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="row">
            <div style="margin-top: 70px;" class="col-md-10 col-md-offset-1">

                @if(Session::has('success'))
                    <div class="alert alert-success" role="alert">
                        {{ Session::get('success') }}
                    </div>
                @endif

                <form id="availability-form" action="/availability/{{ Auth::user()->id }}" method="POST">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    @include('availability.tabs-header')

                    @include('availability.tabs-content')

                    <br>
                    <input type="submit" class="btn btn-primary" value="Save">
                    <a href="/availability" class="btn btn-info" role="button">Back</a>
                    @if($availabilities->count() > 1)<i>Tip: configure all dates before saving</i>@endif
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                </form>
            </div>
        </div>
    </div>



@stop