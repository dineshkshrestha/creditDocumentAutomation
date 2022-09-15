<?php
Auth::routes();
Route::get('/deactivated', 'HomeController@deactive')->name('deactive');
Route::get('/', function () {
    return redirect()->route('home');
});
Route::group(
    [
        'middleware' => ['web'],
    ], function () {
    Route::post('login/', 'Auth\LoginController@login')->name('user.login');
    Route::get('/home', 'HomeController@index')->name('home');
    Route::resource('branch', 'BranchController');
    Route::resource('province', 'ProvinceController');
    Route::resource('district', 'DistrictController');
    Route::resource('userregister', 'Auth\RegisterController');
    Route::resource('localbodies', 'LocalBodiesController');
    Route::resource('department', 'DepartmentController');
    Route::resource('ministry', 'MinistryController');
    Route::resource('facility', 'FacilityController');
    Route::resource('dispatch', 'DispatchController');
    Route::resource('registered_company', 'RegisteredCompanyController');
    Route::resource('dinesh_shrestha_pdf_merge', 'PdfMergeController');
//    for shortcut
    Route::resource('PersonalBorrower', 'PersonalBorrowerShortcutController');
    Route::resource('PersonalGuarantor', 'PersonalGuarantorShortcutController');
    Route::resource('PersonalPropertyOwner', 'PersonalPropertyOwnerShortcutController');
    Route::resource('CorporateBorrower', 'CorporateBorrowerShortcutController');
    Route::resource('CorporateGuarantor', 'CorporateGuarantorShortcutController');
    Route::resource('CorporatePropertyOwner', 'CorporatePropertyOwnerShortcutController');
    Route::put('user/change password/{id}', 'Auth\RegisterController@update_password')->name('user.update_password');
    Route::get('user/change_password/{id}', 'Auth\RegisterController@change_password')->name('user.change_password');
    Route::get('user/profile', 'Auth\RegisterController@profile')->name('user.profile');
    //district

    Route::post('local_body/store/{did}', 'DistrictController@local_body_store')->name('district.local_body_store');
    Route::get('local_body/edit/{id}/{did}', 'DistrictController@local_edit')->name('local_body.edit');

    Route::put('local_body/update/{id}', 'DistrictController@local_update')->name('local_body_update');
    Route::delete('local_body/delete/{id}/{did}', 'DistrictController@local_delete')->name('local_body.destroy');
    Route::post('/select_local_body', 'DistrictController@select_local_body')->name('district.select_local_body');

//personal borrower
    Route::get('personal_borrower/index', 'PersonalBorrowerController@personal_borrower_index')->name('personal_borrower.index');
    Route::post('personal_borrower/store', 'PersonalBorrowerController@personal_borrower_store')->name('personal_borrower.store');
    Route::get('personal_borrower/{id}/edit', 'PersonalBorrowerController@personal_borrower_edit')->name('personal_borrower.edit');
    Route::put('personal_borrower/{id}/update', 'PersonalBorrowerController@personal_borrower_update')->name('personal_borrower.update');
    Route::get('personal_borrower/{id}/show', 'PersonalBorrowerController@personal_borrower_show')->name('personal_borrower.show');
    Route::get('personal_borrower/{id}/select', 'PersonalBorrowerController@personal_borrower_select')->name('personal_borrower.select');
    Route::delete('personal_borrower/{id}/delete', 'PersonalBorrowerController@personal_borrower_destroy')->name('personal_borrower.destroy');
//choosing personal borrower from guarantor
    Route::get('personal_guarantor/borrower/index', 'PersonalBorrowerController@personal_guarantor_borrower_index')->name('personal_guarantor_borrower.index');
    Route::get('personal_guarantor/{gid}/borrower', 'PersonalBorrowerController@personal_guarantor_borrower')->name('personal_guarantor_borrower.select');
    Route::get('personal_guarantor/{gid}/borrower/edit', 'PersonalBorrowerController@personal_guarantor_borrower_edit')->name('personal_guarantor_borrower.edit');
    Route::put('personal_guarantor/{gid}/borrower/update', 'PersonalBorrowerController@personal_guarantor_borrower_update')->name('personal_guarantor_borrower.update');
    Route::delete('personal_guarantor/{gid}/borrower/delete', 'PersonalBorrowerController@personal_guarantor_borrower_destroy')->name('personal_guarantor_borrower.destroy');
//choosing personal borrower from property owner
    Route::get('personal_property_owner/borrower/index', 'PersonalBorrowerController@personal_property_owner_borrower_index')->name('personal_property_owner_borrower.index');
    Route::get('personal_property_owner/{pid}/borrower', 'PersonalBorrowerController@personal_property_owner_borrower')->name('personal_property_owner_borrower.select');
    Route::get('personal_property_owner/{pid}/borrower/edit', 'PersonalBorrowerController@personal_property_owner_borrower_edit')->name('personal_property_owner_borrower.edit');
    Route::put('personal_property_owner/{pid}/borrower/update', 'PersonalBorrowerController@personal_property_owner_borrower_update')->name('personal_property_owner_borrower.update');
    Route::delete('personal_property_owner/{pid}/borrower/delete', 'PersonalBorrowerController@personal_property_owner_borrower_destroy')->name('personal_property_owner_borrower.destroy');
    //personal guarantor
    Route::get('personal_guarantor/{bid}/index', 'PersonalGuarantorController@personal_guarantor_index')->name('personal_guarantor.index');
    //      creating new guarantor for personal borrower
    Route::get('personal_guarantor/{bid}/personal_guarantor', 'PersonalGuarantorController@personal_guarantor_create')->name('personal_guarantor.personal_create');
    Route::post('personal_guarantor/{bid}/personal_guarantor_store', 'PersonalGuarantorController@personal_guarantor_store')->name('personal_guarantor.personal_store');
    Route::get('personal_guarantor/{bid}/corporate_guarantor', 'PersonalGuarantorController@corporate_guarantor_create')->name('personal_guarantor.corporate_create');
    Route::post('personal_guarantor/{bid}/corporate_guarantor_store', 'PersonalGuarantorController@corporate_guarantor_store')->name('personal_guarantor.corporate_store');

    //      assigning personal guarantor
    Route::get('personal_guarantor/{bid}/personal_borrower/{id}', 'PersonalGuarantorController@personal_borrower_guarantor_select')->name('personal_guarantor.personal_borrower');
    Route::get('personal_guarantor/{bid}/personal_guarantor/{id}', 'PersonalGuarantorController@personal_guarantor_select')->name('personal_guarantor.personal_select');
    Route::get('personal_guarantor/{bid}/personal_property_owner/{id}', 'PersonalGuarantorController@personal_property_owner_select')->name('personal_guarantor.personal_property_owner');

    Route::get('personal_guarantor/{bid}/corporate_borrower/{id}', 'PersonalGuarantorController@corporate_borrower_guarantor_select')->name('personal_guarantor.corporate_borrower');
    Route::get('personal_guarantor/{bid}/corporate_guarantor/{id}', 'PersonalGuarantorController@corporate_guarantor_select')->name('personal_guarantor.corporate_select');
    Route::get('personal_guarantor/{bid}/corporate_property_owner/{id}', 'PersonalGuarantorController@corporate_property_owner_select')->name('personal_guarantor.corporate_property_owner');

    Route::delete('personal_guarantor/{bid}/remove/{id}', 'PersonalGuarantorController@guarantor_destroy')->name('personal_guarantor.destroy');
    Route::get('personal_guarantor/{bid}/edit/{id}', 'PersonalGuarantorController@guarantor_edit')->name('personal_guarantor.edit');
    Route::get('personal_guarantor/{bid}/proceed', 'PersonalGuarantorController@proceed')->name('personal_guarantor.proceed');

//Personal property
    Route::get('personal_property/{bid}/index', 'PersonalPropertyController@index')->name('personal_property.index');
    Route::get('personal_property/{bid}/copy', 'PersonalPropertyController@copy_index')->name('personal_property.copy_property_owner');
    Route::get('personal_property/{bid}/choose/{poid}/{status}', 'PersonalPropertyController@choose_property')->name('personal_property.choose');

    Route::get('personal_property/{bid}/personal_borrower/{id}', 'PersonalPropertyController@personal_borrower_property_select')->name('personal_property.personal_borrower');
    Route::get('personal_property/{bid}/personal_guarantor/{id}', 'PersonalPropertyController@personal_guarantor_property_select')->name('personal_property.personal_select');
    Route::get('personal_property/{bid}/personal_property_owner/{id}', 'PersonalPropertyController@personal_property_select')->name('personal_property.personal_property_owner');
    Route::get('personal_property/{bid}/corporate_borrower/{id}', 'PersonalPropertyController@corporate_borrower_property_select')->name('personal_property.corporate_borrower');
    Route::get('personal_property/{bid}/corporate_guarantor/{id}', 'PersonalPropertyController@corporate_guarantor_property_select')->name('personal_property.corporate_select');
    Route::get('personal_property/{bid}/corporate_property_owner/{id}', 'PersonalPropertyController@corporate_property_select')->name('personal_property.corporate_property_owner');
    Route::get('personal_property/{bid}/proceed', 'PersonalPropertyController@proceed')->name('personal_property.proceed');

//creating new property owner
    Route::get('personal_property/{bid}/personal_property', 'PersonalPropertyController@personal_property_create')->name('personal_property.personal_create');
    Route::post('personal_property/{bid}/personal_property_store', 'PersonalPropertyController@personal_property_store')->name('personal_property.personal_store');
    Route::get('personal_property/{bid}/corporate_property', 'PersonalPropertyController@corporate_property_create')->name('personal_property.corporate_create');
    Route::post('personal_property/{bid}/corporate_property_store', 'PersonalPropertyController@corporate_property_store')->name('personal_property.corporate_store');


//property create
//    land property crud
    Route::post('personal_property/land_store', 'PersonalPropertyController@land_store')->name('personal_property.land_store');
    Route::get('personal_property/{bid}/land_edit/{id}/{status}', 'PersonalPropertyController@land_edit')->name('personal_property.land_edit');
    Route::get('personal_property/{bid}/land_borrower_edit/{id}/{stat}/{status}', 'PersonalPropertyController@land_borrower_edit')->name('personal_property.land_borrower_edit');
    Route::put('personal_property/land_update', 'PersonalPropertyController@land_update')->name('personal_property.land_update');
    Route::put('personal_property/land_borrower_update', 'PersonalPropertyController@land_borrower_update')->name('personal_property.land_borrower_update');
//    remove and assign land
    Route::delete('personal_property/{bid}/land_remove/{pid}/{status}', 'PersonalPropertyController@land_destroy')->name('personal_property.land_destroy');
    Route::delete('personal_property/{bid}/land_borrower_remove/{pid}/{stat}/{status}', 'PersonalPropertyController@land_borrower_destroy')->name('personal_property.land_borrower_destroy');
    Route::get('personal_property/{bid}/land_assign/{pid}/{status}', 'PersonalPropertyController@land_assign')->name('personal_property.land_assign');
//front land
    Route::get('personal_property/{bid}/land_front_edit/{id}/{status}', 'PersonalPropertyController@land_front_edit')->name('personal_property.land_front_edit');
    Route::put('personal_property/land_front_update', 'PersonalPropertyController@land_front_update')->name('personal_property.land_front_update');
    Route::delete('personal_property/{bid}/land_front_remove/{pid}/{status}', 'PersonalPropertyController@land_front_destroy')->name('personal_property.land_front_destroy');
//joint front land
    Route::get('personal_property/{bid}/joint land_front_edit/{id}', 'PersonalPropertyController@joint_land_front_edit')->name('personal_property.joint_land_front_edit');
    Route::put('personal_property/joint land_front_update', 'PersonalPropertyController@joint_land_front_update')->name('personal_property.joint_land_front_update');
    Route::delete('personal_property/{bid}/joint land_front_remove/{pid}', 'PersonalPropertyController@joint_land_front_destroy')->name('personal_property.joint_land_front_destroy');
//Share front
    Route::get('personal_property/{bid}/share_front_edit/{pid}/{status}', 'PersonalPropertyController@share_front_edit')->name('personal_property.share_front_edit');
    Route::put('personal_property/share_front_update/{pid}', 'PersonalPropertyController@share_front_update')->name('personal_property.share_front_update');
    Route::delete('personal_property/{bid}/share_front_remove/{pid}/{status}', 'PersonalPropertyController@share_front_destroy')->name('personal_property.share_front_destroy');


//    share property crud
    Route::post('personal_property/share_store', 'PersonalPropertyController@share_store')->name('personal_property.share_store');
    Route::get('personal_property/{bid}/share_edit/{pid}/{status}', 'PersonalPropertyController@share_edit')->name('personal_property.share_edit');
    Route::get('personal_property/{bid}/share_borrower_edit/{pid}/{stat}/{status}', 'PersonalPropertyController@share_borrower_edit')->name('personal_property.share_borrower_edit');
    Route::put('personal_property/share_update/{pid}', 'PersonalPropertyController@share_update')->name('personal_property.share_update');
    Route::put('personal_property/share_borrower_update/{pid}', 'PersonalPropertyController@share_borrower_update')->name('personal_property.share_borrower_update');
//    remove and assign share
    Route::delete('personal_property/{bid}/share_remove/{pid}/{status}', 'PersonalPropertyController@share_destroy')->name('personal_property.share_destroy');
    Route::delete('personal_property/{bid}/share_borrower_remove/{pid}/{stat}/{status}', 'PersonalPropertyController@share_borrower_destroy')->name('personal_property.share_borrower_destroy');
    Route::get('personal_property/{bid}/share_assign/{pid}/{status}', 'PersonalPropertyController@share_assign')->name('personal_property.share_assign');

    //vehicle
    Route::post('personal_borrower/vehicle_store', 'PersonalPropertyController@vehicle_store')->name('personal_property.vehicle_store');
    Route::get('personal_borrower/{bid}/vehicle/{id}/edit', 'PersonalPropertyController@vehicle_edit')->name('personal_property.vehicle_edit');
    Route::put('personal_borrower/vehicle/{id}/update', 'PersonalPropertyController@vehicle_update')->name('personal_property.vehicle_update');
    Route::delete('personal_borrower/{bid}/vehicle/{id}/delete', 'PersonalPropertyController@vehicle_destroy')->name('personal_property.vehicle_destroy');


//    personal facilities
    Route::get('personal_borrower/{bid}/facilities/index', 'PersonalFacilitiesController@index')->name('personal_facilities.index');
    Route::post('personal_borrower/facilities/store', 'PersonalFacilitiesController@store')->name('personal_facilities.store');
    Route::get('personal_borrower/{bid}/facilities/{id}/edit', 'PersonalFacilitiesController@edit')->name('personal_facilities.edit');
    Route::put('personal_borrower/facilities/update/{id}', 'PersonalFacilitiesController@update')->name('personal_facilities.update');
    Route::delete('personal_borrower/{id}/facilities/delete', 'PersonalFacilitiesController@destroy')->name('personal_facilities.destroy');
    Route::get('personal_borrower/{id}/facilities/proceed', 'PersonalFacilitiesController@proceed')->name('personal_facilities.proceed');

//personal Loan
    Route::get('personal_borrower/{bid}/loan/index', 'PersonalLoanController@index')->name('personal_loan.index');
    Route::post('personal_borrower/loan/store', 'PersonalLoanController@store')->name('personal_loan.store');
    Route::get('personal_borrower/{bid}/loan/edit', 'PersonalLoanController@edit')->name('personal_loan.edit');
    Route::put('personal_borrower/loan/update/{id}', 'PersonalLoanController@update')->name('personal_loan.update');
    Route::delete('personal_borrower/{bid}/loan/delete', 'PersonalLoanController@destroy')->name('personal_loan.destroy');
    Route::get('personal_borrower/{bid}/loan/proceed', 'PersonalLoanController@proceed')->name('personal_loan.proceed');


    //personal review and approve
    Route::get('personal_borrower/{bid}/review/index', 'PersonalReviewController@index')->name('personal_review.index');

    Route::get('personal_borrower/review/{id}/borrower_edit', 'PersonalReviewController@borrower_edit')->name('personal_review.borrower_edit');
    Route::put('personal_borrower/{id}/review/borrower_update', 'PersonalReviewController@borrower_update')->name('personal_review.update');

    Route::get('personal_borrower/{bid}/review/{id}/corporate_guarantor_edit', 'PersonalReviewController@corporate_guarantor_edit')->name('personal_review.corporate_guarantor_edit');
    Route::put('personal_borrower/{id}/review/corporate_guarantor_update', 'PersonalReviewController@corporate_guarantor_update')->name('personal_review.corporate_guarantor_update');
    Route::delete('personal_borrower/{bid}/review/{id}/corporate_guarantor_delete', 'PersonalReviewController@corporate_guarantor_destroy')->name('personal_review.corporate_guarantor_destroy');

    Route::get('personal_borrower/review/{bid}/{id}/personal_guarantor_edit', 'PersonalReviewController@personal_guarantor_edit')->name('personal_review.personal_guarantor_edit');
    Route::put('personal_borrower/{id}/review/personal_guarantor_update', 'PersonalReviewController@personal_guarantor_update')->name('personal_review.personal_guarantor_update');
    Route::delete('personal_borrower/{bid}/review/{id}/personal_guarantor_delete', 'PersonalReviewController@personal_guarantor_destroy')->name('personal_review.personal_guarantor_destroy');
//personal property owner
    Route::get('personal_borrower/review/{bid}/{id}/personal_property_owner_edit', 'PersonalReviewController@personal_property_owner_edit')->name('personal_review.personal_property_owner_edit');
    Route::put('personal_borrower/{id}/review/personal_property_owner_update', 'PersonalReviewController@personal_property_owner_update')->name('personal_review.personal_property_owner_update');
    Route::delete('personal_borrower/{bid}/review/{id}/personal_property_owner_delete', 'PersonalReviewController@personal_property_owner_destroy')->name('personal_review.personal_property_owner_destroy');
//personal land
    Route::get('personal_borrower/review/{bid}/{id}/personal_land_edit', 'PersonalReviewController@personal_land_edit')->name('personal_review.personal_land_edit');
    Route::put('personal_borrower/{id}/review/personal_land_update', 'PersonalReviewController@personal_land_update')->name('personal_review.personal_land_update');
    Route::delete('personal_borrower/{bid}/review/{id}/personal_land_delete', 'PersonalReviewController@personal_land_destroy')->name('personal_review.personal_land_destroy');
//personal share
    Route::get('personal_borrower/review/{bid}/{id}/personal_share_edit', 'PersonalReviewController@personal_share_edit')->name('personal_review.personal_share_edit');
    Route::put('personal_borrower/{id}/review/personal_share_update', 'PersonalReviewController@personal_share_update')->name('personal_review.personal_share_update');
    Route::delete('personal_borrower/{bid}/review/{id}/personal_share_delete', 'PersonalReviewController@personal_share_destroy')->name('personal_review.personal_share_destroy');

    //corporate property owner
    Route::get('personal_borrower/review/{bid}/{id}/corporate_property_owner_edit', 'PersonalReviewController@corporate_property_owner_edit')->name('personal_review.corporate_property_owner_edit');
    Route::put('personal_borrower/{id}/review/corporate_property_owner_update', 'PersonalReviewController@corporate_property_owner_update')->name('personal_review.corporate_property_owner_update');
    Route::delete('personal_borrower/{bid}/review/{id}/corporate_property_owner_delete', 'PersonalReviewController@corporate_property_owner_destroy')->name('personal_review.corporate_property_owner_destroy');
//corporate land
    Route::get('personal_borrower/review/{bid}/{id}/corporate_land_edit', 'PersonalReviewController@corporate_land_edit')->name('personal_review.corporate_land_edit');
    Route::put('personal_borrower/{id}/review/corporate_land_update', 'PersonalReviewController@corporate_land_update')->name('personal_review.corporate_land_update');
    Route::delete('personal_borrower/{bid}/review/{id}/corporate_land_delete', 'PersonalReviewController@corporate_land_destroy')->name('personal_review.corporate_land_destroy');
//corporate share
    Route::get('personal_borrower/review/{bid}/{id}/corporate_share_edit', 'PersonalReviewController@corporate_share_edit')->name('personal_review.corporate_share_edit');
    Route::put('personal_borrower/{id}/review/corporate_share_update', 'PersonalReviewController@corporate_share_update')->name('personal_review.corporate_share_update');
    Route::delete('personal_borrower/{bid}/review/{id}/corporate_share_delete', 'PersonalReviewController@corporate_share_destroy')->name('personal_review.corporate_share_destroy');

    //joint property owner
    Route::delete('personal_borrower/{bid}/review/joint_property_owner_delete', 'PersonalReviewController@joint_property_owner_destroy')->name('personal_review.joint_property_owner_destroy');
//joint land
    Route::get('personal_borrower/review/{bid}/{id}/joint_land_edit', 'PersonalReviewController@joint_land_edit')->name('personal_review.joint_land_edit');
    Route::put('personal_borrower/{id}/review/joint_land_update', 'PersonalReviewController@joint_land_update')->name('personal_review.joint_land_update');
    Route::delete('personal_borrower/{bid}/review/{id}/_land_delete', 'PersonalReviewController@joint_land_destroy')->name('personal_review.joint_land_destroy');



//personal facilities
    Route::get('personal_borrower/review/{bid}/{id}/facilities/edit', 'PersonalReviewController@facilities_edit')->name('personal_review.facilities_edit');
    Route::put('personal_borrower/{id}/review/facilities/update', 'PersonalReviewController@facilities_update')->name('personal_review.facilities_update');
    Route::delete('personal_borrower/{bid}/review/{id}/facilities/delete', 'PersonalReviewController@facilities_destroy')->name('personal_review.facilities_destroy');

//   personal loan
    Route::get('personal_borrower/review/{bid}/{id}/loan/edit', 'PersonalReviewController@loan_edit')->name('personal_review.loan_edit');
    Route::put('personal_borrower/{id}/review/loan/update', 'PersonalReviewController@loan_update')->name('personal_review.loan_update');

    Route::get('personal_borrower/review/{bid}/{id}/hire_purchase/edit', 'PersonalReviewController@hirepurchase_edit')->name('personal_review.hirepurchase_edit');
    Route::put('personal_borrower/{id}/review/hire_purchase/update', 'PersonalReviewController@hirepurchase_update')->name('personal_review.hirepurchase_update');
    Route::delete('personal_borrower/{bid}/review/{id}/hirepurchase/delete', 'PersonalReviewController@hirepurchase_destroy')->name('personal_review.hirepurchase_destroy');
    Route::get('personal_borrower/{bid}/review/proceed', 'PersonalReviewController@proceed')->name('personal_review.proceed');





    //corporate Borrower
    Route::get('corporate_borrower/index', 'CorporateBorrowerController@corporate_borrower_index')->name('corporate_borrower.index');
    Route::post('corporate_borrower/store', 'CorporateBorrowerController@corporate_borrower_store')->name('corporate_borrower.store');
    Route::get('corporate_borrower/{id}/edit', 'CorporateBorrowerController@corporate_borrower_edit')->name('corporate_borrower.edit');

    Route::put('corporate_borrower/{id}/update', 'CorporateBorrowerController@corporate_borrower_update')->name('corporate_borrower.update');
    Route::get('corporate_borrower/{id}/show', 'CorporateBorrowerController@corporate_borrower_show')->name('corporate_borrower.show');
    Route::delete('corporate_borrower/{id}/delete', 'CorporateBorrowerController@corporate_borrower_destroy')->name('corporate_borrower.destroy');
    //choosing corporate borrower from corporate guarantor
    Route::get('corporate guarantor/borrower/index', 'CorporateBorrowerController@corporate_guarantor_borrower_index')->name('corporate_guarantor_borrower.index');
    Route::get('corporate guarantor/{gid}/borrower', 'CorporateBorrowerController@corporate_guarantor_borrower')->name('corporate_guarantor_borrower.select');
    Route::get('corporate guarantor/{gid}/borrower/edit', 'CorporateBorrowerController@personal_guarantor_borrower_edit')->name('corporate_guarantor_borrower.edit');
    Route::get('corporate guarantor/{gid}/borrower/show', 'CorporateBorrowerController@personal_guarantor_borrower_show')->name('corporate_guarantor_borrower.show');
    Route::put('corporate guarantor/{gid}/borrower/update', 'CorporateBorrowerController@personal_guarantor_borrower_update')->name('corporate_guarantor_borrower.update');
    Route::delete('corporate guarantor/{gid}/borrower/delete', 'CorporateBorrowerController@personal_guarantor_borrower_destroy')->name('corporate_guarantor_borrower.destroy');
    //choosing corporate borrower from corporate property owner
    Route::get('corporate property owner/borrower/index', 'CorporateBorrowerController@corporate_property_owner_borrower_index')->name('corporate_property_owner_borrower.index');
    Route::get('corporate property owner/{pid}/borrower', 'CorporateBorrowerController@corporate_property_owner_borrower')->name('corporate_property_owner_borrower.select');
    Route::get('corporate property owner/{pid}/borrower/edit', 'CorporateBorrowerController@personal_property_owner_borrower_edit')->name('corporate_property_owner_borrower.edit');
    Route::get('corporate property owner/{pid}/borrower/show', 'CorporateBorrowerController@personal_property_owner_borrower_show')->name('corporate_property_owner_borrower.show');
    Route::put('corporate property owner/{pid}/borrower/update', 'CorporateBorrowerController@personal_property_owner_borrower_update')->name('corporate_property_owner_borrower.update');
    Route::delete('corporate property owner/{pid}/borrower/delete', 'CorporateBorrowerController@personal_property_owner_borrower_destroy')->name('corporate_property_owner_borrower.destroy');


    //corporate guarantor
    Route::get('corporate_guarantor/{bid}/index', 'CorporateGuarantorController@corporate_guarantor_index')->name('corporate_guarantor.index');
    //      creating new guarantor for corporate borrower
    Route::get('corporate_guarantor/{bid}/personal_guarantor', 'CorporateGuarantorController@personal_guarantor_create')->name('corporate_guarantor.personal_create');
    Route::post('corporate_guarantor/{bid}/personal_guarantor_store', 'CorporateGuarantorController@personal_guarantor_store')->name('corporate_guarantor.personal_store');
    Route::get('corporate_guarantor/{bid}/corporate_guarantor', 'CorporateGuarantorController@corporate_guarantor_create')->name('corporate_guarantor.corporate_create');
    Route::post('corporate_guarantor/{bid}/corporate_guarantor_store', 'CorporateGuarantorController@corporate_guarantor_store')->name('corporate_guarantor.corporate_store');

    //      assigning corporate guarantor
    Route::get('corporate_guarantor/{bid}/personal_borrower/{id}', 'CorporateGuarantorController@personal_borrower_guarantor_select')->name('corporate_guarantor.personal_borrower');
    Route::get('corporate_guarantor/{bid}/personal_guarantor/{id}', 'CorporateGuarantorController@personal_guarantor_select')->name('corporate_guarantor.personal_select');
    Route::get('corporate_guarantor/{bid}/personal_property_owner/{id}', 'CorporateGuarantorController@personal_property_owner_select')->name('corporate_guarantor.personal_property_owner');
    Route::get('corporate_guarantor/{bid}/authorized_person/{id}', 'CorporateGuarantorController@authorized_person_guarantor_select')->name('corporate_guarantor.authorized_person');

    Route::get('corporate_guarantor/{bid}/corporate_borrower/{id}', 'CorporateGuarantorController@corporate_borrower_guarantor_select')->name('corporate_guarantor.corporate_borrower');
    Route::get('corporate_guarantor/{bid}/corporate_guarantor/{id}', 'CorporateGuarantorController@corporate_guarantor_select')->name('corporate_guarantor.corporate_select');
    Route::get('corporate_guarantor/{bid}/corporate_property_owner/{id}', 'CorporateGuarantorController@corporate_property_owner_select')->name('corporate_guarantor.corporate_property_owner');

    Route::delete('corporate_guarantor/{bid}/remove/{id}', 'CorporateGuarantorController@guarantor_destroy')->name('corporate_guarantor.destroy');
    Route::get('corporate_guarantor/{bid}/edit/{id}', 'CorporateGuarantorController@guarantor_edit')->name('corporate_guarantor.edit');

    Route::get('corporate_guarantor/{bid}/proceed', 'CorporateGuarantorController@proceed')->name('corporate_guarantor.proceed');

    //Corporate property
    Route::get('corporate_property/{bid}/index', 'CorporatePropertyController@index')->name('corporate_property.index');
    Route::get('corporate_property/{bid}/copy', 'CorporatePropertyController@copy_index')->name('corporate_property.copy_property_owner');
    Route::get('corporate_property/{bid}/choose/{poid}/{status}', 'CorporatePropertyController@choose_property')->name('corporate_property.choose');

    Route::get('corporate_property/{bid}/personal_borrower/{id}', 'CorporatePropertyController@personal_borrower_property_select')->name('corporate_property.personal_borrower');
    Route::get('corporate_property/{bid}/personal_guarantor/{id}', 'CorporatePropertyController@personal_guarantor_property_select')->name('corporate_property.personal_select');
    Route::get('corporate_property/{bid}/personal_property_owner/{id}', 'CorporatePropertyController@personal_property_select')->name('corporate_property.personal_property_owner');
    Route::get('corporate_property/{bid}/corporate_borrower/{id}', 'CorporatePropertyController@corporate_borrower_property_select')->name('corporate_property.corporate_borrower');
    Route::get('corporate_property/{bid}/corporate_guarantor/{id}', 'CorporatePropertyController@corporate_guarantor_property_select')->name('corporate_property.corporate_select');
    Route::get('corporate_property/{bid}/corporate_property_owner/{id}', 'CorporatePropertyController@corporate_property_select')->name('corporate_property.corporate_property_owner');
    Route::get('corporate_property/{bid}/proceed', 'CorporatePropertyController@proceed')->name('corporate_property.proceed');
//creating new property owner
    Route::get('corporate_property/{bid}/personal_property', 'CorporatePropertyController@personal_property_create')->name('corporate_property.personal_create');
    Route::post('corporate_property/{bid}/personal_property_store', 'CorporatePropertyController@personal_property_store')->name('corporate_property.personal_store');
    Route::get('corporate_property/{bid}/corporate_property', 'CorporatePropertyController@corporate_property_create')->name('corporate_property.corporate_create');
    Route::post('corporate_property/{bid}/corporate_property_store', 'CorporatePropertyController@corporate_property_store')->name('corporate_property.corporate_store');


//property create
//    land property crud
    Route::post('corporate_property/land_store', 'CorporatePropertyController@land_store')->name('corporate_property.land_store');
    Route::get('corporate_property/{bid}/land_edit/{id}/{status}', 'CorporatePropertyController@land_edit')->name('corporate_property.land_edit');
    Route::get('corporate_property/{bid}/land_borrower_edit/{id}/{stat}/{status}', 'CorporatePropertyController@land_borrower_edit')->name('corporate_property.land_borrower_edit');
    Route::put('corporate_property/land_update', 'CorporatePropertyController@land_update')->name('corporate_property.land_update');
    Route::put('corporate_property/land_borrower_update', 'CorporatePropertyController@land_borrower_update')->name('corporate_property.land_borrower_update');
//    remove and assign land
    Route::delete('corporate_property/{bid}/land_remove/{pid}/{status}', 'CorporatePropertyController@land_destroy')->name('corporate_property.land_destroy');
    Route::delete('corporate_property/{bid}/land_borrower_remove/{pid}/{stat}/{status}', 'CorporatePropertyController@land_borrower_destroy')->name('corporate_property.land_borrower_destroy');
    Route::get('corporate_property/{bid}/land_assign/{pid}/{status}', 'CorporatePropertyController@land_assign')->name('corporate_property.land_assign');


//    share property crud
    Route::post('corporate_property/share_store', 'CorporatePropertyController@share_store')->name('corporate_property.share_store');
    Route::get('corporate_property/{bid}/share_edit/{pid}/{status}', 'CorporatePropertyController@share_edit')->name('corporate_property.share_edit');
    Route::get('corporate_property/{bid}/share_borrower_edit/{pid}/{stat}/{status}', 'CorporatePropertyController@share_borrower_edit')->name('corporate_property.share_borrower_edit');
    Route::put('corporate_property/share_update/{pid}', 'CorporatePropertyController@share_update')->name('corporate_property.share_update');
    Route::put('corporate_property/share_borrower_update/{pid}', 'CorporatePropertyController@share_borrower_update')->name('corporate_property.share_borrower_update');
//    remove and assign share
    Route::delete('corporate_property/{bid}/share_remove/{pid}/{status}', 'CorporatePropertyController@share_destroy')->name('corporate_property.share_destroy');
    Route::delete('corporate_property/{bid}/share_borrower_remove/{pid}/{stat}/{status}', 'CorporatePropertyController@share_borrower_destroy')->name('corporate_property.share_borrower_destroy');
    Route::get('corporate_property/{bid}/share_assign/{pid}/{status}', 'CorporatePropertyController@share_assign')->name('corporate_property.share_assign');

    //front land
    Route::get('corporate_property/{bid}/land_front_edit/{id}/{status}', 'CorporatePropertyController@land_front_edit')->name('corporate_property.land_front_edit');
    Route::put('corporate_property/land_front_update', 'CorporatePropertyController@land_front_update')->name('corporate_property.land_front_update');
    Route::delete('corporate_property/{bid}/land_front_remove/{pid}/{status}', 'CorporatePropertyController@land_front_destroy')->name('corporate_property.land_front_destroy');
//Share front
    Route::get('corporate_property/{bid}/share_front_edit/{pid}/{status}', 'CorporatePropertyController@share_front_edit')->name('corporate_property.share_front_edit');
    Route::put('corporate_property/share_front_update/{pid}', 'CorporatePropertyController@share_front_update')->name('corporate_property.share_front_update');
    Route::delete('corporate_property/{bid}/share_front_remove/{pid}/{status}', 'CorporatePropertyController@share_front_destroy')->name('corporate_property.share_front_destroy');


    //vehicle
    Route::post('corporate_property/vehicle_store', 'CorporatePropertyController@vehicle_store')->name('corporate_property.vehicle_store');
    Route::get('corporate_property/{bid}/vehicle/{id}/edit', 'CorporatePropertyController@vehicle_edit')->name('corporate_property.vehicle_edit');
    Route::put('corporate_property/vehicle/{id}/update', 'CorporatePropertyController@vehicle_update')->name('corporate_property.vehicle_update');
    Route::delete('corporate_property/{bid}/vehicle/{id}/delete', 'CorporatePropertyController@vehicle_destroy')->name('corporate_property.vehicle_destroy');


//    corporate facilities
    Route::get('corporate_property/{bid}/facilities/index', 'CorporateFacilitiesController@index')->name('corporate_facilities.index');
    Route::post('corporate_property/facilities/store', 'CorporateFacilitiesController@store')->name('corporate_facilities.store');
    Route::get('corporate_property/{bid}/facilities/{id}/edit', 'CorporateFacilitiesController@edit')->name('corporate_facilities.edit');
    Route::put('corporate_property/facilities/update/{id}', 'CorporateFacilitiesController@update')->name('corporate_facilities.update');
    Route::delete('corporate_property/{id}/facilities/delete', 'CorporateFacilitiesController@destroy')->name('corporate_facilities.destroy');
    Route::get('corporate_property/{id}/facilities/proceed', 'CorporateFacilitiesController@proceed')->name('corporate_facilities.proceed');

//corporate Loan
    Route::get('corporate_property/{bid}/loan/index', 'CorporateLoanController@index')->name('corporate_loan.index');
    Route::post('corporate_property/loan/store', 'CorporateLoanController@store')->name('corporate_loan.store');
    Route::get('corporate_property/{bid}/loan/edit', 'CorporateLoanController@edit')->name('corporate_loan.edit');
    Route::put('corporate_property/loan/update/{id}', 'CorporateLoanController@update')->name('corporate_loan.update');
    Route::delete('corporate_property/{bid}/loan/delete', 'CorporateLoanController@destroy')->name('corporate_loan.destroy');
    Route::get('corporate_property/{bid}/loan/proceed', 'CorporateLoanController@proceed')->name('corporate_loan.proceed');

//corporate review
    Route::get('corporate_borrower/{bid}/review/index', 'CorporateReviewController@index')->name('corporate_review.index');

    Route::get('corporate_borrower/review/{id}/borrower_edit', 'CorporateReviewController@borrower_edit')->name('corporate_review.borrower_edit');
    Route::put('corporate_borrower/{id}/review/borrower_update', 'CorporateReviewController@borrower_update')->name('corporate_review.update');


    Route::get('corporate_borrower/{bid}/review/{id}/personal_guarantor_edit', 'CorporateReviewController@personal_guarantor_edit')->name('corporate_review.personal_guarantor_edit');
    Route::put('corporate_borrower/{id}/review/personal_guarantor_update', 'CorporateReviewController@personal_guarantor_update')->name('corporate_review.personal_guarantor_update');
    Route::delete('corporate_borrower/{bid}/review/{id}/personal_guarantor_delete', 'CorporateReviewController@personal_guarantor_destroy')->name('corporate_review.personal_guarantor_destroy');

    Route::get('corporate_borrower/review/{bid}/{id}/corporate_guarantor_edit', 'CorporateReviewController@corporate_guarantor_edit')->name('corporate_review.corporate_guarantor_edit');
    Route::put('corporate_borrower/{id}/review/corporate_guarantor_update', 'CorporateReviewController@corporate_guarantor_update')->name('corporate_review.corporate_guarantor_update');
    Route::delete('corporate_borrower/{bid}/review/{id}/corporate_guarantor_delete', 'CorporateReviewController@corporate_guarantor_destroy')->name('corporate_review.corporate_guarantor_destroy');
//personal property owner
    Route::get('corporate_borrower/review/{bid}/{id}/personal_property_owner_edit', 'CorporateReviewController@personal_property_owner_edit')->name('corporate_review.personal_property_owner_edit');
    Route::put('corporate_borrower/{id}/review/personal_property_owner_update', 'CorporateReviewController@personal_property_owner_update')->name('corporate_review.personal_property_owner_update');
    Route::delete('corporate_borrower/{bid}/review/{id}/personal_property_owner_delete', 'CorporateReviewController@personal_property_owner_destroy')->name('corporate_review.personal_property_owner_destroy');
//personal land
    Route::get('corporate_borrower/review/{bid}/{id}/personal_land_edit', 'CorporateReviewController@personal_land_edit')->name('corporate_review.personal_land_edit');
    Route::put('corporate_borrower/{id}/review/personal_land_update', 'CorporateReviewController@personal_land_update')->name('corporate_review.personal_land_update');
    Route::delete('corporate_borrower/{bid}/review/{id}/personal_land_delete', 'CorporateReviewController@personal_land_destroy')->name('corporate_review.personal_land_destroy');
//personal share
    Route::get('corporate_borrower/review/{bid}/{id}/personal_share_edit', 'CorporateReviewController@personal_share_edit')->name('corporate_review.personal_share_edit');
    Route::put('corporate_borrower/{id}/review/personal_share_update', 'CorporateReviewController@personal_share_update')->name('corporate_review.personal_share_update');
    Route::delete('corporate_borrower/{bid}/review/{id}/personal_share_delete', 'CorporateReviewController@personal_share_destroy')->name('corporate_review.personal_share_destroy');

    //corporate property owner
    Route::get('corporate_borrower/review/{bid}/{id}/corporate_property_owner_edit', 'CorporateReviewController@corporate_property_owner_edit')->name('corporate_review.corporate_property_owner_edit');
    Route::put('corporate_borrower/{id}/review/corporate_property_owner_update', 'CorporateReviewController@corporate_property_owner_update')->name('corporate_review.corporate_property_owner_update');
    Route::delete('corporate_borrower/{bid}/review/{id}/corporate_property_owner_delete', 'CorporateReviewController@corporate_property_owner_destroy')->name('corporate_review.corporate_property_owner_destroy');
//corporate land
    Route::get('corporate_borrower/review/{bid}/{id}/corporate_land_edit', 'CorporateReviewController@corporate_land_edit')->name('corporate_review.corporate_land_edit');
    Route::put('corporate_borrower/{id}/review/corporate_land_update', 'CorporateReviewController@corporate_land_update')->name('corporate_review.corporate_land_update');
    Route::delete('corporate_borrower/{bid}/review/{id}/corporate_land_delete', 'CorporateReviewController@corporate_land_destroy')->name('corporate_review.corporate_land_destroy');
//corporate share
    Route::get('corporate_borrower/review/{bid}/{id}/corporate_share_edit', 'CorporateReviewController@corporate_share_edit')->name('corporate_review.corporate_share_edit');
    Route::put('corporate_borrower/{id}/review/corporate_share_update', 'CorporateReviewController@corporate_share_update')->name('corporate_review.corporate_share_update');
    Route::delete('corporate_borrower/{bid}/review/{id}/corporate_share_delete', 'CorporateReviewController@corporate_share_destroy')->name('corporate_review.corporate_share_destroy');

    //joint property owner
    Route::delete('corporate_borrower/{bid}/review/joint_property_owner_delete', 'CorporateReviewController@joint_property_owner_destroy')->name('corporate_review.joint_property_owner_destroy');
//corporate joint land
    Route::get('corporate_borrower/review/{bid}/{id}/joint_land_edit', 'CorporateReviewController@joint_land_edit')->name('corporate_review.joint_land_edit');
    Route::put('corporate_borrower/{id}/review/joint_land_update', 'CorporateReviewController@joint_land_update')->name('corporate_review.joint_land_update');
    Route::delete('corporate_borrower/{bid}/review/{id}/_land_delete', 'CorporateReviewController@joint_land_destroy')->name('corporate_review.joint_land_destroy');



//corporate facilities
    Route::get('corporate_borrower/review/{bid}/{id}/facilities/edit', 'CorporateReviewController@facilities_edit')->name('corporate_review.facilities_edit');
    Route::put('corporate_borrower/{id}/review/facilities/update', 'CorporateReviewController@facilities_update')->name('corporate_review.facilities_update');
    Route::delete('corporate_borrower/{bid}/review/{id}/facilities/delete', 'CorporateReviewController@facilities_destroy')->name('corporate_review.facilities_destroy');

//   corporate loan
    Route::get('corporate_borrower/review/{bid}/{id}/loan/edit', 'CorporateReviewController@loan_edit')->name('corporate_review.loan_edit');
    Route::put('corporate_borrower/{id}/review/loan/update', 'CorporateReviewController@loan_update')->name('corporate_review.loan_update');

    Route::get('corporate_borrower/review/{bid}/{id}/hire_purchase/edit', 'CorporateReviewController@hirepurchase_edit')->name('corporate_review.hirepurchase_edit');
    Route::put('corporate_borrower/{id}/review/hire_purchase/update', 'CorporateReviewController@hirepurchase_update')->name('corporate_review.hirepurchase_update');
    Route::delete('corporate_borrower/{bid}/review/{id}/hirepurchase/delete', 'CorporateReviewController@hirepurchase_destroy')->name('corporate_review.hirepurchase_destroy');
    Route::get('corporate_borrower/{bid}/review/proceed', 'CorporateReviewController@proceed')->name('corporate_review.proceed');

//end corporate review




//    joint borrower
    Route::get('Joint_Borrower/index', 'JointBorrowerController@index')->name('joint_borrower.index');
    Route::post('joint_borrower/store/new_borrower', 'JointBorrowerController@store')->name('joint_borrower.store');
    Route::post('joint_borrower/save/joint', 'JointBorrowerController@joint_borrower_store')->name('joint_borrower.joint_store');
    Route::get('joint_borrower/{id}/edit', 'JointBorrowerController@joint_borrower_edit')->name('joint_borrower.edit');
    Route::put('joint_borrower/{id}/update', 'JointBorrowerController@joint_borrower_update')->name('joint_borrower.update');
    Route::get('joint_borrower/{id}/show', 'JointBorrowerController@joint_borrower_show')->name('joint_borrower.show');
//    Route::get('joint_borrower/{id}/select', 'JointBorrowerController@joint_borrower_select')->name('joint_borrower.select');
    Route::delete('joint_borrower/{id}/delete', 'JointBorrowerController@joint_borrower_destroy')->name('joint_borrower.destroy');
//choosing joint borrower from guarantor
    Route::get('joint_guarantor/borrower/index', 'JointBorrowerController@joint_guarantor_borrower_index')->name('joint_guarantor_borrower.index');
    Route::get('joint_guarantor/{gid}/borrower', 'JointBorrowerController@joint_guarantor_borrower')->name('joint_guarantor_borrower.select');
    Route::get('joint_guarantor/{gid}/borrower/edit', 'JointBorrowerController@joint_guarantor_borrower_edit')->name('joint_guarantor_borrower.edit');
    Route::put('joint_guarantor/{gid}/borrower/update', 'JointBorrowerController@joint_guarantor_borrower_update')->name('joint_guarantor_borrower.update');
    Route::delete('joint_guarantor/{gid}/borrower/delete', 'JointBorrowerController@joint_guarantor_borrower_destroy')->name('joint_guarantor_borrower.destroy');
//choosing joint borrower from property owner
    Route::get('joint_property_owner/borrower/index', 'JointBorrowerController@joint_property_owner_borrower_index')->name('joint_property_owner_borrower.index');
    Route::get('joint_property_owner/{pid}/borrower', 'JointBorrowerController@joint_property_owner_borrower')->name('joint_property_owner_borrower.select');
    Route::get('joint_property_owner/{pid}/borrower/edit', 'JointBorrowerController@joint_property_owner_borrower_edit')->name('joint_property_owner_borrower.edit');
    Route::put('joint_property_owner/{pid}/borrower/update', 'JointBorrowerController@joint_property_owner_borrower_update')->name('joint_property_owner_borrower.update');
    Route::delete('joint_property_owner/{pid}/borrower/delete', 'JointBorrowerController@joint_property_owner_borrower_destroy')->name('joint_property_owner_borrower.destroy');


    //joint guarantor
    Route::get('joint_guarantor/{bid}/index', 'JointGuarantorController@joint_guarantor_index')->name('joint_guarantor.index');
    //      creating new guarantor for joint borrower
    Route::get('joint_guarantor/{bid}/personal_guarantor', 'JointGuarantorController@joint_guarantor_create')->name('joint_guarantor.personal_create');
    Route::post('joint_guarantor/{bid}/personal_guarantor_store', 'JointGuarantorController@joint_guarantor_store')->name('joint_guarantor.personal_store');

    //      assigning joint guarantor
    Route::get('joint_guarantor/{bid}/joint_borrower/{id}', 'JointGuarantorController@joint_borrower_guarantor_select')->name('joint_guarantor.personal_borrower');
    Route::get('joint_guarantor/{bid}/joint_guarantor/{id}', 'JointGuarantorController@joint_guarantor_select')->name('joint_guarantor.personal_select');
    Route::get('joint_guarantor/{bid}/joint_property_owner/{id}', 'JointGuarantorController@joint_property_owner_select')->name('joint_guarantor.personal_property_owner');


    Route::delete('joint_guarantor/{bid}/remove/{id}', 'JointGuarantorController@guarantor_destroy')->name('joint_guarantor.destroy');
    Route::get('joint_guarantor/{bid}/edit/{id}', 'JointGuarantorController@guarantor_edit')->name('joint_guarantor.edit');
    Route::get('joint_guarantor/{bid}/proceed', 'JointGuarantorController@proceed')->name('joint_guarantor.proceed');

//Joint property
    Route::get('joint_property/{bid}/index', 'JointPropertyController@index')->name('joint_property.index');
    Route::get('joint_property/{bid}/copy', 'JointPropertyController@copy_index')->name('joint_property.copy_property_owner');
    Route::get('joint_property/{bid}/choose/{poid}', 'JointPropertyController@choose_property')->name('joint_property.choose');

    Route::get('joint_property/{bid}/joint_borrower/{id}', 'JointPropertyController@joint_borrower_property_select')->name('joint_property.personal_borrower');
    Route::get('joint_property/{bid}/joint_guarantor/{id}', 'JointPropertyController@joint_guarantor_property_select')->name('joint_property.personal_select');
    Route::get('joint_property/{bid}/joint_property_owner/{id}', 'JointPropertyController@joint_property_select')->name('joint_property.personal_property_owner');
    Route::get('joint_property/{bid}/proceed', 'JointPropertyController@proceed')->name('joint_property.proceed');
//creating new property owner
    Route::get('joint_property/{bid}/joint_property', 'JointPropertyController@joint_property_create')->name('joint_property.personal_create');
    Route::post('joint_property/{bid}/joint_property_store', 'JointPropertyController@joint_property_store')->name('joint_property.personal_store');


//property create
//    land property crud
    Route::post('joint_property/land_store', 'JointPropertyController@land_store')->name('joint_property.land_store');
    Route::get('joint_property/{bid}/land_edit/{id}', 'JointPropertyController@land_edit')->name('joint_property.land_edit');
    Route::get('joint_property/{bid}/land_borrower_edit/{id}', 'JointPropertyController@land_borrower_edit')->name('joint_property.land_borrower_edit');
    Route::put('joint_property/land_update', 'JointPropertyController@land_update')->name('joint_property.land_update');
    Route::put('joint_property/land_borrower_update', 'JointPropertyController@land_borrower_update')->name('joint_property.land_borrower_update');
//    remove and assign land
    Route::delete('joint_property/{bid}/land_remove/{pid}', 'JointPropertyController@land_destroy')->name('joint_property.land_destroy');
    Route::delete('joint_property/{bid}/land_borrower_remove/{pid}', 'JointPropertyController@land_borrower_destroy')->name('joint_property.land_borrower_destroy');
    Route::get('joint_property/{bid}/land_assign/{pid}', 'JointPropertyController@land_assign')->name('joint_property.land_assign');
//front land
    Route::get('joint_property/{bid}/land_front_edit/{id}', 'JointPropertyController@land_front_edit')->name('joint_property.land_front_edit');
    Route::put('joint_property/land_front_update', 'JointPropertyController@land_front_update')->name('joint_property.land_front_update');
    Route::delete('joint_property/{bid}/land_front_remove/{pid}', 'JointPropertyController@land_front_destroy')->name('joint_property.land_front_destroy');

    //    share property crud
    Route::post('joint_property/share_store', 'JointPropertyController@share_store')->name('joint_property.share_store');
    Route::get('joint_property/{bid}/share_edit/{id}', 'JointPropertyController@share_edit')->name('joint_property.share_edit');
    Route::get('joint_property/{bid}/share_borrower_edit/{id}', 'JointPropertyController@share_borrower_edit')->name('joint_property.share_borrower_edit');
    Route::put('joint_property/share_update', 'JointPropertyController@share_update')->name('joint_property.share_update');
    Route::put('joint_property/share_borrower_update', 'JointPropertyController@share_borrower_update')->name('joint_property.share_borrower_update');
//    remove and assign share
    Route::delete('joint_property/{bid}/share_remove/{pid}', 'JointPropertyController@share_destroy')->name('joint_property.share_destroy');
    Route::delete('joint_property/{bid}/share_borrower_remove/{pid}', 'JointPropertyController@share_borrower_destroy')->name('joint_property.share_borrower_destroy');
    Route::get('joint_property/{bid}/share_assign/{pid}', 'JointPropertyController@share_assign')->name('joint_property.share_assign');
//front share
    Route::get('joint_property/{bid}/share_front_edit/{id}', 'JointPropertyController@share_front_edit')->name('joint_property.share_front_edit');
    Route::put('joint_property/share_front_update', 'JointPropertyController@share_front_update')->name('joint_property.share_front_update');
    Route::delete('joint_property/{bid}/share_front_remove/{pid}', 'JointPropertyController@share_front_destroy')->name('joint_property.share_front_destroy');


    //vehicle
    Route::post('joint_borrower/vehicle_store', 'JointPropertyController@vehicle_store')->name('joint_property.vehicle_store');
    Route::get('joint_borrower/{bid}/vehicle/{id}/edit', 'JointPropertyController@vehicle_edit')->name('joint_property.vehicle_edit');
    Route::put('joint_borrower/vehicle/{id}/update', 'JointPropertyController@vehicle_update')->name('joint_property.vehicle_update');
    Route::delete('joint_borrower/{bid}/vehicle/{id}/delete', 'JointPropertyController@vehicle_destroy')->name('joint_property.vehicle_destroy');


//    joint facilities
    Route::get('joint_borrower/{bid}/facilities/index', 'JointFacilitiesController@index')->name('joint_facilities.index');
    Route::post('joint_borrower/facilities/store', 'JointFacilitiesController@store')->name('joint_facilities.store');
    Route::get('joint_borrower/{bid}/facilities/{id}/edit', 'JointFacilitiesController@edit')->name('joint_facilities.edit');
    Route::put('joint_borrower/facilities/update/{id}', 'JointFacilitiesController@update')->name('joint_facilities.update');
    Route::delete('joint_borrower/{id}/facilities/delete', 'JointFacilitiesController@destroy')->name('joint_facilities.destroy');
    Route::get('joint_borrower/{id}/facilities/proceed', 'JointFacilitiesController@proceed')->name('joint_facilities.proceed');

//joint Loan
    Route::get('joint_borrower/{bid}/loan/index', 'JointLoanController@index')->name('joint_loan.index');
    Route::post('joint_borrower/loan/store', 'JointLoanController@store')->name('joint_loan.store');
    Route::get('joint_borrower/{bid}/loan/edit', 'JointLoanController@edit')->name('joint_loan.edit');
    Route::put('joint_borrower/loan/update/{id}', 'JointLoanController@update')->name('joint_loan.update');
    Route::delete('joint_borrower/{bid}/loan/delete', 'JointLoanController@destroy')->name('joint_loan.destroy');
    Route::get('joint_borrower/{bid}/loan/proceed', 'JointLoanController@proceed')->name('joint_loan.proceed');


    //joint review and approve
    Route::get('joint_borrower/{bid}/review/index', 'JointReviewController@index')->name('joint_review.index');

    Route::get('joint_borrower/review/{id}/borrower_edit', 'JointReviewController@borrower_edit')->name('joint_review.borrower_edit');
    Route::put('joint_borrower/{id}/review/borrower_update', 'JointReviewController@borrower_update')->name('joint_review.update');

    Route::get('joint_borrower/{bid}/review/{id}/corporate_guarantor_edit', 'JointReviewController@corporate_guarantor_edit')->name('joint_review.corporate_guarantor_edit');
    Route::put('joint_borrower/{id}/review/corporate_guarantor_update', 'JointReviewController@corporate_guarantor_update')->name('joint_review.corporate_guarantor_update');
    Route::delete('joint_borrower/{bid}/review/{id}/corporate_guarantor_delete', 'JointReviewController@corporate_guarantor_destroy')->name('joint_review.corporate_guarantor_destroy');

    Route::get('joint_borrower/review/{bid}/{id}/joint_guarantor_edit', 'JointReviewController@personal_guarantor_edit')->name('joint_review.personal_guarantor_edit');
    Route::put('joint_borrower/{id}/review/joint_guarantor_update', 'JointReviewController@personal_guarantor_update')->name('joint_review.personal_guarantor_update');
    Route::delete('joint_borrower/{bid}/review/{id}/joint_guarantor_delete', 'JointReviewController@personal_guarantor_destroy')->name('joint_review.personal_guarantor_destroy');
//joint property owner
    Route::get('joint_borrower/review/{bid}/{id}/joint_property_owner_edit', 'JointReviewController@joint_property_owner_edit')->name('joint_review.joint_property_owner_edit');
    Route::put('joint_borrower/{id}/review/joint_property_owner_update', 'JointReviewController@joint_property_owner_update')->name('joint_review.joint_property_owner_update');
    Route::delete('joint_borrower/{bid}/review/joint_property_owner_delete', 'JointReviewController@joint_property_owner_destroy')->name('joint_review.joint_property_owner_destroy');

//joint facilities
    Route::get('joint_borrower/review/{bid}/{id}/facilities_edit', 'JointReviewController@facilities_edit')->name('joint_review.facilities_edit');
    Route::put('joint_borrower/{id}/review/facilities_update', 'JointReviewController@facilities_update')->name('joint_review.facilities_update');
    Route::delete('joint_borrower/{bid}/review/{id}/facilities_delete', 'JointReviewController@facilities_destroy')->name('joint_review.facilities_destroy');

//   joint loan
    Route::get('joint_borrower/review/{bid}/{id}/loan_edit', 'JointReviewController@loan_edit')->name('joint_review.loan_edit');
    Route::put('joint_borrower/{id}/review/loan_update', 'JointReviewController@loan_update')->name('joint_review.loan_update');
    Route::get('joint_borrower/{bid}/review/proceed', 'JointReviewController@proceed')->name('joint_review.proceed');



//joint property owner
    Route::get('joint_borrower/review/{bid}/{id}/personal_property_owner_edit', 'JointReviewController@personal_property_owner_edit')->name('joint_review.personal_property_owner_edit');
    Route::put('joint_borrower/{id}/review/personal_property_owner_update', 'JointReviewController@personal_property_owner_update')->name('joint_review.personal_property_owner_update');
    Route::delete('joint_borrower/{bid}/review/{id}/personal_property_owner_delete', 'JointReviewController@personal_property_owner_destroy')->name('joint_review.personal_property_owner_destroy');
//joint land
    Route::get('joint_borrower/review/{bid}/{id}/personal_land_edit', 'JointReviewController@personal_land_edit')->name('joint_review.personal_land_edit');
    Route::put('joint_borrower/{id}/review/personal_land_update', 'JointReviewController@personal_land_update')->name('joint_review.personal_land_update');
    Route::delete('joint_borrower/{bid}/review/{id}/personal_land_delete', 'JointReviewController@personal_land_destroy')->name('joint_review.personal_land_destroy');
//joint share
    Route::get('joint_borrower/review/{bid}/{id}/personal_share_edit', 'JointReviewController@personal_share_edit')->name('joint_review.personal_share_edit');
    Route::put('joint_borrower/{id}/review/personal_share_update', 'JointReviewController@personal_share_update')->name('joint_review.personal_share_update');
    Route::delete('joint_borrower/{bid}/review/{id}/personal_share_delete', 'JointReviewController@personal_share_destroy')->name('joint_review.personal_share_destroy');

    //joint property owner
    Route::delete('joint_borrower/{bid}/review/joint_property_owner_delete', 'JointReviewController@joint_property_owner_destroy')->name('joint_review.joint_property_owner_destroy');
//joint land
    Route::get('joint_borrower/review/{bid}/{id}/joint_land_edit', 'JointReviewController@joint_land_edit')->name('joint_review.joint_land_edit');
    Route::put('joint_borrower/{id}/review/joint_land_update', 'JointReviewController@joint_land_update')->name('joint_review.joint_land_update');
    Route::delete('joint_borrower/{bid}/review/{id}/_land_delete', 'JointReviewController@joint_land_destroy')->name('joint_review.joint_land_destroy');

//joint facilities
    Route::get('joint_borrower/review/{bid}/{id}/facilities/edit', 'JointReviewController@facilities_edit')->name('joint_review.facilities_edit');
    Route::put('joint_borrower/{id}/review/facilities/update', 'JointReviewController@facilities_update')->name('joint_review.facilities_update');
    Route::delete('joint_borrower/{bid}/review/{id}/facilities/delete', 'JointReviewController@facilities_destroy')->name('joint_review.facilities_destroy');

//   joint loan
    Route::get('joint_borrower/review/{bid}/{id}/loan/edit', 'JointReviewController@loan_edit')->name('joint_review.loan_edit');
    Route::put('joint_borrower/{id}/review/loan/update', 'JointReviewController@loan_update')->name('joint_review.loan_update');

    Route::get('joint_borrower/review/{bid}/{id}/hire_purchase/edit', 'JointReviewController@hirepurchase_edit')->name('joint_review.hirepurchase_edit');
    Route::put('joint_borrower/{id}/review/hire_purchase/update', 'JointReviewController@hirepurchase_update')->name('joint_review.hirepurchase_update');
    Route::delete('joint_borrower/{bid}/review/{id}/hirepurchase/delete', 'JointReviewController@hirepurchase_destroy')->name('joint_review.hirepurchase_destroy');
    Route::get('joint_borrower/{bid}/review/proceed', 'JointReviewController@proceed')->name('joint_review.proceed');

//end review





//end joint
    Route::get('Personal_Joint_Property/create/{bid}', 'PersonalJointPropertyController@owner_create')->name('personal_property_join.owner_create');
    Route::post('Personal_Joint_Property/create/new/owner', 'PersonalJointPropertyController@newstore')->name('personal_property_join.newstore');
    Route::get('Personal_Joint_Property/{bid}/add/{poid}/owner', 'PersonalJointPropertyController@select')->name('personal_property_join.select');
    Route::delete('Personal_Joint_Property/{bid}/remove/{jid}/{poid}', 'PersonalJointPropertyController@remove')->name('personal_property_join.remove');
    Route::post('Personal_Joint_Property/land_store', 'PersonalJointPropertyController@land_store')->name('personal_property_join.land_store');

    Route::get('Documents/make new documents', 'DocumentController@make_document')->name('document.make_document');

    Route::get('reports/generate/{type}','ReportController@create')->name('report.create');

//personal document
    Route::get('personal_document/document_type/{bid}/choose', 'PersonalDocumentController@choose')->name('personal.choose');
    Route::get('personal_document/rejected/{bid}', 'PersonalDocumentController@rejected')->name('personal.rejected');
    Route::post('personal_document/{bid}/document/all_in_one', 'PersonalDocumentController@document')->name('personal.all_in_one');
    Route::get('personal_document/document_type/{bid}/approve/reject', 'PersonalDocumentController@approve_request')->name('personal.approve');
    Route::get('personal_document/document_type/{bid}/approve', 'PersonalDocumentController@approve')->name('personal.approve_select');
    Route::post('personal_document/{bid}/document/reject', 'PersonalDocumentController@reject')->name('personal.reject');
    Route::post('corporate_document/{bid}/document/reject', 'CorporateDocumentController@reject')->name('corporate.reject');

    //corporate
    Route::get('corporate_document/document_type/{bid}/choose', 'CorporateDocumentController@choose')->name('corporate.choose');
    Route::get('corporate_document/rejected/{bid}', 'CorporateDocumentController@rejected')->name('corporate.rejected');
    Route::post('corporate_document/{bid}/document/all_in_one/', 'CorporateDocumentController@document')->name('corporate.all_in_one');
    Route::get('corporate_document/document_type/{bid}/approve/reject', 'CorporateDocumentController@approve_request')->name('corporate.approve');
    Route::get('corporate_document/document_type/{bid}/approve', 'CorporateDocumentController@approve')->name('corporate.approve_select');



//    joint
    Route::get('joint_document/document_type/{bid}/choose', 'JointDocumentController@choose')->name('joint.choose');
    Route::get('joint_document/rejected/{bid}', 'JointDocumentController@rejected')->name('joint.rejected');
    Route::post('joint_document/{bid}/document/all_in_one', 'JointDocumentController@document')->name('joint.all_in_one');
    Route::get('joint_document/document_type/{bid}/approve/reject', 'JointDocumentController@approve_request')->name('joint.approve');
    Route::get('joint_document/document_type/{bid}/approve', 'JointDocumentController@approve')->name('joint.approve_select');
    Route::post('joint_document/{bid}/document/reject', 'JointDocumentController@reject')->name('joint.reject');


    //activity log
    Route::get('activity_log/{id}', 'ActivityLogController@view_log')->name('activity.view');
    Route::get('activity_log_all', 'ActivityLogController@view_all_log')->name('activity.view_all');


    Route::get('Documents/all/prepared/documents', 'DocumentController@index')->name('document.index');
    Route::get('Documents/{filename}/Download', 'DocumentController@download')->name('document.download');
    Route::delete('Documents/{filename}/delete', 'DocumentController@delete')->name('document.delete');
    Route::delete('Documents/{bid}/personal/delete', 'DocumentController@personal_delete')->name('document.personal_delete');
    Route::delete('Documents/{bid}/corporate/delete', 'DocumentController@corporate_delete')->name('document.corporate_delete');
    Route::delete('Documents/{bid}/joint/delete', 'DocumentController@joint_delete')->name('document.joint_delete');

    Route::get('Personal/Review/index', 'ReviewController@personal_index')->name('review.personal_index');
    Route::get('Corporate/Review/index', 'ReviewController@corporate_index')->name('review.corporate_index');
    Route::get('Joint/Review/index', 'ReviewController@joint_index')->name('review.joint_index');


//personal borrower joint property owner
    Route::get('Personal_Joint_Property/index/{bid}', 'PersonalJointPropertyController@index')->name('personal_property_join.index');
    Route::get('Personal_Joint_Property/create/{bid}', 'PersonalJointPropertyController@owner_create')->name('personal_property_join.owner_create');
    Route::post('Personal_Joint_Property/create/new/owner', 'PersonalJointPropertyController@newstore')->name('personal_property_join.newstore');
    Route::get('Personal_Joint_Property/{bid}/add/{poid}/owner', 'PersonalJointPropertyController@select')->name('personal_property_join.select');
    Route::delete('Personal_Joint_Property/{bid}/remove/{jid}/{poid}', 'PersonalJointPropertyController@remove')->name('personal_property_join.remove');
    Route::post('Personal_Joint_Property/land_store', 'PersonalJointPropertyController@land_store')->name('personal_property_join.land_store');
    //joint land
    Route::delete('personal_joint_property/{bid}/land_remove/{pid}', 'PersonalJointPropertyController@land_destroy')->name('personal_property_join.land_destroy');
    Route::get('personal_joint_property/{bid}/land_edit/{pid}', 'PersonalJointPropertyController@land_edit')->name('personal_property_join.land_edit');
    Route::put('personal_joint_property/land_update', 'PersonalJointPropertyController@land_update')->name('personal_property_join.land_update');


//Corporate joint property owner
    Route::get('Corporate_Joint_Property/index/{bid}', 'CorporateJointPropertyController@index')->name('corporate_property_join.index');
    Route::get('Corporate_Joint_Property/create/{bid}', 'CorporateJointPropertyController@owner_create')->name('corporate_property_join.owner_create');
    Route::post('Corporate_Joint_Property/create/new/owner', 'CorporateJointPropertyController@newstore')->name('corporate_property_join.newstore');
    Route::get('Corporate_Joint_Property/{bid}/add/{poid}/owner', 'CorporateJointPropertyController@select')->name('corporate_property_join.select');
    Route::delete('Corporate_Joint_Property/{bid}/remove/{jid}/{poid}', 'CorporateJointPropertyController@remove')->name('corporate_property_join.remove');
    Route::post('Corporate_Joint_Property/land_store', 'CorporateJointPropertyController@land_store')->name('corporate_property_join.land_store');
//joint land
    Route::delete('Corporate_Joint_Property/{bid}/land_remove/{pid}', 'CorporateJointPropertyController@land_destroy')->name('corporate_property_join.land_destroy');
    Route::get('Corporate_Joint_Property/{bid}/land_edit/{pid}', 'CorporateJointPropertyController@land_edit')->name('corporate_property_join.land_edit');
    Route::put('Corporate_Joint_Property/land_update', 'CorporateJointPropertyController@land_update')->name('corporate_property_join.land_update');


//    //joint borrower joint property owner
    Route::get('Joint_Joint_Property/index/{bid}', 'JointJointPropertyController@index')->name('joint_property_personal.index');
    Route::get('Joint_Joint_Property/create/{bid}', 'JointJointPropertyController@owner_create')->name('joint_property_personal.owner_create');
    Route::post('Joint_Joint_Property/create/new/owner', 'JointJointPropertyController@newstore')->name('joint_property_personal.newstore');
    Route::get('Joint_Joint_Property/{bid}/add/{poid}/owner', 'JointJointPropertyController@select')->name('joint_property_personal.select');
    Route::delete('Joint_Joint_Property/{bid}/remove/{jid}/{poid}', 'JointJointPropertyController@remove')->name('joint_property_personal.remove');
    Route::post('Joint_Joint_Property/land_store', 'JointJointPropertyController@land_store')->name('joint_property_personal.land_store');
    //joint land
    Route::delete('Joint_joint_property/{bid}/land_remove/{pid}', 'JointJointPropertyController@land_destroy')->name('joint_property_personal.land_destroy');
    Route::get('Joint_joint_property/{bid}/land_edit/{pid}', 'JointJointPropertyController@land_edit')->name('joint_property_personal.land_edit');
    Route::put('Joint_joint_property/land_update', 'JointJointPropertyController@land_update')->name('joint_property_personal.land_update');

}
);