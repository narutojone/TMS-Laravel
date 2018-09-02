@extends('layouts.app')

@section('title', 'File library')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Library</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li class="active">
                    <strong>Files library</strong>
                </li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-5">
            <div class="wrapper wrapper-content">

                {{-- Categories --}}
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Folders</h5>
                    </div>
                    <div class="ibox-content">
                        <ul class="folder-structure-template client-folders no-padding">
                            @foreach($mainFolders as $mainFolder)
                                @if($mainFolder->visible || Auth::user()->isAdminOrCustomerService())
                                    <li>
                                        <i class="fa fa-folder-o" aria-hidden="true"></i>
                                        <span class="folder-name" data-folder-id="{{$mainFolder->id}}">{{ $mainFolder->name }}</span>

                                        <a href="#" data-folder-id="{{$mainFolder->id}}" class="add-subfolder"><i class="fa fa-plus" aria-hidden="true"></i> Add subfolder</a>

                                        @include('settings.library.folder-structure-helper',['subfolders' => $mainFolder->subfolders, 'checkVisibility' => true])
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="wrapper wrapper-content">
                <div class="ibox">
                    <div class="ibox-title"><h5>Files <small>Client on a folder to view it's content</small></h5></div>
                    <div class="ibox-content">
                        <div class="client-files">
                            @foreach($files as $file)
                                <div class="row file-row" data-folder-id="{{$file->folder_id}}">
                                    <div class="col-md-6 file-name"><i class="fa fa-file-o" aria-hidden="true"></i> {{$file->name}}</div>
                                    <div class="col-md-2 actions pull-right text-right">
                                        <a class="text-info m-r" target="_blank" href="{{action('LibraryController@downloadFile', $file->id)}}"><i class="fa fa-cloud-download" aria-hidden="true"></i></a>
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
        $(document).ready(function() {
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

            });
        });
    </script>
@append