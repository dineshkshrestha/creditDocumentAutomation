@extends ('layouts.backend')
@section('title',' Corporate Borrower List ')
@section('heading','Select Corporate')
@section('small_heading','Borrower')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li class="active"><strong>Select Corporate Borrower</strong></li>
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
    <script>
        $('#district_id1').change(function () {
            var district_id = $(this).val();
            console.log(district_id);
            $.ajax({
                url: '/select_local_body',
                data: {'district_id': district_id},
                method: 'post',
                success: function (resp) {
                    $('#localbody_id1').html('');
                    $('#localbody_id1').html(resp);
                }
            })
        })
    </script>

@endsection

@section('content')
    <div class="col-sm-12">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">

                    <div class="btn-group">
                        <a class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-plus fa-fw"></i>
                            Add Borrower <span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a  data-toggle="modal"
                                   data-target=".bs-example-modal-lg" href=""><i class="fa fa-group fa-fw"></i>Create New
                                    Corporate Borrower</a>
                            </li>
                            <li>
                                <a  href="{{route('corporate_guarantor_borrower.index')}}"> <i class="fa fa-clone"></i>Copy Details From
                                    Guarantor</a>
                            </li>
                            <li>
                                <a href="{{route('corporate_property_owner_borrower.index')}}">
                                    <i class="fa fa-clone"></i> Copy Details From Property Owner</a>
                            </li>
                        </ul>
                    </div>
</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                @include('modal.corporate_borrower_error')
                <table id="permission" class="table table-striped table-bordered table-hover dataTables-example">
                    <thead>
                    <tr>
                        <th>SN</th>
                        <th>English Name</th>
                        <th>Nepali name</th>
                        <th>clientId</th>
                        <th>Registration No.</th>
                        <th>Reg. Date</th>
                        <th>Authorised Person</th>
                        <th>Action</th>
                        <th>Status</th>

                    </tr>
                    </thead>
                    <tbody>
                    @php($i=1)
                    @foreach($corporate_borrower as $a)
                        <tr>@php($bid=$a->id)
                            <td>{{$i++}}</td>
                            <td>{{$a->english_name}}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->nepali_name}}</td>
                            <td>{{$a->client_id}}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->registration_number}}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->reg_day}}
                                ÷{{$a->reg_month}}÷{{$a->reg_year}}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">
                                {{$a->authorised_person}}
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a class="btn btn-primary  btn-xs dropdown-toggle" data-toggle="dropdown" href="#">
                                        <i class="fa fa-user fa-fw"></i>
                                        Action <span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{route('corporate_guarantor.index',$bid)}}">
                                                <i class="fa fa-navicon fa-fw"></i>Select as Borrower</a>
                                        </li>
                                        <li>
                                            <a href="{{route('corporate_borrower.show',$a->id)}}">
                                                <i class="fa fa-eye fa-fw"></i>View All Details</a>
                                        </li>
                                        <li>
                                            <a href="{{route('corporate_borrower.edit',$a->id)}}" class="btn-xs"> <i
                                                        class="fa fa-pencil fa-fw"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="">
                                                <form action="{{route('corporate_borrower.destroy',$a->id)}}"
                                                      method="post">
                                                    {{csrf_field()}}
                                                    <input type="hidden" name="_method" value="delete">
                                                    <button type="submit" class="btn btn-default btn-xs"
                                                            style="padding: 0; outline:none;border: none;background: none;"
                                                            onclick="return confirm('Are you sure to delete?')"><i
                                                                class="fa fa-trash fa-fw"></i>Delete
                                                    </button>
                                                </form>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                            <td> @if($a->status==1)
                                    <label class="label label-success label-xs">Active</label>
                                @else
                                    <label class="label label-danger label-xs">Inactive</label>
                                @endif
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div></div>
        <!-- /.box-body -->
    {!! Form::open(['route' => 'corporate_borrower.store','method'=>'post','novalidate' => 'novalidate'],['class'=>'form-horizontal']) !!}
    @include('modal.corporate_borrower_modal')
@endsection
