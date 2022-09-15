@extends ('layouts.backend')
@section('title',' Property List ')
@section('heading','Select Property')
@section('small_heading','for joint borrower')
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
    <li><a href="{{route('joint_guarantor.index',$bid)}}">Guarantor</a></li>
    <li><a href="{{route('joint_property.index',$bid)}}">Property Owner</a></li>
    <li class="active"><strong>Choose Property</strong></li>
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
                        <li>
                            <a data-toggle="modal" data-target="#Share" href="#">
                                <i class="fa fa-circle fa-fw"></i>
                                Add Share
                            </a>

                        </li>

                    </ul>
                </div>


                <a class="btn btn-primary btn-xs waves-effect waves-light" data-toggle="modal" data-target="#View"><i
                            class="fa fa-eye fa-fw"></i>View Assigned Property</a>

            </h3>
            <span><a class="btn btn-info btn-xm pull-right" href="{{route('joint_property.proceed',$bid)}}">                 <i
                            class="fa fa-save fa-fw"></i>Proceed to Banking Facilities </a></span>

        </div>
        <!-- /.box-header -->
        <div class="box-body">
            @if($land_property)
                <table
                        class=" table table-striped table-bordered table-hover dataTables-example">
                    <thead>
                    <tr>
                        <th colspan="8">Available Land of Property Owner </th>
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
                    @foreach($land_property as $a)
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

                                            <a href="{{route('joint_property.land_assign',[$bid,$a->id])}}"
                                               class="btn-xs"> <i class="fa  fa-check-square fa-fw"></i>Assign Property</a>
                                        </li>

                                        <li>

                                            <a href="{{route('joint_property.land_edit',[$bid,$a->id])}}"
                                               class="btn-xs"> <i class="fa fa-pencil fa-fw"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a href="">
                                                <form action="{{route('joint_property.land_destroy',[$bid,$a->id])}}"
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
            @endif
                @if($share_property)

                    <table
                            class=" table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th colspan="6">Available Share properties</th>
                        </tr>
                        <tr>
                            <th>SN</th>
                            <th>ISIN</th>
                            <th>Client ID</th>
                            <th>DPID</th>
                            <th>Kitta No</th>
                            <th>Action</th>
                            <th>Status</th>

                        </tr>
                        </thead>

                        <tbody>
                        @php($i=1)
                        @foreach($share_property as $a)
                            <tr>
                                <td>{{$i++}}</td>
                                <td>{{App\RegisteredCompany::find($a->isin)->isin}}</td>
                                <td>{{$a->client_id}}</td>
                                <td>{{$a->dpid}}</td>
                                <td>{{$a->kitta}}</td>
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

                                                <a href="{{route('joint_property.share_assign',[$bid,$a->id])}}"
                                                   class="btn-xs"> <i class="fa  fa-check-square fa-fw"></i>Assign Property</a>
                                            </li>

                                            <li>

                                                <a href="{{route('joint_property.share_edit',[$bid,$a->id])}}"
                                                   class="btn-xs"> <i class="fa fa-pencil fa-fw"></i>Edit</a>
                                            </li>
                                            <li>
                                                <a href="">
                                                    <form action="{{route('joint_property.share_destroy',[$bid,$a->id])}}"
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
                @endif




        </div>
    </div>

    {!! Form::open(['route' => 'joint_property.land_store','method'=>'post'],['class'=>'form-horizontal']) !!}
    {!! form::hidden('bid',$bid,['class'=>'form-control','hidden'=>'hidden','id'=>'bid','hidden']) !!}
    {!! form::hidden('poid',$poid,['class'=>'form-control','hidden','id'=>'poid','hidden']) !!}
    @include('modal.land_create_modal')
    {!! Form::open(['route' => 'joint_property.share_store','method'=>'post'],['class'=>'form-horizontal']) !!}
    {!! form::hidden('bid',$bid,['class'=>'form-control','hidden'=>'hidden','id'=>'bid','hidden']) !!}
    {!! form::hidden('poid',$poid,['class'=>'form-control','hidden','id'=>'poid','hidden']) !!}
    @include('modal.share_create_modal')


    @include('modal.joint_view_property_modal')


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