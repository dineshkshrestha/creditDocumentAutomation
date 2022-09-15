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
                                    @php($a=App\PersonalPropertyOwner::find($jid->joint1))

                                    <td>{{$a->english_name}}</td>
                                    <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$a->citizenship_number}}</td>
                                @else
                                    <td></td><td></td>
                                @endif
                                @if($jid->joint2)
                                    @php($b=App\PersonalPropertyOwner::find($jid->joint2))
                                    <td>{{$b->english_name}}</td>
                                    <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$b->citizenship_number}}</td>
                                @else
                                    <td></td><td></td>
                                @endif
                                @if($jid->joint3)
                                    @php($c=App\PersonalPropertyOwner::find($jid->joint3))

                                    <td>{{$c->english_name}}</td>
                                    <td style="font-family:Bishallb,FONTASY_ HIMALI_ TT; font-size: 14px;">{{$c->citizenship_number}}</td>

                                @else
                                    <td></td><td></td>
                                @endif
                                <td>
                                    Edits
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