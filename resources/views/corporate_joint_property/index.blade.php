@extends ('layouts.backend')
@section('title',' Joint Property List ')
@section('heading','Add Property')
@section('small_heading','Please select the Property')
@section('borrower')
    {{App\CorporateBorrower::find($bid)->english_name}}
@endsection
@section('breadcrumb')
    <li><a href="{{route('home')}}">Dashboard</a></li>
    <li><a href="{{route('corporate_borrower.index')}}">Corporate Borrower</a></li>
    <li><a href="{{route('corporate_guarantor.index',$bid)}}">Corporate Guarantor</a></li>
    <li><a href="{{route('corporate_property.index',$bid)}}">Corporate Owner</a></li>
    <li><a href="{{route('corporate_property_join.owner_create',$bid)}}">Create Joint Owner</a></li>
    <li class="active"><strong>Joint Property</strong></li>
@endsection

@section('content')

    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">
                <div class="btn-group">
                    <a class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-plus fa-fw"></i>
                        Add Property <span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a data-toggle="modal" data-target="#Land" href="#">
                                <i class="fa fa-square fa-fw"></i>
                                Add Land </span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="btn-group">


                    <a class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-group fa-fw"></i>
                        Joint Owners <span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a data-toggle="modal" data-target="#joint_view" href="#">
                                <i class="fa fa-eye fa-fw"></i>
                                View Owners
                            </a>
                        </li>
                        <li>
                            <a href="{{route('corporate_property_join.owner_create',$bid)}}">
                                <i class="fa fa-plus fa-fw"></i>
                                Add New Owner
                            </a>
                        </li>
                    </ul>
                </div>
            </h3>
            <span><a class="btn btn-info btn-xm pull-right" href="{{route('corporate_property.proceed',$bid)}}">
                    <i class="fa fa-save fa-fw"></i>Proceed to Banking Facilities </a></span>

        </div>
        <!-- /.box-header -->
        <div class="box-body">


            <table
                    class=" table table-striped table-bordered table-hover dataTables-example">
                <thead>
                <tr>
                    <th colspan="8">Land Properties For Joint Property Owners</th>
                </tr>
                <tr>
                    <th>SN</th>
                    <th>District</th>
                    <th>Local Body</th>
                    <th>Sheet No</th>
                    <th>Kitta No</th>
                    <th>Area</th>
                    <th>Action</th>
                    <th>Status</th>

                </tr>

                </thead>
                <tbody>

                @php($i=1)
                @foreach($land as $a)
                    <tr>
                        <td>{{$i++}}</td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{App\District::find($a->district_id)->name}}</td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{App\LocalBodies::find($a->local_bodies_id)->name}}</td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->sheet_no}}</td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->kitta_no}}</td>
                        <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->area}}</td>
                        <td>
                            <div class="btn-group">
                                <a class="btn btn-primary  btn-xs dropdown-toggle"
                                   data-toggle="dropdown" href="#">
                                    <i class="fa fa-user fa-fw"></i>
                                    Action <span class="fa fa-caret-down"
                                                 title="Toggle dropdown menu"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{route('corporate_property_join.land_edit',[$bid,$a->id])}}"
                                           class="btn-xs"> <i class="fa fa-pencil fa-fw"></i>Edit</a>
                                    </li>
                                    <li>
                                        <a href="">
                                            <form action="{{route('corporate_property_join.land_destroy',[$bid,$a->id])}}"
                                                  method="post">
                                                {{csrf_field()}}
                                                <input type="hidden" name="_method" value="delete">
                                                <button type="submit" class="btn btn-default btn-xs"
                                                        style="padding: 0; outline:none;border: none;background: none;"
                                                        onclick="return confirm('Are you sure to remove?')">
                                                    <i class="fa fa-close fa-fw"></i>Remove Property
                                                </button>
                                            </form>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                        <td> @if($a->status==1)
                                <label class="btn btn-success btn-xs">Active</label>

                            @else
                                <label class="btn btn-danger btn-xs">Inactive</label>
                            @endif
                        </td>

                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>
    </div>

    {!! Form::open(['route' => 'corporate_property_join.land_store','method'=>'post'],['class'=>'form-horizontal']) !!}

    {!! form::hidden('bid',$bid,['class'=>'form-control','hidden'=>'hidden','id'=>'bid','hidden']) !!}
    {!! form::hidden('jid',$jid,['class'=>'form-control','hidden','id'=>'jid','hidden']) !!}
    @include('modal.land_create_modal')



    @include('modal.corporate_joint_view_modal')


@endsection
@section('js')

    <script type="text/javascript">
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
        });

        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });


        //            another
        $(document).ready(function () {
            //here first get the contents of the div with name class copy-fields and add it to after "after-add-more" div class.
            $(".add-more11").click(function () {
                var html = $(".copy-fields11").html();
                $(".after-add-more11").after(html);
            });
//here it will remove the current value of the remove button which has been pressed
            $("body").on("click", ".remove11", function () {
                $(this).parents(".control-group11").remove();
            });

        });


    </script>
@endsection