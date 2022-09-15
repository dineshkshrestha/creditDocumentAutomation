{{--auto loan for hire purchase --}}
<div class="modal fade" id="joint_view" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

    <!-- Change class .modal-sm to change the size of the modal -->
    <div class="modal-dialog modal-lg" role="document">


        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title w-100" id="myModalLabel">All The Joint Property Owners</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">


                <table
                        class=" table table-striped table-bordered table-hover dataTables-example">
                    <thead>
                    <tr>
                        <th>SN</th>
                        <th>English Name</th>
                        <th>Nepali Name</th>
                        <th>Grandfather Name</th>
                        <th>Citizenship No.</th>
                        <th>Date of Birth</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!$jid==null)

                        @php($i=1)
                        @php($j=App\JointPropertyOwner::find($jid))


                        @if($j->joint1)

                              @php($a=App\PersonalPropertyOwner::find($j->joint1))
                            <tr>
                                <td>Joint {{$i++}}</td>
                                <td>{{$a->english_name}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->nepali_name}}</td>
                                <td>{{$a->client_id}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->citizenship_number}}</td>

                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->dob_day}}
                                    ÷{{$a->dob_month}}÷{{$a->dob_year}}</td>
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
                                                <a href="">
                                                    <form action="{{route('joint_property_personal.remove',[$bid,$jid,$a->id])}}"
                                                          method="post">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="_method" value="delete">
                                                        <button type="submit" class="btn btn-default btn-xs"
                                                                style="padding: 0; outline:none;border: none;background: none;"
                                                                onclick="return confirm('Are you sure to Delete?')">
                                                            <i class="fa fa-close fa-fw"></i>Remove Owner
                                                        </button>
                                                    </form>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>


                                </td>

                            </tr>

                        @endif
                        @if($j->joint2)
                            @php($b=App\PersonalPropertyOwner::find($j->joint2))
                            <tr>
                                <td>Joint {{$i++}}</td>
                                <td>{{$b->english_name}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$b->nepali_name}}</td>
                                <td>{{$b->client_id}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$b->citizenship_number}}</td>

                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$b->dob_day}}
                                    ÷{{$b->dob_month}}÷{{$b->dob_year}}</td>
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
                                                <a href="">
                                                    <form action="{{route('joint_property_personal.remove',[$bid,$jid,$b->id])}}"
                                                          method="post">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="_method" value="delete">
                                                        <button type="submit" class="btn btn-default btn-xs"
                                                                style="padding: 0; outline:none;border: none;background: none;"
                                                                onclick="return confirm('Are you sure to Delete?')">
                                                            <i class="fa fa-close fa-fw"></i>Remove Owner
                                                        </button>
                                                    </form>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>


                                </td>

                            </tr>
                        @endif
                        @if($j->joint3)
                            @php($c=App\PersonalPropertyOwner::find($j->joint3))
                            <tr>
                                <td>Joint {{$i++}}</td>
                                <td>{{$c->english_name}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$c->nepali_name}}</td>
                                <td>{{$c->client_id}}</td>
                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$c->citizenship_number}}</td>

                                <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$c->dob_day}}
                                    ÷{{$c->dob_month}}÷{{$c->dob_year}}</td>
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
                                                <a href="">
                                                    <form action="{{route('joint_property_personal.remove',[$bid,$jid,$c->id])}}"
                                                          method="post">
                                                        {{csrf_field()}}
                                                        <input type="hidden" name="_method" value="delete">
                                                        <button type="submit" class="btn btn-default btn-xs"
                                                                style="padding: 0; outline:none;border: none;background: none;"
                                                                onclick="return confirm('Are you sure to Delete?')">
                                                            <i class="fa fa-close fa-fw"></i>Remove Owner
                                                        </button>
                                                    </form>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>


                                </td>

                            </tr>
                        @endif
                    @endif
                    </tbody>


                </table>

            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>