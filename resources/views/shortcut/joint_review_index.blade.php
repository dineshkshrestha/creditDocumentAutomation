@extends ('layouts.backend')
@section('title',' Borrower List ')
@section('heading','Joint Borrower Status')
@section('small_heading','You can find the document processing status of the borrower')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li class="active"><strong>Select Joint Borrower</strong></li>
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
                Please Select a Borrower to proceed.
            </h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table id="permission" class="table table-striped table-bordered table-hover dataTables-example">
                <thead>
                <tr>
                    <th>SN</th>
                    <th>English Name</th>
                    <th>Nepali name</th>
                    <th>clientId</th>
                    <th>Citizenship No.</th>
                    <th>Action</th>
                    <th>Document Status</th>

                </tr>
                </thead>
                <tbody>
                @php($i=1)
                @foreach($borrower as $a)
                    <tr>@php($bid=$a->id)
                        <td>{{$i++}}</td>
                        <td>
                            @if($a->joint1)
                                {{App\PersonalBorrower::find($a->joint1)->english_name}},
                            @endif
                            @if($a->joint2)
                                {{App\PersonalBorrower::find($a->joint2)->english_name}},
                            @endif
                            @if($a->joint3)
                                {{App\PersonalBorrower::find($a->joint2)->english_name}}
                            @endif
                        </td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">
                            @if($a->joint1)
                                {{App\PersonalBorrower::find($a->joint1)->nepali_name}},
                            @endif
                            @if($a->joint2)
                                {{App\PersonalBorrower::find($a->joint2)->nepali_name}},
                            @endif
                            @if($a->joint3)
                                {{App\PersonalBorrower::find($a->joint2)->nepali_name}}
                            @endif
                        </td>
                        <td>
                            @if($a->joint1)
                                {{App\PersonalBorrower::find($a->joint1)->client_id}},
                            @endif
                            @if($a->joint2)
                                {{App\PersonalBorrower::find($a->joint2)->client_id}},
                            @endif
                            @if($a->joint3)
                                {{App\PersonalBorrower::find($a->joint2)->client_id}}
                            @endif


                        </td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">
                            @if($a->joint1)
                                {{App\PersonalBorrower::find($a->joint1)->citizenship_number}},
                            @endif
                            @if($a->joint2)
                                {{App\PersonalBorrower::find($a->joint2)->citizenship_number}},
                            @endif
                            @if($a->joint3)
                                {{App\PersonalBorrower::find($a->joint2)->citizenship_number}}
                            @endif
                        </td>


                        <td>
                            <a class="btn btn-primary btn-xs" href="{{route('joint_review.index',$bid)}}">
                                <i class="fa fa-navicon fa-fw"></i>Proceed to Review</a>
                        </td>
                        <td >

                            <label class="label label-success label-xs">{{\App\JointLoan::where([['borrower_id',$bid]])->first()->document_status}}
                            </label>

                        </td>

                    </tr>


                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <!-- /.box-body -->
@endsection
