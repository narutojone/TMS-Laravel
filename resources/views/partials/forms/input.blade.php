<div class="form-group{{ $errors->has($name) ? ' has-error' : '' }}">
    <label class="col-sm-2 control-label" for="{{ $name }}">{{ $label or ucfirst($name) }}</label>

    <div class="col-sm-10">
        <input type="{{ $type or 'text' }}" class="form-control" name="{{ $name }}" id="{{ $name }}" value="{{ isset($value) ? old($name, $value) : old($name) }}" {{ $disabled ?? '' }}>

        @if ($errors->has($name))
            <span class="help-block m-b-none">
                <strong>{{ $errors->first($name) }}</strong>
            </span>
        @endif
    </div>
</div>
