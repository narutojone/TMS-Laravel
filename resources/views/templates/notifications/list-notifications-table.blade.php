<table class="table table-striped">
    <thead>
    <tr>
        <th style="width: 110px">
            <div>
                <label>
                    <input type="checkbox" id="select-all" class="">
                    <label id="label-text">Select all</label>
                </label>
            </div>
        </th>
        <th>Template</th>
        <th>Type</th>
        <th>User type</th>
        <th>Name</th>
        <th>To</th>
        <th>Subject</th>
        <th>Created At</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($processedNotifications as $processedNotification)
        <tr class="table-rows">
            <td>
                <div class="icheckbox_square-green">
                    <input type="checkbox" class="i-checks" name="processedNotificationIds[]" value="{{ $processedNotification->id }}">
                </div>
            </td>
            <td>
                @if ($processedNotification->templateNotification)
                    {{ $processedNotification->templateNotification->template->title }}
                @endif

            </td>
            <td>
                @if ($processedNotification->templateNotification)
                    {{ $processedNotification->templateNotification->type }}
                @elseif ($processedNotification->type)
                    template
                @else
                    sms
                @endif
            </td>
            <td>
                @if ($processedNotification->templateNotification)
                    {{ $processedNotification->templateNotification->user_type }}
                @endif
            </td>
            <td>
                @if (isset($processedNotification->decodedData['clientName']))
                    {{ $processedNotification->decodedData['clientName'] }}
                @endif
            </td>
            <td>
                @if ($processedNotification->decodedData['to'] && gettype($processedNotification->decodedData['to']) == 'string')
                    {{ $processedNotification->decodedData['to'] }}
                @elseif($processedNotification->decodedData['to'] && gettype($processedNotification->decodedData['to']) == 'array')
                    {{ implode(",", $processedNotification->decodedData['to']) }}
                @else
                    N\A
                @endif
            </td>
            <td>
                {{ (isset($processedNotification->decodedData['email_template'])) ? $processedNotification->decodedData['email_template']['subject'] : 'N\A' }}
            </td>
            <td>
                @date($processedNotification->created_at)
            </td>
            <td>
                <a href="{{ action('ProcessedTemplatesController@show', $processedNotification) }}" class="btn btn-white btn-sm">
                    <i class="fa fa-folder"></i> View
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>