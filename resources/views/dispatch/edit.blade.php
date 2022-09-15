@extends ('layouts.backend')

@section('title',' Branch Create ')
@section('heading','Branch')
@section('small_heading','Create')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li><a href="{{route('branch.index')}}">View All Branches</a></li>
    <li class="active"><strong>Create Branch</strong></li>
@endsection
@section('css')
@endsection
@section('content')
    <div class="col-md-8">
        <div class="box box-default ">
            <div class="box-header with-border">
                <h3 class="box-title"></h3>
            </div>
            <!-- /.box-header -->

            {!! Form::open(['route' =>['branch.update',$branch->id],'method'=>'post','class'=>'form-horizontal','enctype'=>'multipart/form-data']) !!}
            <input type="hidden" name="_method" value="put">
            <input type="hidden" name="id" value="{{$branch->id}}">

            <div class="box-body">


                <table>
                    <tr>
                        <td style="padding-right: 12px" width="200">  {!! form::label('location','Location') !!}</td>
                        <td style="padding-right: 12px" width="200">  {!! form::label('district','District') !!}</td>
                        <td style="padding-right: 12px" width="150">  {!! form::label('local_body','Local Government') !!}</td>
                    </tr>
                    <tr>
                        <td style="padding-right: 12px">  {!! form::text('location',$branch->location,['class'=>'form-control input-sm','id'=>'location','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                            @if($errors->has('location'))
                                <span class="label label-danger">{{$errors->first('location')}} </span>
                            @endif</td>

                        <td style="padding-right: 12px">  {!! form::select('district',$district,$branch->district,['class'=>'form-control input-sm','id'=>'district_id','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                            @if($errors->has('district'))
                                <span class="label label-danger">{{$errors->first('district')}} </span>
                            @endif</td>
                        <td style="padding-right: 12px">  {!! form::select('local_body',$localbody,$branch->local_body,['class'=>'form-control input-sm','id'=>'localbody_id','required'=>'required','style'=>'font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px;','autocomplete'=>'off']) !!}
                            @if($errors->has('local_body'))
                                <span class="label label-danger">{{$errors->first('local_body')}} </span>
                            @endif</td>

                    </tr>
                    <tr>
                        <td>  {!! form::label('status','Status',['class'=>'col-sm-2 control-label']) !!}</td>
                    </tr><tr>
                        <td>  @if($branch->status==1)
                            {!! Form::radio('status', '1', true) !!}Active
                            {!! Form::radio('status', '0') !!}
                            Inactive
                        @else
                            {!! Form::radio('status', '1') !!}Active
                            {!! Form::radio('status', '0', true) !!} Inactive
                        @endif</td>
                    </tr>
                </table>


            </div>

            <div class="box-footer">
                <button type="reset" class="btn btn-warning ">
                    Reset
                </button>

                {!! Form::submit('Save',['class'=>'btn btn-primary pull-right']) !!}
            </div>


        </div>
    </div>

@endsection
@section('js')


    <script type="text/javascript">
        $('#district_id').change(function () {

            var district_id = $(this).val();
            console.log(district_id);
            $.ajax({
                url: '/select_local_body',
                data: {'district_id': district_id},
                method: 'post',
                success: function (resp) {
                    $('#localbody_id').html('');
                    $('#localbody_id').html(resp);
                }
            })
        })
    </script>
@endsection