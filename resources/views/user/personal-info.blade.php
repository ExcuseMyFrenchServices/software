<div class="col-xs-12 col-sm-6 form-group {{ $errors->has('first_name') ? 'has-error' : '' }}">
    <label for="first_name">First Name</label>
    @if(isset($profile))
        <input disabled type="text" name="first_name" id="first_name" class="form-control" value="{{ $profile->first_name }}">
    @else
        <input type="text" name="first_name" id="first_name" class="form-control"  value="{{ old('first_name') }}">
    @endif
</div>

<div class="col-xs-12 col-sm-6 form-group {{ $errors->has('last_name') ? 'has-error' : '' }}">
    <label for="last_name">Last Name</label>
    @if(isset($profile))
        <input disabled type="text" name="last_name" id="last_name" class="form-control" value="{{ $profile->last_name }}">
    @else
        <input type="text" name="last_name" id="last_name" class="form-control"  value="{{ old('last_name') }}">
    @endif
</div>

<div class="col-xs-12 form-group {{ $errors->has('email') ? 'has-error' : '' }}">
    <label for="email">Email</label>
    <input type="email" name="email" id="email" class="form-control" value="{{ $profile->email or old('email') }}">
</div>

<div class="col-xs-7 form-group {{ $errors->has('phone_number') ? 'has-error' : '' }}">
    <label for="phone_number">Phone Number</label>
    <input type="text" name="phone_number" id="phone_number" class="form-control" value="{{ $profile->phone_number or old('phone_number') }}">
</div>


<div class="col-xs-12 form-group">
    <label for="address">Address</label>
    <textarea rows="2"  name="address" id="address" class="form-control">{{ $profile->address or old('address') }}</textarea>
</div>