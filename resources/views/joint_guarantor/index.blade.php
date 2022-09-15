@extends ('layouts.backend')
@section('title',' Guarantor List ')
@section('heading','Select Guarantor')
@section('small_heading','Please select the guarantors')
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
    <li><a href="{{route('joint_borrower.index')}}">Joint Borrower</a></li>
    <li class="active"><strong>Select Guarantor</strong></li>
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
@section('content')
    {!! Form::open(['route' => ['joint_guarantor.personal_store',$bid],'method'=>'post','novalidate' => 'novalidate'],['class'=>'form-horizontal']) !!}
    @include('modal.personal_borrower_modal')
    @include('modal.joint_personal_guarantor_list_modal')

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">
                <div class="btn-group">
                    <a class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-plus fa-fw"></i>
                        New Guarantor<span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a data-toggle="modal"
                               data-target=".bs-example-modal-lg" href=""><i class="fa fa-male fa-fw"></i>Personal Guarantor</a>
                        </li>

                    </ul>
                </div>


                <a class="btn btn-success btn-xs" data-toggle="modal"
                   data-target="#view_guarantor" href="#"><i class="fa fa-eye fa-fw"></i>View Guarantors</a>

            </h3>
            <span><a class="btn btn-info btn-xm pull-right" href="{{route('joint_guarantor.proceed',$bid)}}"><i
                            class="fa fa-save fa-fw"></i>Proceed to Property Selection </a></span>

        </div>
            <!-- /.box-header -->
        <div class="box-body">
            @include('modal.personal_borrower_error')
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
                @foreach($personal_borrower as $a)
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
                                   href="{{route('joint_guarantor.personal_borrower',[$bid,$a->id])}}">
                                    <i class="fa  fa-square-o fa-fw"></i>Select as Guarantor</a>
                            </td>
                            <td><label class="label label-info label-xs">Personal Borrower</label>

                            </td>
                        </tr>
                    @endif
                @endforeach

                @foreach($personal_guarantor as $a)
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
                                   href="{{route('joint_guarantor.personal_select',[$bid,$a->id])}}">
                                    <i class="fa  fa-square-o fa-fw"></i>Select as Guarantor</a>
                            </td>
                            <td><label class="label label-info label-xs">Personal Guarantor</label>

                            </td>
                        </tr>
                    @endif
                @endforeach

                @foreach($personal_property_owner as $a)
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
                                   href="{{route('joint_guarantor.personal_property_owner',[$bid,$a->id])}}">
                                    <i class="fa  fa-square-o fa-fw"></i>Select as Guarantor</a>
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

@endsection
