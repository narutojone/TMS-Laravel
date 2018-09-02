<ul>
    @foreach($subfolders as $subfolder)
        <li>
            <input type="radio" name="modules[{{$module->id}}][folder]" value="{{$subfolder->id}}" {{$subfolder->id == $selectedFolder ? 'checked="checked"' : ''}}   />

            <i class="fa fa-folder-o" aria-hidden="true"></i> <span class="folder-name">{{ $subfolder->name }}</span>

            @if(isset($filesCount) && $filesCount)
                <span class="files-count">({{$subfolder->files($clientId)->count()}})</span>
            @endif

            @if(isset($addFolderButton) && $addFolderButton)
                <a href="{{ action('FoldersStructureController@addFolder', $subfolder->id) }}"><i class="fa fa-plus" aria-hidden="true"></i> Add subfolder</a>
            @endif
            @if(count($subfolder->subfolders))
                @include('templates.subtasks.modules.file-upload.folder-structure-helper',['subfolders' => $subfolder->subfolders])
            @endif
        </li>
    @endforeach
</ul>