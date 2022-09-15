@extends('layouts.backend')
@section('title')Local Body Edit @endsection
@section('bread')
    <div class="col-sm-8">
        <ol class="breadcrumb">
            <li>
                <a href="{{route('home')}}">Dashboard</a>
            </li>
            <li>
                <a href="{{route('district.index')}}">View All District</a>
            </li>
            <li class="active">
                <strong>Update Local Body</strong>
            </li>
        </ol>
    </div>
@endsection
@section('content')

    <div class="row">

        <div class="col-lg-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">

                    <h5>Edit Local Body</h5>
                </div>
                <div class="ibox-content">
                    {!! Form::open(['route' =>['local_body_update',$local->id,],'method'=>'post','class'=>'form-horizontal','enctype'=>'multipart/form-data']) !!}
                    <input type="hidden" name="_method" value="put">
                    <input type="hidden" name="id" value="{{$local->id}}">
                    <input type="hidden" name="did" value="{{$local->district_id}}">

                    <table>
                        <tr>
                            <td>Local Body</td>
<td></td>
    <td>Local Body Type</td>                    </tr>
                        <tr>
                            <td>      {!! form::text('name',$local->name,['autocomplete'=>'off','class'=>'form-control','id'=>'name','placeholder'=>'','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}
                                @if($errors->has('name'))
                                    <span>{{$errors->first('name')}} </span>
                                @endif</td>
<td width="15"></td>
                            <td>   {!! form::select('local_body',['===================================='=>'=========================','dxfgu/kflnsf' => 'dxfgu/kflnsf', 'pkdxfgu/kflnsf' => 'pkdxfgu/kflnsf','gu/kflnsf'=>'gu/kflnsf','ufp¤kflnsf'=>'ufp¤kflnsf','uf=la=;'=>'uf=la=;'],$local->body_type,['class'=>'form-control','id'=>'localtype','placeholder'=>'','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;']) !!}</td>
                        </tr>
                    <tr><td><br></td></tr>
<tr><td>
        {!! Form::submit('Update',['class'=>'btn btn-primary']) !!}
    </td></tr>
                    </table>

                </div>

            </div>


        </div>
        {!! Form::close() !!}
    </div>


    <!-- /.box-header -->


@endsection