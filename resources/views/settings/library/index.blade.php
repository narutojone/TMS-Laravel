@extends('layouts.app')

@section('title', 'Library')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Library</h2>
            <ol class="breadcrumb">
                <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                <li class="active"><strong>Library</strong></li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-5">
            <div class="wrapper wrapper-content">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Folders management</h5>
                    </div>
                    <div class="ibox-content">
                        <ul id="tree1" class="folder-structure-template">
                            <li><a href="{{ action('LibraryController@addFolder', 0) }}" class=""><i class="fa fa-plus" aria-hidden="true"></i> Add root folder</a></li>
                            @foreach($mainFolders as $mainFolder)
                                <li>
                                    @if(! $mainFolder->visible)
                                        <div class="pull-right">
                                            <span class="label label-danger m-l-md">Hidden folder</span>
                                        </div>
                                    @endif
                                    <i class="fa fa-folder-o" aria-hidden="true"></i> <span class="folder-name" data-folder-id="{{ $mainFolder->id }}">{{ $mainFolder->name }}</span>
                                    <a href="{{ action('LibraryController@addFolder', $mainFolder->id) }}" class=""><i class="fa fa-plus" aria-hidden="true"></i> Add subfolder</a>
                                    <a href="#" class="delete-folder" data-folder-id="{{ $mainFolder->id }}"><i class="fa fa-minus" aria-hidden="true"></i> Delete</a>

                                    @include('settings.library.folder-structure-helper',['subfolders' => $mainFolder->subfolders, 'addFolderButton'=>true, 'addDeleteButton'=>true])
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="wrapper wrapper-content">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Files management <small>click on a folder to manage files inside it</small></h5>
                    </div>
                    <div class="ibox-content">
                        <div id="upload-control">
                            <form method="post" action="{{ action('LibraryController@uploadFiles') }}" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                    <div class="form-control" data-trigger="fileinput">
                                        <i class="glyphicon glyphicon-file fileinput-exists"></i>
                                        <span class="fileinput-filename"></span>
                                    </div>
                                    <span class="input-group-addon btn btn-default btn-file">
                                        <span class="fileinput-new">Select file(s)</span>
                                        <span class="fileinput-exists">Change</span>
                                        <input type="file" multiple name="files[]" />
                                    </span>
                                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                </div>
                                <input type="hidden" id="folderId" name="folderId" value="6" />
                                <input type="submit" class="btn btn-default" value="UPLOAD" />
                            </form>
                        </div>
                        <div class="client-files">
                            @foreach($files as $file)
                                <div class="row file-row" data-folder-id="{{$file->folder_id}}">
                                    <div class="col-md-6 file-name"><i class="fa fa-file-o" aria-hidden="true"></i> {{$file->name}}</div>
                                    <div class="col-md-2 actions pull-right text-right">
                                        <a class="text-info m-r" target="_blank" href="{{action('LibraryController@downloadFile', $file->id)}}"><i class="fa fa-cloud-download" aria-hidden="true"></i></a>
                                        <a class="delete-file text-danger" href="javascript:void(0)" data-file-id="{{$file->id}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                    </div>
                                    <div class="col-md-4 actions pull-right text-right">{{$file->created_at}}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $( ".folder-name" ).on( "click", function( event ) {
            var folderId = $(this).attr('data-folder-id');
            // show selected folder
            $('.folder-structure-template .folder-name').removeClass('selected');
            $(this).addClass('selected');
            // hide all files and show only those ones that belong to this folder
            $('.client-files .file-row').hide();
            $('.client-files .file-row[data-folder-id="'+folderId+'"]').show();
            $('#dropzoneForm').show();
            $('#folderId').val(folderId);
        });

        $(".delete-file").on( "click", function( event ) {
            var fileId = $(this).data('file-id');
            var $fileRow = $(this).closest('.file-row');
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
                    url: "{{action('LibraryController@deleteFile')}}",
                    data: { _token: "{{csrf_token()}}", fileId: fileId }
                })
                .done(function( msg ) {
                    if(msg.success == true) {
                        swal( 'Deleted!', 'Your file has been deleted.', 'success');
                        $fileRow.remove();
                    }
                    else {
                        swal( 'Deletion failed!', 'File has not been deleted.', 'error');
                    }
                })
                .error(function(){
                    swal( 'Deleted!', 'Internal error', 'error');
                });
            })
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
                    url: "{{action('LibraryController@deleteFolder')}}",
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
    </script>
@append
