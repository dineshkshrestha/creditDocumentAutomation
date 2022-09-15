{{--auto loan for hire purchase --}}
<div class="modal fade" id="reject" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <!-- Change class .modal-sm to change the size of the modal -->
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title w-100 box-danger" id="myModalLabel">Please Add The Reasons To Reject.</h4>
            </div>
            <div class="modal-body">
                <div class="col-sm-12 ">

                     <textarea id="editor1" name="reject" rows="10" required="required" cols="10">


                    </textarea>
                    @if($errors->has('reject'))
                        <span class="label label-danger">{{$errors->first('reject')}} </span>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                <br><br>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                {!! Form::submit('Reject Document',['class'=>'btn btn-danger btn-sm']) !!}
            </div>

        </div>
    </div>
</div>
{!! Form::close() !!}