    @if($errors->has('english_name'))
        <span class="label label-danger">Error!! {{$errors->first('english_name')}} </span><br>
    @endif

    @if($errors->has('nepali_name'))
        <span class="label label-danger">Error!! {{$errors->first('nepali_name')}} </span><br>
    @endif
    @if($errors->has('client_id'))
        <span class="label label-danger">Error!! {{$errors->first('client_id')}} </span><br>
    @endif
    @if($errors->has('a_issued_year'))
        <span class="label label-danger">Error!! {{$errors->first('a_issued_year')}} </span><br>
    @endif

    @if($errors->has('district_id'))
        <span class="label label-danger">Error!! {{$errors->first('district_id')}} </span><br>
    @endif  @if($errors->has('phone'))
        <span class="label label-danger">Error!! {{$errors->first('phone')}} </span><br>
    @endif
    @if($errors->has('local_bodies_id'))
        <span class="label label-danger">Error!! {{$errors->first('local_bodies_id')}} </span><br>
    @endif
    @if($errors->has('wardno'))
        <span class="label label-danger">Error!! {{$errors->first('wardno')}} </span><br>
    @endif
    @if($errors->has('registration_number'))
        <span class="label label-danger">Error!! {{$errors->first('registration_number')}} </span><br>
    @endif
    @if($errors->has('authorised_person'))
        <span class="label label-danger">Error!! {{$errors->first('authorised_person')}} </span><br>
    @endif

    @if($errors->has('ministry_id'))
        <span class="label label-danger">Error!! {{$errors->first('ministry_id')}} </span><br>
    @endif
    @if($errors->has('department_id'))
        <span class="label label-danger">Error!! {{$errors->first('department_id')}} </span><br>
    @endif





    @if($errors->has('a_english_name'))
        <span class="label label-danger">Error!! {{$errors->first('a_english_name')}} </span><br>
    @endif

    @if($errors->has('a_nepali_name'))
        <span class="label label-danger">Error!! {{$errors->first('a_nepali_name')}} </span><br>
    @endif

    @if($errors->has('a_district_id'))
        <span class="label label-danger">Error!! {{$errors->first('a_district_id')}} </span><br>
    @endif
    @if($errors->has('a_local_bodies_id'))
        <span class="label label-danger">Error!! {{$errors->first('a_local_bodies_id')}} </span><br>
    @endif
    @if($errors->has('a_wardno'))
        <span class="label label-danger">Error!! {{$errors->first('a_wardno')}} </span><br>
    @endif
    @if($errors->has('a_citizenship_number'))
        <span class="label label-danger">Error!! {{$errors->first('a_citizenship_number')}} </span><br>
    @endif
    @if($errors->has('a_issued_district'))
        <span class="label label-danger">Error!! {{$errors->first('a_issued_district')}} </span><br>
    @endif

    @if($errors->has('a_grandfather_name'))
        <span class="label label-danger">Error!! {{$errors->first('a_grandfather_name')}} </span><br>
    @endif
    @if($errors->has('a_grandfather_relation'))
        <span class="label label-danger">Error!! {{$errors->first('a_grandfather_relation')}} </span><br>
    @endif

    @if($errors->has('a_father_name'))
        <span class="label label-danger">Error!! {{$errors->first('a_father_name')}} </span><br>
    @endif
    @if($errors->has('a_father_relation'))
        <span class="label label-danger">Error!! {{$errors->first('a_father_relation')}} </span><br>
    @endif
