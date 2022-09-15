@extends ('layouts.backend')
@section('title',' Property Owner List ')
@section('heading','Property Owner')
@section('small_heading','select')
@section('borrower')
    @php($b=App\JointBorrower::find($bid))
    @if($b->joint1)
        {{App\PersonalBorrower::find($b->joint1)->english_name}}
    @endif
    @if($b->joint2)
        ,{{App\PersonalBorrower::find($b->joint2)->english_name}}
    @endif
    @if($b->joint3)
        ,{{App\PersonalBorrower::find($b->joint3)->english_name}}
    @endif
@endsection
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li><a href="{{route('joint_borrower.index')}}">joint Borrower</a></li>
    <li><a href="{{route('joint_guarantor.index',$bid)}}">joint Guarantor</a></li>
    <li class="active"><strong>Select Property Owner </strong></li>
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
            var GARBAGE = "!)!%&-15-85--)!%&%!*9%&";
            var table = $('#permission').DataTable({
                dom: 'l<"custom-search"f>tip'
            });
            table.search(GARBAGE).draw();
            var newSearch = $('<input type="text">');
            newSearch.on('keyup', function () {
                if ($(this).val().toString().trim() === "")
                    table.search(GARBAGE).draw();
                else
                    table.search($(this).val()).draw();
            });
            $('.custom-search input').replaceWith(newSearch);
        });
    </script>
@endsection
@section('content')
    {!! Form::open(['route' => 'joint_property.vehicle_store','method'=>'post'],['class'=>'form-horizontal']) !!}
    {!! form::hidden('bid',$bid,['class'=>'form-control','hidden'=>'hidden','id'=>'bid','hidden']) !!}
    @include('modal.vehicle_create_modal')
    @include('modal.joint_property_from_personal_guarantor_modal')
    @include('modal.joint_vehicle_view_modal')

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">
                <div class="btn-group">
                    <a class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-plus fa-fw"></i>
                        New Property owner <span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="{{route('joint_property_personal.index',$bid)}}">
                                <i class="fa fa-group fa-fw"></i>Joint Property Owner</a>
                        </li>

                        <li>
                            <a href="{{route('joint_property.personal_create',$bid)}}">
                                <i class="fa fa-male fa-fw"></i>New Personal Property Owner</a>
                        </li>
                      <li>

                            <a data-toggle="modal"
                               data-target=".bs-example-modal-lg" href=""> <i class="fa  fa-briefcase fa-fw"></i>Copy From
                                Assigned Guarantors</a>
                        </li>
                        <li>

                            <a href="{{route('joint_property.copy_property_owner',$bid)}}">
                                <i class="fa fa-th-large fa-fw"></i> Copy From Borrower and Guarantors</a>
                        </li>



                    </ul>
                </div>


                <div class="btn-group">
                    <a class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-plus fa-fw"></i>
                        Add Vehicle <span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a data-toggle="modal" data-target="#vehicle" href="#">
                                <i class="fa fa-plus-square fa-fw"></i>
                                Add New Vehicle </span>
                            </a>
                        </li>
                        <li><small style="color: red;">To add land property you have to choose property owner first</small></li>
                    </ul>
                </div>

                <div class="btn-group">
                    <a class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-eye fa-fw"></i>
                        View Assigned Properties <span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a data-toggle="modal" data-target="#vehicle_view" href="#">
                                <i class="fa fa-bus fa-fw"></i>
                                View Vehicle
                            </a>
                        </li>

                        <li>
                            <a   data-toggle="modal" href="#" data-target="#View"><i
                                        class="fa fa-home fa-fw"></i>View Assigned Land and Share</a>
                        </li>



                    </ul>
                </div>


            </h3>
            <span><a class="btn btn-info btn-xm pull-right" href="{{route('joint_property.proceed',$bid)}}">                                <i
                            class="fa fa-save fa-fw"></i>Proceed to Banking Facilities </a></span>

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
                    <th>Citizenship No./Reg. No</th>
                    <th>Date of (Birth/Reg.)</th>
                    <th>Action</th>
                    <th>Current Status</th>

                </tr>
                </thead>
                <tbody>
                @php($i=1)



                @foreach($joint_property_owner as $a)
                    @if($a->status==1)
                        <tr>

                            <td>{{$i++}}</td>
                            <td>{{$a->english_name}}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->nepali_name}}</td>
                            <td>{{$a->client_id}}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->citizenship_number}}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->dob_day}}
                                ÷{{$a->dob_month}}÷{{$a->dob_year}}</td>
                            <td>
                                <a class=" btn btn-primary btn-xs"
                                   href="{{route('joint_property.personal_property_owner',[$bid,$a->id])}}">
                                    <i class="fa  fa-square-o fa-fw"></i>Select as Property Owner</a>
                            </td>
                            <td><label class="label label-info label-xs">Personal Property Owner</label>

                            </td>

                        </tr>
                    @endif
                @endforeach

                           </tbody>
            </table>
        </div>
    </div>
    <!-- /.box-body --
    >
  @include('modal.joint_view_property_modalfront')

@endsection
