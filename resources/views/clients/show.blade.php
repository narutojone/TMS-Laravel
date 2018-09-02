@extends('layouts.app')

@section('title', $client->name)

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>{{ $client->name }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    @if($client->internal)
                        <a href="{{ url('/clients/internal') }}">Internal Projects</a>
                    @else
                        <a href="{{ action('ClientController@index') }}">Clients</a>
                    @endif
                </li>
                <li class="active">
                    <strong>{{ $client->name }}</strong>
                </li>
            </ol>
        </div>
    </div>
@endsection

@section('content')

    @if(! $client->active)
        <div class="row">
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                    <div class="alert alert-danger m-b-none">
                        <strong>Client is not active!</strong>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($client->paused)
        <div class="row">
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                    <div class="alert alert-danger m-b-none">
                        <strong>Client is currently paused!</strong>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(! $client->paid)
        <div class="row">
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                    <div class="alert alert-danger m-b-none">
                        <strong>Client is deactived!</strong> Invoice has not been paid.
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($client->complaint_case)
        <div class="row">
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                    <div class="alert alert-warning m-b-none">
                        <strong>This customer has an active complaint case!</strong> Please be careful regarding work on this customer.
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($client->internal)
        <div class="row">
            <div class="col-lg-12">
                <div class="wrapper wrapper-content animated fadeInUp" style="padding-bottom: 0;">
                    <div class="alert alert-warning m-b-none">
                        <strong>Please note: </strong>This is an internal project!
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">
                <div class="ibox">
                    <div class="ibox-title">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="m-b-md">
                                    @can('update', $client)
                                        <a href="{{ action('ClientController@edit', $client) }}" class="btn btn-white btn-xs pull-right">Edit client</a>
                                    @endcan
                                    @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_EMPLOYEE))
                                        <a target="_blank" href="http://synega.zendesk.com/agent/tickets/new/1" class="btn btn-primary btn-xs pull-right" style="margin-right: 10px;">New email</a>
                                    @elseif(!$client->internal && $client->zendesk_id)
                                        <a href="{{ action('ClientController@showTickets', $client) }}" class="btn btn-primary btn-xs pull-right" style="margin-right: 10px;">See emails</a>
                                    @endif
                                    <a class="collapse-link">
                                        <div class="btn btn-primary btn-xs pull-right" style="margin-right: 10px;">Show client information</div>
                                    </a>
                                    @if(! $client->internal)
                                        @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN) || Auth::user()->hasRole(\App\Repositories\User\User::ROLE_CUSTOMER_SERVICE))
                                            @if ( ! $client->complaint_case)
                                                <form method="post" action="{{ action('ClientController@addComplaint', $client) }}">
                                                    {{ csrf_field() }}

                                                    <button type="submit" class="btn btn-xs btn-warning pull-right" style="margin-right: 10px;">Add active complaint case</button>
                                                </form>
                                            @endif
                                            @if ($client->complaint_case)
                                                <form method="post" action="{{ action('ClientController@removeComplaint', $client) }}">
                                                    {{ csrf_field() }}

                                                    <button type="submit" class="btn btn-xs btn-warning pull-right m-r-sm">Remove active complaint case</button>
                                                </form>
                                            @endif
                                        @endif
                                    @endif

                                    <h2>{{ $client->name }}</h2>
                                
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ibox-content" style="display: none;">
                        @include('clients.details')
                    </div>
                </div>
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="pull-right">
                                    @if($notes->count() > 1)
                                        <a href="{{ action('ClientController@showNotes', $client) }}" class="btn btn-white btn-xs">Show All Notes</a>
                                    @endif
                                    <button data-toggle="modal" data-target="#add-note-modal" class="btn btn-primary btn-xs">Add note</button>
                                </div>
                                <h3 id="tasks">Notes</h3>
                                @php($note = $notes->first())
                                @if( ! empty($note))
                                    <div class="social-comment">
                                        <div class="media-body">
                                            <strong>{{ $note->user->name }}</strong>
                                            @if(! $note->user->active)
                                                (Deactivated)
                                            @endif
                                            <br>
                                            {{ $note->note }}<br>
                                            <small class="text-muted">@datetime($note->created_at)</small>
                                        </div>
                                    </div>
                                @else
                                    <i class="text-muted">no notes</i>
                                @endif
                            </div>
                        </div>


                        <div class="hr-line-dashed"></div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="pull-right">
                                    <a href="{{ action('ClientController@completed', $client) }}" class="btn btn-white btn-xs">Completed tasks</a>
                                    @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN))
                                        <div class="btn-group align-right">
                                            <button data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle">Add task <span class="caret"></span></button>
                                            <ul class="dropdown-menu">
                                                <li><a href="{{ action('TaskController@create', $client) }}">From template</a></li>
                                                <li><a href="{{ action('TaskController@createCustom', $client) }}">Custom</a></li>
                                            </ul>
                                        </div>
                                    @else
                                        <div class="btn-group align-right">
                                            <a href="{{ action('TaskController@createCustom', $client) }}" class="btn btn-primary btn-xs">Add task</a>
                                        </div>
                                    @endif
                                </div>
                                <h3 id="tasks">Tasks</h3>

                                <div class="table-responsive m-t">
                                    <table class="table table-hover issue-tracker">
                                        <tbody>
                                            @forelse ($tasks as $task)
                                                @if ( ! Auth::user()->admin && ($client->manager_id != Auth::user()->id) && $task->taskOverdueReasons()->orderBy('created_at', 'DESC')->first() && ! $task->taskOverdueReasons()->orderBy('created_at', 'DESC')->first()->overdueReason->visible)
                                                    @continue
                                                @endif
                                                <tr>
                                                    <td class="issue-info">
                                                        <a href="{{ action('TaskController@show', $task) }}">{{ $task->title }}</a>
                                                        <small>{{ $task->category }}</small>
                                                    </td>
                                                    <td>
                                                        @if ($task->user)
                                                            @if (Auth::user()->hasRole(\App\Repositories\User\User::ROLE_ADMIN) || Auth::user()->hasRole(\App\Repositories\User\User::ROLE_CUSTOMER_SERVICE))
                                                                <a href="{{ action('UserController@show', $task->user) }}">{{ $task->user->name }}</a>
                                                            @else
                                                                {{ $task->user->name }}
                                                            @endif
                                                            @if($task->user->out_of_office)
                                                                <span class="label label-danger m-l-md">Out of office</span>
                                                            @endif
                                                        @else
                                                            <i class="text-muted">no user</i>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="label label-{{ $task->deadlineClass() }}" style="{{ $task->isOverdue() && $task->overdueReason ? 'background-color:' . $task->overdueReason->overdueReason->hex : '' }}">@date($task->deadline)</span>
                                                    </td>
                                                    <td>
                                                        <span class="label label-{{ $task->dueDateCountDown()['class'] }}">{{ $task->dueDateCountDown()['label'] }}</span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <i class="text-muted">no tasks</i>
                                            @endforelse
                                        </tbody>
                                    </table>

                                    <div class="text-center">
                                        {{ $tasks->fragment('tasks')->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if (count($mainFolders) && $client->show_folders)
                            {{-- Client files --}}
                            <div class="hr-line-dashed"></div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <h3>Folders <span class="badge m-l-xs"><input type="checkbox" id="folder-edit" class="no-margins" /> Edit mode</span></h3>
                                            <ul id="tree1" class="folder-structure-template client-folders">
                                                @foreach($mainFolders as $mainFolder)
                                                    <li class="main-level">
                                                        <i class="fa fa-folder-o" aria-hidden="true"></i>
                                                        <span class="folder-name" data-folder-id="{{$mainFolder->id}}">{{ $mainFolder->name }}</span>

                                                        <span class="files-count">({{$mainFolder->files($client->id)->count() + $mainFolder->subfolders($client->id)->count()}})</span>

                                                        <a href="#" data-folder-id="{{$mainFolder->id}}" class="add-subfolder">Add subfolder</a>

                                                        @if($mainFolder->client_id == $client->id)
                                                            <a href="#" data-folder-id="{{$mainFolder->id}}" class="delete-folder">Delete</a>
                                                        @endif

                                                        @include('clients.folder-structure-helper',['subfolders' => $mainFolder->subfolders($client->id), 'clientId'=>$client->id])
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="col-md-7">
                                            <h3>Files</h3>
                                            <div class="client-files">
                                                @foreach($client->filesLatest as $file)
                                                    <div class="row file-row" data-folder-id="{{$file->folder_id}}">
                                                        <div class="col-md-5 file-name"><i class="fa fa-file-o" aria-hidden="true"></i> {{$file->name}}</div>
                                                        <div class="col-md-3 actions pull-right text-right">
                                                            <a class="text-info m-r copylink" title="Copy link" data-clipboard-text="{{FileVault::publicUrl($file->filevault_id)}}"><i class="fa fa-files-o" aria-hidden="true"></i></a>
                                                            <a class="text-info m-r" title="Download" target="_blank" href="{{FileVault::publicUrl($file->filevault_id)}}"><i class="fa fa-cloud-download" aria-hidden="true"></i></a>
                                                            <a class="update-file text-warning m-r" data-toggle="modal" data-target="#update-file-modal" title="Update" target="_blank" data-file-id="{{$file->id}}" href="#"><i class="fa fa-cloud-upload" aria-hidden="true"></i></a>
                                                            <a class="delete-file text-danger" href="javascript:void(0)" data-file-id="{{$file->id}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                        </div>
                                                        <div class="col-md-4 actions pull-right text-right">{{$file->updated_at}}</div>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <form style="display:none;" enctype="multipart/form-data" action="{{action('AjaxController@storeClientFile', $client->id)}}" class="dropzone" id="dropzoneForm">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="folder" value="" />
                                                <div class="fallback">
                                                    <input name="file" type="file" />
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($client->phoneSystemLogs()->hasEmployee()->count())
                            <div class="hr-line-dashed"></div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <h3>Phone Call Log</h3>
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th class="text-center">Call Made</th>
                                            <th class="text-center">From</th>
                                            <th class="text-center">Answered</th>
                                            <th class="text-center">Call Start</th>
                                            <th class="text-center">Call End</th>
                                            <th class="text-center">Call Duration</th>
                                            <th class="text-center">Recording</th>
                                            <th class="text-center">Unanswered Call Task</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php($logs = $client->phoneSystemLogsHasEmployeePaginated())
                                        @foreach ($logs as $log)
                                            <tr>
                                                <td class="text-center text-muted">
                                                    {{ $log->created_at->format('j M, H:i:s') }}
                                                </td>
                                                <td class="text-center text-muted">
                                                    @if($log->client)
                                                        +{{ $log->from }}
                                                        <br/>
                                                        <small>
                                                            {{ $log->client->name }}
                                                        </small>
                                                    @else
                                                        +{{ $log->from }}
                                                        <br/>
                                                        <small>
                                                            - not a client -
                                                        </small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($log->media_file)
                                                        {{ $log->employee->name }}
                                                        <br/>
                                                        <small class="text-warning">
                                                            +{{ $log->to }}
                                                        </small>
                                                    @else
                                                        {{ $log->employee->name }}
                                                        <br/>
                                                        <small class="text-danger">
                                                            - no answer -
                                                        </small>
                                                    @endif
                                                </td>
                                                <td class="text-center text-muted">
                                                    @if ($log->media_file)
                                                        {{ $log->start_time->format('j M, H:i:s') }}
                                                    @else
                                                        <small>-</small>
                                                    @endif
                                                </td>
                                                <td class="text-center text-muted">
                                                    @if ($log->media_file)
                                                        {{ $log->end_time->format('j M, H:i:s') }}
                                                    @else
                                                        <small>-</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($log->call_duration)
                                                        {{ gmdate("i:s", $log->call_duration) }}
                                                    @else
                                                        <small>-</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($log->media_file)
                                                        <a href="{{ $log->media_file }}" target="_blank">
                                                            <i class="fa fa-play-circle text-info action-icons" aria-hidden="true"></i>
                                                        </a>
                                                    @else
                                                        <small>-</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if ($task = $log->task)
                                                        <a href="{{ route('tasks', $task) }}">
                                                            <i class="fa fa-list text-info action-icons" aria-hidden="true"></i>
                                                            {{ $task->title }}
                                                        </a>
                                                    @else
                                                        <small>-</small>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    {{ $logs->links() }}
                                </div>
                            </div>
                        @endif
                        {{-- Show all notification logs --}}
                        <div class="hr-line-dashed"></div>
                        <div class="row">
                            <div class="col-lg-12">
                                @if($notifications->count() > 0)
                                    <div class="pull-right">
                                        <a href="{{ action('ClientController@showNotifications', $client) }}" class="btn btn-white btn-xs">Show All Notifications</a>
                                    </div>
                                @endif
                                <h3 id="notifications">Notifications</h3>
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th class="text-center">To</th>
                                        <th class="text-center">Date sent</th>
                                        <th class="text-center">Notification type</th>
                                        <th class="text-center">Actions/Content</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($notifications as $notification)
                                        <tr>
                                            <td class="text-center text-muted">
                                                {{ $notification->to }}
                                            </td>
                                            <td class="text-center text-muted">
                                                @date($notification->created_at)
                                            </td>
                                            <td class="text-center text-muted">
                                                {{ $notification->type }}
                                            </td>
                                            <td class="text-center text-muted">
                                                <a href="{{ action('NotifierLogController@show', $notification) }}" class="btn btn-white btn-sm">
                                                    <i class="fa fa-folder"></i> Show content
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="hr-line-dashed"></div>
                            <div class="col-lg-12">
                                @if($client->hasFlags())
                                    <div class="pull-right">
                                        <a href="{{ action('ClientController@showFlags', $client) }}" class="btn btn-white btn-xs">Show All Flags</a>
                                    </div>
                                @endif
                                <h3>Flags</h3>
                                <table class="table table-hover">
                                    @if ($client->hasFlags())
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Flag title</th>
                                            <th>Flag date</th>
                                            <th>Valid To</th>
                                            <th>Client</th>
                                            <th>Comment</th>
                                        </tr>
                                    </thead>
                                    @endif
                                    <tbody>
                                    @forelse ($flags as $flag)
                                        <tr>
                                            <td>@include('flag-user.flagged', ['color' => $flag->hex])</td>
                                            <td class="project-title">
                                                {{ $flag->reason }}
                                            </td>
                                            <td>
                                                {{ Carbon\Carbon::parse($flag->pivot->created_at)->format('Y-m-d') }}
                                            </td>
                                            <td>
                                                {{ $flag->validTo() }}
                                            </td>
                                            <td>
                                                {{ App\Repositories\User\User::find($flag->pivot->user_id)->name }}
                                            </td>
                                            <td>
                                                {{ $flag->pivot->comment }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <span><em>no flags...</em></span>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Modal for FileVault file update/replace --}}
    <div class="modal inmodal in" id="update-file-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content animated bounceInRight">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <i class="fa fa-laptop modal-icon"></i>
                    <h4 class="modal-title">File update/replace</h4>
                </div>
                <form method="post" id="update-file-form" enctype="multipart/form-data" action="{{action('FilesController@updateFile')}}">
                    <div class="modal-body">
                        <p>By uploading a new file you'll be replacing the existing one.</p>

                        {{ csrf_field() }}
                        <input type="hidden" name="file-id" value="" />
                        <input type="hidden" name="client-id" value="{{$client->id}}" />
                        <input type="hidden" name="folder-id" value="" />

                        <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                            <div class="form-control" data-trigger="fileinput">
                                <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                <span class="fileinput-filename"></span>
                            </div>
                            <span class="input-group-addon btn btn-default btn-file">
                                <span class="fileinput-new">Select file</span>
                                <span class="fileinput-exists">Change</span>
                                <input type="file" name="file" required/>
                            </span>
                            <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="add-note-modal" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add note</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ action('ClientController@updateNote', $client) }}">
                        {{ csrf_field() }}

                        {{-- Note --}}
                        <div class="form-group{{ $errors->has('note') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="note">Client note</label>

                            <div class="col-sm-10">
                                <textarea type="text" class="form-control" name="note" id="note"></textarea>

                                @if ($errors->has('organization_number'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('note') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group m-b-none">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button id="cancel-add-note" type="button" class="btn btn-default">Cancel</button>
                                <button class="btn btn-primary" type="submit">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        /* Initialization of clipboard */
        new Clipboard('.copylink');

        $( document ).ready(function() {
            $( "#client-risk-note" ).on( "click", function( event ) {
                swal({
                    title: "High risk reason",
                    text: $(this).data('content'),
                    type: "info"
                });
            });

            $( ".folder-name" ).on( "click", function( event ) {
                var folderId = $(this).attr('data-folder-id');
                // show selected folder
                $('.folder-structure-template .folder-name').removeClass('selected');
                $(this).addClass('selected');
                // hide all files and show only those ones that belong to this folder
                $('.client-files .file-row').hide();
                $('.client-files .file-row[data-folder-id="'+folderId+'"]').show();
                $('#dropzoneForm').show();
                $('#dropzoneForm').find('input[name="folder"]').val(folderId);
                $('#update-file-form').find('input[name="folder-id"]').val(folderId);
            });

            $(".add-subfolder").on( "click", function( event ) {
                var folderId = $(this).attr('data-folder-id');
                swal({
                  title: 'Enter folder name',
                  input: 'text',
                  showCancelButton: true,
                  confirmButtonText: 'Submit',
                  showLoaderOnConfirm: true,
                  preConfirm: function (text) {
                    return new Promise(function (resolve, reject) {
                        $.ajax({
                            method: "POST",
                            url: "{{action('AjaxController@createClientFolder', $client->id)}}",
                            data: { _token: "{{csrf_token()}}", folderName: text, parent: folderId }
                        })
                        .done(function( msg ) {
                            if(msg.success == true) {
                                resolve();
                                location.reload();
                            }
                            else {
                                reject(msg.error);
                            }

                        })
                        .error(function(){
                            reject('Internal error')
                        });
                    })
                  },
                  allowOutsideClick: false
                }).catch(swal.noop);
            });



            $(".delete-folder").on( "click", function( event ) {
                var folderId = $(this).data('folder-id');
                swal({
                  title: 'Are you sure?',
                  text: "You won't be able to revert this!",
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes, delete it!'
                }).then(function () {
                    $.ajax({
                        method: "POST",
                        url: "{{action('AjaxController@deleteClientFolder', $client->id)}}",
                        data: { _token: "{{csrf_token()}}", folderId: folderId }
                    })
                    .done(function( msg ) {
                        if(msg.success == true) {
                            swal( 'Deleted!', 'Your folder has been deleted.', 'success');
                            location.reload();
                        }
                        else {
                            swal( 'Delete failed', msg.error, 'error');
                        }
                    })
                    .error(function(){
                        swal( 'Deleted!', 'Internal error', 'error');
                    });
                })
            });

            $("#folder-edit").on( "change", function( event ) {
                if($(this).is(':checked')) {
                    $('.client-folders').addClass('edit-mode');
                }
                else {
                    $('.client-folders').removeClass('edit-mode');
                }
            });


            $(".delete-file").on( "click", function( event ) {
                var fileId = $(this).data('file-id');
                var $fileRow = $(this).closest('.file-row');
                swal({
                  title: 'Are you sure?',
                  text: "You won't be able to revert this! All deletions of files located in TMS are reported and logged to the user who deleted it.",
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes, delete it!'
                }).then(function () {
                    $.ajax({
                        method: "POST",
                        url: "{{action('AjaxController@deleteFile', $client->id)}}",
                        data: { _token: "{{csrf_token()}}", fileId: fileId }
                    })
                    .done(function( msg ) {
                        if(msg.success == true) {
                            swal( 'Deleted!', 'Your file has been deleted.', 'success');
                            $fileRow.remove();
                        }
                        else {
                            swal( 'Deleted!', 'File has not been deleted.', 'error');
                        }
                    })
                    .error(function(){
                        swal( 'Deleted!', 'Internal error', 'error');
                    });
                })
            });

            $(".update-file").on( "click", function( event ) {
                $('#update-file-form').find('input[name="file-id"]').val($(this).data('file-id'));
            });

            @if (request()->has('logs_page'))
                $('#employee-logs-modal').modal('show');
            @endif

            $('#employee-logs-modal').on('hidden.bs.modal', function () {
                var url = window.location.href.replace(window.location.search, '');
                window.history.pushState({path:url},'',url);
            });

            $('.modal').on('hidden.bs.modal', function () {
                $('.wrapper').removeClass('animated');
            });

            $("#risk").on("change", function (event) {
                if ($(this).val() == 1) {
                    $('#risk-reason-container').show();
                }
                else {
                    $('#risk-reason-container').hide();
                }
            });
        });

        /* Initialization of treeviews */
        $('#tree1').treed();

        $('#cancel-add-note').click(function (e) {
            var $modal = $('#add-note-modal');

            $modal.find('textarea').val('');
            $modal.modal('hide');
        });
    </script>
@append
