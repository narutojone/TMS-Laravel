<div class="form-group{{ $errors->has($name) ? ' has-error' : '' }}">
    <label class="col-sm-2 control-label" for="{{ $name }}">{{ $label or ucfirst($name) }}</label>

    <div class="col-sm-10">
        @if (! isset($value))
            @include('quill.create', ['id' => $id, 'name' => $name, 'form' => $form])
        @else
            @include('quill.edit', ['id' => $id, 'name' => $name, 'form' => $form, 'delta' => $value])
        @endif

        @if ($errors->has($name))
            <span class="help-block m-b-none">
                <strong>{{ $errors->first($name) }}</strong>
            </span>
        @endif
    </div>
</div>
