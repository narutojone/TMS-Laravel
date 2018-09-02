{{-- Custom title --}}
<div class="form-group">
    <label class="col-sm-2 control-label">Custom title</label>
    <div class="col-md-10">
        <input type="text" class="form-control" name="modules[{{$module->id}}][custom-title]" value="{{ $settings['custom-title'] }}" />
    </div>
</div>
