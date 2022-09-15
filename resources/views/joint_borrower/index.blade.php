@extends ('layouts.backend')
@section('title',' Personal Borrower List ')
@section('heading','Select Joint Borrower')
@section('small_heading','First Borrower')
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li class="active"><strong>Select Borrower</strong></li>
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


    <script type="text/javascript">
        var table = $('#permission').DataTable({

        });

        $('form').on('submit', function(e){
            var $form = $(this);

            // Iterate over all checkboxes in the table
            table.$('input[type="checkbox"]').each(function(){
                // If checkbox doesn't exist in DOM
                if(!$.contains(document, this)){
                    // If checkbox is checked
                    if(this.checked){
                        // Create a hidden element
                        $form.append(
                            $('<input>')
                                .attr('type', 'hidden')
                                .attr('name', this.name)
                                .val(this.value)
                        );
                    }
                }
            });
        });
    </script>

@endsection
@section('content')
    <div class="box box-default">

            {!! Form::open(['route' => 'joint_borrower.joint_store','method'=>'post','id'=>'myForm','novalidate' => 'novalidate'],['class'=>'form-horizontal']) !!}
<div>
            <div class="box-header with-border">
                <h3 class="box-title">
                    <div class="btn-group">
                        <a class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-plus fa-fw"></i>
                            Add Borrower <span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a data-toggle="modal"
                                   data-target=".bs-example-modal-lg" href=""><i class="fa fa-male fa-fw"></i>New
                                    Personal Borrower</a>
                            </li>
                            <li>
                                <a href="{{route('joint_guarantor_borrower.index')}}">
                                    <i class="fa fa-clone"></i>Copy Details
                                    From
                                    Guarantor</a>
                            </li>
                            <li>
                                <a href="{{route('joint_property_owner_borrower.index')}}">
                                    <i class="fa fa-clone"></i>Copy Details
                                    From property Owner</a>
                            </li>
                        </ul>
                    </div>

                    <a data-toggle="modal" class="btn btn-primary btn-xs" data-target="#jointindex" href="#">
                        <i class="fa fa-eye fa-fw"></i>
                        Old Joint Borrowers
                    </a>


                </h3>
                <button type="submit" id="test"  class="pull-right btn btn-info btn-xm "><i
                        class="fa fa-save fa-fw"></i>Save Selected Joint Borrowers
                </button>

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
                    <th>Select</th>
                   </tr>
                    </thead>

                    @php($i=1)

                    @foreach($joint_borrower as $a)
                        <tr>@php($bid=$a->id)
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
                                        Action <span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>

                                            <a href="{{route('joint_borrower.show',$a->id)}}">
                                                <i class="fa fa-eye fa-fw"></i>View All Details</a>
                                        </li>
                                        <li>
                                            <a href="{{route('joint_borrower.edit',$a->id)}}" class="btn-xs"> <i
                                                        class="fa fa-pencil fa-fw"></i>Edit</a>
                                        </li>
                                        {{--<li>--}}

                                            {{--<a href="">--}}
                                                {{--<form action="{{route('joint_borrower.destroy',$a->id)}}" method="post">--}}
                                                    {{--{{csrf_field()}}--}}
                                                    {{--<input type="hidden" name="_method" value="delete">--}}
                                                    {{--<button type="submit" class="btn btn-default btn-xs"--}}
                                                            {{--style="padding: 0; outline:none;border: none;background: none;"--}}
                                                            {{--onclick="return confirm('Are you sure to delete?')"><i--}}
                                                                {{--class="fa fa-trash fa-fw"></i>Delete--}}
                                                    {{--</button>--}}
                                                {{--</form>--}}
                                            {{--</a>--}}
                                        {{--</li>--}}
                                    </ul>
                                </div>
                            </td>
                            <td>
                                <input type="checkbox" name="checkbox-1[]" id="single-checkbox" class="single-checkbox" value="{{$a->id}}"/>
                            </td>
                        </tr>
                    @endforeach

                </table>
            </div></div>
        {!! Form::close() !!}
</div>
<!-- /.box-body -->
{!! Form::open(['route' => 'joint_borrower.store','method'=>'post','novalidate' => 'novalidate'],['class'=>'form-horizontal']) !!}
    @include('modal.personal_borrower_modal')
    @include('modal.personal_joint_borrower_modal')
@endsection
