{{--auto loan for hire purchase --}}
<div class="modal fade" id="vehicle_view" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

    <!-- Change class .modal-sm to change the size of the modal -->
    <div class="modal-dialog modal-lg" role="document">


        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title w-100" id="myModalLabel">Add New Vehicle</h4>
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
            <th>Model No.</th>
            <th>Registration No.</th>
            <th>Engine No.</th>
            <th>Chassis No.</th>
            <th>Created By.</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @php($i=1)
        @foreach($vehicle as $a)
            <tr>
                <td>{{$i++}}</td>
                <td>{{$a->model_number}}</td>
                <td style="font-size: 14px; font-family: Bishallb;">{{$a->registration_number}}       </td>
               <td>{{$a->engine_number}}</td>
                <td>{{$a->chassis_number}}</td>
                <td> {{App\User::find($a->created_by)->name}}</td>
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

                                <a href="{{route('joint_property.vehicle_edit',[$bid,$a->id])}}"
                                   class="btn-xs"> <i class="fa fa-pencil fa-fw"></i>Edit</a>
                            </li>
                            <li>
                                <a href="">
                                    <form action="{{route('joint_property.vehicle_destroy',[$bid,$a->id])}}"
                                          method="post">
                                        {{csrf_field()}}
                                        <input type="hidden" name="_method" value="delete">
                                        <button type="submit" class="btn btn-default btn-xs"
                                                style="padding: 0; outline:none;border: none;background: none;"
                                                onclick="return confirm('Are you sure to Delete?')">
                                            <i class="fa fa-close fa-fw"></i>Delete Vehicle
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
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>