@extends('layouts.backend')
@section('title',' Dashboard ')
@section('heading','Dashboard')
@section('small_heading','Welcome')
@section('breadcrumb')
    <li><a href="{{route('home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Dashboard</li>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">Dashboard</div>

                    <div class="panel-body">

                            @if(session('success'))
                                <div class="alert alert-success">{{session('success')}}</div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger">{{session('error')}}</div>
                            @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
