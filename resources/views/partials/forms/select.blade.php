@php($value = isset($value) ? $value : null)
@php($options = isset($options) ? $options : [])
@php($name = isset($name) ? $name : null)
<div class="form-group{{ $errors->has($name) ? ' has-error' : '' }}">
    <label class="col-sm-2 control-label" for="{{ $name }}">{{ $label or ucfirst($name) }}</label>

    <div class="col-sm-10">
        <select class="form-control chosen-select" name="{{ $name }}" id="{{ $name }}" {{ $disabled ?? '' }}>
            @foreach ($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}"{{ old($name, $value) == $optionValue ? ' selected' : '' }}>{{ $optionLabel }}</option>
            @endforeach
        </select>

        @if ($errors->has($name))
            <span class="help-block m-b-none">
                <strong>{{ $errors->first($name) }}</strong>
            </span>
        @endif
    </div>
</div>
