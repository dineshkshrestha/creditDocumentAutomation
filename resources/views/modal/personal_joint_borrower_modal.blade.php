{{--auto loan for hire purchase --}}
<div class="modal fade" id="jointindex" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

    <!-- Change class .modal-sm to change the size of the modal -->
    <div class="modal-dialog modal-lg" role="document">


        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title w-100" id="myModalLabel">All The Joint Borrowers</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">


                <table
                        class=" table table-striped table-bordered table-hover dataTables-example">
                    <thead>
                    <tr>
                        <th></th>
                        <th colspan="2">Joint1</th>
                        <th colspan="2">joint2</th>
                        <th colspan="2">Joint3</th>
                        <th></th>
                    </tr>
                    <tr>
                        <th>S.N</th>
                        <th>Name</th>
                        <th>Citizenship</th>
                        <th>Name</th>
                        <th>Citizenship</th>
                        <th>Name</th>
                        <th>Citizenship</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(!$joint==null)

                        @php($i=1)
                        @foreach ($joint as $jid)
                            <tr>

                                <td>{{$i++}}</td>
                                @if($jid->joint1)
                                    @php($a=App\PersonalBorrower::find($jid->joint1))

                                    <td>{{$a->english_name}}</td>
                                    <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->citizenship_number}}</td>
                                @else
                                    <td></td><td></td>
                                @endif
                                @if($jid->joint2)
                                    @php($b=App\PersonalBorrower::find($jid->joint2))
                                    <td>{{$b->english_name}}</td>
                                    <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$b->citizenship_number}}</td>
                                @else
                                    <td></td><td></td>
                                @endif
                                @if($jid->joint3)
                                    @php($c=App\PersonalBorrower::find($jid->joint3))

                                    <td>{{$c->english_name}}</td>
                                    <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$c->citizenship_number}}</td>

                                @else
                                    <td></td><td></td>
                                @endif
                                <td>
                                    <div class="btn-group">
                                        <a class="btn btn-primary  btn-xs dropdown-toggle" data-toggle="dropdown" href="#">
                                            <i class="fa fa-user fa-fw"></i>
                                            Action <span class="fa fa-caret-down" title="Toggle dropdown menu"></span>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="{{route('joint_guarantor.index',$jid->id)}}">
                                                    <i class="fa fa-navicon fa-fw"></i>Select as Borrower</a>
                                            </li>
                                            {{--<li>--}}

                                                {{--<a href="#">--}}
                                                    {{--<i class="fa fa-eye fa-fw"></i>View All Details</a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}
                                                {{--<a href="#" class="btn-xs"> <i--}}
                                                            {{--class="fa fa-pencil fa-fw"></i>Edit</a>--}}
                                            {{--</li>--}}
                                            {{--<li>--}}

                                                {{--<a href="">--}}
                                                    {{--<form action="#" method="post">--}}
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
                            </tr>
                        @endforeach
                    @endif
                    </tbody>


                </table>

            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>