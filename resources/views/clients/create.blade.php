@extends('layouts.app')

@section('title', 'Create client')

@section('heading')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Create client</h2>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </li>
            <li>
                <a href="{{ action('ClientController@index') }}">Clients</a>
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
                    <h5>Create client <small>Add a new client to the system.</small></h5>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal" role="form" method="POST" action="{{ action('ClientController@store') }}">
                        {{ csrf_field() }}

                        {{-- Name --}}
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="name">Name</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Organization number --}}
                        <div class="form-group{{ $errors->has('organization_number') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="organization_number">Organization Number</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="organization_number" id="organization_number" value="{{ old('organization_number') }}">

                                @if ($errors->has('organization_number'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('organization_number') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Systems --}}
                        <div class="form-group{{ $errors->has('system_id') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="system_id">Software</label>

                            <div class="col-sm-10">
                                <select class="form-control chosen-select" name="system_id" id="system_id">
                                    @foreach (\App\Repositories\System\System::visible()->get() as $system)
                                        <option value="{{ $system->id }}"{{ (old('system_id') == $system->id) ? ' selected' : '' }}>{{ $system->name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('system_id'))
                                    <span class="help-block m-b-none">
                                            <strong>{{ $errors->first('system_id') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        {{-- Manager --}}
                        <div class="form-group{{ $errors->has('manager_id') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="manager_id">Manager</label>

                            <div class="col-sm-10">
                                <select class="form-control chosen-select" name="manager_id" id="manager_id">
                                    @foreach (App\Repositories\User\User::active()->orderBy('name')->get() as $user)
                                        <option value="{{ $user->id }}"{{ (old('manager_id') == $user->id) ? ' selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('manager_id'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('manager_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Employee --}}
                        <div class="form-group{{ $errors->has('employee_id') ? ' has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="employee_id">Employee</label>

                            <div class="col-sm-10">
                                <select class="form-control chosen-select" name="employee_id" id="employee_id">
                                    @foreach (App\Repositories\User\User::active()->orderBy('name')->get() as $user)
                                        <option value="{{ $user->id }}"{{ (old('employee_id') == $user->id) ? ' selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('employee_id'))
                                    <span class="help-block m-b-none">
                                        <strong>{{ $errors->first('employee_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        {{-- Contact type --}}
                        <div class="form-group {{ $errors->has('contact_type') ? 'has-error' : '' }}">
                            <label class="col-sm-2 control-label" for="contact_type">Contact Type</label>
                            <div class="col-sm-10">
                                <select class="form-control chosen-select m-b-lg" name="contact_type" id="contact_type">
                                    <option value="existing">Existing Contact</option>
                                    <option value="new" {{ old('contact_type') == 'new' ? 'selected' : '' }}>New Contact</option>
                                </select>
                            </div>
                        </div>

                        {{-- Existing contact person --}}
                        <div id="existing-contact" class="{{ (old('contact_type', 'existing') == 'new') ? 'hidden' : '' }}">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="contact_id">Contact Name</label>
                                <div class="col-sm-10">
                                    <select class="form-control chosen-select chosen-select-hidden m-b-lg" name="contact_id" id="contact_id">
                                        @foreach($contacts as $contact)
                                            <option value="{{ $contact->id }}">{{ $contact->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- New contact person --}}
                        <div id="new-contact" class="{{ (old('contact_type', 'existing') == 'existing') ? 'hidden' : '' }}">
                            {{-- Name --}}
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="contact_name">Contact Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="contact_name" id="contact_name" value="{{ old('contact_name') }}" />
                                </div>
                            </div>
                            {{-- Email --}}
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="contact_email">Contact Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" name="contact_email" id="contact_email" value="{{ old('contact_email') }}" />
                                </div>
                            </div>
                            {{-- Phone number --}}
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="contact_phone">Contact Phone</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="contact_phone" id="contact_phone" placeholder="47xxxxxxxx" value="{{ old('contact_phone') }}" />
                                </div>
                            </div>
                        </div>


                        <div class="hr-line-dashed"></div>

                        <div class="form-group m-b-none">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a class="btn btn-white" href="{{ action('ClientController@index') }}">Cancel</a>
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
        $(document).ready(function() {
            $('#contact_type').on('change', function(){
                if(this.value == "new") {
                    $('#existing-contact').addClass('hidden');
                    $('#new-contact').removeClass('hidden');
                }
                else {
                    $('#existing-contact').removeClass('hidden');
                    $('#new-contact').addClass('hidden');
                }
            });
        });
    </script>
@append

