<ul>
    @foreach($subfolders as $subfolder)
        @if( (!isset($checkVisibility) || !$checkVisibility) || ( isset($checkVisibility) && $checkVisibility) && ($subfolder->visible || Auth::user()->isAdminOrCustomerService()))
            <li>
                <i class="fa fa-folder-o" aria-hidden="true"></i> <span class="folder-name" data-folder-id="{{$subfolder->id}}">{{ $subfolder->name }}</span>

                @if(isset($addFolderButton) && $addFolderButton)
                    <a href="{{ action('LibraryController@addFolder', $subfolder->id) }}"><i class="fa fa-plus" aria-hidden="true"></i> Add subfolder</a>
                @endif

                @if(isset($addDeleteButton) && $addDeleteButton)
                    <a href="#" class="delete-folder" data-folder-id="{{ $subfolder->id }}"><i class="fa fa-minus" aria-hidden="true"></i> Delete</a>
                @endif

                @include('settings.library.folder-structure-helper',['subfolders' => $subfolder->subfolders])
            </li>
        @endif
    @endforeach
</ul>