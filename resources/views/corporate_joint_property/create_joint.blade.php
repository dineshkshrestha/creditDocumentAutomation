@extends ('layouts.backend')
@section('title','Create Joint Owner')
@section('heading','Joint Owner')
@section('small_heading','Please select Joint Property Owner')
@section('borrower')

    {{App\CorporateBorrower::find($bid)->english_name}}
@endsection
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li><a href="{{route('corporate_borrower.index')}}">Corporate Borrower</a></li>
    <li><a href="{{route('corporate_guarantor.index',$bid)}}">Guarantor</a></li>
    <li><a href="{{route('corporate_property.index',$bid)}}">Property Owner</a></li>
    <li class="active"><strong>Choose joint owner</strong></li>
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
                dom: '<"custom-search"f>tip'
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

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">
                <div class="btn-group">
                    <a class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-plus fa-fw"></i>
                        Add New Owner <span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a data-toggle="modal"
                               data-target=".bs-example-modal-lg" href=""><i class="fa fa-male fa-fw"></i>New
                                Property Owner</a>
                        </li>
                    </ul>
                </div>

                <div class="btn-group">



                    <a class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-eye fa-fw"></i>
                        View  <span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a data-toggle="modal" data-target="#joint_view" href="#">
                                <i class="fa fa-eye fa-fw"></i>
                                Assigned Joint Owners
                            </a>
                        </li>
                    </ul>
                </div>


            </h3>
            <span><a class="btn btn-info btn-xm pull-right" href="{{route('corporate_property_join.index',$bid)}}">
                    <i class="fa fa-save fa-fw"></i>Assign Land For Joint Borrower </a></span>

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
                    <th>Citizenship No.</th>
                    <th>Date of Birth</th>
                    <th>Borrower Type</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @php($i=1)
                @foreach($property_owner as $a)
                    <tr>
                        <td>{{$i++}}</td>
                        <td>{{$a->english_name}}</td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->nepali_name}}</td>
                        <td>{{$a->client_id}}</td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->citizenship_number}}</td>

                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->dob_day}}
                            ÷{{$a->dob_month}}÷{{$a->dob_year}}</td>
                        <td> @if($a->single==1)
                                <label class="label label-info label-xs">Single Borrower</label>
                            @endif
                            @if($a->joint==1)
                                <label class="label label-info label-xs">Joint Borrower
                                    with {{(App\PersonalBorrower::find($a->joint_id))->english_name}}</label>
                        @endif
                        <td> @if($a->status==1)
                                <label class="label label-success label-xs">Active</label>

                            @else
                                <label class="label label-danger label-xs">Inactive</label>
                            @endif
                        </td>

                        <td>
                            <div class="btn-group">
                                <a class="btn btn-primary  btn-xs dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-user fa-fw"></i>
                                    Choose Joint Owner <span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{route('corporate_property_join.select',[$bid,$a->id])}}">
                                            <i class="fa fa-add fa-fw"></i>     Select</a>
                                    </li>

                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>

    {!! Form::open(['route' => 'corporate_property_join.newstore','method'=>'post','novalidate' => 'novalidate'],['class'=>'form-horizontal']) !!}
    {!! form::hidden('bid',$bid,['class'=>'form-control','hidden'=>'hidden','id'=>'bid','hidden']) !!}
    {!! form::hidden('jid',$jid,['class'=>'form-control','hidden'=>'hidden','id'=>'jid','hidden']) !!}
    @include('modal.personal_borrower_modal')

        @include('modal.corporate_joint_view_modal')
    @endsection
