<ul>
    @foreach($subfolders as $subfolder)
        <li>
            @if(isset($selectors) && $selectors)
                <input type="radio" name="folder" value="{{$subfolder->id}}" {{$subfolder->id == $selectedFolder ? 'checked="checked"' : ''}}   />
            @endif

            <i class="fa fa-folder-o" aria-hidden="true"></i> <span class="folder-name" data-folder-id="{{$subfolder->id}}">{{ $subfolder->name }}</span>

            @if(isset($filesCount) && $filesCount)
                <span class="files-count">({{$subfolder->files($clientId)->count()}})</span>
            @endif

            @if(isset($addFolderButton) && $addFolderButton)
                <a href="{{ action('FoldersStructureController@addFolder', $subfolder->id) }}"><i class="fa fa-plus" aria-hidden="true"></i> Add subfolder</a>
            @endif

            <a href="#" class="delete-folder" data-folder-id="{{ $subfolder->id }}"><i class="fa fa-minus" aria-hidden="true"></i> Delete</a>

            @include('settings.folder-structure-helper',['subfolders' => $subfolder->subfolders])
        </li>
    @endforeach
</ul>