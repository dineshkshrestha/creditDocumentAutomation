

    @if($errors->has('english_name'))
        <span class="label label-danger">Error!! {{$errors->first('english_name')}} </span><br>
    @endif

    @if($errors->has('issued_year'))
        <span class="label label-danger">Error!! {{$errors->first('issued_year')}} </span><br>
    @endif

    @if($errors->has('nepali_name'))
        <span class="label label-danger">Error!! {{$errors->first('nepali_name')}} </span><br>
    @endif
    @if($errors->has('client_id'))
        <span class="label label-danger">Error!! {{$errors->first('client_id')}} </span><br>
    @endif

    @if($errors->has('district_id'))
        <span class="label label-danger">Error!! {{$errors->first('district_id')}} </span><br>
    @endif
    @if($errors->has('phone'))
        <span class="label label-danger">Error!! {{$errors->first('phone')}} </span><br>
    @endif
    @if($errors->has('local_bodies_id'))
        <span class="label label-danger">Error!! {{$errors->first('local_bodies_id')}} </span><br>
    @endif
    @if($errors->has('wardno'))
        <span class="label label-danger">Error!! {{$errors->first('wardno')}} </span><br>
    @endif
    @if($errors->has('citizenship_number'))
        <span class="label label-danger">Error!! {{$errors->first('citizenship_number')}} </span><br>
    @endif
    @if($errors->has('issued_district'))
        <span class="label label-danger">Error!! {{$errors->first('issued_district')}} </span><br>
    @endif

    @if($errors->has('grandfather_name'))
        <span class="label label-danger">Error!! {{$errors->first('grandfather_name')}} </span><br>
    @endif
    @if($errors->has('grandfather_relation'))
        <span class="label label-danger">Error!! {{$errors->first('grandfather_relation')}} </span><br>
    @endif

    @if($errors->has('father_name'))
        <span class="label label-danger">Error!! {{$errors->first('father_name')}} </span><br>
    @endif
    @if($errors->has('father_relation'))
        <span class="label label-danger">Error!! {{$errors->first('father_relation')}} </span><br>
    @endif
