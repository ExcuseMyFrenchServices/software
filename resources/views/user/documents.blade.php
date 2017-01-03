<div class="col-xs-12 col-sm-7 form-group {{ $errors->has('rsa_number') ? 'has-error' : '' }}">
    <label for="rsa_number">RSA Number</label>
    <input type="text" name="rsa_number" id="rsa_number" class="form-control" value="{{ $profile->rsa_number or old('rsa_number') }}">
</div>

<div class="col-xs-12 col-sm-7 form-group">
    <label for="drivers_license">Drivers License</label>
    @if(isset($profile))
        <input type="text" name="drivers_license" id="drivers_license" {{ $profile->has_car ? '' : 'disabled' }} class="form-control" value="{{ $profile->drivers_license }}">
    @else
        <input type="text" name="drivers_license" id="drivers_license" {{ old('has_car') == '0' ? '' : 'disabled' }} class="form-control" value="{{ old('drivers_license') }}">
    @endif
</div>

<div class="col-xs-12 col-sm-5 form-group">
    <label class="hidden-xs">&nbsp;</label>
    <div class="checkbox">
        <label>
            @if(isset($profile))
                <input type="checkbox" name="has_car" id="has_car" {{ $profile->has_car ? 'checked' : '' }}> Car
            @else
                <input type="checkbox" name="has_car" id="has_car" {{ old('has_car') == '0' ? 'checked' : '' }}> Car
            @endif
        </label>
    </div>
</div>

<div class="col-xs-12">
    <p>Sizes</p>
</div>

<div class="col-xs-12 col-sm-4 col-md-4 form-group">
    <input type="text" name="shirt_size" id="shirt_size" class="form-control" placeholder="Shirt" value="{{ $profile->shirt_size or old('shirt_size') }}"">
</div>

<div class="col-xs-12 col-sm-4 col-md-4 form-group">
    <input type="text" name="pant_size" id="pant_size" class="form-control" placeholder="Pants" value="{{ $profile->pant_size or old('pant_size') }}">
</div>

<div class="col-xs-12 col-sm-4 col-md-4 form-group">
    <input type="text" name="shoe_size" id="shoe_size" class="form-control" placeholder="Shoes" value="{{ $profile->shoe_size or old('shoe_size') }}">
</div>