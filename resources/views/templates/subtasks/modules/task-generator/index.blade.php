{{-- Custom title --}}
<div class="form-group">
    <label class="col-sm-2 control-label">Custom title</label>
    <div class="col-md-10">
        <input type="text" class="form-control" name="modules[{{$module->id}}][custom-title]" value="{{ $settings['custom-title'] }}" />
    </div>
</div>
<div class="hr-line-dashed"></div>

@foreach($settings['assignments'] as $key => $assignment)
    <div class="form-group" template="template-assignee" no="{{ $key +1 }}">
        <div class="row">
            <label class="col-sm-1 control-label" for="template">Template</label>
            <div class="col-md-2">
                <select class="form-control chosen-select chosen-select-hidden" title="Template" name="modules[{{$module->id}}][template][]" id="" style="min-width: 160px;">
                    <option></option>
                    @foreach ($settings['templates'] as $template)
                        <option value="{{ $template->id }}" {{$template->id == $assignment['template'] ? 'selected' : ''}} >{{ $template->title }}</option>
                    @endforeach
                </select>
            </div>

            <label class="col-sm-1 control-label" for="assignee">Assignee</label>
            <div class="col-md-2">
                <select class="form-control chosen-select-hidden" title="Assignee" name="modules[{{$module->id}}][assignee][]" id="" style="min-width: 160px;">
                    <option></option>
                    @foreach($settings['assignees'] as $value => $title)
                        <option value="{{ $value }}" {{ (isset($assignment['assignee']) && $value == $assignment['assignee']) ? 'selected' : ''}}>{{ $title }}</option>
                    @endforeach
                </select>
            </div>

            <label class="col-sm-1 control-label" for="assignee">Target</label>
            <div class="col-md-1">
                <select class="form-control chosen-select-hidden" title="Target" name="modules[{{$module->id}}][target][]" id="" style="min-width: 160px;">
                    <option></option>
                    @foreach($settings['targets'] as $value => $title)
                        <option value="{{ $value }}" {{(isset($assignment['target']) && $value == $assignment['target']) ? 'selected' : ''}}>{{ $title }}</option>
                    @endforeach
                </select>
            </div>

            <label class="col-sm-2 control-label" for="deadline-offset">Deadline Offset (days)</label>
            <div class="col-md-1">
                <input type="number" value="{{ $assignment['deadline-offset'] }}" class="form-control" name="modules[{{$module->id}}][deadline-offset][]">
            </div>

            <div class="col-sm-1">
                <button type="button" id="remove_{{$key + 1}}" class="btn btn-xs btn-danger" style="margin-top: 5px;">Remove</button>
            </div>
        </div>

        <div class="row">
            <br />
            <label class="col-sm-1 control-label" for="deadline-offset">Repeating</label>
            <div class="col-sm-2">
                <select class="form-control chosen-select-hidden" title="Repeating" name="modules[{{$module->id}}][repeating][]" id="" style="min-width: 160px;">
                    <option></option>
                    @foreach($settings['repeatingOptions'] as $value => $title)
                        <option value="{{ $value }}" {{(isset($assignment['repeating']) && $value == $assignment['repeating']) ? 'selected' : ''}}>{{ $title }}</option>
                    @endforeach
                </select>
            </div>
            <label class="col-sm-1 control-label" for="deadline-offset">Frequency</label>
            <div class="col-sm-2">
                <input value="{{ $assignment['frequency'] }}" class="form-control" name="modules[{{$module->id}}][frequency][]">
            </div>
            {{--Private--}}
            <label class="col-sm-1 control-label" for="authorized">Private</label>
            <div class="col-md-2">
                <input type="hidden" class="private-hiddens" name="modules[{{$module->id}}][private][]">
                <input type="checkbox" id="private" data-size="" class="js-switch private-checkbox" name="private_checkboxes" {{ isset($assignment['private']) && $assignment['private'] ? 'checked' : ''}}>
            </div>
            <br/><br/>
        </div>
        <div class="hr-line-dashed"></div>

    </div>
@endforeach

<div class="form-group" id="button-add-new-container">
    <label class="col-sm-2 control-label">&nbsp;</label>
    <div class="col-md-2">
        <button id="add-new" type="button" class="btn btn-primary btn-xs">Add New Row</button>
    </div>
</div>

@section('script')
    <script type="text/javascript">
        function removeClickEvent() {
            $('[id^="remove_"]').off('click').on('click', function(){
                $(this).parents('.form-group:first').remove();
            });
        }

        $('#add-new').on('click', function(){
            $no = $("[template='template-assignee']").length;

            $templateAssigneeClone = $("[template='template-assignee'][no='1']").clone();
            $templateAssigneeClone.attr('no', $no + 1);
            $templateAssigneeClone.find('button').attr('id', 'remove_'+($no + 1));

            $templateAssigneeClone.find(".chosen-container.chosen-container-single").remove();

            $('#button-add-new-container').before($templateAssigneeClone);
            removeClickEvent();

            $templateAssigneeClone.find('select').val('');
            $templateAssigneeClone.find('input').val(0);

            var elem = $templateAssigneeClone.find('input[type=checkbox]');
            //remove cloned switchery checkbox as it's 'dead'
            elem.siblings().not('input[type=hidden]').remove();
            elem.prop('checked', false);
            //init a new switchery checkbox
            new Switchery(elem[0], { color: '#1ab394', secondaryColor: '#dfdfdf', jackColor: '#ffffff', jackSecondaryColor: '#ffffff' });

            $templateAssigneeClone.find(".chosen-select-hidden").chosen({
                allow_single_deselect: true,
                width: "100%",
            });
        });

        $('form').one("submit", function(e){
            e.preventDefault();
            var private_checkboxes = $('.private-checkbox');
            var private_hiddens = $('.private-hiddens');
            $.each(private_checkboxes, function (index, element) {
                $(private_hiddens[index]).val($(element).is(":checked") ? 1 : 0); //this is done because checkbox doesn't send a value at all if it's unchecked. But we need it
            });
            $(this).submit();
        });

        removeClickEvent();
    </script>
@append
