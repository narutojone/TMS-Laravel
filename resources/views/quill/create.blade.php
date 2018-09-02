<input type="hidden" name="{{ $name }}">
<input type="hidden" name="{{ $name }}_html">
<div id="{{ $id or 'editor' }}"></div>

@section('script')
@php($ucName = ucfirst($name))
<script>
// Initialize the editor
var quill{{ $ucName }} = new Quill('#{{ $id or 'editor' }}', {
    modules: {
        toolbar: [
            ['bold', 'italic', 'underline', 'strike'],
            ['link', 'image']
        ]
    },
    theme: 'snow'
});

// Get old form data
quill{{ $ucName }}.setContents({!! str_replace('</script>', '<\/script>', old($name)) !!});

quill{{ $ucName }}.on('editor-change', function() {
    var field = document.querySelector('{{ $form }} input[name={{ $name }}]');
    field.value = JSON.stringify(quill{{ $ucName }}.getContents());
    var htmlField = document.querySelector('{{ $form }} input[name={{ $name }}_html]');
    htmlField.value = quill{{ $ucName }}.root.innerHTML;
});
</script>
@parent
@endsection
