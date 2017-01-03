@for ($hour = 6; $hour < 24; $hour++)
    <div class="col-xs-4 col-sm-2 hour">
        <div id="{{ $availability->id }}-{{ $hour }}" class="hour-box-editable {{ in_array($hour, $times) ? 'on' : 'off' }}" style="{{ $hour % 6 < 5 ? 'border-right: 0' : ''  }}">
            <input type="hidden" name="{{ $availability->id }}-{{ $hour }}" value="1" {{ in_array($hour, $times) ? '' : 'disabled="disabled"' }}>
            <div class="hour-label">{{ $hour < 12 ? $hour . ' am' : ($hour > 12 ? $hour-12 . ' pm' : $hour . ' pm') }}</div>
        </div>
    </div>
@endfor