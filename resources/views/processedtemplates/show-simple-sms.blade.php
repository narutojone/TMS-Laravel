{{-- Type --}}
<div class="form-group">
    <label class="col-sm-2 control-label" for="title">Type</label>

    <div class="col-sm-10">
        <select name="type" id="type" class="form-control chosen-select" required autofocus disabled>
            @foreach(array_keys(config('tms.notifiers')) as $type)
            <option value="{{ $type }}" {{ old('type', 'sms') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="hr-line-dashed"></div>

{{-- To --}}
<div class="form-group">
    <label class="col-sm-2 control-label">To</label>

    <div class="col-sm-10">
        @if (isset($processedNotification['data']['sms_data']['phones']))
            <input class="form-control" value="{{ $processedNotification['data']['sms_data']['phones'] }}" disabled>
        @else
            @if (is_array($processedNotification['data']['to']))
                <input class="form-control" value="{{ implode(" ", $processedNotification['data']['to']) }}" disabled>
            @else
                <input class="form-control" value="{{$processedNotification['data']['to'] }}" disabled>
            @endif
        @endif
    </div>
</div>

{{-- Content --}}
<div class="form-group">
    <label class="col-sm-2 control-label">Content</label>

    <div class="col-sm-10">
        @if (isset($processedNotification['data']['sms_data']))
            {!! $processedNotification['data']['sms_data']['content'] !!}
        @else
            {!! $processedNotification['data']['email_template']['content'] !!}
        @endif
    </div>
</div>