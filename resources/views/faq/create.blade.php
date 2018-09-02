@extends('layouts.app')

@section('title', 'Create FAQ')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>Create FAQ</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ action('FaqCategoryController@index') }}">Faq Categories</a>
                </li>
                @if (isset($faqCategory))
                    <li>
                        <a href="{{ action('FaqCategoryController@show', $faqCategory) }}">{{ $faqCategory->name }}</a>
                    </li>
                @endif
                <li class="active">
                    <strong>Create FAQ</strong>
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
                        <h5>Create FAQ</h5>
                    </div>
                    <div class="ibox-content">
                        <form id="form" class="form-horizontal" role="form" method="POST" action="{{ action('FaqController@store') }}">
                            {{ csrf_field() }}

                            {{-- Category --}}
                            <div class="form-group{{ $errors->has('faq_category_id') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="faq_category_id">Category</label>

                                <div class="col-sm-10">
                                    <select class="form-control chosen-select" name="faq_category_id" id="faq_category_id" onchange="newCategory()">
                                        <option value=""></option>
                                        <option value="add_category">Add new category</option>
                                        @foreach ($faqCategories as $category)
                                            <option value="{{ $category->id }}"{{ ((isset($faqCategory) && ($category->id == $faqCategory->id)) || (old('faq_category_id') == $category->id)) ? ' selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('faq_category_id'))
                                        <span class="help-block m-b-none"><strong>{{ $errors->first('faq_category_id') }}</strong></span>
                                    @endif
                                </div>
                            </div>

                            {{-- New category --}}
                            <div id="new_category_section" class="form-group{{ $errors->has('new_category') ? ' has-error' : ' hidden' }}">
                                <label class="col-sm-2 control-label" for="new_category">New category</label>

                                <div class="col-sm-10">
                                    <input type="text" disabled class="form-control" name="new_category" id="new_category" value="{{ old('new_category') }}">

                                    @if ($errors->has('new_category'))
                                        <span class="help-block m-b-none"><strong>{{ $errors->first('new_category') }}</strong></span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            {{-- Use template --}}
                            <div class="form-group{{ $errors->has('use_template') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="use_template">Use template</label>

                                <div class="col-sm-10">
                                    <input type="hidden" name="use_template" value="0" />
                                    <input type="checkbox" class="js-switch" value="1" id="use_template" name="use_template" onchange="useTemplate()" @if(old('use_template', "0") === "1") checked @endif>
                                    @if ($errors->has('use_template'))
                                        <span class="help-block m-b-none"><strong>{{ $errors->first('use_template') }}</strong></span>
                                    @endif
                                </div>
                            </div>

                            {{-- Template --}}
                            <div id="template_section" class="form-group{{ $errors->has('template_id') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="template_id">Template</label>

                                <div class="col-sm-10">
                                    <select class="form-control chosen-select" name="template_id" id="template_id" onchange="useTemplate()">
                                        <option value=""></option>
                                        @foreach ($templates as $template)
                                            <option value="{{ $template->id }}">{{ $template->title }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('template_id'))
                                        <span class="help-block m-b-none"><strong>{{ $errors->first('template_id') }}</strong></span>
                                    @endif
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            {{-- Title --}}
                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="title">Title</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="title" id="title" value="{{ old('title') }}">

                                    @if ($errors->has('title'))
                                        <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="form-group{{ $errors->has('content') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="content">Content</label>

                                <div class="col-sm-10">
                                    <textarea name="content" class="wysiwyg" id="content">{{ old('content') }}</textarea>
                                    @if ($errors->has('content'))
                                        <span class="help-block m-b-none"><strong>{{ $errors->first('content') }}</strong></span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            {{-- Visible --}}
                            <div class="form-group{{ $errors->has('visible') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="visible">Visible</label>

                                <div class="col-sm-10">
                                    <input type="hidden" name="visible" value="0" />
                                    <input type="checkbox" class="js-switch" value="1" id="visible" name="visible">
                                    @if ($errors->has('visible'))
                                        <span class="help-block m-b-none"><strong>{{ $errors->first('visible') }}</strong></span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="subpage">

                            </div>
                            <div class="form-group m-b-none">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <a class="btn btn-white" href="{{ action('FaqCategoryController@index') }}">Cancel</a>
                                    <button class="btn btn-primary" type="submit">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">
        function newCategory() {
            if ($('#faq_category_id').val() == 'add_category') {
                $('#new_category_section').removeClass('hidden');
                $('#new_category').prop('disabled', false);
            } else {
                $('#new_category_section').addClass('hidden');
                $('#new_category').prop('disabled', true);
            }
        }

        function useTemplate() {
            $('.subpage').html('');
            if ($('#use_template').prop('checked') && $('#template_id').val() != "") {
                subQuill = new Array();
                $.ajax({
                    'url': "{{ action('AjaxController@getTemplate') }}",
                    'method': 'GET',
                    'data': {
                        'templateId': $('#template_id').val(),
                    }
                }).done(function(response) {
                    $('#title').val(response.template.title);

                    if (response.templateSubtasks.length != 0) {
                        $('.subpage').append('<h5><b>Sub Pages</b></h5>');
                        var i = 1;

                        for (var key in response.templateSubtasks) {
                            $('.subpage').append(
                                '<div class="form-group">'+
                                    '<label class="col-sm-2 control-label" for="subTitle['+ i +']">Title</label>'+
                                    '<div class="col-sm-10">'+
                                        '<div class="form-control">' + key + '</div>'+
                                    '</div>'+
                                '</div>'+
                                '<div class="form-group">'+
                                    '<label class="col-sm-2 control-label" for="subContent[' + i + ']">Content</label>'+
                                    '<div class="col-sm-10">'+
                                        '<div>'+response.templateSubtasks[key]+'</div>'+
                                    '</div>'+
                                '</div>'


                            );
                            i++;
                        }
                    }
                });
            }
        }
    </script>
@endsection