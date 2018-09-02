{{-- Custom title --}}
<div class="form-group">
    <label class="col-sm-2 control-label">Custom title</label>
    <div class="col-md-10">
        <input type="text" class="form-control" name="modules[{{$module->id}}][custom-title]" value="{{ $settings['custom-title'] }}" />
    </div>
</div>

{{-- Filename structure --}}
<div class="form-group">
    <label class="col-sm-2 control-label">Filename structure</label>
    <div class="col-md-10">
        <input type="text" class="form-control" name="modules[{{$module->id}}][filename-structure]" value="{{ $settings['filename-structure'] }}" placeholder="Example: filename-DDMMYYYY.docx" />
    </div>
</div>

{{--File template--}}
<div class="form-group">
    <label class="col-sm-2 control-label" for="has-template">File template</label>
    <div class="col-md-1">
        <input type="checkbox" id="has-template" data-size="" class="js-switch" value="on" name="modules[{{$module->id}}][has-template]" {{ $settings['has-template'] ? 'checked' : ''}}>
    </div>
    <div class="col-md-5 template-file-container" style="{{ !$settings['has-template'] ? 'display:none' : '' }}">
        <div class="fileinput fileinput-new input-group m-b" data-provides="fileinput">
            <div class="form-control" data-trigger="fileinput">
                <i class="glyphicon glyphicon-file fileinput-exists"></i>
            <span class="fileinput-filename"></span>
            </div>
            <span class="input-group-addon btn btn-default btn-file">
                <span class="fileinput-new">Select file</span>
                <span class="fileinput-exists">Change</span>
                <input type="file" name="modules[{{$module->id}}][fileTemplate]" value=""/>
            </span>
            <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
        </div>
    </div>
    <div class="col-md-3 template-file-container" style="{{ !$settings['has-template'] ? 'display:none' : '' }}">
        @if($settings['has-template'])
            <label class="control-label"><a target="_blank" href="{{ action('FilesController@download', ["subtasks-templates/{$subtask->id}", $settings['file-template']]) }}"><i class="glyphicon glyphicon-file fileinput-exists"></i> {{ $settings['file-template'] }}</a></label>
        @endif
    </div>

    <div class="clear"></div>

    @if($settings['has-template'])
        <div class="col-md-5 col-md-push-3 template-file-container">By uploading another file you'll overwrite the existing one</div>
    @endif
</div>

{{--File required--}}
<div class="form-group">
    <label class="col-sm-2 control-label" for="authorized">Upload is required</label>
    <div class="col-md-2">
        <input type="checkbox" id="upload-required" data-size="" class="js-switch" value="1" name="modules[{{$module->id}}][upload-required]" {{ $settings['upload-required'] ? 'checked' : ''}}>
    </div>
</div>

{{-- Custom subfolders for tasks /year and /year/termin --}}
<div class="form-group">
    <label class="col-sm-2 control-label" for="authorized">Subfolders</label>
    <div class="col-md-2">
        <input type="checkbox" id="year" data-size="" class="js-switch" value="on" name="modules[{{$module->id}}][year]" {{ $settings['year'] ? 'checked' : ''}}> <label class="fileinput-inline">Year</label>
    </div>
    <div class="col-md-2">
        <input type="checkbox" id="termin" data-size="" class="js-switch" value="on" name="modules[{{$module->id}}][termin]" {{ $settings['termin'] ? 'checked' : ''}}> <label class="fileinput-inline">Termin</label>
    </div>
    <div class="col-md-3">
        <div class="input-group m-b">
            <input type="number" step="1" id="deadline-offset" class="form-control" value="{{ $settings['deadline-offset'] }}" name="modules[{{$module->id}}][deadline-offset]" >
            <span class="input-group-addon">Deadline offset</span>
        </div>
    </div>
</div>

{{-- Number of file uploads --}}
<div class="form-group">
    <label class="col-sm-2 control-label" for="authorized">Files count</label>
    <div class="col-md-2">
        <select class="form-control" name="modules[{{$module->id}}][file-count-type]">
            @foreach($settings['file-count-types'] as $typeId=>$typeValue)
                <option value="{{$typeId}}" {{$typeId == $settings['file-count-type'] ? 'selected' : ''}}>{{$typeValue}}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-1">
        <input type="number" min="1" step="1" class="form-control" value="{{$settings['file-count']}}" name="modules[{{$module->id}}][file-count]" placeholder="No. of files" />
    </div>
</div>

{{-- File upload location --}}
<div class="form-group">
    <label class="col-sm-2 control-label" for="authorized">File upload location</label>
    <div class="col-sm-5">
        <ul id="tree1" class="folder-structure-template" style="padding-left:0;">
            @foreach($settings['mainFolders'] as $mainFolder)
                <li>
                    <input type="radio" name="modules[{{$module->id}}][folder]" value="{{$mainFolder->id}}"  {{$settings['folder'] == $mainFolder->id ? 'checked="checked"' : ''}} />
                    <i class="fa fa-folder-o" aria-hidden="true"></i> <span class="folder-name">{{ $mainFolder->name }}</span>
                    @if(count($mainFolder->subfolders))
                        @include('templates.subtasks.modules.file-upload.folder-structure-helper',['subfolders' => $mainFolder->subfolders, 'selectedFolder'=>$settings['folder']])
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>

@section('script')
    <script type="text/javascript">
        $('#has-template').on('change', function(){
            if($(this).is(':checked')) {
                $('.template-file-container').show();
            }
            else {
                $('.template-file-container').hide();
            }
        });
    </script>
@append
