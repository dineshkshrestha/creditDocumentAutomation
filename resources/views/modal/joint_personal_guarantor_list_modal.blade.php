<div class="modal fade" id="view_guarantor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="myModalLabel"></h5>
            </div>
            <div class="modal-body">
                <table
                        class=" table table-striped table-bordered table-hover dataTables-example">
                    <tr>
                        <th>SN</th>
                        <th>Guarantor Name</th>
                        <th>Guarantor Type</th>
                        <th>Created By</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>

                    <tbody>
                    @php($i=1)
                    @foreach($personal_guarantor_list as $a)
                        <tr>
                            <td>{{$i++}}</td>
                            <td>{{(App\PersonalGuarantor::find($a->personal_guarantor_id))->english_name}}</td>
                            <td><label class="label label-info label-xs">Personal Guarantor</label>
                            </td>
                            <td> {{App\User::find($a->created_by)->name}}</td>
                            <td> @if($a->status==1)
                                    <label class="btn btn-success btn-xs">Active</label>

                                @else
                                    <label class="btn btn-danger btn-xs">Inactive</label>
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

                                            <a href="{{route('joint_guarantor.edit',[$bid,$a->id])}}" class="btn-xs"> <i class="fa fa-pencil fa-fw"></i>Change Status</a>
                                        </li>
                                        <li>
                                            <a href="">
                                                <form action="{{route('joint_guarantor.destroy',[$bid,$a->id])}}" method="post">
                                                    {{csrf_field()}}

                                                    <input type="hidden" name="_method" value="delete">
                                                    <button type="submit" class="btn btn-default btn-xs"
                                                            style="padding: 0; outline:none;border: none;background: none;"
                                                            onclick="return confirm('Are you sure to remove?')"> <i class="fa fa-close fa-fw"></i>Remove Guarantor
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
            </div>
        </div>
    </div>
</div>