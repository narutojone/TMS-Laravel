<div class="table-responsive">
    <table class="table table-hover issue-tracker m-b-none">
        @foreach ($tasks as $task)
            <tr>
                <td class="issue-info">
                    <a href="{{ action('TaskController@show', $task) }}">{{ $task->title }}</a>
                    <small>{{ $task->category }}</small>
                </td>
                <td>
                    <a href="{{ action('ClientController@show', $task->client) }}">{{ $task->client->name }}</a>
                </td>
                <td class="reason-td">
                    @if($task->overdueReason)
                        Reason: {{ $task->overdueReason->reason->reason }}
                    @endif
                </td>
                <td style="width:130px">
                    <span class="label label-{{ $severity }}" style="{{ $task->isOverdue() && $task->overdueReason ? 'background-color:' . $task->overdueReason->reason->hex : '' }}">@date($task->deadline)</span>
                </td>
                <td style="width:100px">
                    <span class="label label-{{ $task->dueDateCountDown()['class'] }}">{{ $task->dueDateCountDown()['label'] }}</span>
                </td>
                <td style="width:145px">
                    @if($task->askForOverdueReason())
                            <a data-toggle="modal" class="btn btn-white btn-xs" href="#modal-form-{{ $task->id }}">Add overdue reason</a>
                            @include('tasks.add-overdue-reason', ['tasks' => $task, 'overdues' => $overdues])
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
</div>
