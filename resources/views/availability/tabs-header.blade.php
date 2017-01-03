<ul id="days-of-week" class="nav nav-tabs" role="tablist">
    <?php $first = true; ?>
    @foreach($availabilities as $availability)
        <li role="presentation" class="{{ $first ? 'active' : '' }}">
            <a href="#{{ $availability->id }}" aria-controls="{{ $availability->id }}" role="tab" data-toggle="tab" style="padding: 10px 12px;">
                <span>{{ date_format(date_create($availability->date), 'd/m') }}</span>
            </a>
        </li>
        <?php $first = false; ?>
    @endforeach
</ul>