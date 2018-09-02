@extends('layouts.app')

@section('title', 'Edit ' . $documentationPage->name)

@section('head')
    <style>
        .editor-toolbar.fullscreen, .CodeMirror-fullscreen, .editor-preview-side.editor-preview-active-side {
            z-index: 6000;
        }
    </style>
@endsection
@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>Edit {{ $documentationPage->name }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ action('DocumentationPagesController@index') }}">Developer Documentation</a>
                </li>
                <li class="active">
                    <strong>{{ $documentationPage->title }}</strong>
                </li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Edit page</h5>
                    </div>
                    <div class="ibox-content">
                        <form class="form-horizontal" role="form" method="POST" action="{{ action('DocumentationPagesController@update', $documentationPage) }}">
                            <input name="_method" type="hidden" value="PUT">
                            {{ csrf_field() }}

                            {{-- Title --}}
                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="title">Title</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="title" id="title" value="{{ old('title', $documentationPage->title) }}" required autofocus>
                                    @if ($errors->has('title'))
                                        <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Order --}}
                            <div class="form-group{{ $errors->has('order') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="order">Order</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" name="order" id="order" value="{{ old('order', $documentationPage->order) }}" min="0" oninput="validity.valid||(value='');" required autofocus>
                                    @if ($errors->has('order'))
                                        <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('order') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Parent Page --}}
                            <div class="form-group{{ $errors->has('parent_page_id') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="parent_page_id">Parent Page</label>
                                <div class="col-sm-10">
                                    <select name="parent_page_id" id="parent_page_id" class="form-control chosen-select">
                                    <option value="">No Parent</option>
                                        @foreach($parentPageDropdown as $page_id => $page_name)
                                            <option value="{{ $page_id }}" {{ ($documentationPage->parent_page_id == $page_id || old('parent_page_id') == $page_id) ? 'selected' : '' }}>{{ $page_name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('parent_page_id'))
                                        <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('parent_page_id') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="content">Content</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="content" id="content" required autofocus>{!! old('content', $documentationPage->content)  !!}</textarea>
                                    @if ($errors->has('content'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('content') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            {{-- Submit --}}
                            <div class="form-group m-b-none">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <a class="btn btn-white" href="{{ action('DocumentationPagesController@index') }}">Cancel</a>
                                    <a href="{{ route('documentation.pages.destroy', $documentationPage->id) }}" class="btn btn-danger delete-page" {{ $documentationPage->childPages->count() ? 'disabled' : '' }}>Delete</a>
                                    <button class="btn btn-primary" type="submit">Save</button>
                                </div>
                            </div>
                        </form>
                        <form id="delete-form" action="{{ route('documentation.pages.destroy', $documentationPage->id) }}" method="POST" class="hidden">
                        {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function () {

            var simplemde = new SimpleMDE({
                element: $("#content")[0],
                toolbar: [
                    "bold",
                    "italic",
                    "strikethrough",
                    "heading",
                    "heading-smaller",
                    "heading-bigger",
                    "heading-1",
                    "heading-2",
                    "heading-3",
                    "code",
                    "quote",
                    "unordered-list",
                    "ordered-list",
                    "clean-block",
                    "link",
                    "image",
                    "table",
                    "horizontal-rule",
                    "preview",
                    "side-by-side",
                    "fullscreen",
                    "guide"
                ]
            });

            simplemde.codemirror.on("change", function () {
                $("#content").text(simplemde.value());
            });

            $('.chosen-container').css('width', '100%');

            $('.delete-page').on('click', function () {
                event.preventDefault();
                if ($(this).attr('disabled')) {
                    return false;
                }
                swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then(function () {
                    document.getElementById('delete-form').submit();
                })

            })
        });
    </script>
@endsection