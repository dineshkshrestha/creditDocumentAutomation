@extends ('layouts.backend')
@section('title',' Property Owner List ')
@section('heading','Property Owner')
@section('small_heading','Please select the Property Owner')
@section('borrower')
    {{App\CorporateBorrower::find($bid)->english_name}}
@endsection
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li><a href="{{route('corporate_borrower.index')}}">Corporate Borrower</a></li>
    <li><a href="{{route('corporate_guarantor.index',$bid)}}">Corporate Guarantor</a></li>
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
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title"><a class="btn btn-success btn-xs" data-toggle="modal"
                                     data-target=".bs-example-modal-lg" href="">Guarantors</a>
                <span><a class="btn btn-success btn-xs"  href="{{route('corporate_property.proceed',$bid)}}">Choose From Borrowers and Guarantors </a></span>
            </h3><span><a class="btn btn-info btn-xm pull-right"  href="{{route('corporate_property.proceed',$bid)}}">Proceed to Banking Facilities </a></span>

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
                                <a class=" btn btn-primary btn-xs" href="{{route('corporate_property.personal_borrower',[$bid,$a->id])}}">
                                    <i class="fa  fa-square-o fa-fw"></i>Select as Property Owner</a>
                            </td>
                            <td>    <label class="label label-info label-xs">Personal Borrower</label>

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
                                <a class=" btn btn-primary btn-xs" href="{{route('corporate_property.personal_select',[$bid,$a->id])}}">
                                    <i class="fa  fa-square-o fa-fw"></i>Select as Property Owner</a>
                            </td>
                            <td>    <label class="label label-info label-xs">Personal Guarantor</label>

                            </td>

                        </tr>
                    @endif
                @endforeach

                @foreach($corporate_borrower as $a)
                    @if($a->status==1)
                        <tr>

                            <td>{{$i++}}</td>
                            <td>{{$a->english_name}}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->nepali_name}}</td>
                            <td>{{$a->client_id}}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->registration_number}}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->reg_day}}
                                ÷{{$a->reg_month}}÷{{$a->reg_year}}</td>
                            <td>
                                <a class=" btn btn-primary btn-xs" href="{{route('corporate_property.corporate_borrower',[$bid,$a->id])}}">
                                    <i class="fa  fa-square-o fa-fw"></i>Select as Property Owner</a>
                            </td>
                            <td>    <label class="label label-info label-xs">Corporate Borrower</label>

                            </td>

                        </tr>
                    @endif
                @endforeach

                @foreach($corporate_guarantor as $a)
                    @if($a->status==1)
                        <tr>
                            <td>{{$i++}}</td>
                            <td>{{$a->english_name}}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->nepali_name}}</td>
                            <td>{{$a->client_id}}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->registration_number}}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->reg_day}}
                                ÷{{$a->reg_month}}÷{{$a->reg_year}}</td>
                            <td>
                                <a class=" btn btn-primary btn-xs" href="{{route('corporate_property.corporate_select',[$bid,$a->id])}}">
                                    <i class="fa  fa-square-o fa-fw"></i>Select as Property Owner</a>
                            </td>
                            <td>    <label class="label label-info label-xs">Corporate Guarantor</label>
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
