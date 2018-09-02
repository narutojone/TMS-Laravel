@extends('layouts.app')
@section('title', 'Users information')
@section('head')
    <style>
        .navbar-default{display:none;}
        .navbar{display:none;}
        #page-wrapper{margin:0px;} 
    </style>
@append
@section('heading')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-4">
            <h2></h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                </li>
                <li class="active">
                    <strong>User information</strong>
                </li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="wrapper wrapper-content">
                @forelse($information as $info)
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>{{ $info->title }}</h5>
                        </div>
                        <div class="ibox-content">
                            @include('quill.view', ['id' => 'edtor' . $info->id, 'delta' => $info->description])
                            <div class="hr-line-dashed"></div>
                            <form action="{{ route('information.store') }}" method="POST">
                                {{ csrf_field() }}
                                <input type="hidden" name="information_id" value="{{ $info->id }}">
                                <button class="btn btn-primary">I have read and understood the information</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <i class="text-muted">no users information</i>
                @endforelse
                <div class="text-center">
                    {{ $information->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
