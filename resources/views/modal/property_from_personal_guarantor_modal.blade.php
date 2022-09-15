<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
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
                    <thead>
                    <tr>
                        <th>SN</th>
                        <th>Guarantor Name</th>
                        <th>Guarantor Type</th>
                        <th>Created By</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
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
                                @php($id=(App\PersonalGuarantor::find($a->personal_guarantor_id))->id)

                                <a class=" btn btn-primary btn-xs" href="{{route('personal_property.personal_select',[$bid,$id])}}">
                                    <i class="fa  fa-square-o fa-fw"></i>Select as Property Owner</a>

                            </td>
                        </tr>
                    @endforeach
                    @foreach($corporate_guarantor_list as $a)
                        <tr>
                            <td>{{$i++}}</td>
                            <td>{{(App\CorporateGuarantor::find($a->corporate_guarantor_id))->english_name}}</td>
                            <td><label class="label label-warning label-xs">Corporate Guarantor</label>
                            </td>
                            <td> {{App\User::find($a->created_by)->name}}</td>
                            <td> @if($a->status==1)
                                    <label class="btn btn-success btn-xs">Active</label>

                                @else
                                    <label class="btn btn-danger btn-xs">Inactive</label>
                                @endif
                            </td>
                            <td>
                                @php(($id=App\CorporateGuarantor::find($a->corporate_guarantor_id))->id)
                                <a class=" btn btn-primary btn-xs" href="{{route('personal_property.corporate_select',[$bid,$id])}}">
                                    <i class="fa  fa-square-o fa-fw"></i>Select as Property Owner</a>

                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
            </div>
        </div>
    </div>
</div>