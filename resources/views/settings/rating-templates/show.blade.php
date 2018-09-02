@extends('layouts.app')

@section('title', 'Rating Template')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Rating Template</h2>
        <ol class="breadcrumb">
            <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
            <li><a href="{{ route('rating_templates.index') }}">Rating Templates</a></li>
            <li class="active"><strong>{{ $ratingTemplate->title }}</strong></li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInUp">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>{{ $ratingTemplate->id }} - preview</h5>
                    <div class="ibox-tools">
                        @superAdmin
                            <a href="{{ route('rating_templates.edit', $ratingTemplate) }}" class="btn btn-primary btn-xs">
                                <i class="fa fa-folder"></i> Edit
                            </a>
                            <button data-toggle="modal" data-target="#confirm-rating-template-deletion" class="btn btn-danger btn-xs">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        @endif
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="project-list">
                        <form class="form-horizontal">
                            @include('partials.forms.select', [
                                'name' => 'subject',
                                'disabled' => 'disabled',
                                'options' => ['client' => 'Client', 'user' => 'User'],
                                'value' => $ratingTemplate->subject
                            ])

                            <div class="hr-line-dashed"></div>

                            @include('partials.forms.select', [
                                'name' => 'email_template',
                                'label' => 'Email template',
                                'disabled' => 'disabled',
                                'options' => [null => 'Select email template ...'] + \App\Repositories\EmailTemplate\EmailTemplate::pluck('title', 'id')->toArray(),
                                'value' => $ratingTemplate->email_template
                            ])

                            <div class="hr-line-dashed"></div>

                            @include('partials.forms.input', [
                                'type' => 'number',
                                'disabled' => 'disabled',
                                'name' => 'tasks_completed',
                                'label' => 'Tasks completed',
                                'value' => $ratingTemplate->tasks_completed
                            ])

                            <div class="hr-line-dashed"></div>

                            @include('partials.forms.input', [
                                'type' => 'number',
                                'name' => 'days_from_last_review',
                                'label' => 'Days from last review',
                                'disabled' => 'disabled',
                                'value' => $ratingTemplate->days_from_last_review
                            ])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="confirm-rating-template-deletion" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Delete rating template</h4>
            </div>
            <div class="modal-body">
                <p>Are you really want to delete rating template?</p>
                <br>
                <div class="text-center">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <form action="{{ route('rating_templates.destroy', $ratingTemplate) }}" style="display: inline-block" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('delete') }}
                        <button class="btn btn-danger">Confirm</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
