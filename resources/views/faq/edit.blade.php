@extends('layouts.app')

@section('title', 'Edit ' . $faq->title)

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>Edit {{ $faq->title }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ action('FaqCategoryController@index') }}">Faq Categories</a>
                </li>
                <li>
                    <a href="{{ action('FaqCategoryController@show', $faq->faqCategory) }}">{{ $faq->faqCategory->name }}</a>
                </li>
                <li>
                    <a href="{{ action('FaqController@show', $faq) }}">{{ $faq->title }}</a>
                </li>
                <li class="active">
                    <strong>Edit</strong>
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
                        <h5>Edit {{ $faq->title }}</h5>
                    </div>
                    <div class="ibox-content">
                        <form id="form" class="form-horizontal" role="form" method="POST" action="{{ action('FaqController@update', $faq) }}">
                            {{ csrf_field() }}

                            {{-- Category --}}
                            <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="category">Category</label>

                                <div class="col-sm-10">
                                    <select class="form-control chosen-select" name="category" id="category" onchange="newCategory()">
                                        <option value="add_category">Add new category</option>
                                        @foreach ($faqCategories as $category)
                                            <option value="{{ $category->id }}"{{ ($faq->faq_category_id == $category->id) ? ' selected' : ((isset($faq->faqCategories) && ($faqCategories->id == $faq->faqCategories->id)) ? ' selected' : '') }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('category'))
                                        <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('category') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            {{-- New category --}}
                            <div id="new_category_section" class="form-group{{ $errors->has('new_category') ? ' has-error' : ' hidden' }}">
                                <label class="col-sm-2 control-label" for="new_category">New category</label>

                                <div class="col-sm-10">
                                    <input type="text" disabled class="form-control" name="new_category" id="new_category">

                                    @if ($errors->has('new_category'))
                                        <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('new_category') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>

                            {{-- Title --}}
                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="title">Title</label>

                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="title" id="title" value="{{ $faq->title }}">

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
                                    <textarea class="wysiwyg" name="content" id="content">{!! old('content', $faq->content) !!}</textarea>
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
                                    <input type="checkbox" class="js-switch" id="visible" value="1" name="visible" {{ ($faq->visible) ? ' checked' : '' }}>
                                    @if ($errors->has('visible'))
                                        <span class="help-block m-b-none"><strong>{{ $errors->first('visible') }}</strong></span>
                                    @endif
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            @if ($faq->tasks() != NULL)
                                <div class="subpage">
                                    <h5><b>Sub Pages</b></h5>

                                    @foreach ($faq->tasks() as $subFaq)
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label" for="subTitle[{{ $subFaq->id }}]">Title</label>
                                                <div class="col-sm-10">
                                                    <div class="form-control">{{ $subFaq->title }}</div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label" for="subContent[' + i + ']">Content</label>
                                                <div class="col-sm-10">
                                                    <input type="hidden" id="subContent_{{ $subFaq->id }}" name="subContent[{{ $subFaq->id }}]">
                                                    <div id="editor_{{ $subFaq->id }}">{!! $subFaq->versions->first()->description !!}</div>
                                                </div>
                                            </div>
                                            <div class="hr-line-dashed"></div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="form-group m-b-none">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <a class="btn btn-white" href="{{ action('FaqController@show', $faq) }}">Cancel</a>
                                    <button class="btn btn-primary" type="submit">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Delete FAQ form --}}
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Danger Zone</h5>
                    </div>
                    <div class="ibox-content">
                        <form role="form" method="POST" action="{{ action('FaqController@destroy', $faq) }}">
                            {{ csrf_field() }}

                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="active" value="{{ $faq->active }}">

                            <button class="btn btn-danger btn-outline" type="submit">{{ $faq->active ? 'Deactivate FAQ' : 'Activate FAQ' }}</button>
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
            if ($('#category').val() == 'add_category') {
                $('#new_category_section').removeClass('hidden');
                $('#new_category').prop('disabled', false);
            } else {
                $('#new_category_section').addClass('hidden');
                $('#new_category').prop('disabled', true);
            }
        }
    </script>
@endsection