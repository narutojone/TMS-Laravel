@php
    $editorId = isset($id) ? $id : 'editor';
@endphp

<div id="{{$editorId}}"></div>

@section('script')
<script>
    $( document ).ready(function() {
        // Initialize the editor as read-only
        var {{$editorId}} = new Quill('#{{$editorId}}', {
            readOnly: true
        });

        // Set the contents
        {{$editorId}}.setContents({!! str_replace('</script>', '<\/script>', $delta) !!});
    });
</script>
@append

