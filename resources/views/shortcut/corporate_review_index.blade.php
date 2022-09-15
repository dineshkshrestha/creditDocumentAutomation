@extends ('layouts.backend')
@section('title',' Borrower List ')
@section('heading','Corporate Borrower Status')
@section('small_heading','You can find the document processing status of the borrower')
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
            $('#permission').DataTable();
        });
    </script>
@endsection

@section('content')
    <div class="col-sm-12">
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
                        <th>Registration No.</th>
                        <th>Reg. Date</th>
                        <th>Authorised Person</th>
                        <th>Action</th>
                        <th>Document Status</th>

                    </tr>
                    </thead>
                    <tbody>
                    @php($i=1)
                    @foreach($borrower as $a)
                        <tr>@php($bid=$a->id)
                            <td>{{$i++}}</td>
                            <td>{{$a->english_name}}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->nepali_name}}</td>
                            <td>{{$a->client_id}}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->registration_number}}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->reg_day}}
                                ÷{{$a->reg_month}}÷{{$a->reg_year}}</td>
                            <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">
                                {{\App\AuthorizedPerson::find($a->authorized_person_id)->nepali_name}}
                            </td>
                            <td>
                                <a class="btn btn-primary btn-xs" href="{{route('corporate_review.index',$bid)}}">
                                                <i class="fa fa-navicon fa-fw"></i>Proceed to Review</a>
                            </td>
                            <td><label class="label label-success label-xs">{{\App\CorporateLoan::where([['borrower_id',$bid]])->first()->document_status}}
                                </label>

                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
