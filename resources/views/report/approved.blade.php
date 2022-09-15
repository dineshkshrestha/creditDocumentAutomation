@extends ('layouts.backend')
@section('title',' Approved List ')
@section('heading','Approved')
@section('small_heading','List')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li class="active"><strong>Approved List</strong></li>

@endsection

@section('css')
    <link rel="stylesheet"
          href="{{asset('assets/backend/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')}}">
@endsection

@section('js')
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"></script>

    <script>

        $(document).ready(function() {
            $('#permission').DataTable( {
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            } );
        } );
        // $(document).ready(function() {
        //     $('#permission').DataTable( {
        //         dom: 'lBfrtip',
        //         buttons: [
        //             'copyHtml5',
        //             'excelHtml5',
        //             'csvHtml5',
        //             'pdfHtml5'
        //         ]
        //     } );
        // } );




    </script>
@endsection

@section('content')

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Approved List</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">

            <table id="permission" class="table table-striped table-bordered table-hover dataTables-example">
                <thead>
                <tr>
                    <th>SN</th>
                    <th>Client ID</th>
                    <th>Client Name</th>
                    <th>Branch</th>
                    <th>Submitted At</th>
                    <th>Approved At</th>
                    <th>Approved By</th>
                    <th>Type</th>
                    <th>Guarantors</th>
                </tr>
                </thead>
                <tbody>
                @php($i=1)
                @foreach($papproved as $a)
                    <tr>
                        <td>{{$i++}}</td>
                        <td>{{\App\PersonalBorrower::find($a->borrower_id)->client_id}}</td>
                        <td>{{\App\PersonalBorrower::find($a->borrower_id)->english_name}}</td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{\App\Branch::find($a->branch_id)->location}}</td>
                        <td>{{$a->submitted_at}}</td>
                        <td>{{$a->approved_at}}</td>
                        <td>{{\App\User::find($a->approvedby)->name}}</td>
                        <td>Individual</td>
                    </tr>
                @endforeach

                @foreach($pdownloaded as $a)
                    <tr>
                        <td>{{$i++}}</td>
                        <td>{{\App\PersonalBorrower::find($a->borrower_id)->client_id}}</td>
                        <td>{{\App\PersonalBorrower::find($a->borrower_id)->english_name}}</td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{\App\Branch::find($a->branch_id)->location}}</td>
                        <td>{{$a->submitted_at}}</td>
                        <td>{{$a->approved_at}}</td>
                        <td>{{\App\User::find($a->approvedby)->name}}</td>
                        <td>Individual</td>
                    </tr>
                @endforeach

                @foreach($capproved as $a)
                    <tr>
                        <td>{{$i++}}</td>
                        <td>{{\App\CorporateBorrower::find($a->borrower_id)->client_id}}</td>
                        <td>{{\App\CorporateBorrower::find($a->borrower_id)->english_name}}</td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{\App\Branch::find($a->branch_id)->location}}</td>
                        <td>{{$a->submitted_at}}</td>
                        <td>{{$a->approved_at}}</td>
                        <td>{{\App\User::find($a->approvedby)->name}}</td>
                        <td>Corporate</td>
                    </tr>
                @endforeach

                @foreach($cdownloaded as $a)
                    <tr>
                        <td>{{$i++}}</td>
                        <td>{{\App\CorporateBorrower::find($a->borrower_id)->client_id}}</td>
                        <td>{{\App\CorporateBorrower::find($a->borrower_id)->english_name}}</td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{\App\Branch::find($a->branch_id)->location}}</td>
                        <td>{{$a->submitted_at}}</td>
                        <td>{{$a->approved_at}}</td>
                        <td>{{\App\User::find($a->approvedby)->name}}</td>
                        <td>Individual</td>
                    </tr>
                @endforeach
                @foreach($japproved as $a)
                    @php($b=App\JointBorrower::find($a->borrower_id))

                    <tr>
                        <td>{{$i++}}</td>
                        <td>

                            @if($b->joint1)
                                {{App\PersonalBorrower::find($b->joint1)->client_id}}
                            @endif
                            @if($b->joint2)
                                ,{{App\PersonalBorrower::find($b->joint2)->client_id}}
                            @endif
                            @if($b->joint3)
                                ,{{App\PersonalBorrower::find($b->joint3)->client_id}}
                            @endif
                        </td>
                        <td>
                            @if($b->joint1)
                                {{App\PersonalBorrower::find($b->joint1)->english_name}}
                            @endif
                            @if($b->joint2)
                                ,{{App\PersonalBorrower::find($b->joint2)->english_name}}
                            @endif
                            @if($b->joint3)
                                ,{{App\PersonalBorrower::find($b->joint3)->english_name}}
                            @endif
                        </td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{\App\Branch::find($a->branch_id)->location}}</td>
                        <td>{{$a->submitted_at}}</td>
                        <td>{{$a->approved_at}}</td>
                        <td>{{\App\User::find($a->approvedby)->name}}</td>
                        <td>Joint</td>
                    </tr>
                @endforeach

                @foreach($jdownloaded as $a)
                    @php($b=App\JointBorrower::find($a->borrower_id))
                    <tr>
                        <td>{{$i++}}</td>
                        <td>
                            @if($b->joint1)
                                {{App\PersonalBorrower::find($b->joint1)->client_id}}
                            @endif
                            @if($b->joint2)
                                ,{{App\PersonalBorrower::find($b->joint2)->client_id}}
                            @endif
                            @if($b->joint3)
                                ,{{App\PersonalBorrower::find($b->joint3)->client_id}}
                            @endif
                        </td>
                        <td>
                            @if($b->joint1)
                                {{App\PersonalBorrower::find($b->joint1)->english_name}}
                            @endif
                            @if($b->joint2)
                                ,{{App\PersonalBorrower::find($b->joint2)->english_name}}
                            @endif
                            @if($b->joint3)
                                ,{{App\PersonalBorrower::find($b->joint3)->english_name}}
                            @endif
                        </td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{\App\Branch::find($a->branch_id)->location}}</td>
                        <td>{{$a->submitted_at}}</td>
                        <td>{{$a->approved_at}}</td>
                        <td>{{\App\User::find($a->approvedby)->name}}</td>
                        <td>Joint</td>
                    </tr>
                @endforeach


                </tbody>
                <tfoot>
                <tr>
                    <th>SN</th>
                    <th>Client ID</th>
                    <th>Client Name</th>
                    <th>Branch</th>
                    <th>Submitted At</th>
                    <th>Approved At</th>
                    <th>Approved By</th>
                    <th>Type</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <!-- /.box-body -->

@endsection