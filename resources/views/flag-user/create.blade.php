@extends('layouts.app')

@section('title', 'Create reason')

@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2>Flag User {{ $user->name }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li>
                    <a href="{{ action('UserController@show', $user) }}">{{ $user->name }}</a>
                </li>
                <li class="active">
                    <strong>Create</strong>
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
                        <h5>Flag user {{ $user->name }}</h5>
                    </div>
                    <div class="ibox-content">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('flag.user.store', $user) }}">
                            {{ csrf_field() }}

                            <div class="form-group{{ $errors->has('flag_id') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="flag-id">Reason</label>

                                <div class="col-sm-10">
                                    <select class="form-control chosen-select" name="flag_id" id="flag-id">
                                        @foreach (App\Repositories\Flag\Flag::all() as $flag)
                                            <option data-client-specific="{{ $flag->client_specific }}" value="{{ $flag->id }}" >{{ $flag->reason }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('reason'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('reason') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('client') ? ' has-error' : '' }}" id="client-container">
                                <label class="col-sm-2 control-label" for="client">Client</label>

                                <div class="col-sm-10">
                                    <select class="form-control chosen-select" name="client" id="client">
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}"{{ (old('client') == $client->id) ? ' selected' : '' }}>{{ $client->name }}</option>
                                        @endforeach
                                    </select>

                                    @if ($errors->has('client'))
                                        <span class="help-block m-b-none">
                                                <strong>{{ $errors->first('client') }}</strong>
                                            </span>
                                    @endif

                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('comment') ? ' has-error' : '' }}">
                                <label class="col-sm-2 control-label" for="comment">Comment</label>

                                <div class="col-sm-10">
                                    <textarea type="text" class="form-control" name="comment" id="comment"></textarea>

                                    @if ($errors->has('comment'))
                                        <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('comment') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            <div class="form-group m-b-none">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <a class="btn btn-white" href="{{ action('UserController@show', $user) }}">Cancel</a>
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
@section('script')
    <script type="text/javascript">
        $( document ).ready(function() {
            $('#flag-id').on('change', function (e) {
                var flagIsClientSpeciffic = !!$("option:selected", this).data('client-specific');

                if(flagIsClientSpeciffic) {
                    $('#client-container').show();
                }
                else {
                    $('#client-container').hide();
                }
            });

            // Initialize
            $('#flag-id').trigger('change');
        });
    </script>
@append