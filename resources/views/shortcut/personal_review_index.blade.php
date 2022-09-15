@extends ('layouts.backend')
@section('title',' Borrower Status ')
@section('heading','Personal Borrower Status')
@section('small_heading','You can find the document processing status of the borrower')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li class="active"><strong>Select Personal Borrower</strong></li>
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
                    <th>Date of Birth</th>
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
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->citizenship_number}}</td>

                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->dob_day}}
                            ÷{{$a->dob_month}}÷{{$a->dob_year}}</td>

                        <td>
                                        <a class="btn btn-primary btn-xs" href="{{route('personal_review.index',$bid)}}">
                                            <i class="fa fa-navicon fa-fw"></i>Proceed to Review</a>
                        </td>

                        <td><label class="label label-success label-xs">{{\App\PersonalLoan::where([['borrower_id',$bid]])->first()->document_status}}
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
