<input type="hidden" name="modules[{{$module->id}}][id]" value="{{$module->id}}" />
<input type="hidden" name="modules[{{$module->id}}][has-files-for-upload]" id="has-files-for-upload" value="1">
@if ($settings["upload-required"] == 0)
    <div class="row" id="upload-not-needed-reason-container" style="display: none;">
        <div class="col-md-12">
            <h3>Add a reason the file(s) are not needed.<small> Note: All documentation on this task that is required by law, must be uploaded on the client page if not done on this task now.</small></h3>
            <textarea name="modules[{{$module->id}}][upload-not-needed-reason]" id="upload-not-needed-reason" class="form-control" rows="10" placeholder="Give us the reason to why we are not documenting this file(s)."></textarea>
        </div>
    </div>
@endif

<div class="row" id="file-upload-container">
    <div class="col-md-12">
        @if(!is_null($settings['file-template']) && $settings['has-template'])
            <h4 style="margin: 0 0 15px 0;">File template: <a target="_blank" href="{{ action('FilesController@download', ["subtasks-templates/{$subtask->template->id}", $settings['file-template']]) }}"><i class="glyphicon glyphicon-file fileinput-exists"></i> {{ $settings['file-template'] }}</a></h4>
        @endif
    </div>
    <div class="clear"></div>

    <div class="col-md-12 m-b">
        <h3>In order to complete this subtask you need to submit {{$settings['file-count-type'] == 2 ? 'at least' :''}} {{$settings['file-count']}} file(s)</h3>
    </div>

    <div class="file-upload-container">
        @for($fileIndex=0 ; $fileIndex < $settings['file-count'] ; $fileIndex++)
            <div class="file-upload-row">
                <div class="col-md-12">
                    <div class="input-group">
                        <span class="input-group-addon">File name</span>
                        <input type="text" id="files-to-upload-name-{{$fileIndex}}" name="modules[{{$module->id}}][fileName][]" class="form-control" placeholder="{{ $settings['filename-structure'] }}" required>
                    </div>
                </div>
                <div class="col-md-12 m-b-lg">
                    <div class="dragupload">
                        <input type="file"  id="files-to-upload-{{$fileIndex}}" name="modules[{{$module->id}}][file][]" required>
                        <h3>Drag your file into this box or click in this area to add the file...</h3>
                    </div>
                </div>
            </div>
        @endfor
    </div>

    @if($settings['file-count-type'] == 2)
        <div class="col-md-12">
            <a href="#" class="btn btn-primary" id="add-new-file">+ add new</a>
        </div>
    @endif

</div>
@if ($settings["upload-required"] == 0)
<div class="hr-line-dashed"></div>
<div class="row" id="show-textarea-link-container">
    <div class="col-md-12">
        <div class="form-group">
            <label class="col-md-10 m-b">It is not possible for you to get the required files? <a href="#" id="show-textarea-reason" href="">Click here</a> to give us a reason.</label>
        </div>
    </div>
</div>

<div class="row" id="show-file-link-container" style="display: none;">
    <div class="col-md-12">
        <div class="form-group">
            <label class="col-md-10 m-b">Do you wish to upload file(s) instead? <a href="#" id="show-file-for-upload" href="#">Click here</a> to go back.</label>
        </div>
    </div>
</div>
@endif

@section('script')
<script>    
$(document).ready(function(){
    $('.dragupload input').change(function () {
        $(this).siblings('h3').text( $(this).val().split('\\').pop() );
    });
    $('#add-new-file').on('click', function(e){
        e.preventDefault();
        var template = $('.file-upload-container .file-upload-row:first').clone();
        template.find('input').val('').removeAttr('required');
        template.find('h3').html('Drag your file into this box or click in this area to add the file...');
        $('.file-upload-container').append(template);
        $('.dragupload input').change(function () {
            $(this).siblings('h3').text( $(this).val().split('\\').pop() );
        });
    });

    $("#show-textarea-reason").on('click', function(){
        $("#file-upload-container").hide();
        $("#show-file-link-container").show();
        $("#show-textarea-link-container").hide();
        $("#upload-not-needed-reason-container").show();
        $("#upload-not-needed-reason").prop('required',true);
        $("[id^=files-to-upload-]").removeAttr("required");
        $("[id^=files-to-upload-name-]").removeAttr("required");
        $("#has-files-for-upload").val(0);
    });

    $("#show-file-for-upload").on('click', function(){
        $("#show-textarea-link-container").show();
        $("#show-file-link-container").hide();
        $("#file-upload-container").show();
        $("#upload-not-needed-reason-container").hide();
        $("#upload-not-needed-reason").removeAttr("required");
        $("[id^=files-to-upload-]").prop('required',true);
        $("[id^=files-to-upload-name-]").prop('required',true);
        $("#has-files-for-upload").val(1);
    });

});
</script>
@append