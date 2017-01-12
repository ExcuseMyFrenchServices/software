$( document ).ready(function() {

    // USERS

    // Table view

    $('.delete_button').click(function() {
        var id = $(this).siblings('.user_id').html();
        $('#form_delete').prop('action', '/user/' + id);

        var username = $(this).siblings('.username').html();
        $('p#confirmation_message').html('Are you sure you want to delete the  user <b>' + username + '</b>?');
    });

    // Create/Update form

    $('select[name="role"]').selectpicker();

    $('#has_car').change(function() {
        $('#drivers_license').prop('disabled', function(i, v) { return !v; });
    });

    // AVAILABILITY

    $('.a_delete_btn').click(function() {
        var id = $(this).siblings('.availability_id').html();
        $('#availability_form_delete').prop('action', '/availability/' + id);
        $('#a_confirm_message').html('Are you sure you want to delete your availabilities for this date?');
    });

    $('#days-of-week').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    $('.hour-box-editable').click(function() {
        $(this).toggleClass('on');
        $(this).toggleClass('off');

        var disabled = $(this).find('input').attr('disabled');
        if (typeof disabled !== typeof undefined && disabled !== false) {
            $(this).find('input').removeAttr('disabled');
        } else {
            $(this).find('input').attr('disabled', 'disabled');
        }
    });

    // Toggle on
    $('.toggle-on').click(function () {
        var day = $(this).attr('id');
        $('div[id*="' + day + '-"]').each(function() {
            $(this).prop('class', 'hour-box-editable on');
            var disabled = $(this).find('input').attr('disabled');

            if (typeof disabled !== typeof undefined && disabled !== false) {
                $(this).find('input').removeAttr('disabled');
            }
        });
    });

    // Toggle off
    $('.toggle-off').click(function () {
        var day = $(this).attr('id');
        $('div[id*="' + day + '-"]').each(function() {
            $(this).prop('class', 'hour-box-editable off');
            var disabled = $(this).find('input').attr('disabled');

            if (disabled !== 'disabled') {
                $(this).find('input').attr('disabled', 'disabled');
            }
        });
    });

    $('.dates-repeater').repeater({
        show: function () {
            $(this).show();

            var newDate = $(this).find('#date.input-group');

            newDate.datetimepicker({
                format: 'YYYY-MM-DD',
                defaultDate: moment($(this).prev().find('input').val()).add(1, 'd')
            });
        },

        isFirstItemUndeletable: true
    });

    $('#date.input-group').datetimepicker({
        format: 'YYYY-MM-DD',
        defaultDate: moment().add(1, 'd')
    });

    // EVENTS

    $('select[name="client"]').selectpicker();

    $('#booking_date').datetimepicker({
        format: 'YYYY-MM-DD',
    });

    $('#event_date').datetimepicker({
        format: 'YYYY-MM-DD',
    });

    $('#time.input-group').datetimepicker({
        format:'HH:mm'
    });

    $('#date-range').datetimepicker({
        format: 'YYYY-MM',
    });

    var event_list = [];
    $('.events').each(function() {
        var event_name = $(this).find('.event_name').html();
        var event_date = $(this).find('.event_date').html();
        var event = { title: event_name, start: event_date };
        event_list.push(event);
    });

    $('.calendar_btn').click(function() {
        $('#users_container').addClass('hidden');
        $('#calendar_container').removeClass('hidden');
        $('#calendar').fullCalendar({
            events: event_list,
        });
    });

    $('.users_btn').click(function() {
        $('#calendar_container').addClass('hidden');
        $('#users_container').removeClass('hidden');
    });


    $('.e_delete_btn').click(function() {
        var id = $(this).siblings('.event_id').html();
        $('#event_form_delete').prop('action', '/event/' + id);
        $('p#e_confirm_message').html('Are you sure you want to delete this event?');
    });

    $('.start-time-repeater').repeater({
        show: function () {
            $(this).show();

            $('#time.input-group').datetimepicker({
                format:'HH:mm'
            });
        },

        isFirstItemUndeletable: true
    });

    // CLIENTS

    $('.c_delete_btn').click(function() {
        var id = $(this).siblings('.client_id').html();
        $('#client_form_delete').prop('action', '/client/' + id);
        $('p#c_confirm_message').html('Are you sure you want to delete this client?');
    });


    // FEEDBACK

    $('#rating').barrating({
        theme: 'bootstrap-stars'
    });

    $('#rating-read').barrating({
        theme: 'bootstrap-stars',
        readonly: true
    });

    //PUBLIC HOLIDAYS

    $('.public_holiday_delete_btn').click(function() {
        var id = $(this).siblings('.public_holiday_id').html();
        $('#public_holiday_form_delete').prop('action', '/public-holidays/' + id);
        $('p#public_holiday_confirm_message').html('Are you sure you want to delete this public holiday?');
    });    
});