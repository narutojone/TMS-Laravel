@extends('layouts.app')

@section('title', 'Client storage structure')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Client folders structure</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li class="active">
                <strong>Folders structure</strong>
            </li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content">

            <div class="ibox">
                <div class="ibox-title">
                    <h5>General storage structure that will be applied for each client</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-6">
                            <ul id="tree1" class="folder-structure-template">
                                <li><a href="{{ action('FoldersStructureController@addFolder', 0) }}" class=""><i class="fa fa-plus" aria-hidden="true"></i> Add root folder</a></li>
                                @foreach($mainFolders as $mainFolder)
                                    <li>
                                        <i class="fa fa-folder-o" aria-hidden="true"></i> <span class="folder-name">{{ $mainFolder->name }}</span>
                                        <a href="{{ action('FoldersStructureController@addFolder', $mainFolder->id) }}" class=""><i class="fa fa-plus" aria-hidden="true"></i> Add subfolder</a>
                                        <a href="#" class="delete-folder" data-folder-id="{{ $mainFolder->id }}"><i class="fa fa-minus" aria-hidden="true"></i> Delete</a>

                                        @include('settings.folder-structure-helper',['subfolders' => $mainFolder->subfolders, 'addFolderButton'=>true])
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
    <script type="text/javascript">
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
                    url: "{{action('FoldersStructureController@deleteFolder')}}",
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
