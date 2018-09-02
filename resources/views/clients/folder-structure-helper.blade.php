<ul>
    @foreach($subfolders->get() as $subfolder)
        <li>
            <i class="fa fa-folder-o" aria-hidden="true"></i> <span class="folder-name" data-folder-id="{{$subfolder->id}}">{{ $subfolder->name }}</span>
            <span class="files-count">({{$subfolder->files($clientId)->count() + $subfolder->subfolders($clientId)->count()}})</span>
            <a href="#" data-folder-id="{{$subfolder->id}}" class="add-subfolder">Add subfolder</a>

            @if($subfolder->client_id == $client->id)
                <a href="#" data-folder-id="{{$subfolder->id}}" class="delete-folder">Delete</a>
            @endif

            @include('clients.folder-structure-helper',['subfolders' => $subfolder->subfolders($clientId)])
        </li>
    @endforeach
</ul>