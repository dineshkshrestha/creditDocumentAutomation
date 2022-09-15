<!-- List of Property -->
<div class="modal fade" id="View" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <!-- Change class .modal-sm to change the size of the modal -->
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title w-100" id="myModalLabel">List of Assigned Properties</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if($pland or $cland)
                    <table
                            class=" table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th>SN</th>
                            <th>Owner Name</th>
                            <th>District</th>
                            <th>Local Body</th>
                            <th>Sheet No</th>
                            <th>Kitta No</th>
                            <th>Area</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                        $i=1;
                        @endphp
                        @foreach($pland as $a)

                            <tr>
                                <td>{{$i++}}</td>

                                <td>

                                    {{App\PersonalPropertyOwner::find($a->property_owner_id)->english_name}}
                                </td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{App\District::find($a->district_id)->name}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{App\LocalBodies::find($a->local_bodies_id)->name}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->sheet_no}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->kitta_no}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->area}}</td>
                                <td> @if($a->status==1)
                                        <label class="btn btn-success btn-xs">Active</label>

                                    @else
                                        <label class="btn btn-danger btn-xs">Inactive</label>
                                    @endif
                                </td>
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

                                                <a href="{{route('personal_property.land_borrower_edit',[$bid,$a->id,'personal',$status])}}"
                                                   class="btn-xs"> <i class="fa fa-pencil fa-fw"></i>Edit</a>
                                            </li>
                                            <li>
                                                <a href="">
                                                    <form action="{{route('personal_property.land_borrower_destroy',[$bid,$a->id,'personal',$status])}}"
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
                            </tr>
                        @endforeach

                        @foreach($jland as $a)
                            @php
                                $j=\App\JointPropertyOwner::find($a->joint_id);
                            @endphp
                            <tr>
                                <td>{{$i++}}</td>

                                <td>
                                    @if($j->joint1 && $j->joint2 && $j->joint3)
                                        {{\App\PersonalPropertyOwner::find($j->joint1)->english_name}}
                                        ,{{\App\PersonalPropertyOwner::find($j->joint2)->english_name}}
                                        and {{\App\PersonalPropertyOwner::find($j->joint3)->english_name}}
                                    @elseif($j->joint1 && $j->joint2)
                                        {{\App\PersonalPropertyOwner::find($j->joint1)->english_name}}
                                        and {{\App\PersonalPropertyOwner::find($j->joint2)->english_name}}

                                    @elseif($j->joint1 && $j->joint3)

                                        {{\App\PersonalPropertyOwner::find($j->joint1)->english_name}}
                                        and {{\App\PersonalPropertyOwner::find($j->joint3)->english_name}}
                                    @elseif($j->joint2 && $j->joint3)
                                        {{\App\PersonalPropertyOwner::find($j->joint2)->english_name}}
                                        and {{\App\PersonalPropertyOwner::find($j->joint3)->english_name}}
                                    @else
                                    @endif

                                </td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{App\District::find($a->district_id)->name}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{App\LocalBodies::find($a->local_bodies_id)->name}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->sheet_no}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->kitta_no}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->area}}</td>
                                <td> @if($a->status==1)
                                        <label class="btn btn-success btn-xs">Active</label>

                                    @else
                                        <label class="btn btn-danger btn-xs">Inactive</label>
                                    @endif
                                </td>
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

                                                <a href="{{route('personal_property.joint_land_front_edit',[$bid,$a->id])}}"
                                                   class="btn-xs"> <i class="fa fa-pencil fa-fw"></i>Edit</a>
                                            </li>
                                            <li>
                                                <a href="">
                                                    <form action="{{route('personal_property.joint_land_front_destroy',[$bid,$a->id])}}"
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
                            </tr>
                        @endforeach

                        @foreach($cland as $a)

                            <tr>
                                <td>{{$i++}}</td>

                                <td>
                                    {{App\CorporatePropertyOwner::find($a->property_owner_id)->english_name}}
                                </td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{App\District::find($a->district_id)->name}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{App\LocalBodies::find($a->local_bodies_id)->name}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->sheet_no}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->kitta_no}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->area}}</td>
                                <td> @if($a->status==1)
                                        <label class="btn btn-success btn-xs">Active</label>

                                    @else
                                        <label class="btn btn-danger btn-xs">Inactive</label>
                                    @endif
                                </td>
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

                                                <a href="{{route('personal_property.land_borrower_edit',[$bid,$a->id,'corporate',$status])}}"
                                                   class="btn-xs"> <i class="fa fa-pencil fa-fw"></i>Edit</a>
                                            </li>
                                            <li>
                                                <a href="">
                                                    <form action="{{route('personal_property.land_borrower_destroy',[$bid,$a->id,'corporate',$status])}}"
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
                            </tr>
                        @endforeach


                        </tbody>
                    </table>
                @endif
                @if($pshare or $cshare)
                    <table
                            class=" table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                        <tr>
                            <th>SN</th>
                            <th>Owner Name</th>
                            <th>ISIN</th>
                            <th>Client ID</th>
                            <th>DPID</th>
                            <th>Kitta No</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php($i=1)

                        @foreach($pshare as $a)
                            <tr>
                                <td>{{$i++}}</td>


                                <td>
                                    {{App\PersonalPropertyOwner::find($a->property_owner_id)->english_name}}
                                </td>
                                <td>{{App\RegisteredCompany::find($a->isin)->isin}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->client_id}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->dpid}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->kitta}}</td>
                                <td> @if($a->status==1)
                                        <label class="btn btn-success btn-xs">Active</label>

                                    @else
                                        <label class="btn btn-danger btn-xs">Inactive</label>
                                    @endif
                                </td>
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
                                                <a href="{{route('personal_property.share_borrower_edit',[$bid,$a->id,'personal',$status])}}"
                                                   class="btn-xs"> <i class="fa fa-pencil fa-fw"></i>Edit</a>
                                            </li>
                                            <li>
                                                <a href="">
                                                    <form action="{{route('personal_property.share_borrower_destroy',[$bid,$a->id,'personal',$status])}}"
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
                            </tr>
                        @endforeach
                        @foreach($cshare as $a)
                            <tr>
                                <td>{{$i++}}</td>


                                <td>
                                    {{App\CorporatePropertyOwner::find($a->property_owner_id)->english_name}}
                                </td>
                                <td>{{App\RegisteredCompany::find($a->isin)->isin}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->client_id}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->dpid}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->kitta}}</td>
                                <td> @if($a->status==1)
                                        <label class="btn btn-success btn-xs">Active</label>

                                    @else
                                        <label class="btn btn-danger btn-xs">Inactive</label>
                                    @endif
                                </td>
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

                                                <a href="{{route('personal_property.share_borrower_edit',[$bid,$a->id,'corporate',$status])}}"
                                                   class="btn-xs"> <i class="fa fa-pencil fa-fw"></i>Edit</a>
                                            </li>
                                            <li>
                                                <a href="">
                                                    <form action="{{route('personal_property.share_borrower_destroy',[$bid,$a->id,'corporate',$status])}}"
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
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                @endif

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

