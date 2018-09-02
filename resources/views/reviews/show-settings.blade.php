@extends('layouts.app')

@section('title', 'Review settings')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>
                {{ 'Review settings' }}
            </h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li class="active">
                    <a href="#">Review Settings</a>
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
                    <form id="form" class="form-horizontal" method="POST" {{ action('ReviewsController@saveSettings') }}>
                        <div class="ibox-title">
                            <h5>Review Settings</h5>
                        </div>
                        <div class="ibox-content">
                            {{ csrf_field() }}
                            {{-- Number of tasks needed by a level 1 user to increase it's level to level 2 --}}
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="no_of_tasks_for_level_two">No of tasks for review</label>
                                <div class="col-sm-10">
                                    <input type="number" name="no_of_tasks_for_level_two" class="form-control" value="{{ old('no_of_tasks_for_level_two', $reviewSettings->no_of_tasks_for_level_two) }}" min="0">
                                    <small>Number of tasks needed by a level to increase it's level to level 1 or 2.</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="deadline_offset">Deadline offset</label>
                                <div class="col-sm-10">
                                    <input type="number" name="deadline_offset" class="form-control" value="{{ old('deadline_offset', $reviewSettings->deadline_offset) }}" min="0">
                                    <small>Deadline for reviewer's task. When a review is create, a task for the reviewer is created, this property sets the deadline for that task (no of days).</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="review_template_id">Template for review</label>
                                <div class="col-sm-10">
                                    <select class="form-control chosen-select" name="review_template_id" id="review_template_id">
                                        <option value="">Select template for review task</option>
                                        @foreach ($templates as $template)
                                            <option value="{{ $template->id }}" {{  ($template->id == old('review_template_id', $reviewSettings->review_template_id)) ? "selected" : ""  }}>{{ $template->title }}</option>
                                        @endforeach
                                    </select>
                                    <small>The template that is going to be used, when the task for review is created for a reviewer.</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="first_review_group_id">First review group</label>
                                <div class="col-sm-10">
                                    <select class="form-control chosen-select" name="first_review_group_id" id="first_review_group_id">
                                        <option value="">Select group</option>
                                        @foreach ($groups as $group)
                                            <option value="{{ $group->id }}" {{ ($group->id == old('first_review_group_id', $reviewSettings->first_review_group_id)) ? "selected" : ""  }}>{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                    <small>Group assigned for the first review of a review or for a new review if the previous one was not closed with critical issues.</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="second_review_group_id">Second review group</label>
                                <div class="col-sm-10">
                                    <select class="form-control chosen-select" name="second_review_group_id" id="second_review_group_id">
                                        <option value="">Select group</option>
                                        @foreach ($groups as $group)
                                            <option value="{{ $group->id }}" {{ ($group->id == old('second_review_group_id', $reviewSettings->second_review_group_id)) ? "selected" : ""  }}>{{ $group->name }}</option>
                                        @endforeach
                                    </select>
                                    <small>Group assigned for the a review if the previous one was closed with critical issues.</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="second_review_group_id">Templates to be reviewed</label>
                                <div class="col-sm-10">
                                    <select class="form-control chosen-select" multiple="multiple" name="templates_for_review[]" id="templates_for_review">
                                        @foreach ($templates as $template)
                                            <option value="{{ $template->id }}" {{ ( (collect(old('templates_for_review'))->contains($template->id)) || (in_array($template->id, $reviewTemplates)) )? "selected" : ""  }}>{{ $template->title }}</option>
                                        @endforeach
                                    </select>
                                    <small>Select the templates that it will be considered for review.</small>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                        </div>
                        <div class="ibox-title">
                            <button type="submit" class="btn btn-primary btn-sm">Save settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection