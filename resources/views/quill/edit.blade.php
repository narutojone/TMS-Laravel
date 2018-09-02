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

quill{{ $ucName }}.setSelection(0, 100)

// Set the contents
quill{{ $ucName }}.setContents({!! str_replace('</script>', '<\/script>', old($name, $delta)) !!});

quill{{ $ucName }}.on('editor-change', function() {
    var field = document.querySelector('{{ $form }} input[name={{ $name }}]');
    field.value = JSON.stringify(quill{{ $ucName }}.getContents());
    var htmlField = document.querySelector('{{ $form }} input[name={{ $name }}_html]');
    htmlField.value = quill{{ $ucName }}.root.innerHTML;
});

document.querySelector('{{ $form }}').onsubmit = function () {
    var field = document.querySelector('{{ $form }} input[name={{ $name }}]');
    field.value = JSON.stringify(quill{{ $ucName }}.getContents());
    var htmlField = document.querySelector('{{ $form }} input[name={{ $name }}_html]');
    htmlField.value = quill{{ $ucName }}.root.innerHTML;
};
</script>
@parent
@endsection
