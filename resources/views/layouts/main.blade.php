<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

        <title>Excuse My French</title>

        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/bootstrap-select.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/fullcalendar.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/bootstrap-stars.css') }}" rel="stylesheet">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        @yield('styles')
    </head>
    <body>
       @section('header')
           @include('partials.header')
       @show

    @yield('content')

        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap-select.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('js/moment.js') }}"></script>
        <script src="{{ asset('js/bootstrap-datetimepicker.js') }}"></script>
        <script src="{{ asset('js/fullcalendar.min.js') }}"></script>
        <script src="{{ asset('js/jquery.barrating.min.js') }}"></script>
        <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
        <script src="{{ asset('js/app.js') }}"></script>

        @yield('scripts')

       @section('footer')
           @include('partials.footer')
       @show

    </body>
</html>



