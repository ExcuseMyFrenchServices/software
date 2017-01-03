
<div id="calendar_container" class="hidden">
    <div class="container">
        <div class="row">
            <div class="col-xs-6 col-xs-offset-3" style="text-align: center;">
                <button type="button" class="btn btn-primary btn-lg users_btn" aria-label="Left Align">
                    <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span>
                </button>
                <button type="button" class="btn btn-primary btn-lg calendar_btn" aria-label="Left Align">
                    <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                </button>
            </div>
            <div class="col-xs-12 col-md-8 col-md-offset-2">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    @foreach($events as $event)
        <div class="hidden events">
            <div class="hidden event_name">{{ $event->client->name }}</div>
            <div class="hidden event_date">{{ date_format(date_create($event->event_date), 'Y-m-d') }}</div>
        </div>
    @endforeach
</div>

