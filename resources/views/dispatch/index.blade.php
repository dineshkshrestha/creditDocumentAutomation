@extends ('layouts.backend')
@section('title',' Dispatch List ')
@section('heading','Dispatch')
@section('small_heading','List')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li class="active"><strong>View All Dispatch</strong></li>

@endsection

@section('css')
    <link rel="stylesheet"
          href="{{asset('assets/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
@endsection

@section('js')
    <script src="{{asset('assets/backend/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/backend/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#permission').DataTable();
        });
    </script>
@endsection

@section('content')

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">
                <a data-toggle="modal" class="btn btn-success btn-xs" data-target="#vehicle" href="#">
                    <i class="fa fa-plus-square fa-fw"></i>
                    Add New Dispatch
                </a>
            </h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">

            <table id="permission" class="table table-striped table-bordered table-hover dataTables-example">
                <thead>
                <tr>
                    <th>SN</th>
                    <th>Date</th>
                    <th>Reference Number</th>
                    <th>Subject</th>
                    <th>Receiver</th>
                    <th>Branch</th>
                    <th>Remarks</th>
                    <th>Created By</th>
                    <th>Created Date</th>
                    {{--<th>Action</th>--}}
                </tr>
                </thead>
                <tbody>
                @php($i=1)
                @foreach($dispatch as $a)
                    <tr>
                        <td>{{$i++}}</td>
                        <td>{{$a->date}}</td>
                        <td>{{$a->reference_number}}</td>
                        <td>{{$a->subject}}</td>
                        <td >{{$a->receiver}}</td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; ">{{\App\Branch::find($a->branch)->location}}</td>
                        <td>{{$a->remarks}}</td>
                        <td> {{App\User::find($a->created_by)->name}}</td>

                        <td>{{$a->created_at}}</td>
                        {{--<td>--}}
                            {{--<div class="btn-group">--}}
                                {{--<a class="btn btn-primary  btn-xs dropdown-toggle" data-toggle="dropdown" href="#">--}}
                                    {{--<i class="fa fa-user fa-fw"></i>--}}
                                    {{--Action <span class="fa fa-caret-down" title="Toggle dropdown menu"></span>--}}
                                {{--</a>--}}
                                {{--<ul class="dropdown-menu">--}}
                                    {{--<li>--}}
                                        {{--<a href="{{route('dispatch.edit',$a->id)}}" class="btn-xs"> <i--}}
                                                    {{--class="fa fa-pencil fa-fw"></i>Edit</a>--}}
                                    {{--</li>--}}
                                {{--</ul>--}}
                            {{--</div>--}}


                        {{--</td>--}}

                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <th>SN</th>
                    <th>Date</th>
                    <th>Reference Number</th>
                    <th>Subject</th>
                    <th>Receiver</th>
                    <th>Branch</th>
                    <th>Remarks</th>
                    <th>Created By</th>
                    <th>Created Date</th>
                    {{--<th>Action</th>--}}
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <!-- /.box-body -->
    <!-- List of Property -->
    {{--auto loan for hire purchase --}}
    {!! Form::open(['route' => 'dispatch.store','method'=>'post'],['class'=>'form-horizontal']) !!}
    <div class="modal fade" id="vehicle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">

        <!-- Change class .modal-sm to change the size of the modal -->
        <div class="modal-dialog modal-lg" role="document">


            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title w-100" id="myModalLabel">Please Enter Dispatch Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                        <table>
                            <tr>
                                <td width="200"
                                    style="padding-right: 30px">  {!! form::label('subject','Subject*') !!}</td>
                                <td width="200"
                                    style="padding-right: 30px">  {!! form::label('receiver','Receiver*') !!}</td>
                            </tr>
                            <tr>

                                <td style="padding-right: 30px">  {!! form::text('subject','Offer Letter',['class'=>'form-control input-sm','id'=>'model_number','required'=>'required','autocomplete'=>'off']) !!}
                                    @if($errors->has('subject'))
                                        <span>{{$errors->first('subject')}} </span>
                                    @endif</td>
                                <td style="padding-right: 30px"> {!! form::text('receiver','',['class'=>'form-control input-sm col-sm-2','id'=>'registration_number','required'=>'required','autocomplete'=>'off']) !!}
                                    @if($errors->has('receiver'))
                                        <span>{{$errors->first('receiver')}} </span>
                                    @endif</td>
                            </tr>
                        </table>
                        <table>
                            <tr>
                                <td width="200"
                                    style="padding-right: 30px">{!! form::label('remarks','Remarks') !!}</td>
                                <td width="200"
                                    style="padding-right: 12px">{!! form::label('branch','Branch*') !!}</td>
                            </tr>
                            <tr>
                                <td style="padding-right: 30px;  ">{!! form::text('remarks','',['class'=>'form-control input-sm','id'=>'engine_number','autocomplete'=>'off','required'=>'required']) !!}</td>
                                <td style=" padding-right: 30px;  ">{!! form::select('branch',$branch,'',['class'=>'form-control input-sm','id'=>'chassis_number','autocomplete'=>'off','required'=>'required','style'=>" font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size:14px; "]) !!}</td>
                                   </tr>
                        </table>


                    <br>
                </div>
                <div class="modal-footer">
                    {!! Form::submit('Save',['class'=>'btn btn-primary pull-left']) !!}
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>

                </div>

            </div>
        </div>
    </div>
    {!! Form::close() !!}



@endsection