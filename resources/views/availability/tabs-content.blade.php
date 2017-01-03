<div class="tab-content">
    <?php $first = true; ?>
    @foreach($availabilities as $availability)
        <div role="tabpanel" class="tab-pane fade {{ $first ? 'active in' : '' }}" id="{{ $availability->id }}">
            <div class="day-content">
                <div class="row">
                    <div class="col-xs-12" style="padding: 0 5px;">
                        {{ date_format(date_create($availability->date), 'l, F d Y') }}
                    </div>
                    <div class="col-xs-12" style="padding: 0 3px;">
                        <a id="{{ $availability->id }}" class="btn btn-success toggle-on">Available</a>
                        <a id="{{ $availability->id }}" class="btn btn-danger toggle-off">Unavailable</a>
                    </div>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>

                    @include('availability.hour-boxes', ['times' => $availability->times])

                    <input type="hidden" name="availabilities[]" value="{{ $availability->id }}">
                </div>
            </div>
        </div>
        <?php $first = false; ?>
    @endforeach
</div>