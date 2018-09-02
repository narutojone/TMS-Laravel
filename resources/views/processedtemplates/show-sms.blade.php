{{-- Type --}}
<div class="form-group">
    <label class="col-sm-2 control-label" for="title">Type</label>

    <div class="col-sm-10">
        <select name="type" id="type" class="form-control chosen-select" required autofocus disabled>
            @foreach(array_keys(config('tms.notifiers')) as $type)
                <option value="{{ $type }}" {{ old('type', $processedNotification['template_notification']['type']) == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="hr-line-dashed"></div>

{{-- User Type --}}
<div class="form-group">
    <label class="col-sm-2 control-label" for="user_type">User Type</label>

    <div class="col-sm-10">
        <select name="user_type" id="user_type" class="form-control chosen-select" required autofocus disabled>
            @foreach(\App\Repositories\TemplateNotification\TemplateNotification::$userTypes as $value => $label)
                <option value="{{ $value }}" {{ old('user_type', $processedNotification['template_notification']['type']) == $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="hr-line-dashed"></div>

{{-- To --}}
<div class="form-group">
    <label class="col-sm-2 control-label">To</label>

    <div class="col-sm-10">
        <input class="form-control" value="{{ $processedNotification['data']['sms_data']['phones'] }}" disabled>
    </div>
</div>

{{-- Content --}}
<div class="form-group">
    <label class="col-sm-2 control-label">Content</label>

    <div class="col-sm-10">
        {!! $processedNotification['data']['sms_data']['content'] !!}
    </div>
</div>