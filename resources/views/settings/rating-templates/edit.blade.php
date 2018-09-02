@extends('layouts.app')

@section('title', 'Edit Rating Template')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Edit Rating Template</h2>
            <ol class="breadcrumb">
                <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('rating_templates.index') }}">Rating Templates</a></li>
                <li class="active"><strong>Edit</strong></li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content animated fadeInUp">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Edit Rating Template</h5>
                    </div>
                    <div class="ibox-content">
                        <form class="form-horizontal" method="POST" action="{{ route('rating_templates.update', $ratingTemplate) }}">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}

                            @include('partials.forms.select', [
                                'name' => 'subject',
                                'label' => 'Subject',
                                'options' => ['client' => 'Client', 'user' => 'User'],
                                'value' => $ratingTemplate->subject
                            ])

                            <div class="hr-line-dashed"></div>

                            @include('partials.forms.select', [
                                'name' => 'email_template',
                                'label' => 'Email template',
                                'options' => [null => 'Select email template ...'] + \App\Repositories\EmailTemplate\EmailTemplate::pluck('title', 'id')->toArray(),
                                'value' => $ratingTemplate->email_template
                            ])

                            <div class="hr-line-dashed"></div>

                            @include('partials.forms.input', [
                                'type' => 'number',
                                'name' => 'tasks_completed',
                                'label' => 'Tasks completed',
                                'value' => $ratingTemplate->tasks_completed
                            ])

                            <div class="hr-line-dashed"></div>

                            @include('partials.forms.input', [
                                'type' => 'number',
                                'name' => 'days_from_last_review',
                                'label' => 'Days from last review',
                                'value' => $ratingTemplate->days_from_last_review
                            ])

                            <div class="hr-line-dashed"></div>

                            <div class="form-group m-b-none">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <a class="btn btn-white" href="{{ route('rating_templates.index') }}">Cancel</a>
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