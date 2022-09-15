<?php


namespace App\Http\Controllers;

use App\AuthorizedPerson;
use App\Branch;
use App\CorporateGuarantor;
use App\CorporateLand;
use App\CorporateLoan;
use App\CorporatePropertyOwner;
use App\CorporateShare;
use App\Department;
use App\District;
use App\Facility;
use App\Http\Requests\Reject;
use App\JointLand;
use App\JointLoan;
use App\JointPropertyOwner;
use App\LocalBodies;
use App\Ministry;
use App\PersonalBorrower;
use App\PersonalFacilities;
use App\PersonalGuarantor;
use App\PersonalGuarantorBorrower;
use App\PersonalHirePurchase;
use App\PersonalLand;
use App\PersonalLandBorrower;
use App\PersonalLoan;
use App\PersonalPropertyOwner;
use App\PersonalShare;
use App\PersonalShareBorrower;
use App\Province;
use App\RegisteredCompany;
use Carbon\Carbon;
use Chumper\Zipper\Facades\Zipper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;

class PersonalDocumentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function condition($bid)
    {
        $vehicle = PersonalBorrower::find($bid)->personal_hire_purchase;
        $land = PersonalBorrower::find($bid)->personal_land_borrower;
        $share = PersonalBorrower::find($bid)->personal_share_borrower;
        if ($vehicle && $land && $share) {
            return redirect()->route('personal.all_in_one', compact('bid'));
        }
    }

    private function share_fukka_third($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);
        $shareproperty = [];
        $owner = [];
        $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();
        foreach ($borrower->personalborrowerpersonalshare as $share) {
            if ($propertyOwner) {
                if ($share->property_owner_id == $propertyOwner->id) {
                } else {
                    $shareproperty[] = PersonalShare::find($share->id);
                    $owner[] = PersonalPropertyOwner::find($share->property_owner_id);
                }
            } else {
                $shareproperty[] = PersonalShare::find($share->id);
                $owner[] = PersonalPropertyOwner::find($share->property_owner_id);
            }
        }
        $shareowner = (array_unique($owner));

        foreach ($shareowner as $own) {
            //File name
            $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $ShareRokkaThird = new TemplateProcessor(storage_path('document/personal/Share Fukka Third.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $ShareRokkaThird->setValue('branch', value(Branch::find($loan->branch_id)->location));

                $ShareRokkaThird->setValue('amount', value($loan->loan_amount));
                $ShareRokkaThird->setValue('nepali_name', value($borrower->nepali_name));
//                Property Owner Details
                $ShareRokkaThird->setValue('pnepali_name', value($own->nepali_name));

                $sh = [];
                foreach ($shareproperty as $a) {
                    if ($own->id == $a->property_owner_id) {
                        $sh[] = PersonalShare::find($a->id);
                    }
                }

                $count = count($sh);
                $ShareRokkaThird->cloneRow('sharesn', $count);
                $c = 1;
                $total = 0;
                foreach ($sh as $share) {
                    $n = $c++;
                    $ShareRokkaThird->setValue('sharesn#' . $n, $n);
                    $ShareRokkaThird->setValue('ownername#' . $n, PersonalPropertyOwner::find($share->property_owner_id)->nepali_name);
                    $ShareRokkaThird->setValue('dpid#' . $n, $share->dpid);
                    $ShareRokkaThird->setValue('clientid#' . $n, $share->client_id);
                    $ShareRokkaThird->setValue('kitta#' . $n, $share->kitta);
                    $ShareRokkaThird->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                    $ShareRokkaThird->setValue('isin_name#' . $n, RegisteredCompany::find($share->isin)->name);
                    $ShareRokkaThird->setValue('share_type#' . $n, $share->share_type);
                }
                $ShareRokkaThird->setValue('share_total', value($total));

            }
            $ShareRokkaThird->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Share_Fukka_Third_' . $own->english_name . '.docx'));
        }

    }

    private function share_fukka_third_corporate($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);
        $shareproperty = [];
        $owner = [];
        foreach ($borrower->personalborrowercorporateshare as $share) {
            $shareproperty[] = CorporateShare::find($share->id);
            $owner[] = CorporatePropertyOwner::find($share->property_owner_id);
        }
        $shareowner = (array_unique($owner));

        foreach ($shareowner as $p) {
            //File name
            $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $PledgeDeedThird = new TemplateProcessor(storage_path('document/personal/Share Fukka Third.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $PledgeDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                $PledgeDeedThird->setValue('nepali_name', value($borrower->nepali_name));
//                Property Owner Details

                $PledgeDeedThird->setValue('pnepali_name', value($p->nepali_name));

                $sh = [];
                foreach ($shareproperty as $a) {
                    if ($p->id == $a->property_owner_id) {
                        $sh[] = CorporateShare::find($a->id);
                    }
                }


                $count = count($sh);

                $PledgeDeedThird->cloneRow('sharesn', $count);
                $c = 1;
                $total = 0;
                foreach ($sh as $share) {
                    $n = $c++;
                    $PledgeDeedThird->setValue('sharesn#' . $n, $n);
                    $PledgeDeedThird->setValue('ownername#' . $n, CorporatePropertyOwner::find($share->property_owner_id)->nepali_name);
                    $PledgeDeedThird->setValue('dpid#' . $n, $share->dpid);
                    $PledgeDeedThird->setValue('clientid#' . $n, $share->client_id);
                    $PledgeDeedThird->setValue('kitta#' . $n, $share->kitta);

                    $PledgeDeedThird->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                    $PledgeDeedThird->setValue('isin_name#' . $n, RegisteredCompany::find($share->isin)->name);
                    $PledgeDeedThird->setValue('share_type#' . $n, $share->share_type);
                }
                $PledgeDeedThird->setValue('share_total', value($total));
            }
            $PledgeDeedThird->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Share_Fukka_Third_Corporate_' . $p->english_name . '.docx'));

        }

    }

    private function Promissory_Note($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name
        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            //promissory note
            $templateProcessor = new TemplateProcessor(storage_path('document/personal/Promissory_Note.docx'));
            Settings::setOutputEscapingEnabled(true);
// Var  iables on different parts of document
            $templateProcessor->setValue('amount', value($loan->loan_amount));
            $templateProcessor->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $templateProcessor->setValue('words', value($loan->loan_amount_words));
            $templateProcessor->setValue('grandfather_name', value($borrower->grandfather_name));
            $templateProcessor->setValue('grandfather_relation', value($borrower->grandfather_relation));
            $templateProcessor->setValue('father_name', value($borrower->father_name));
            $templateProcessor->setValue('father_relation', value($borrower->father_relation));
            if ($borrower->spouse_name) {
                $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation . null;
                $templateProcessor->setValue('spouse_name', value($spouse_name));
            } else {
                $spouse_name = null;
                $templateProcessor->setValue('spouse_name', value($spouse_name));
            }
            $templateProcessor->setValue('district', value(District::find($borrower->district_id)->name));
            $templateProcessor->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
            $templateProcessor->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
            $templateProcessor->setValue('wardno', value($borrower->wardno));

            $templateProcessor->setValue('age', value($cyear - ($borrower->dob_year)));
            $g = $borrower->gender;
            if ($g == 1) {
                $gender = 'sf]';
                $male = 'k\"?if';
            } else {
                $gender = 'sL';
                $male = 'dlxnf';
            }
            $templateProcessor->setValue('gender', value($gender));
            $templateProcessor->setValue('nepali_name', value($borrower->nepali_name));
            $templateProcessor->setValue('citizenship_number', value($borrower->citizenship_number));
            $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
            $templateProcessor->setValue('issued_date', value($issued_date));
            $templateProcessor->setValue('issued_district', value(District::find($borrower->issued_district)->name));
            $templateProcessor->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Personal_Promissory_Note.docx'));
        }
    }

    private function loan_deed_land_share_vehicle($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name
        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            //share loan deed
            $LandShareVehicleLoanDeed = new TemplateProcessor(storage_path('document/personal/Land & Share & vehicle.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
            $LandShareVehicleLoanDeed->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $LandShareVehicleLoanDeed->setValue('offerletterdate', value($offerletterdate));
            $LandShareVehicleLoanDeed->setValue('amount', value($loan->loan_amount));
            $LandShareVehicleLoanDeed->setValue('words', value($loan->loan_amount_words));
            $LandShareVehicleLoanDeed->setValue('grandfather_name', value($borrower->grandfather_name));
            $LandShareVehicleLoanDeed->setValue('grandfather_relation', value($borrower->grandfather_relation));
            $LandShareVehicleLoanDeed->setValue('father_name', value($borrower->father_name));
            $LandShareVehicleLoanDeed->setValue('father_relation', value($borrower->father_relation));
            if ($borrower->spouse_name) {
                $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                $LandShareVehicleLoanDeed->setValue('spouse_name', value($spouse_name));
            } else {
                $spouse_name = null;
                $LandShareVehicleLoanDeed->setValue('spouse_name', value($spouse_name));
            }
            $LandShareVehicleLoanDeed->setValue('district', value(District::find($borrower->district_id)->name));
            $LandShareVehicleLoanDeed->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
            $LandShareVehicleLoanDeed->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
            $LandShareVehicleLoanDeed->setValue('wardno', value($borrower->wardno));

            $LandShareVehicleLoanDeed->setValue('age', value($cyear - ($borrower->dob_year)));
            $g = $borrower->gender;
            if ($g == 1) {
                $gender = 'sf]';
                $male = 'k\"?if';
            } else {
                $gender = 'sL';
                $male = 'dlxnf';
            }
            $LandShareVehicleLoanDeed->setValue('gender', value($gender));
            $LandShareVehicleLoanDeed->setValue('nepali_name', value($borrower->nepali_name));
            $LandShareVehicleLoanDeed->setValue('citizenship_number', value($borrower->citizenship_number));
            $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
            $LandShareVehicleLoanDeed->setValue('issued_date', value($issued_date));
            $LandShareVehicleLoanDeed->setValue('issued_district', value(District::find($borrower->issued_district)->name));

            $facility = PersonalBorrower::find($bid)->personal_facilities;
            $LandShareVehicleLoanDeed->cloneRow('facilitysn', count($facility));
            $i = 1;
            foreach ($facility as $f) {
                $n = $i++;
                $LandShareVehicleLoanDeed->setValue('facilitysn#' . $n, $n);
                $LandShareVehicleLoanDeed->setValue('facility#' . $n, Facility::find($f->facility_id)->name);
                $LandShareVehicleLoanDeed->setValue('facility_amount#' . $n, $f->amount);
                $LandShareVehicleLoanDeed->setValue('rate#' . $n, $f->rate);
                if ($f->tyear && $f->tmonth && $f->tday) {
                    $LandShareVehicleLoanDeed->setValue('time#' . $n, 'ldlt ' . $f->tyear . '÷' . $f->tmonth . '÷' . $f->tday . ' ;Dd');
                } else {
                    $LandShareVehicleLoanDeed->setValue('time#' . $n, $f->tenure);
                }
                $LandShareVehicleLoanDeed->setValue('remarks#' . $n, $f->remarks);
            }

            $joint = PersonalBorrower::find($bid)->personal_joint_land()->first();
            if ($joint) {
                $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
            } else {
                $jointp = [];
            }
            $count = count($borrower->personalborrowerpersonalland) + count($borrower->personalborrowercorporateland) + count($jointp);

            $LandShareVehicleLoanDeed->cloneRow('landsn', $count);

            $i = 1;
            foreach ($borrower->personalborrowerpersonalland as $land) {
                $n = $i++;
                $LandShareVehicleLoanDeed->setValue('landsn#' . $n, $n);
                $LandShareVehicleLoanDeed->setValue('ownername#' . $n, PersonalPropertyOwner::find($land->property_owner_id)->nepali_name);
                $LandShareVehicleLoanDeed->setValue('ldistrict#' . $n, District::find($land->district_id)->name);
                $LandShareVehicleLoanDeed->setValue('llocalbody#' . $n, LocalBodies::find($land->local_bodies_id)->name);
                $LandShareVehicleLoanDeed->setValue('lwardno#' . $n, $land->wardno);
                $LandShareVehicleLoanDeed->setValue('sheetno#' . $n, $land->sheet_no);
                $LandShareVehicleLoanDeed->setValue('kittano#' . $n, $land->kitta_no);
                $LandShareVehicleLoanDeed->setValue('area#' . $n, $land->area);
                $LandShareVehicleLoanDeed->setValue('remarks#' . $n, $land->remarks);
            }
            foreach ($borrower->personalborrowercorporateland as $land) {
                $n = $i++;
                $LandShareVehicleLoanDeed->setValue('landsn#' . $n, $n);
                $LandShareVehicleLoanDeed->setValue('ownername#' . $n, CorporatePropertyOwner::find($land->property_owner_id)->nepali_name);
                $LandShareVehicleLoanDeed->setValue('ldistrict#' . $n, District::find($land->district_id)->name);
                $LandShareVehicleLoanDeed->setValue('llocalbody#' . $n, LocalBodies::find($land->local_bodies_id)->name);
                $LandShareVehicleLoanDeed->setValue('lwardno#' . $n, $land->wardno);
                $LandShareVehicleLoanDeed->setValue('sheetno#' . $n, $land->sheet_no);
                $LandShareVehicleLoanDeed->setValue('kittano#' . $n, $land->kitta_no);
                $LandShareVehicleLoanDeed->setValue('area#' . $n, $land->area);
                $LandShareVehicleLoanDeed->setValue('remarks#' . $n, $land->remarks);
            }

            foreach ($jointp as $land) {
                $n = $i++;
                $LandShareVehicleLoanDeed->setValue('landsn#' . $n, $n);

                $j1 = PersonalPropertyOwner::find(JointPropertyOwner::find($land->joint_id)->joint1);
                $j2 = PersonalPropertyOwner::find(JointPropertyOwner::find($land->joint_id)->joint2);
                $j3 = PersonalPropertyOwner::find(JointPropertyOwner::find($land->joint_id)->joint3);
                if ($j1 && $j2) {
                    $LandShareVehicleLoanDeed->setValue('ownername#' . $n, $j1->nepali_name . ' / ' . $j2->nepali_name);
                } elseif ($j2 && $j3) {
                    $LandShareVehicleLoanDeed->setValue('ownername#' . $n, $j2->nepali_name . ' / ' . $j3->nepali_name);
                } elseif ($j1 && $j3) {
                    $LandShareVehicleLoanDeed->setValue('ownername#' . $n, $j1->nepali_name . ' / ' . $j3->nepali_name);
                } elseif ($j1 && $j2 && $j3) {
                    $LandShareVehicleLoanDeed->setValue('ownername#' . $n, $j1->nepali_name . ', ' . $j2->nepali_name . ' / ' . $j3->nepali_name);
                }
                $LandShareVehicleLoanDeed->setValue('ldistrict#' . $n, District::find($land->district_id)->name);
                $LandShareVehicleLoanDeed->setValue('llocalbody#' . $n, LocalBodies::find($land->local_bodies_id)->name);
                $LandShareVehicleLoanDeed->setValue('lwardno#' . $n, $land->wardno);
                $LandShareVehicleLoanDeed->setValue('sheetno#' . $n, $land->sheet_no);
                $LandShareVehicleLoanDeed->setValue('kittano#' . $n, $land->kitta_no);
                $LandShareVehicleLoanDeed->setValue('area#' . $n, $land->area);
                $LandShareVehicleLoanDeed->setValue('remarks#' . $n, $land->remarks);
            }


            $count = count($borrower->personalborrowerpersonalshare) + count($borrower->personalborrowercorporateshare);

            $LandShareVehicleLoanDeed->cloneRow('sharesn', $count);
            $i = 1;
            foreach ($borrower->personalborrowerpersonalshare as $share) {
                $n = $i++;
                $LandShareVehicleLoanDeed->setValue('sharesn#' . $n, $n);
                $LandShareVehicleLoanDeed->setValue('ownername#' . $n, PersonalPropertyOwner::find($share->property_owner_id)->nepali_name);
                $LandShareVehicleLoanDeed->setValue('dpid#' . $n, $share->dpid);
                $LandShareVehicleLoanDeed->setValue('clientid#' . $n, $share->client_id);
                $LandShareVehicleLoanDeed->setValue('kitta#' . $n, $share->kitta);
                $LandShareVehicleLoanDeed->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                $LandShareVehicleLoanDeed->setValue('share_type#' . $n, $share->share_type);
            }
            foreach ($borrower->personalborrowercorporateshare as $share) {
                $n = $i++;
                $LandShareVehicleLoanDeed->setValue('sharesn#' . $n, $n);
                $LandShareVehicleLoanDeed->setValue('ownername#' . $n, CorporatePropertyOwner::find($share->property_owner_id)->nepali_name);
                $LandShareVehicleLoanDeed->setValue('dpid#' . $n, $share->dpid);
                $LandShareVehicleLoanDeed->setValue('clientid#' . $n, $share->client_id);
                $LandShareVehicleLoanDeed->setValue('kitta#' . $n, $share->kitta);
                $LandShareVehicleLoanDeed->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                $LandShareVehicleLoanDeed->setValue('share_type#' . $n, $share->share_type);
            }
            $count = count(PersonalBorrower::find($bid)->personal_hire_purchase);
            $LandShareVehicleLoanDeed->cloneRow('vehiclesn', $count);

            $i = 1;
            foreach (PersonalBorrower::find($bid)->personal_hire_purchase as $vehicle) {
                $n = $i++;
                $LandShareVehicleLoanDeed->setValue('vehiclesn#' . $n, $n);
                $LandShareVehicleLoanDeed->setValue('model_number#' . $n, $vehicle->model_number);
                $LandShareVehicleLoanDeed->setValue('registration_number#' . $n, $vehicle->registration_number);
                $LandShareVehicleLoanDeed->setValue('engine_number#' . $n, $vehicle->engine_number);
                $LandShareVehicleLoanDeed->setValue('chassis_number#' . $n, $vehicle->chassis_number);

            }
        }
        $LandShareVehicleLoanDeed->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Personal_Loan_Deed.docx'));
    }

    private function loan_deed_land_share($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name
        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            //share loan deed
            $LandandShareLoanDeed = new TemplateProcessor(storage_path('document/personal/Land & Share.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
            $LandandShareLoanDeed->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $LandandShareLoanDeed->setValue('offerletterdate', value($offerletterdate));
            $LandandShareLoanDeed->setValue('amount', value($loan->loan_amount));
            $LandandShareLoanDeed->setValue('words', value($loan->loan_amount_words));
            $LandandShareLoanDeed->setValue('grandfather_name', value($borrower->grandfather_name));
            $LandandShareLoanDeed->setValue('grandfather_relation', value($borrower->grandfather_relation));
            $LandandShareLoanDeed->setValue('father_name', value($borrower->father_name));
            $LandandShareLoanDeed->setValue('father_relation', value($borrower->father_relation));
            if ($borrower->spouse_name) {
                $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                $LandandShareLoanDeed->setValue('spouse_name', value($spouse_name));
            } else {
                $spouse_name = null;
                $LandandShareLoanDeed->setValue('spouse_name', value($spouse_name));
            }
            $LandandShareLoanDeed->setValue('district', value(District::find($borrower->district_id)->name));
            $LandandShareLoanDeed->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
            $LandandShareLoanDeed->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
            $LandandShareLoanDeed->setValue('wardno', value($borrower->wardno));

            $LandandShareLoanDeed->setValue('age', value($cyear - ($borrower->dob_year)));
            $g = $borrower->gender;
            if ($g == 1) {
                $gender = 'sf]';
                $male = 'k\"?if';
            } else {
                $gender = 'sL';
                $male = 'dlxnf';
            }
            $LandandShareLoanDeed->setValue('gender', value($gender));
            $LandandShareLoanDeed->setValue('nepali_name', value($borrower->nepali_name));
            $LandandShareLoanDeed->setValue('citizenship_number', value($borrower->citizenship_number));
            $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
            $LandandShareLoanDeed->setValue('issued_date', value($issued_date));
            $LandandShareLoanDeed->setValue('issued_district', value(District::find($borrower->issued_district)->name));

            $facility = PersonalBorrower::find($bid)->personal_facilities;
            $LandandShareLoanDeed->cloneRow('facilitysn', count($facility));
            $i = 1;
            foreach ($facility as $f) {
                $n = $i++;
                $LandandShareLoanDeed->setValue('facilitysn#' . $n, $n);
                $LandandShareLoanDeed->setValue('facility#' . $n, Facility::find($f->facility_id)->name);
                $LandandShareLoanDeed->setValue('facility_amount#' . $n, $f->amount);
                $LandandShareLoanDeed->setValue('rate#' . $n, $f->rate);
                if ($f->tyear && $f->tmonth && $f->tday) {
                    $LandandShareLoanDeed->setValue('time#' . $n, 'ldlt ' . $f->tyear . '÷' . $f->tmonth . '÷' . $f->tday . ' ;Dd');
                } else {
                    $LandandShareLoanDeed->setValue('time#' . $n, $f->tenure);
                }
                $LandandShareLoanDeed->setValue('remarks#' . $n, $f->remarks);
            }
            $joint = PersonalBorrower::find($bid)->personal_joint_land()->first();
            if ($joint) {
                $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
            } else {
                $jointp = [];
            }
            $count = count($borrower->personalborrowerpersonalland) + count($borrower->personalborrowercorporateland) + count($jointp);
            $LandandShareLoanDeed->cloneRow('landsn', $count);

            $i = 1;
            foreach ($borrower->personalborrowerpersonalland as $land) {
                $n = $i++;
                $LandandShareLoanDeed->setValue('landsn#' . $n, $n);
                $LandandShareLoanDeed->setValue('ownername#' . $n, PersonalPropertyOwner::find($land->property_owner_id)->nepali_name);
                $LandandShareLoanDeed->setValue('ldistrict#' . $n, District::find($land->district_id)->name);
                $LandandShareLoanDeed->setValue('llocalbody#' . $n, LocalBodies::find($land->local_bodies_id)->name);
                $LandandShareLoanDeed->setValue('lwardno#' . $n, $land->wardno);
                $LandandShareLoanDeed->setValue('sheetno#' . $n, $land->sheet_no);
                $LandandShareLoanDeed->setValue('kittano#' . $n, $land->kitta_no);
                $LandandShareLoanDeed->setValue('area#' . $n, $land->area);
                $LandandShareLoanDeed->setValue('remarks#' . $n, $land->remarks);
            }
            foreach ($borrower->personalborrowercorporateland as $land) {
                $n = $i++;
                $LandandShareLoanDeed->setValue('landsn#' . $n, $n);
                $LandandShareLoanDeed->setValue('ownername#' . $n, CorporatePropertyOwner::find($land->property_owner_id)->nepali_name);
                $LandandShareLoanDeed->setValue('ldistrict#' . $n, District::find($land->district_id)->name);
                $LandandShareLoanDeed->setValue('llocalbody#' . $n, LocalBodies::find($land->local_bodies_id)->name);
                $LandandShareLoanDeed->setValue('lwardno#' . $n, $land->wardno);
                $LandandShareLoanDeed->setValue('sheetno#' . $n, $land->sheet_no);
                $LandandShareLoanDeed->setValue('kittano#' . $n, $land->kitta_no);
                $LandandShareLoanDeed->setValue('area#' . $n, $land->area);
                $LandandShareLoanDeed->setValue('remarks#' . $n, $land->remarks);
            }


            foreach ($jointp as $land) {
                $n = $i++;
                $LandandShareLoanDeed->setValue('landsn#' . $n, $n);
                $j1 = PersonalPropertyOwner::find(JointPropertyOwner::find($land->joint_id)->joint1);
                $j2 = PersonalPropertyOwner::find(JointPropertyOwner::find($land->joint_id)->joint2);
                $j3 = PersonalPropertyOwner::find(JointPropertyOwner::find($land->joint_id)->joint3);
                if ($j1 && $j2) {

                    $LandandShareLoanDeed->setValue('ownername#' . $n, $j1->nepali_name . ' / ' . $j2->nepali_name);
                } elseif ($j2 && $j3) {
                    $LandandShareLoanDeed->setValue('ownername#' . $n, $j2->nepali_name . ' / ' . $j3->nepali_name);

                } elseif ($j1 && $j3) {

                    $LandandShareLoanDeed->setValue('ownername#' . $n, $j1->nepali_name . ' / ' . $j3->nepali_name);

                } elseif ($j1 && $j2 && $j3) {
                    $LandandShareLoanDeed->setValue('ownername#' . $n, $j1->nepali_name . ', ' . $j2->nepali_name . ' / ' . $j3->nepali_name);

                }


                $LandandShareLoanDeed->setValue('ldistrict#' . $n, District::find($land->district_id)->name);
                $LandandShareLoanDeed->setValue('llocalbody#' . $n, LocalBodies::find($land->local_bodies_id)->name);
                $LandandShareLoanDeed->setValue('lwardno#' . $n, $land->wardno);
                $LandandShareLoanDeed->setValue('sheetno#' . $n, $land->sheet_no);
                $LandandShareLoanDeed->setValue('kittano#' . $n, $land->kitta_no);
                $LandandShareLoanDeed->setValue('area#' . $n, $land->area);
                $LandandShareLoanDeed->setValue('remarks#' . $n, $land->remarks);
            }


            $count = count($borrower->personalborrowerpersonalshare) + count($borrower->personalborrowercorporateshare);
            $LandandShareLoanDeed->cloneRow('sharesn', $count);
            $i = 1;
            foreach ($borrower->personalborrowerpersonalshare as $share) {
                $n = $i++;
                $LandandShareLoanDeed->setValue('sharesn#' . $n, $n);
                $LandandShareLoanDeed->setValue('ownername#' . $n, PersonalPropertyOwner::find($share->property_owner_id)->nepali_name);
                $LandandShareLoanDeed->setValue('dpid#' . $n, $share->dpid);
                $LandandShareLoanDeed->setValue('clientid#' . $n, $share->client_id);
                $LandandShareLoanDeed->setValue('kitta#' . $n, $share->kitta);
                $LandandShareLoanDeed->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                $LandandShareLoanDeed->setValue('share_type#' . $n, $share->share_type);
            }
            foreach ($borrower->personalborrowercorporateshare as $share) {
                $n = $i++;
                $LandandShareLoanDeed->setValue('sharesn#' . $n, $n);
                $LandandShareLoanDeed->setValue('ownername#' . $n, CorporatePropertyOwner::find($share->property_owner_id)->nepali_name);
                $LandandShareLoanDeed->setValue('dpid#' . $n, $share->dpid);
                $LandandShareLoanDeed->setValue('clientid#' . $n, $share->client_id);
                $LandandShareLoanDeed->setValue('kitta#' . $n, $share->kitta);
                $LandandShareLoanDeed->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                $LandandShareLoanDeed->setValue('share_type#' . $n, $share->share_type);
            }


        }
        $LandandShareLoanDeed->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Personal_Loan_Deed.docx'));

    }

    private function loan_deed_land_vehicle($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name
        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            //share loan deed
            $LandvehicleLoanDeed = new TemplateProcessor(storage_path('document/personal/Land & vehicle.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
            $LandvehicleLoanDeed->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $LandvehicleLoanDeed->setValue('offerletterdate', value($offerletterdate));
            $LandvehicleLoanDeed->setValue('amount', value($loan->loan_amount));
            $LandvehicleLoanDeed->setValue('words', value($loan->loan_amount_words));
            $LandvehicleLoanDeed->setValue('grandfather_name', value($borrower->grandfather_name));
            $LandvehicleLoanDeed->setValue('grandfather_relation', value($borrower->grandfather_relation));
            $LandvehicleLoanDeed->setValue('father_name', value($borrower->father_name));
            $LandvehicleLoanDeed->setValue('father_relation', value($borrower->father_relation));
            if ($borrower->spouse_name) {
                $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                $LandvehicleLoanDeed->setValue('spouse_name', value($spouse_name));
            } else {
                $spouse_name = null;
                $LandvehicleLoanDeed->setValue('spouse_name', value($spouse_name));
            }
            $LandvehicleLoanDeed->setValue('district', value(District::find($borrower->district_id)->name));
            $LandvehicleLoanDeed->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
            $LandvehicleLoanDeed->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
            $LandvehicleLoanDeed->setValue('wardno', value($borrower->wardno));

            $LandvehicleLoanDeed->setValue('age', value($cyear - ($borrower->dob_year)));
            $g = $borrower->gender;
            if ($g == 1) {
                $gender = 'sf]';
                $male = 'k\"?if';
            } else {
                $gender = 'sL';
                $male = 'dlxnf';
            }
            $LandvehicleLoanDeed->setValue('gender', value($gender));
            $LandvehicleLoanDeed->setValue('nepali_name', value($borrower->nepali_name));
            $LandvehicleLoanDeed->setValue('citizenship_number', value($borrower->citizenship_number));
            $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
            $LandvehicleLoanDeed->setValue('issued_date', value($issued_date));
            $LandvehicleLoanDeed->setValue('issued_district', value(District::find($borrower->issued_district)->name));

            $facility = PersonalBorrower::find($bid)->personal_facilities;
            $LandvehicleLoanDeed->cloneRow('facilitysn', count($facility));
            $i = 1;
            foreach ($facility as $f) {
                $n = $i++;
                $LandvehicleLoanDeed->setValue('facilitysn#' . $n, $n);
                $LandvehicleLoanDeed->setValue('facility#' . $n, Facility::find($f->facility_id)->name);
                $LandvehicleLoanDeed->setValue('facility_amount#' . $n, $f->amount);
                $LandvehicleLoanDeed->setValue('rate#' . $n, $f->rate);
                if ($f->tyear && $f->tmonth && $f->tday) {
                    $LandvehicleLoanDeed->setValue('time#' . $n, 'ldlt ' . $f->tyear . '÷' . $f->tmonth . '÷' . $f->tday . ' ;Dd');
                } else {
                    $LandvehicleLoanDeed->setValue('time#' . $n, $f->tenure);
                }
                $LandvehicleLoanDeed->setValue('remarks#' . $n, $f->remarks);
            }
            $joint = PersonalBorrower::find($bid)->personal_joint_land()->first();
            if ($joint) {
                $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
            } else {
                $jointp = [];
            }
            $count = count($borrower->personalborrowerpersonalland) + count($borrower->personalborrowercorporateland) + count($jointp);
            $LandvehicleLoanDeed->cloneRow('landsn', $count);
            $i = 1;
            foreach ($borrower->personalborrowerpersonalland as $land) {
                $n = $i++;
                $LandvehicleLoanDeed->setValue('landsn#' . $n, $n);
                $LandvehicleLoanDeed->setValue('ownername#' . $n, PersonalPropertyOwner::find($land->property_owner_id)->nepali_name);
                $LandvehicleLoanDeed->setValue('ldistrict#' . $n, District::find($land->district_id)->name);
                $LandvehicleLoanDeed->setValue('llocalbody#' . $n, LocalBodies::find($land->local_bodies_id)->name);
                $LandvehicleLoanDeed->setValue('lwardno#' . $n, $land->wardno);
                $LandvehicleLoanDeed->setValue('sheetno#' . $n, $land->sheet_no);
                $LandvehicleLoanDeed->setValue('kittano#' . $n, $land->kitta_no);
                $LandvehicleLoanDeed->setValue('area#' . $n, $land->area);
                $LandvehicleLoanDeed->setValue('remarks#' . $n, $land->remarks);
            }
            foreach ($borrower->personalborrowercorporateland as $land) {
                $n = $i++;
                $LandvehicleLoanDeed->setValue('landsn#' . $n, $n);
                $LandvehicleLoanDeed->setValue('ownername#' . $n, CorporatePropertyOwner::find($land->property_owner_id)->nepali_name);
                $LandvehicleLoanDeed->setValue('ldistrict#' . $n, District::find($land->district_id)->name);
                $LandvehicleLoanDeed->setValue('llocalbody#' . $n, LocalBodies::find($land->local_bodies_id)->name);
                $LandvehicleLoanDeed->setValue('lwardno#' . $n, $land->wardno);
                $LandvehicleLoanDeed->setValue('sheetno#' . $n, $land->sheet_no);
                $LandvehicleLoanDeed->setValue('kittano#' . $n, $land->kitta_no);
                $LandvehicleLoanDeed->setValue('area#' . $n, $land->area);
                $LandvehicleLoanDeed->setValue('remarks#' . $n, $land->remarks);
            }
            foreach ($jointp as $land) {
                $n = $i++;
                $LandvehicleLoanDeed->setValue('landsn#' . $n, $n);
                $j1 = PersonalPropertyOwner::find(JointPropertyOwner::find($land->joint_id)->joint1);
                $j2 = PersonalPropertyOwner::find(JointPropertyOwner::find($land->joint_id)->joint2);
                $j3 = PersonalPropertyOwner::find(JointPropertyOwner::find($land->joint_id)->joint3);
                if ($j1 && $j2) {

                    $LandvehicleLoanDeed->setValue('ownername#' . $n, $j1->nepali_name . ' / ' . $j2->nepali_name);
                } elseif ($j2 && $j3) {
                    $LandvehicleLoanDeed->setValue('ownername#' . $n, $j2->nepali_name . ' / ' . $j3->nepali_name);

                } elseif ($j1 && $j3) {

                    $LandvehicleLoanDeed->setValue('ownername#' . $n, $j1->nepali_name . ' / ' . $j3->nepali_name);

                } elseif ($j1 && $j2 && $j3) {
                    $LandvehicleLoanDeed->setValue('ownername#' . $n, $j1->nepali_name . ', ' . $j2->nepali_name . ' / ' . $j3->nepali_name);

                }

                $LandvehicleLoanDeed->setValue('ldistrict#' . $n, District::find($land->district_id)->name);
                $LandvehicleLoanDeed->setValue('llocalbody#' . $n, LocalBodies::find($land->local_bodies_id)->name);
                $LandvehicleLoanDeed->setValue('lwardno#' . $n, $land->wardno);
                $LandvehicleLoanDeed->setValue('sheetno#' . $n, $land->sheet_no);
                $LandvehicleLoanDeed->setValue('kittano#' . $n, $land->kitta_no);
                $LandvehicleLoanDeed->setValue('area#' . $n, $land->area);
                $LandvehicleLoanDeed->setValue('remarks#' . $n, $land->remarks);
            }


            $count = count(PersonalBorrower::find($bid)->personal_hire_purchase);
            $LandvehicleLoanDeed->cloneRow('vehiclesn', $count);

            $i = 1;
            foreach (PersonalBorrower::find($bid)->personal_hire_purchase as $vehicle) {
                $n = $i++;
                $LandvehicleLoanDeed->setValue('vehiclesn#' . $n, $n);
                $LandvehicleLoanDeed->setValue('model_number#' . $n, $vehicle->model_number);
                $LandvehicleLoanDeed->setValue('registration_number#' . $n, $vehicle->registration_number);
                $LandvehicleLoanDeed->setValue('engine_number#' . $n, $vehicle->engine_number);
                $LandvehicleLoanDeed->setValue('chassis_number#' . $n, $vehicle->chassis_number);

            }


        }
        $LandvehicleLoanDeed->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Personal_Loan_Deed.docx'));

    }

    private function loan_deed_share_vehicle($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name
        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            //share loan deed
            $ShareVehicleLoanDeed = new TemplateProcessor(storage_path('document/personal/Share & Vehicle.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
            $ShareVehicleLoanDeed->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $ShareVehicleLoanDeed->setValue('offerletterdate', value($offerletterdate));
            $ShareVehicleLoanDeed->setValue('amount', value($loan->loan_amount));
            $ShareVehicleLoanDeed->setValue('words', value($loan->loan_amount_words));
            $ShareVehicleLoanDeed->setValue('grandfather_name', value($borrower->grandfather_name));
            $ShareVehicleLoanDeed->setValue('grandfather_relation', value($borrower->grandfather_relation));
            $ShareVehicleLoanDeed->setValue('father_name', value($borrower->father_name));
            $ShareVehicleLoanDeed->setValue('father_relation', value($borrower->father_relation));
            if ($borrower->spouse_name) {
                $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                $ShareVehicleLoanDeed->setValue('spouse_name', value($spouse_name));
            } else {
                $spouse_name = null;
                $ShareVehicleLoanDeed->setValue('spouse_name', value($spouse_name));
            }
            $ShareVehicleLoanDeed->setValue('district', value(District::find($borrower->district_id)->name));
            $ShareVehicleLoanDeed->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
            $ShareVehicleLoanDeed->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
            $ShareVehicleLoanDeed->setValue('wardno', value($borrower->wardno));

            $ShareVehicleLoanDeed->setValue('age', value($cyear - ($borrower->dob_year)));
            $g = $borrower->gender;
            if ($g == 1) {
                $gender = 'sf]';
                $male = 'k\"?if';
            } else {
                $gender = 'sL';
                $male = 'dlxnf';
            }
            $ShareVehicleLoanDeed->setValue('gender', value($gender));
            $ShareVehicleLoanDeed->setValue('nepali_name', value($borrower->nepali_name));
            $ShareVehicleLoanDeed->setValue('citizenship_number', value($borrower->citizenship_number));
            $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
            $ShareVehicleLoanDeed->setValue('issued_date', value($issued_date));
            $ShareVehicleLoanDeed->setValue('issued_district', value(District::find($borrower->issued_district)->name));

            $facility = PersonalBorrower::find($bid)->personal_facilities;
            $ShareVehicleLoanDeed->cloneRow('facilitysn', count($facility));
            $i = 1;
            foreach ($facility as $f) {
                $n = $i++;
                $ShareVehicleLoanDeed->setValue('facilitysn#' . $n, $n);
                $ShareVehicleLoanDeed->setValue('facility#' . $n, Facility::find($f->facility_id)->name);
                $ShareVehicleLoanDeed->setValue('facility_amount#' . $n, $f->amount);
                $ShareVehicleLoanDeed->setValue('rate#' . $n, $f->rate);
                if ($f->tyear && $f->tmonth && $f->tday) {
                    $ShareVehicleLoanDeed->setValue('time#' . $n, 'ldlt ' . $f->tyear . '÷' . $f->tmonth . '÷' . $f->tday . ' ;Dd');
                } else {
                    $ShareVehicleLoanDeed->setValue('time#' . $n, $f->tenure);
                }
                $ShareVehicleLoanDeed->setValue('remarks#' . $n, $f->remarks);
            }

            $count = count($borrower->personalborrowerpersonalshare) + count($borrower->personalborrowercorporateshare);

            $ShareVehicleLoanDeed->cloneRow('sharesn', $count);
            $i = 1;
            foreach ($borrower->personalborrowerpersonalshare as $share) {
                $n = $i++;
                $ShareVehicleLoanDeed->setValue('sharesn#' . $n, $n);
                $ShareVehicleLoanDeed->setValue('ownername#' . $n, PersonalPropertyOwner::find($share->property_owner_id)->nepali_name);
                $ShareVehicleLoanDeed->setValue('dpid#' . $n, $share->dpid);
                $ShareVehicleLoanDeed->setValue('clientid#' . $n, $share->client_id);
                $ShareVehicleLoanDeed->setValue('kitta#' . $n, $share->kitta);
                $ShareVehicleLoanDeed->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                $ShareVehicleLoanDeed->setValue('share_type#' . $n, $share->share_type);
            }
            foreach ($borrower->personalborrowercorporateshare as $share) {
                $n = $i++;
                $ShareVehicleLoanDeed->setValue('sharesn#' . $n, $n);
                $ShareVehicleLoanDeed->setValue('ownername#' . $n, CorporatePropertyOwner::find($share->property_owner_id)->nepali_name);
                $ShareVehicleLoanDeed->setValue('dpid#' . $n, $share->dpid);
                $ShareVehicleLoanDeed->setValue('clientid#' . $n, $share->client_id);
                $ShareVehicleLoanDeed->setValue('kitta#' . $n, $share->kitta);
                $ShareVehicleLoanDeed->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                $ShareVehicleLoanDeed->setValue('share_type#' . $n, $share->share_type);
            }


            $count = count(PersonalBorrower::find($bid)->personal_hire_purchase);
            $ShareVehicleLoanDeed->cloneRow('vehiclesn', $count);

            $i = 1;
            foreach (PersonalBorrower::find($bid)->personal_hire_purchase as $vehicle) {
                $n = $i++;
                $ShareVehicleLoanDeed->setValue('vehiclesn#' . $n, $n);
                $ShareVehicleLoanDeed->setValue('model_number#' . $n, $vehicle->model_number);
                $ShareVehicleLoanDeed->setValue('registration_number#' . $n, $vehicle->registration_number);
                $ShareVehicleLoanDeed->setValue('engine_number#' . $n, $vehicle->engine_number);
                $ShareVehicleLoanDeed->setValue('chassis_number#' . $n, $vehicle->chassis_number);

            }


        }
        $ShareVehicleLoanDeed->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Personal_Loan_Deed.docx'));

    }

    private function loan_deed_share_only($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name
        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            //share loan deed
            $ShareOnlyLoanDeed = new TemplateProcessor(storage_path('document/personal/Share_Only.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
            $ShareOnlyLoanDeed->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $ShareOnlyLoanDeed->setValue('offerletterdate', value($offerletterdate));
            $ShareOnlyLoanDeed->setValue('amount', value($loan->loan_amount));
            $ShareOnlyLoanDeed->setValue('words', value($loan->loan_amount_words));
            $ShareOnlyLoanDeed->setValue('grandfather_name', value($borrower->grandfather_name));
            $ShareOnlyLoanDeed->setValue('grandfather_relation', value($borrower->grandfather_relation));
            $ShareOnlyLoanDeed->setValue('father_name', value($borrower->father_name));
            $ShareOnlyLoanDeed->setValue('father_relation', value($borrower->father_relation));
            if ($borrower->spouse_name) {
                $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                $ShareOnlyLoanDeed->setValue('spouse_name', value($spouse_name));
            } else {
                $spouse_name = null;
                $ShareOnlyLoanDeed->setValue('spouse_name', value($spouse_name));
            }
            $ShareOnlyLoanDeed->setValue('district', value(District::find($borrower->district_id)->name));
            $ShareOnlyLoanDeed->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
            $ShareOnlyLoanDeed->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
            $ShareOnlyLoanDeed->setValue('wardno', value($borrower->wardno));

            $ShareOnlyLoanDeed->setValue('age', value($cyear - ($borrower->dob_year)));
            $g = $borrower->gender;
            if ($g == 1) {
                $gender = 'sf]';
                $male = 'k\"?if';
            } else {
                $gender = 'sL';
                $male = 'dlxnf';
            }
            $ShareOnlyLoanDeed->setValue('gender', value($gender));
            $ShareOnlyLoanDeed->setValue('nepali_name', value($borrower->nepali_name));
            $ShareOnlyLoanDeed->setValue('citizenship_number', value($borrower->citizenship_number));
            $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
            $ShareOnlyLoanDeed->setValue('issued_date', value($issued_date));
            $ShareOnlyLoanDeed->setValue('issued_district', value(District::find($borrower->issued_district)->name));

            $facility = PersonalBorrower::find($bid)->personal_facilities;
            $ShareOnlyLoanDeed->cloneRow('facilitysn', count($facility));
            $i = 1;
            foreach ($facility as $f) {
                $n = $i++;
                $ShareOnlyLoanDeed->setValue('facilitysn#' . $n, $n);
                $ShareOnlyLoanDeed->setValue('facility#' . $n, Facility::find($f->facility_id)->name);
                $ShareOnlyLoanDeed->setValue('facility_amount#' . $n, $f->amount);
                $ShareOnlyLoanDeed->setValue('rate#' . $n, $f->rate);
                if ($f->tyear && $f->tmonth && $f->tday) {
                    $ShareOnlyLoanDeed->setValue('time#' . $n, 'ldlt ' . $f->tyear . '÷' . $f->tmonth . '÷' . $f->tday . ' ;Dd');
                } else {
                    $ShareOnlyLoanDeed->setValue('time#' . $n, $f->tenure);
                }
                $ShareOnlyLoanDeed->setValue('remarks#' . $n, $f->remarks);
            }

            $count = count($borrower->personalborrowerpersonalshare) + count($borrower->personalborrowercorporateshare);
            $ShareOnlyLoanDeed->cloneRow('sharesn', $count);
            $i = 1;
            foreach ($borrower->personalborrowerpersonalshare as $share) {
                $n = $i++;
                $ShareOnlyLoanDeed->setValue('sharesn#' . $n, $n);
                $ShareOnlyLoanDeed->setValue('ownername#' . $n, PersonalPropertyOwner::find($share->property_owner_id)->nepali_name);
                $ShareOnlyLoanDeed->setValue('dpid#' . $n, $share->dpid);
                $ShareOnlyLoanDeed->setValue('clientid#' . $n, $share->client_id);
                $ShareOnlyLoanDeed->setValue('kitta#' . $n, $share->kitta);
                $ShareOnlyLoanDeed->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                $ShareOnlyLoanDeed->setValue('share_type#' . $n, $share->share_type);
            }
            foreach ($borrower->personalborrowercorporateshare as $share) {
                $n = $i++;
                $ShareOnlyLoanDeed->setValue('sharesn#' . $n, $n);
                $ShareOnlyLoanDeed->setValue('ownername#' . $n, CorporatePropertyOwner::find($share->property_owner_id)->nepali_name);
                $ShareOnlyLoanDeed->setValue('dpid#' . $n, $share->dpid);
                $ShareOnlyLoanDeed->setValue('clientid#' . $n, $share->client_id);
                $ShareOnlyLoanDeed->setValue('kitta#' . $n, $share->kitta);
                $ShareOnlyLoanDeed->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                $ShareOnlyLoanDeed->setValue('share_type#' . $n, $share->share_type);
            }

        }
        $ShareOnlyLoanDeed->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Personal_Loan_Deed.docx'));

    }

    private function loan_deed_vehicle_only($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name
        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            //share loan deed
            $VehicleOnlyLoanDeed = new TemplateProcessor(storage_path('document/personal/Vehicle_Only.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
            $VehicleOnlyLoanDeed->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $VehicleOnlyLoanDeed->setValue('offerletterdate', value($offerletterdate));
            $VehicleOnlyLoanDeed->setValue('amount', value($loan->loan_amount));
            $VehicleOnlyLoanDeed->setValue('words', value($loan->loan_amount_words));
            $VehicleOnlyLoanDeed->setValue('grandfather_name', value($borrower->grandfather_name));
            $VehicleOnlyLoanDeed->setValue('grandfather_relation', value($borrower->grandfather_relation));
            $VehicleOnlyLoanDeed->setValue('father_name', value($borrower->father_name));
            $VehicleOnlyLoanDeed->setValue('father_relation', value($borrower->father_relation));
            if ($borrower->spouse_name) {
                $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                $VehicleOnlyLoanDeed->setValue('spouse_name', value($spouse_name));
            } else {
                $spouse_name = null;
                $VehicleOnlyLoanDeed->setValue('spouse_name', value($spouse_name));
            }
            $VehicleOnlyLoanDeed->setValue('district', value(District::find($borrower->district_id)->name));
            $VehicleOnlyLoanDeed->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
            $VehicleOnlyLoanDeed->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
            $VehicleOnlyLoanDeed->setValue('wardno', value($borrower->wardno));

            $VehicleOnlyLoanDeed->setValue('age', value($cyear - ($borrower->dob_year)));
            $g = $borrower->gender;
            if ($g == 1) {
                $gender = 'sf]';
                $male = 'k\"?if';
            } else {
                $gender = 'sL';
                $male = 'dlxnf';
            }
            $VehicleOnlyLoanDeed->setValue('gender', value($gender));
            $VehicleOnlyLoanDeed->setValue('nepali_name', value($borrower->nepali_name));
            $VehicleOnlyLoanDeed->setValue('citizenship_number', value($borrower->citizenship_number));
            $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
            $VehicleOnlyLoanDeed->setValue('issued_date', value($issued_date));
            $VehicleOnlyLoanDeed->setValue('issued_district', value(District::find($borrower->issued_district)->name));

            $facility = PersonalBorrower::find($bid)->personal_facilities;
            $VehicleOnlyLoanDeed->cloneRow('facilitysn', count($facility));
            $i = 1;
            foreach ($facility as $f) {
                $n = $i++;
                $VehicleOnlyLoanDeed->setValue('facilitysn#' . $n, $n);
                $VehicleOnlyLoanDeed->setValue('facility#' . $n, Facility::find($f->facility_id)->name);
                $VehicleOnlyLoanDeed->setValue('facility_amount#' . $n, $f->amount);
                $VehicleOnlyLoanDeed->setValue('rate#' . $n, $f->rate);
                if ($f->tyear && $f->tmonth && $f->tday) {
                    $VehicleOnlyLoanDeed->setValue('time#' . $n, 'ldlt ' . $f->tyear . '÷' . $f->tmonth . '÷' . $f->tday . ' ;Dd');
                } else {
                    $VehicleOnlyLoanDeed->setValue('time#' . $n, $f->tenure);
                }
                $VehicleOnlyLoanDeed->setValue('remarks#' . $n, $f->remarks);
            }

            $count = count(PersonalBorrower::find($bid)->personal_hire_purchase);
            $VehicleOnlyLoanDeed->cloneRow('vehiclesn', $count);

            $i = 1;
            foreach (PersonalBorrower::find($bid)->personal_hire_purchase as $vehicle) {
                $n = $i++;
                $VehicleOnlyLoanDeed->setValue('vehiclesn#' . $n, $n);
                $VehicleOnlyLoanDeed->setValue('model_number#' . $n, $vehicle->model_number);
                $VehicleOnlyLoanDeed->setValue('registration_number#' . $n, $vehicle->registration_number);
                $VehicleOnlyLoanDeed->setValue('engine_number#' . $n, $vehicle->engine_number);
                $VehicleOnlyLoanDeed->setValue('chassis_number#' . $n, $vehicle->chassis_number);

            }
        }
        $VehicleOnlyLoanDeed->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Personal_Loan_Deed.docx'));

    }

    private function loan_deed_land_only($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name
        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            //share loan deed
            $LandOnlyLoanDeed = new TemplateProcessor(storage_path('document/personal/Land_Only.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
            $LandOnlyLoanDeed->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $LandOnlyLoanDeed->setValue('offerletterdate', value($offerletterdate));
            $LandOnlyLoanDeed->setValue('amount', value($loan->loan_amount));
            $LandOnlyLoanDeed->setValue('words', value($loan->loan_amount_words));
            $LandOnlyLoanDeed->setValue('grandfather_name', value($borrower->grandfather_name));
            $LandOnlyLoanDeed->setValue('grandfather_relation', value($borrower->grandfather_relation));
            $LandOnlyLoanDeed->setValue('father_name', value($borrower->father_name));
            $LandOnlyLoanDeed->setValue('father_relation', value($borrower->father_relation));
            if ($borrower->spouse_name) {
                $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                $LandOnlyLoanDeed->setValue('spouse_name', value($spouse_name));
            } else {
                $spouse_name = null;
                $LandOnlyLoanDeed->setValue('spouse_name', value($spouse_name));
            }
            $LandOnlyLoanDeed->setValue('district', value(District::find($borrower->district_id)->name));
            $LandOnlyLoanDeed->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
            $LandOnlyLoanDeed->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
            $LandOnlyLoanDeed->setValue('wardno', value($borrower->wardno));

            $LandOnlyLoanDeed->setValue('age', value($cyear - ($borrower->dob_year)));
            $g = $borrower->gender;
            if ($g == 1) {
                $gender = 'sf]';
                $male = 'k\"?if';
            } else {
                $gender = 'sL';
                $male = 'dlxnf';
            }
            $LandOnlyLoanDeed->setValue('gender', value($gender));
            $LandOnlyLoanDeed->setValue('nepali_name', value($borrower->nepali_name));
            $LandOnlyLoanDeed->setValue('citizenship_number', value($borrower->citizenship_number));
            $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
            $LandOnlyLoanDeed->setValue('issued_date', value($issued_date));
            $LandOnlyLoanDeed->setValue('issued_district', value(District::find($borrower->issued_district)->name));

            $facility = PersonalBorrower::find($bid)->personal_facilities;
            $LandOnlyLoanDeed->cloneRow('facilitysn', count($facility));
            $i = 1;
            foreach ($facility as $f) {
                $n = $i++;
                $LandOnlyLoanDeed->setValue('facilitysn#' . $n, $n);
                $LandOnlyLoanDeed->setValue('facility#' . $n, Facility::find($f->facility_id)->name);
                $LandOnlyLoanDeed->setValue('facility_amount#' . $n, $f->amount);
                $LandOnlyLoanDeed->setValue('rate#' . $n, $f->rate);
                if ($f->tyear && $f->tmonth && $f->tday) {
                    $LandOnlyLoanDeed->setValue('time#' . $n, 'ldlt ' . $f->tyear . '÷' . $f->tmonth . '÷' . $f->tday . ' ;Dd');
                } else {
                    $LandOnlyLoanDeed->setValue('time#' . $n, $f->tenure);
                }
                $LandOnlyLoanDeed->setValue('remarks#' . $n, $f->remarks);
            }

            $joint = PersonalBorrower::find($bid)->personal_joint_land()->first();
            if ($joint) {
                $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
            } else {
                $jointp = [];
            }
            $count = count($borrower->personalborrowerpersonalland) + count($borrower->personalborrowercorporateland) + count($jointp);
            $LandOnlyLoanDeed->cloneRow('landsn', $count);

            $i = 1;
            foreach ($borrower->personalborrowerpersonalland as $land) {

                $n = $i++;
                $LandOnlyLoanDeed->setValue('landsn#' . $n, $n);
                $LandOnlyLoanDeed->setValue('ownername#' . $n, PersonalPropertyOwner::find($land->property_owner_id)->nepali_name);
                $LandOnlyLoanDeed->setValue('ldistrict#' . $n, District::find($land->district_id)->name);
                $LandOnlyLoanDeed->setValue('llocalbody#' . $n, LocalBodies::find($land->local_bodies_id)->name);
                $LandOnlyLoanDeed->setValue('lwardno#' . $n, $land->wardno);
                $LandOnlyLoanDeed->setValue('sheetno#' . $n, $land->sheet_no);
                $LandOnlyLoanDeed->setValue('kittano#' . $n, $land->kitta_no);
                $LandOnlyLoanDeed->setValue('area#' . $n, $land->area);
                $LandOnlyLoanDeed->setValue('remarks#' . $n, $land->remarks);
            }
            foreach ($borrower->personalborrowercorporateland as $land) {

                $n = $i++;
                $LandOnlyLoanDeed->setValue('landsn#' . $n, $n);
                $LandOnlyLoanDeed->setValue('ownername#' . $n, CorporatePropertyOwner::find($land->property_owner_id)->nepali_name);
                $LandOnlyLoanDeed->setValue('ldistrict#' . $n, District::find($land->district_id)->name);
                $LandOnlyLoanDeed->setValue('llocalbody#' . $n, LocalBodies::find($land->local_bodies_id)->name);
                $LandOnlyLoanDeed->setValue('lwardno#' . $n, $land->wardno);
                $LandOnlyLoanDeed->setValue('sheetno#' . $n, $land->sheet_no);
                $LandOnlyLoanDeed->setValue('kittano#' . $n, $land->kitta_no);
                $LandOnlyLoanDeed->setValue('area#' . $n, $land->area);
                $LandOnlyLoanDeed->setValue('remarks#' . $n, $land->remarks);
            }
            foreach ($jointp as $land) {
                $n = $i++;
                $LandOnlyLoanDeed->setValue('landsn#' . $n, $n);
                $j1 = PersonalPropertyOwner::find(JointPropertyOwner::find($land->joint_id)->joint1);
                $j2 = PersonalPropertyOwner::find(JointPropertyOwner::find($land->joint_id)->joint2);
                $j3 = PersonalPropertyOwner::find(JointPropertyOwner::find($land->joint_id)->joint3);
                if ($j1 && $j2) {

                    $LandOnlyLoanDeed->setValue('ownername#' . $n, $j1->nepali_name . ' / ' . $j2->nepali_name);
                } elseif ($j2 && $j3) {
                    $LandOnlyLoanDeed->setValue('ownername#' . $n, $j2->nepali_name . ' / ' . $j3->nepali_name);

                } elseif ($j1 && $j3) {

                    $LandOnlyLoanDeed->setValue('ownername#' . $n, $j1->nepali_name . ' / ' . $j3->nepali_name);

                } elseif ($j1 && $j2 && $j3) {
                    $LandOnlyLoanDeed->setValue('ownername#' . $n, $j1->nepali_name . ', ' . $j2->nepali_name . ' / ' . $j3->nepali_name);

                }

                $LandOnlyLoanDeed->setValue('ldistrict#' . $n, District::find($land->district_id)->name);
                $LandOnlyLoanDeed->setValue('llocalbody#' . $n, LocalBodies::find($land->local_bodies_id)->name);
                $LandOnlyLoanDeed->setValue('lwardno#' . $n, $land->wardno);
                $LandOnlyLoanDeed->setValue('sheetno#' . $n, $land->sheet_no);
                $LandOnlyLoanDeed->setValue('kittano#' . $n, $land->kitta_no);
                $LandOnlyLoanDeed->setValue('area#' . $n, $land->area);
                $LandOnlyLoanDeed->setValue('remarks#' . $n, $land->remarks);
            }

            $LandOnlyLoanDeed->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Personal_Loan_Deed.docx'));
        }
    }

    private function loan_deed_facilities_only($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name
        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            //share loan deed
            $FacilitiesOnly = new TemplateProcessor(storage_path('document/personal/Facilities_Only.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
            $FacilitiesOnly->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $FacilitiesOnly->setValue('offerletterdate', value($offerletterdate));
            $FacilitiesOnly->setValue('amount', value($loan->loan_amount));
            $FacilitiesOnly->setValue('words', value($loan->loan_amount_words));
            $FacilitiesOnly->setValue('grandfather_name', value($borrower->grandfather_name));
            $FacilitiesOnly->setValue('grandfather_relation', value($borrower->grandfather_relation));
            $FacilitiesOnly->setValue('father_name', value($borrower->father_name));
            $FacilitiesOnly->setValue('father_relation', value($borrower->father_relation));
            if ($borrower->spouse_name) {
                $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                $FacilitiesOnly->setValue('spouse_name', value($spouse_name));
            } else {
                $spouse_name = null;
                $FacilitiesOnly->setValue('spouse_name', value($spouse_name));
            }
            $FacilitiesOnly->setValue('district', value(District::find($borrower->district_id)->name));
            $FacilitiesOnly->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
            $FacilitiesOnly->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
            $FacilitiesOnly->setValue('wardno', value($borrower->wardno));

            $FacilitiesOnly->setValue('age', value($cyear - ($borrower->dob_year)));
            $g = $borrower->gender;
            if ($g == 1) {
                $gender = 'sf]';
                $male = 'k\"?if';
            } else {
                $gender = 'sL';
                $male = 'dlxnf';
            }
            $FacilitiesOnly->setValue('gender', value($gender));
            $FacilitiesOnly->setValue('nepali_name', value($borrower->nepali_name));
            $FacilitiesOnly->setValue('citizenship_number', value($borrower->citizenship_number));
            $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
            $FacilitiesOnly->setValue('issued_date', value($issued_date));
            $FacilitiesOnly->setValue('issued_district', value(District::find($borrower->issued_district)->name));

            $facility = PersonalBorrower::find($bid)->personal_facilities;
            $FacilitiesOnly->cloneRow('facilitysn', count($facility));
            $i = 1;
            foreach ($facility as $f) {
                $n = $i++;
                $FacilitiesOnly->setValue('facilitysn#' . $n, $n);
                $FacilitiesOnly->setValue('facility#' . $n, Facility::find($f->facility_id)->name);
                $FacilitiesOnly->setValue('facility_amount#' . $n, $f->amount);
                $FacilitiesOnly->setValue('rate#' . $n, $f->rate);
                if ($f->tyear && $f->tmonth && $f->tday) {
                    $FacilitiesOnly->setValue('time#' . $n, 'ldlt ' . $f->tyear . '÷' . $f->tmonth . '÷' . $f->tday . ' ;Dd');
                } else {
                    $FacilitiesOnly->setValue('time#' . $n, $f->tenure);
                }
                $FacilitiesOnly->setValue('remarks#' . $n, $f->remarks);
            }
        }
        $FacilitiesOnly->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Personal_Loan_Deed.docx'));

    }

    private function personal_guarantor($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);
        $guarantor = PersonalBorrower::find($bid)->personal_guarantor_borrower;
        //File name
        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
//personal guarantor


            foreach ($borrower->personalborrowerpersonalguarantor as $pg) {

                $personalguarantor = new TemplateProcessor(storage_path('document/personal/Personal_Guarantor.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $personalguarantor->setValue('branch', value(Branch::find($loan->branch_id)->location));
                $personalguarantor->setValue('offerletterdate', value($offerletterdate));
                $personalguarantor->setValue('amount', value($loan->loan_amount));
                $personalguarantor->setValue('words', value($loan->loan_amount_words));
                $personalguarantor->setValue('grandfather_name', value($borrower->grandfather_name));
                $personalguarantor->setValue('grandfather_relation', value($borrower->grandfather_relation));
                $personalguarantor->setValue('father_name', value($borrower->father_name));
                $personalguarantor->setValue('father_relation', value($borrower->father_relation));
                if ($borrower->spouse_name) {
                    $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                    $personalguarantor->setValue('spouse_name', value($spouse_name));
                } else {
                    $spouse_name = null;
                    $personalguarantor->setValue('spouse_name', value($spouse_name));
                }
                $personalguarantor->setValue('district', value(District::find($borrower->district_id)->name));
                $personalguarantor->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
                $personalguarantor->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
                $personalguarantor->setValue('wardno', value($borrower->wardno));

                $personalguarantor->setValue('age', value($cyear - ($borrower->dob_year)));
                $g = $borrower->gender;
                if ($g == 1) {
                    $gender = 'sf]';
                    $male = 'k\"?if';
                } else {
                    $gender = 'sL';
                    $male = 'dlxnf';
                }
                $personalguarantor->setValue('gender', value($gender));
                $personalguarantor->setValue('nepali_name', value($borrower->nepali_name));
                $personalguarantor->setValue('citizenship_number', value($borrower->citizenship_number));
                $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                $personalguarantor->setValue('issued_date', value($issued_date));
                $personalguarantor->setValue('issued_district', value(District::find($borrower->issued_district)->name));

//            guarantor details
                $personalguarantor->setValue('ggrandfather_name', value($pg->grandfather_name));
                $personalguarantor->setValue('ggrandfather_relation', value($pg->grandfather_relation));
                $personalguarantor->setValue('gfather_name', value($pg->father_name));
                $personalguarantor->setValue('gfather_relation', value($pg->father_relation));
                if ($pg->spouse_name) {
                    $spouse_name = ' ' . $pg->spouse_name . 'sf] ' . $pg->spouse_relation;
                    $personalguarantor->setValue('gspouse_name', value($spouse_name));
                } else {
                    $spouse_name = null;
                    $personalguarantor->setValue('gspouse_name', value($spouse_name));
                }
                $personalguarantor->setValue('gdistrict', value(District::find($pg->district_id)->name));
                $personalguarantor->setValue('glocalbody', value(LocalBodies::find($pg->local_bodies_id)->name));
                $personalguarantor->setValue('gbody_type', value(LocalBodies::find($pg->local_bodies_id)->body_type));
                $personalguarantor->setValue('gwardno', value($pg->wardno));

                $personalguarantor->setValue('gage', value($cyear - ($pg->dob_year)));
                $g = $pg->gender;
                if ($g == 1) {
                    $gender = 'sf]';
                    $male = 'k\"?if';
                } else {
                    $gender = 'sL';
                    $male = 'dlxnf';
                }

                $personalguarantor->setValue('ggender', value($gender));
                $personalguarantor->setValue('gnepali_name', value($pg->nepali_name));
                $personalguarantor->setValue('gcitizenship_number', value($pg->citizenship_number));
                $issued_date = $pg->issued_year . '÷' . $pg->issued_month . '÷' . $pg->issued_day;
                $personalguarantor->setValue('gissued_date', value($issued_date));
                $personalguarantor->setValue('gissued_district', value(District::find($pg->issued_district)->name));
                $personalguarantor->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Personal_Guarantor_' . $pg->english_name . '.docx'));

            }
        }


    }

    private function corporate_guarantor($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);
        $guarantor = PersonalBorrower::find($bid)->personal_guarantor_borrower;
        //File name
        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
//personal guarantor

            foreach ($borrower->personalborrowercorporateguarantor as $c) {

                $corporateguarantor = new TemplateProcessor(storage_path('document/personal/Corporate_Guarantor.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $corporateguarantor->setValue('branch', value(Branch::find($loan->branch_id)->location));
                $corporateguarantor->setValue('offerletterdate', value($offerletterdate));
                $corporateguarantor->setValue('amount', value($loan->loan_amount));
                $corporateguarantor->setValue('words', value($loan->loan_amount_words));
                $corporateguarantor->setValue('grandfather_name', value($borrower->grandfather_name));
                $corporateguarantor->setValue('grandfather_relation', value($borrower->grandfather_relation));
                $corporateguarantor->setValue('father_name', value($borrower->father_name));
                $corporateguarantor->setValue('father_relation', value($borrower->father_relation));
                if ($borrower->spouse_name) {
                    $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                    $corporateguarantor->setValue('spouse_name', value($spouse_name));
                } else {
                    $spouse_name = null;
                    $corporateguarantor->setValue('spouse_name', value($spouse_name));
                }
                $corporateguarantor->setValue('district', value(District::find($borrower->district_id)->name));
                $corporateguarantor->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
                $corporateguarantor->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
                $corporateguarantor->setValue('wardno', value($borrower->wardno));

                $corporateguarantor->setValue('age', value($cyear - ($borrower->dob_year)));
                $g = $borrower->gender;
                if ($g == 1) {
                    $gender = 'sf]';
                    $male = 'k\"?if';
                } else {
                    $gender = 'sL';
                    $male = 'dlxnf';
                }
                $corporateguarantor->setValue('gender', value($gender));
                $corporateguarantor->setValue('nepali_name', value($borrower->nepali_name));
                $corporateguarantor->setValue('citizenship_number', value($borrower->citizenship_number));
                $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                $corporateguarantor->setValue('issued_date', value($issued_date));
                $corporateguarantor->setValue('issued_district', value(District::find($borrower->issued_district)->name));

//            guarantor details

                $corporateguarantor->setValue('cministry', value(Ministry::find($c->ministry_id)->name));
                $corporateguarantor->setValue('cdepartment', value(Department::find($c->department_id)->name));
                $corporateguarantor->setValue('cregistrationno', value($c->registration_number));
                $reg_date = $c->reg_year . '÷' . $c->reg_month . '÷' . $c->reg_day;
                $corporateguarantor->setValue('cregistrationdate', value($reg_date));
                $corporateguarantor->setValue('cdistrict', value(District::find($c->district_id)->name));
                $corporateguarantor->setValue('clocalbody', value(LocalBodies::find($c->local_bodies_id)->name));
                $corporateguarantor->setValue('cbodytype', value(LocalBodies::find($c->local_bodies_id)->body_type));
                $corporateguarantor->setValue('cwardno', value($c->wardno));
                $corporateguarantor->setValue('cname', value($c->nepali_name));

                $pg = AuthorizedPerson::find($c->authorized_person_id);
                $corporateguarantor->setValue('gpost', value($pg->post));
                $corporateguarantor->setValue('ggrandfather_name', value($pg->grandfather_name));
                $corporateguarantor->setValue('ggrandfather_relation', value($pg->grandfather_relation));
                $corporateguarantor->setValue('gfather_name', value($pg->father_name));
                $corporateguarantor->setValue('gfather_relation', value($pg->father_relation));
                if ($pg->spouse_name) {
                    $spouse_name = ' ' . $pg->spouse_name . 'sf] ' . $pg->spouse_relation;
                    $corporateguarantor->setValue('gspouse_name', value($spouse_name));
                } else {
                    $spouse_name = null;
                    $corporateguarantor->setValue('gspouse_name', value($spouse_name));
                }
                $corporateguarantor->setValue('gdistrict', value(District::find($pg->district_id)->name));
                $corporateguarantor->setValue('glocalbody', value(LocalBodies::find($pg->local_bodies_id)->name));
                $corporateguarantor->setValue('gbody_type', value(LocalBodies::find($pg->local_bodies_id)->body_type));
                $corporateguarantor->setValue('gwardno', value($pg->wardno));

                $corporateguarantor->setValue('gage', value($cyear - ($pg->dob_year)));
                $g = $pg->gender;
                if ($g == 1) {
                    $gender = 'sf]';
                    $male = 'k\"?if';
                } else {
                    $gender = 'sL';
                    $male = 'dlxnf';
                }

                $corporateguarantor->setValue('ggender', value($gender));
                $corporateguarantor->setValue('gnepali_name', value($pg->nepali_name));
                $corporateguarantor->setValue('gcitizenship_number', value($pg->citizenship_number));
                $issued_date = $pg->issued_year . '÷' . $pg->issued_month . '÷' . $pg->issued_day;
                $corporateguarantor->setValue('gissued_date', value($issued_date));
                $corporateguarantor->setValue('gissued_district', value(District::find($pg->issued_district)->name));
                $corporateguarantor->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Corporate_Guarantor_' . $c->english_name . '.docx'));

            }
        }


    }

    private function mortgage_deed_third_joint2($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);
        $joint = PersonalBorrower::find($bid)->personal_joint_land()->first();
        if ($joint) {
            //File name

            $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {

                $borrower = PersonalBorrower::find($bid);
                $joint = PersonalBorrower::find($bid)->personal_joint_land()->first();
                if ($joint) {
                    $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
                } else {
                    $jointp = [];
                }


                if ($joint->joint1 && $joint->joint2) {
                    $p1 = PersonalPropertyOwner::find($joint->joint1);
                    $p2 = PersonalPropertyOwner::find($joint->joint2);

                } elseif ($joint->joint3 && $joint->joint2) {
                    $p1 = PersonalPropertyOwner::find($joint->joint2);
                    $p2 = PersonalPropertyOwner::find($joint->joint3);

                } elseif ($joint->joint1 && $joint->joint3) {
                    $p1 = PersonalPropertyOwner::find($joint->joint1);
                    $p2 = PersonalPropertyOwner::find($joint->joint3);
                }
                $malpot = [];
                foreach ($jointp as $land) {
                    $malpot[] = $land->malpot;
                }

                $malpot = array_unique($malpot);
                $mlp = 1;
                foreach ($malpot as $m) {
                    $ld = 0;
                    foreach ($jointp as $l) {
                        if ($m == $l->malpot) {
                            $ld = $ld + 1;
                        }
                    }

                    $malpotland = JointLand::where([
                        ['joint_id', $joint->id],
                        ['malpot', $m]
                    ])->first();

                    if ($malpotland) {
                        if (!$ld == 0) {
                            //mortgage deed
                            $MortgageDeedThird = new TemplateProcessor(storage_path('document/personal/Mortgage Deed Third_joint2.docx'));
                            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document

                            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                            $MortgageDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                            $dob = $borrower->dob_year . '÷' . $borrower->dob_month . '÷' . $borrower->dob_day;
                            $MortgageDeedThird->setValue('offerletterdate', value($offerletterdate));
                            $MortgageDeedThird->setValue('amount', value($loan->loan_amount));
                            $MortgageDeedThird->setValue('dob', value($dob));
                            $MortgageDeedThird->setValue('words', value($loan->loan_amount_words));
                            $MortgageDeedThird->setValue('grandfather_name', value($borrower->grandfather_name));
                            $MortgageDeedThird->setValue('phone', value($borrower->phone));
                            $MortgageDeedThird->setValue('grandfather_relation', value($borrower->grandfather_relation));
                            $MortgageDeedThird->setValue('father_name', value($borrower->father_name));
                            $MortgageDeedThird->setValue('father_relation', value($borrower->father_relation));
                            if ($borrower->spouse_name) {
                                $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                                $MortgageDeedThird->setValue('spouse_name', value($spouse_name));
                                $dspouse_name = $borrower->spouse_name;
                                $MortgageDeedThird->setValue('dspouse_name', value($dspouse_name));
                            } else {
                                $spouse_name = null;
                                $MortgageDeedThird->setValue('spouse_name', value($spouse_name));
                                $MortgageDeedThird->setValue('dspouse_name', value($spouse_name));
                            }
                            $MortgageDeedThird->setValue('district', value(District::find($borrower->district_id)->name));
                            $MortgageDeedThird->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
                            $MortgageDeedThird->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
                            $MortgageDeedThird->setValue('wardno', value($borrower->wardno));

                            $MortgageDeedThird->setValue('age', value($cyear - ($borrower->dob_year)));
                            $g = $borrower->gender;
                            if ($g == 1) {
                                $gender = 'sf]';
                                $male = 'k\"?if';
                            } else {
                                $gender = 'sL';
                                $male = 'dlxnf';
                            }
                            $MortgageDeedThird->setValue('gender', value($gender));
                            $MortgageDeedThird->setValue('male', value($male));
                            $MortgageDeedThird->setValue('nepali_name', value($borrower->nepali_name));
                            $MortgageDeedThird->setValue('dnepali_name', value($borrower->nepali_name));
                            $MortgageDeedThird->setValue('english_name', value($borrower->english_name));
                            $MortgageDeedThird->setValue('citizenship_number', value($borrower->citizenship_number));
                            $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                            $MortgageDeedThird->setValue('issued_date', value($issued_date));
                            $MortgageDeedThird->setValue('issued_district', value(District::find($borrower->issued_district)->name));
//for property owner
//                Property Owner Details

                            $MortgageDeedThird->setValue('p1phone', value($p1->phone));
                            $MortgageDeedThird->setValue('p1grandfather_name', value($p1->grandfather_name));
                            $MortgageDeedThird->setValue('p1grandfather_relation', value($p1->grandfather_relation));
                            $MortgageDeedThird->setValue('p1father_name', value($p1->father_name));
                            $MortgageDeedThird->setValue('p1father_relation', value($p1->father_relation));
                            if ($p1->spouse_name) {
                                $spouse_name1 = $p1->spouse_name;
                            } else {
                                $spouse_name1 = null;
                            }
                            $MortgageDeedThird->setValue('p1spouse_name', value($spouse_name1));
                            $MortgageDeedThird->setValue('dp1spouse_name', value($spouse_name1));
                            $MortgageDeedThird->setValue('p1district', value(District::find($p1->district_id)->name));
                            $MortgageDeedThird->setValue('p1localbody', value(LocalBodies::find($p1->local_bodies_id)->name));
                            $MortgageDeedThird->setValue('p1bodytype', value(LocalBodies::find($p1->local_bodies_id)->body_type));
                            $MortgageDeedThird->setValue('p1wardno', value($p1->wardno));

                            $p1address = District::find($p1->district_id)->name . ' lhNnf ' . LocalBodies::find($p1->local_bodies_id)->name . ' ' . LocalBodies::find($p1->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $p1->wardno;
                            $MortgageDeedThird->setValue('p1address', value($p1address));

                            $age1 = $cyear - $p1->dob_year;

                            $MortgageDeedThird->setValue('p1age', value($age1));
                            $g1 = $p1->gender;
                            if ($g1 == 1) {
                                $gender1 = 'sf]';
                                $male1 = 'k\"?if';
                            } else {
                                $gender1 = 'sL';
                                $male1 = 'dlxnf';
                            }
                            $MortgageDeedThird->setValue('p1gender', value($gender1));
                            $MortgageDeedThird->setValue('p1male', value($male1));
                            $MortgageDeedThird->setValue('pnepali_name', value($p1->nepali_name . ' –1 / ' . $p2->nepali_name . ' –1 ;d]t hgf b"O ;+o"Qm'));
                            $MortgageDeedThird->setValue('dp1nepali_name', value($p1->nepali_name));
                            $MortgageDeedThird->setValue('p1english_name', value($p1->english_name));
                            $MortgageDeedThird->setValue('p1citizenship_number', value($p1->citizenship_number));
                            $issued_date1 = $p1->issued_year . '÷' . $p1->issued_month . '÷' . $p1->issued_day;
                            $pdob1 = $p1->dob_year . '÷' . $p1->dob_month . '÷' . $p1->dob_day;
                            $MortgageDeedThird->setValue('p1issued_date', value($issued_date1));
                            $MortgageDeedThird->setValue('p1dob', value($pdob1));
                            $MortgageDeedThird->setValue('p1issued_district', value(District::find($p1->issued_district)->name));
//second property owner

                            $MortgageDeedThird->setValue('p2phone', value($p2->phone));
                            $MortgageDeedThird->setValue('p2grandfather_name', value($p2->grandfather_name));
                            $MortgageDeedThird->setValue('p2grandfather_relation', value($p2->grandfather_relation));
                            $MortgageDeedThird->setValue('p2father_name', value($p2->father_name));
                            $MortgageDeedThird->setValue('p2father_relation', value($p2->father_relation));
                            if ($p2->spouse_name) {
                                $spouse_name2 = $p2->spouse_name;

                            } else {
                                $spouse_name2 = null;

                            }
                            $MortgageDeedThird->setValue('p2spouse_name', value($spouse_name2));
                            $MortgageDeedThird->setValue('dp2spouse_name', value($spouse_name2));
                            $MortgageDeedThird->setValue('p2district', value(District::find($p2->district_id)->name));
                            $MortgageDeedThird->setValue('p2localbody', value(LocalBodies::find($p2->local_bodies_id)->name));
                            $MortgageDeedThird->setValue('p2bodytype', value(LocalBodies::find($p2->local_bodies_id)->body_type));
                            $MortgageDeedThird->setValue('p2wardno', value($p2->wardno));
                            $p2address = District::find($p2->district_id)->name . ' lhNnf ' . LocalBodies::find($p2->local_bodies_id)->name . ' ' . LocalBodies::find($p2->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $p2->wardno;
                            $MortgageDeedThird->setValue('p2address', value($p2address));
                            $age2 = $cyear - $p2->dob_year;

                            $MortgageDeedThird->setValue('p2age', value($age2));
                            $g2 = $p2->gender;
                            if ($g2 == 1) {
                                $gender2 = 'sf]';
                                $male2 = 'k\"?if';
                            } else {
                                $gender2 = 'sL';
                                $male2 = 'dlxnf';
                            }

                            $MortgageDeedThird->setValue('p2gender', value($gender2));
                            $MortgageDeedThird->setValue('p2male', value($male2));
                            $MortgageDeedThird->setValue('dp2nepali_name', value($p2->nepali_name));
                            $MortgageDeedThird->setValue('p2english_name', value($p2->english_name));
                            $MortgageDeedThird->setValue('p2citizenship_number', value($p2->citizenship_number));
                            $issued_date2 = $p2->issued_year . '÷' . $p2->issued_month . '÷' . $p2->issued_day;
                            $pdob2 = $p2->dob_year . '÷' . $p2->dob_month . '÷' . $p2->dob_day;
                            $MortgageDeedThird->setValue('p2issued_date', value($issued_date2));
                            $MortgageDeedThird->setValue('p2dob', value($pdob2));
                            $MortgageDeedThird->setValue('p2issued_district', value(District::find($p2->issued_district)->name));


                            $MortgageDeedThird->cloneRow('llocalbody', $ld);
                            $i = 1;
                            foreach ($jointp as $l) {
                                if ($m == $l->malpot) {
                                    $n = $i++;
                                    $MortgageDeedThird->setValue('ldistrict', value(District::find($l->district_id)->name));
                                    $MortgageDeedThird->setValue('lprovince', value(Province::find(District::find($l->district_id)->province_id))->name);
                                    $MortgageDeedThird->setValue('llocalbody#' . $n, LocalBodies::find($l->local_bodies_id)->name);
                                    $MortgageDeedThird->setValue('lwardno#' . $n, $l->wardno);
                                    $MortgageDeedThird->setValue('sheetno#' . $n, $l->sheet_no);
                                    $MortgageDeedThird->setValue('kittano#' . $n, $l->kitta_no);
                                    $MortgageDeedThird->setValue('area#' . $n, $l->area);
                                    $MortgageDeedThird->setValue('remarks#' . $n, $l->remarks);


                                }
                            }
                            $MortgageDeedThird->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Mortgage_Deed_Third_Joint2_malpot_' . $mlp++ . '.docx'));
                        }
                    }
                }


            }

        }
    }

    private function old_mortgage_deed_third_joint2($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);
        $joint = PersonalBorrower::find($bid)->personal_joint_land()->first();
        if ($joint) {
            //File name

            $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {

                $borrower = PersonalBorrower::find($bid);
                $joint = PersonalBorrower::find($bid)->personal_joint_land()->first();
                if ($joint) {
                    $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
                } else {
                    $jointp = [];
                }


                if ($joint->joint1 && $joint->joint2) {
                    $p1 = PersonalPropertyOwner::find($joint->joint1);
                    $p2 = PersonalPropertyOwner::find($joint->joint2);

                } elseif ($joint->joint3 && $joint->joint2) {
                    $p1 = PersonalPropertyOwner::find($joint->joint2);
                    $p2 = PersonalPropertyOwner::find($joint->joint3);

                } elseif ($joint->joint1 && $joint->joint3) {
                    $p1 = PersonalPropertyOwner::find($joint->joint1);
                    $p2 = PersonalPropertyOwner::find($joint->joint3);
                }
                $malpot = [];
                foreach ($jointp as $land) {
                    $malpot[] = $land->malpot;
                }

                $malpot = array_unique($malpot);
                $mlp = 1;
                foreach ($malpot as $m) {
                    $ld = 0;
                    foreach ($jointp as $l) {
                        if ($m == $l->malpot) {
                            $ld = $ld + 1;
                        }
                    }

                    $malpotland = JointLand::where([
                        ['joint_id', $joint->id],
                        ['malpot', $m]
                    ])->first();

                    if ($malpotland) {
                        if (!$ld == 0) {
                            //mortgage deed
                            $MortgageDeedThird = new TemplateProcessor(storage_path('document/personal/old mortgage deed joint 2.docx'));
                            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document

                            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                            $MortgageDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                            $dob = $borrower->dob_year . '÷' . $borrower->dob_month . '÷' . $borrower->dob_day;
                            $MortgageDeedThird->setValue('offerletterdate', value($offerletterdate));
                            $MortgageDeedThird->setValue('amount', value($loan->loan_amount));
                            $MortgageDeedThird->setValue('dob', value($dob));
                            $MortgageDeedThird->setValue('words', value($loan->loan_amount_words));
                            $MortgageDeedThird->setValue('grandfather_name', value($borrower->grandfather_name));
                            $MortgageDeedThird->setValue('phone', value($borrower->phone));
                            $MortgageDeedThird->setValue('grandfather_relation', value($borrower->grandfather_relation));
                            $MortgageDeedThird->setValue('father_name', value($borrower->father_name));
                            $MortgageDeedThird->setValue('father_relation', value($borrower->father_relation));
                            if ($borrower->spouse_name) {
                                $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                                $MortgageDeedThird->setValue('spouse_name', value($spouse_name));
                                $dspouse_name = $borrower->spouse_name;
                                $MortgageDeedThird->setValue('dspouse_name', value($dspouse_name));
                            } else {
                                $spouse_name = null;
                                $MortgageDeedThird->setValue('spouse_name', value($spouse_name));
                                $MortgageDeedThird->setValue('dspouse_name', value($spouse_name));
                            }
                            $MortgageDeedThird->setValue('district', value(District::find($borrower->district_id)->name));
                            $MortgageDeedThird->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
                            $MortgageDeedThird->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
                            $MortgageDeedThird->setValue('wardno', value($borrower->wardno));

                            $MortgageDeedThird->setValue('age', value($cyear - ($borrower->dob_year)));
                            $g = $borrower->gender;
                            if ($g == 1) {
                                $gender = 'sf]';
                                $male = 'k\"?if';
                            } else {
                                $gender = 'sL';
                                $male = 'dlxnf';
                            }
                            $MortgageDeedThird->setValue('gender', value($gender));
                            $MortgageDeedThird->setValue('male', value($male));
                            $MortgageDeedThird->setValue('nepali_name', value($borrower->nepali_name));
                            $MortgageDeedThird->setValue('dnepali_name', value($borrower->nepali_name));
                            $MortgageDeedThird->setValue('english_name', value($borrower->english_name));
                            $MortgageDeedThird->setValue('citizenship_number', value($borrower->citizenship_number));
                            $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                            $MortgageDeedThird->setValue('issued_date', value($issued_date));
                            $MortgageDeedThird->setValue('issued_district', value(District::find($borrower->issued_district)->name));
//for property owner
//                Property Owner Details

                            if ($p1->spouse_name) {
                                $p1spouse_name = ' ' . $p1->spouse_name . 'sf] ' . $p1->spouse_relation . null;
                            } else {
                                $p1spouse_name = null;
                            }
                            $g = $p1->gender;
                            if ($g == 1) {
                                $p1gender = 'sf]';
                                $p1male = 'k\"?if';
                            } else {
                                $p1gender = 'sL';
                                $p1male = 'dlxnf';
                            }
                            $p1issued_date = $p1->issued_year . '÷' . $p1->issued_month . '÷' . $p1->issued_day;
                            if ($p2->spouse_name) {
                                $p2spouse_name = ' ' . $p2->spouse_name . 'sf] ' . $p2->spouse_relation . null;
                            } else {
                                $p2spouse_name = null;
                            }
                            $g = $p2->gender;
                            if ($g == 1) {
                                $p2gender = 'sf]';
                                $p2male = 'k\"?if';
                            } else {
                                $p2gender = 'sL';
                                $p2male = 'dlxnf';
                            }
                            $p2issued_date = $p2->issued_year . '÷' . $p2->issued_month . '÷' . $p2->issued_day;
                            $property_owner = $p1->grandfather_name . 'sf] ' . $p1->grandfather_relation . ' ' . $p1->father_name . 'sf] ' . $p1->father_relation . $p1spouse_name . ' ' . District::find($p1->district_id)->name . ' lhNnf ' . LocalBodies::find($p1->local_bodies_id)->name . ' ' . LocalBodies::find($p1->local_bodies_id)->body_type .
                                ' j*f g+=' . $p1->wardno . ' a:g] aif{ ' . ($cyear - ($p1->dob_year)) . ' ' . $p1gender . ' ' . $p1->nepali_name . ' -gf=k|=k=g+=' . $p1->citizenship_number . ' hf/L ldlt ' . $p1issued_date . ' lhNnf ' . District::find($p1->issued_district)->name . '_ —1' . ' / ' . $p2->grandfather_name . 'sf] ' . $p2->grandfather_relation . ' ' .
                                $p2->father_name . 'sf] ' . $p2->father_relation . $p2spouse_name . ' ' . District::find($p2->district_id)->name . ' lhNnf ' . LocalBodies::find($p2->local_bodies_id)->name . ' ' . LocalBodies::find($p2->local_bodies_id)->body_type .
                                ' j*f g+=' . $p2->wardno . ' a:g] aif{ ' . ($cyear - ($p2->dob_year)) . ' ' . $p2gender . ' ' . $p2->nepali_name . ' -gf=k|=k=g+=' . $p2->citizenship_number . ' hf/L ldlt ' . $p2issued_date . ' lhNnf ' . District::find($p2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';

                            $MortgageDeedThird->setValue('property_owner', value($property_owner));


                            $MortgageDeedThird->cloneRow('llocalbody', $ld);
                            $i = 1;
                            foreach ($jointp as $l) {
                                if ($m == $l->malpot) {
                                    $n = $i++;
                                    $MortgageDeedThird->setValue('lsn#' . $n, $n);
                                    $MortgageDeedThird->setValue('ldistrict#' . $n, District::find($l->district_id)->name);
                                    $MortgageDeedThird->setValue('llocalbody#' . $n, LocalBodies::find($l->local_bodies_id)->name);
                                    $MortgageDeedThird->setValue('lwardno#' . $n, $l->wardno);
                                    $MortgageDeedThird->setValue('sheetno#' . $n, $l->sheet_no);
                                    $MortgageDeedThird->setValue('kittano#' . $n, $l->kitta_no);
                                    $MortgageDeedThird->setValue('area#' . $n, $l->area);
                                    $MortgageDeedThird->setValue('remarks#' . $n, $l->remarks);


                                }
                            }
                            $MortgageDeedThird->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Old Mortgage_Deed_Third_Joint2_malpot_' . $mlp++ . '.docx'));
                        }
                    }
                }


            }

        }
    }

    private function mortgage_deed_third_joint3($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);
        $joint = PersonalBorrower::find($bid)->personal_joint_land()->first();
        if ($joint) {
            //File name

            $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {

                $borrower = PersonalBorrower::find($bid);
                $joint = PersonalBorrower::find($bid)->personal_joint_land()->first();
                if ($joint) {
                    $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
                } else {
                    $jointp = [];
                }

                if ($joint->joint1 && $joint->joint2 && $joint->joint3) {
                    $p1 = PersonalPropertyOwner::find($joint->joint1);
                    $p2 = PersonalPropertyOwner::find($joint->joint2);
                    $p3 = PersonalPropertyOwner::find($joint->joint3);
                }
                $malpot = [];
                foreach ($jointp as $land) {
                    $malpot[] = $land->malpot;
                }

                $malpot = array_unique($malpot);
                $mlp = 1;
                foreach ($malpot as $m) {
                    $ld = 0;
                    foreach ($jointp as $l) {
                        if ($m == $l->malpot) {
                            $ld = $ld + 1;
                        }
                    }

                    $malpotland = JointLand::where([
                        ['joint_id', $joint->id],
                        ['malpot', $m]
                    ])->first();

                    if ($malpotland) {

                        if (!$ld == 0) {
                            //mortgage deed
                            $MortgageDeedThird = new TemplateProcessor(storage_path('document/personal/Mortgage Deed Third_joint3.docx'));
                            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                            $MortgageDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                            $dob = $borrower->dob_year . '÷' . $borrower->dob_month . '÷' . $borrower->dob_day;
                            $MortgageDeedThird->setValue('offerletterdate', value($offerletterdate));
                            $MortgageDeedThird->setValue('amount', value($loan->loan_amount));
                            $MortgageDeedThird->setValue('dob', value($dob));
                            $MortgageDeedThird->setValue('words', value($loan->loan_amount_words));
                            $MortgageDeedThird->setValue('grandfather_name', value($borrower->grandfather_name));
                            $MortgageDeedThird->setValue('phone', value($borrower->phone));
                            $MortgageDeedThird->setValue('grandfather_relation', value($borrower->grandfather_relation));
                            $MortgageDeedThird->setValue('father_name', value($borrower->father_name));
                            $MortgageDeedThird->setValue('father_relation', value($borrower->father_relation));
                            if ($borrower->spouse_name) {
                                $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                                $MortgageDeedThird->setValue('spouse_name', value($spouse_name));
                                $dspouse_name = $borrower->spouse_name;
                                $MortgageDeedThird->setValue('dspouse_name', value($dspouse_name));
                            } else {
                                $spouse_name = null;
                                $MortgageDeedThird->setValue('spouse_name', value($spouse_name));
                                $MortgageDeedThird->setValue('dspouse_name', value($spouse_name));
                            }
                            $MortgageDeedThird->setValue('district', value(District::find($borrower->district_id)->name));
                            $MortgageDeedThird->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
                            $MortgageDeedThird->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
                            $MortgageDeedThird->setValue('wardno', value($borrower->wardno));

                            $MortgageDeedThird->setValue('age', value($cyear - ($borrower->dob_year)));
                            $g = $borrower->gender;
                            if ($g == 1) {
                                $gender = 'sf]';
                                $male = 'k\"?if';
                            } else {
                                $gender = 'sL';
                                $male = 'dlxnf';
                            }
                            $MortgageDeedThird->setValue('gender', value($gender));
                            $MortgageDeedThird->setValue('male', value($male));
                            $MortgageDeedThird->setValue('nepali_name', value($borrower->nepali_name));
                            $MortgageDeedThird->setValue('dnepali_name', value($borrower->nepali_name));
                            $MortgageDeedThird->setValue('english_name', value($borrower->english_name));
                            $MortgageDeedThird->setValue('citizenship_number', value($borrower->citizenship_number));
                            $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                            $MortgageDeedThird->setValue('issued_date', value($issued_date));
                            $MortgageDeedThird->setValue('issued_district', value(District::find($borrower->issued_district)->name));
//for property owner
//                Property Owner Details
//property 1
                            $MortgageDeedThird->setValue('p1phone', value($p1->phone));
                            $MortgageDeedThird->setValue('p1grandfather_name', value($p1->grandfather_name));
                            $MortgageDeedThird->setValue('p1grandfather_relation', value($p1->grandfather_relation));
                            $MortgageDeedThird->setValue('p1father_name', value($p1->father_name));
                            $MortgageDeedThird->setValue('p1father_relation', value($p1->father_relation));
                            if ($p1->spouse_name) {
                                $spouse_name1 = $p1->spouse_name;
                            } else {
                                $spouse_name1 = null;
                            }
                            $MortgageDeedThird->setValue('p1spouse_name', value($spouse_name1));
                            $MortgageDeedThird->setValue('dp1spouse_name', value($spouse_name1));
                            $MortgageDeedThird->setValue('p1district', value(District::find($p1->district_id)->name));
                            $MortgageDeedThird->setValue('p1localbody', value(LocalBodies::find($p1->local_bodies_id)->name));
                            $MortgageDeedThird->setValue('p1bodytype', value(LocalBodies::find($p1->local_bodies_id)->body_type));
                            $MortgageDeedThird->setValue('p1wardno', value($p1->wardno));

                            $p1address = District::find($p1->district_id)->name . ' lhNnf ' . LocalBodies::find($p1->local_bodies_id)->name . ' ' . LocalBodies::find($p1->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $p1->wardno;
                            $MortgageDeedThird->setValue('p1address', value($p1address));

                            $age1 = $cyear - $p1->dob_year;

                            $MortgageDeedThird->setValue('p1age', value($age1));
                            $g1 = $p1->gender;
                            if ($g1 == 1) {
                                $gender1 = 'sf]';
                                $male1 = 'k\"?if';
                            } else {
                                $gender1 = 'sL';
                                $male1 = 'dlxnf';
                            }
                            $MortgageDeedThird->setValue('p1gender', value($gender1));
                            $MortgageDeedThird->setValue('p1male', value($male1));
                            $MortgageDeedThird->setValue('dp1nepali_name', value($p1->nepali_name));
                            $MortgageDeedThird->setValue('p1english_name', value($p1->english_name));
                            $MortgageDeedThird->setValue('p1citizenship_number', value($p1->citizenship_number));
                            $issued_date1 = $p1->issued_year . '÷' . $p1->issued_month . '÷' . $p1->issued_day;
                            $pdob1 = $p1->dob_year . '÷' . $p1->dob_month . '÷' . $p1->dob_day;
                            $MortgageDeedThird->setValue('p1issued_date', value($issued_date1));
                            $MortgageDeedThird->setValue('p1dob', value($pdob1));
                            $MortgageDeedThird->setValue('p1issued_district', value(District::find($p1->issued_district)->name));

//property 2
                            $MortgageDeedThird->setValue('p2phone', value($p2->phone));
                            $MortgageDeedThird->setValue('p2grandfather_name', value($p2->grandfather_name));
                            $MortgageDeedThird->setValue('p2grandfather_relation', value($p2->grandfather_relation));
                            $MortgageDeedThird->setValue('p2father_name', value($p2->father_name));
                            $MortgageDeedThird->setValue('p2father_relation', value($p2->father_relation));
                            if ($p2->spouse_name) {
                                $spouse_name2 = $p2->spouse_name;

                            } else {
                                $spouse_name2 = null;

                            }
                            $MortgageDeedThird->setValue('p2spouse_name', value($spouse_name2));
                            $MortgageDeedThird->setValue('dp2spouse_name', value($spouse_name2));
                            $MortgageDeedThird->setValue('p2district', value(District::find($p2->district_id)->name));
                            $MortgageDeedThird->setValue('p2localbody', value(LocalBodies::find($p2->local_bodies_id)->name));
                            $MortgageDeedThird->setValue('p2bodytype', value(LocalBodies::find($p2->local_bodies_id)->body_type));
                            $MortgageDeedThird->setValue('p2wardno', value($p2->wardno));
                            $p2address = District::find($p2->district_id)->name . ' lhNnf ' . LocalBodies::find($p2->local_bodies_id)->name . ' ' . LocalBodies::find($p2->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $p2->wardno;
                            $MortgageDeedThird->setValue('p2address', value($p2address));
                            $age2 = $cyear - $p2->dob_year;

                            $MortgageDeedThird->setValue('p2age', value($age2));
                            $g2 = $p2->gender;
                            if ($g2 == 1) {
                                $gender2 = 'sf]';
                                $male2 = 'k\"?if';
                            } else {
                                $gender2 = 'sL';
                                $male2 = 'dlxnf';
                            }

                            $MortgageDeedThird->setValue('p2gender', value($gender2));
                            $MortgageDeedThird->setValue('p2male', value($male2));
                            $MortgageDeedThird->setValue('dp2nepali_name', value($p2->nepali_name));
                            $MortgageDeedThird->setValue('p2english_name', value($p2->english_name));
                            $MortgageDeedThird->setValue('p2citizenship_number', value($p2->citizenship_number));
                            $issued_date2 = $p2->issued_year . '÷' . $p2->issued_month . '÷' . $p2->issued_day;
                            $pdob2 = $p2->dob_year . '÷' . $p2->dob_month . '÷' . $p2->dob_day;
                            $MortgageDeedThird->setValue('p2issued_date', value($issued_date2));
                            $MortgageDeedThird->setValue('p2dob', value($pdob2));
                            $MortgageDeedThird->setValue('p2issued_district', value(District::find($p2->issued_district)->name));

//property3
                            $MortgageDeedThird->setValue('p3grandfather_name', value($p3->grandfather_name));
                            $MortgageDeedThird->setValue('p3phone', value($p3->phone));
                            $MortgageDeedThird->setValue('p3grandfather_relation', value($p3->grandfather_relation));
                            $MortgageDeedThird->setValue('p3father_name', value($p3->father_name));
                            $MortgageDeedThird->setValue('p3father_relation', value($p3->father_relation));
                            if ($p3->spouse_name) {
                                $spouse_name3 = $p3->spouse_name;
                            } else {
                                $spouse_name3 = null;
                            }
                            $MortgageDeedThird->setValue('p3spouse_name', value($spouse_name3));
                            $MortgageDeedThird->setValue('dp3spouse_name', value($spouse_name3));
                            $MortgageDeedThird->setValue('p3district', value(District::find(District::find($p3->district_id)->name)));
                            $MortgageDeedThird->setValue('p3localbody', value(LocalBodies::find(LocalBodies::find($p3->local_bodies_id)->name)));
                            $MortgageDeedThird->setValue('p3bodytype', value(LocalBodies::find(LocalBodies::find($p3->local_bodies_id)->body_type)));
                            $MortgageDeedThird->setValue('p3wardno', value($p3->wardno));

                            $p3address = District::find($p3->district_id)->name . ' lhNnf ' . LocalBodies::find($p3->local_bodies_id)->name . ' ' . LocalBodies::find($p3->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $p3->wardno;
                            $MortgageDeedThird->setValue('p3address', value($p3address));
                            $age3 = $cyear - $p3->dob_year;
                            $MortgageDeedThird->setValue('p3age', value($age3));

                            $g3 = $p3->gender;
                            if ($g3 == 1) {
                                $gender3 = 'sf]';
                                $male3 = 'k\"?if';
                            } else {
                                $gender3 = 'sL';
                                $male3 = 'dlxnf';
                            }
                            $MortgageDeedThird->setValue('p3gender', value($gender3));
                            $MortgageDeedThird->setValue('p3male', value($male3));
                            $MortgageDeedThird->setValue('pnepali_name', value($p1->nepali_name . ' –1, ' . $p2->nepali_name . ' –1 / ' . $p3->nepali_name . ' –1 ;d]t hgf tLg ;+o"Qm'));
                            $MortgageDeedThird->setValue('dp3nepali_name', value($p3->nepali_name));
                            $MortgageDeedThird->setValue('p3english_name', value($p3->english_name));
                            $MortgageDeedThird->setValue('p3citizenship_number', value($p3->citizenship_number));
                            $issued_date3 = $p3->issued_year . '÷' . $p3->issued_month . '÷' . $p3->issued_day;
                            $pdob3 = $p3->dob_year . '÷' . $p3->dob_month . '÷' . $p3->dob_day;
                            $MortgageDeedThird->setValue('p3issued_date', value($issued_date3));
                            $MortgageDeedThird->setValue('p3dob', value($pdob3));
                            $MortgageDeedThird->setValue('p3issued_district', value(District::find($p3->issued_district)->name));


                            $MortgageDeedThird->cloneRow('llocalbody', $ld);
                            $i = 1;
                            foreach ($jointp as $l) {
                                if ($m == $l->malpot) {
                                    $n = $i++;
                                    $MortgageDeedThird->setValue('ldistrict', value(District::find($l->district_id)->name));
                                    $MortgageDeedThird->setValue('lprovince', value(Province::find(District::find($l->district_id)->province_id))->name);
                                    $MortgageDeedThird->setValue('llocalbody#' . $n, LocalBodies::find($l->local_bodies_id)->name);
                                    $MortgageDeedThird->setValue('lwardno#' . $n, $l->wardno);
                                    $MortgageDeedThird->setValue('sheetno#' . $n, $l->sheet_no);
                                    $MortgageDeedThird->setValue('kittano#' . $n, $l->kitta_no);
                                    $MortgageDeedThird->setValue('area#' . $n, $l->area);
                                    $MortgageDeedThird->setValue('remarks#' . $n, $l->remarks);
                                }
                            }
                            $MortgageDeedThird->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Mortgage_Deed_Third_Joint3_malpot_' . $mlp++ . '.docx'));
                        }
                    }
                }
            }
        }
    }

    private function old_mortgage_deed_third_joint3($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);
        $joint = PersonalBorrower::find($bid)->personal_joint_land()->first();
        if ($joint) {
            //File name

            $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {

                $borrower = PersonalBorrower::find($bid);
                $joint = PersonalBorrower::find($bid)->personal_joint_land()->first();
                if ($joint) {
                    $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
                } else {
                    $jointp = [];
                }

                if ($joint->joint1 && $joint->joint2 && $joint->joint3) {
                    $p1 = PersonalPropertyOwner::find($joint->joint1);
                    $p2 = PersonalPropertyOwner::find($joint->joint2);
                    $p3 = PersonalPropertyOwner::find($joint->joint3);
                }
                $malpot = [];
                foreach ($jointp as $land) {
                    $malpot[] = $land->malpot;
                }

                $malpot = array_unique($malpot);
                $mlp = 1;
                foreach ($malpot as $m) {
                    $ld = 0;
                    foreach ($jointp as $l) {
                        if ($m == $l->malpot) {
                            $ld = $ld + 1;
                        }
                    }

                    $malpotland = JointLand::where([
                        ['joint_id', $joint->id],
                        ['malpot', $m]
                    ])->first();

                    if ($malpotland) {

                        if (!$ld == 0) {
                            //mortgage deed
                            $MortgageDeedThird = new TemplateProcessor(storage_path('document/personal/old mortgage deed joint 3.docx'));
                            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                            $MortgageDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                            $dob = $borrower->dob_year . '÷' . $borrower->dob_month . '÷' . $borrower->dob_day;
                            $MortgageDeedThird->setValue('offerletterdate', value($offerletterdate));
                            $MortgageDeedThird->setValue('amount', value($loan->loan_amount));
                            $MortgageDeedThird->setValue('dob', value($dob));
                            $MortgageDeedThird->setValue('words', value($loan->loan_amount_words));
                            $MortgageDeedThird->setValue('grandfather_name', value($borrower->grandfather_name));
                            $MortgageDeedThird->setValue('phone', value($borrower->phone));
                            $MortgageDeedThird->setValue('grandfather_relation', value($borrower->grandfather_relation));
                            $MortgageDeedThird->setValue('father_name', value($borrower->father_name));
                            $MortgageDeedThird->setValue('father_relation', value($borrower->father_relation));
                            if ($borrower->spouse_name) {
                                $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                                $MortgageDeedThird->setValue('spouse_name', value($spouse_name));
                                $dspouse_name = $borrower->spouse_name;
                                $MortgageDeedThird->setValue('dspouse_name', value($dspouse_name));
                            } else {
                                $spouse_name = null;
                                $MortgageDeedThird->setValue('spouse_name', value($spouse_name));
                                $MortgageDeedThird->setValue('dspouse_name', value($spouse_name));
                            }
                            $MortgageDeedThird->setValue('district', value(District::find($borrower->district_id)->name));
                            $MortgageDeedThird->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
                            $MortgageDeedThird->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
                            $MortgageDeedThird->setValue('wardno', value($borrower->wardno));

                            $MortgageDeedThird->setValue('age', value($cyear - ($borrower->dob_year)));
                            $g = $borrower->gender;
                            if ($g == 1) {
                                $gender = 'sf]';
                                $male = 'k\"?if';
                            } else {
                                $gender = 'sL';
                                $male = 'dlxnf';
                            }
                            $MortgageDeedThird->setValue('gender', value($gender));
                            $MortgageDeedThird->setValue('male', value($male));
                            $MortgageDeedThird->setValue('nepali_name', value($borrower->nepali_name));
                            $MortgageDeedThird->setValue('dnepali_name', value($borrower->nepali_name));
                            $MortgageDeedThird->setValue('english_name', value($borrower->english_name));
                            $MortgageDeedThird->setValue('citizenship_number', value($borrower->citizenship_number));
                            $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                            $MortgageDeedThird->setValue('issued_date', value($issued_date));
                            $MortgageDeedThird->setValue('issued_district', value(District::find($borrower->issued_district)->name));

//for property owner
//                Property Owner Details
//property 1

                            if ($p1->spouse_name) {
                                $p1spouse_name = ' ' . $p1->spouse_name . 'sf] ' . $p1->spouse_relation . null;
                            } else {
                                $p1spouse_name = null;
                            }
                            $g = $p1->gender;
                            if ($g == 1) {
                                $p1gender = 'sf]';
                                $p1male = 'k\"?if';
                            } else {
                                $p1gender = 'sL';
                                $p1male = 'dlxnf';
                            }
                            $p1issued_date = $p1->issued_year . '÷' . $p1->issued_month . '÷' . $p1->issued_day;
                            if ($p2->spouse_name) {
                                $p2spouse_name = ' ' . $p2->spouse_name . 'sf] ' . $p2->spouse_relation . null;
                            } else {
                                $p2spouse_name = null;
                            }
                            $g = $p2->gender;
                            if ($g == 1) {
                                $p2gender = 'sf]';
                                $p2male = 'k\"?if';
                            } else {
                                $p2gender = 'sL';
                                $p2male = 'dlxnf';
                            }
                            $p2issued_date = $p2->issued_year . '÷' . $p2->issued_month . '÷' . $p2->issued_day;
                            if ($p3->spouse_name) {
                                $p3spouse_name = ' ' . $p3->spouse_name . 'sf] ' . $p3->spouse_relation . null;
                            } else {
                                $p3spouse_name = null;
                            }
                            $g = $p3->gender;
                            if ($g == 1) {
                                $p3gender = 'sf]';
                                $p3male = 'k\"?if';
                            } else {
                                $p3gender = 'sL';
                                $p3male = 'dlxnf';
                            }
                            $p3issued_date = $p3->issued_year . '÷' . $p3->issued_month . '÷' . $p3->issued_day;


                            $property_owner = $p1->grandfather_name . 'sf] ' . $p1->grandfather_relation . ' ' .
                                $p1->father_name . 'sf] ' . $p1->father_relation . $p1spouse_name . ' ' . District::find($p1->district_id)->name . ' lhNnf ' . LocalBodies::find($p1->local_bodies_id)->name . ' ' . LocalBodies::find($p1->local_bodies_id)->body_type .
                                ' j*f g+=' . $p1->wardno . ' a:g] aif{ ' . ($cyear - ($p1->dob_year)) . ' ' . $p1gender . ' ' . $p1->nepali_name . ' -gf=k|=k=g+=' . $p1->citizenship_number . ' hf/L ldlt ' . $p1issued_date . ' lhNnf ' . District::find($p1->issued_district)->name . '_ —1' . ', ' . $p2->grandfather_name . 'sf] ' . $p2->grandfather_relation . ' ' .
                                $p2->father_name . 'sf] ' . $p2->father_relation . $p2spouse_name . ' ' . District::find($p2->district_id)->name . ' lhNnf ' . LocalBodies::find($p2->local_bodies_id)->name . ' ' . LocalBodies::find($p2->local_bodies_id)->body_type .
                                ' j*f g+=' . $p2->wardno . ' a:g] aif{ ' . ($cyear - ($p2->dob_year)) . ' ' . $p2gender . ' ' . $p2->nepali_name . ' -gf=k|=k=g+=' . $p2->citizenship_number . ' hf/L ldlt ' . $p2issued_date . ' lhNnf ' . District::find($p2->issued_district)->name . '_ —1 / ' . $p3->grandfather_name . 'sf] ' . $p3->grandfather_relation . ' ' .
                                $p3->father_name . 'sf] ' . $p3->father_relation . $p3spouse_name . ' ' . District::find($p3->district_id)->name . ' lhNnf ' . LocalBodies::find($p3->local_bodies_id)->name . ' ' . LocalBodies::find($p3->local_bodies_id)->body_type . ' j*f g+=' . $p3->wardno . ' a:g] aif{ ' . ($cyear - ($p3->dob_year)) . ' ' . $p3gender . ' ' . $p3->nepali_name . ' -gf=k|=k=g+=' . $p3->citizenship_number . ' hf/L ldlt ' . $p3issued_date . ' lhNnf ' . District::find($p3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
                            $MortgageDeedThird->setValue('property_owner', value($property_owner));

                            $MortgageDeedThird->cloneRow('llocalbody', $ld);
                            $i = 1;
                            foreach ($jointp as $l) {
                                if ($m == $l->malpot) {
                                    $n = $i++;
                                    $MortgageDeedThird->setValue('lsn#' . $n, $n);
                                    $MortgageDeedThird->setValue('ldistrict#' . $n, District::find($l->district_id)->name);
                                    $MortgageDeedThird->setValue('llocalbody#' . $n, LocalBodies::find($l->local_bodies_id)->name);
                                    $MortgageDeedThird->setValue('lwardno#' . $n, $l->wardno);
                                    $MortgageDeedThird->setValue('sheetno#' . $n, $l->sheet_no);
                                    $MortgageDeedThird->setValue('kittano#' . $n, $l->kitta_no);
                                    $MortgageDeedThird->setValue('area#' . $n, $l->area);
                                    $MortgageDeedThird->setValue('remarks#' . $n, $l->remarks);
                                }
                            }
                            $MortgageDeedThird->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Old Mortgage_Deed_Third_Joint3_malpot_' . $mlp++ . '.docx'));
                        }
                    }
                }
            }
        }
    }

    private function mortgage_deed_self($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name

        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {

            $borrower = PersonalBorrower::find($bid);
            $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();
            if ($propertyOwner) {
                $landowner = [];
                $malpot = [];
                foreach ($borrower->personalborrowerpersonalland as $land) {
                    if ($propertyOwner->id == $land->property_owner_id) {
                        $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
                        $malpot[] = $land->malpot;
                    }
                }
                $personal_land_owner = array_unique($landowner);
                $malpot = array_unique($malpot);

                $mlp = 1;
                foreach ($malpot as $m) {
                    foreach ($personal_land_owner as $p) {

                        $ld = 0;
                        foreach ($borrower->personalborrowerpersonalland as $l) {
                            if ($l->property_owner_id == $p->id && $m == $l->malpot) {
                                $ld = $ld + 1;
                            }
                        }

                        $malpotland = PersonalLand::where([
                            ['property_owner_id', $p->id],
                            ['malpot', $m]
                        ])->first();

                        if ($malpotland) {
                            //mortgage deed
                            $MortgageDeedSelf = new TemplateProcessor(storage_path('document/personal/Mortgage Deed Self.docx'));
                            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                            $dob = $borrower->dob_year . '÷' . $borrower->dob_month . '÷' . $borrower->dob_day;
                            $MortgageDeedSelf->setValue('branch', value(Branch::find($loan->branch_id)->location));
                            $MortgageDeedSelf->setValue('offerletterdate', value($offerletterdate));
                            $MortgageDeedSelf->setValue('amount', value($loan->loan_amount));
                            $MortgageDeedSelf->setValue('words', value($loan->loan_amount_words));
                            $MortgageDeedSelf->setValue('grandfather_name', value($borrower->grandfather_name));
                            $MortgageDeedSelf->setValue('phone', value($borrower->phone));
                            $MortgageDeedSelf->setValue('grandfather_relation', value($borrower->grandfather_relation));
                            $MortgageDeedSelf->setValue('father_name', value($borrower->father_name));
                            $MortgageDeedSelf->setValue('dob', value($dob));
                            $MortgageDeedSelf->setValue('father_relation', value($borrower->father_relation));
                            if ($borrower->spouse_name) {
                                $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                                $MortgageDeedSelf->setValue('spouse_name', value($spouse_name));
                                $dspouse_name = $borrower->spouse_name;
                                $MortgageDeedSelf->setValue('dspouse_name', value($dspouse_name));
                            } else {
                                $spouse_name = null;
                                $MortgageDeedSelf->setValue('spouse_name', value($spouse_name));
                                $MortgageDeedSelf->setValue('dspouse_name', value($spouse_name));
                            }
                            $MortgageDeedSelf->setValue('district', value(District::find($borrower->district_id)->name));
                            $MortgageDeedSelf->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
                            $MortgageDeedSelf->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
                            $MortgageDeedSelf->setValue('wardno', value($borrower->wardno));

                            $MortgageDeedSelf->setValue('age', value($cyear - ($borrower->dob_year)));
                            $g = $borrower->gender;
                            if ($g == 1) {
                                $gender = 'sf]';
                                $male = 'k\"?if';
                            } else {
                                $gender = 'sL';
                                $male = 'dlxnf';
                            }
                            $MortgageDeedSelf->setValue('gender', value($gender));
                            $MortgageDeedSelf->setValue('male', value($male));
                            $MortgageDeedSelf->setValue('nepali_name', value($borrower->nepali_name));
                            $MortgageDeedSelf->setValue('dnepali_name', value($borrower->nepali_name));
                            $MortgageDeedSelf->setValue('english_name', value($borrower->english_name));
                            $MortgageDeedSelf->setValue('citizenship_number', value($borrower->citizenship_number));
                            $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                            $MortgageDeedSelf->setValue('issued_date', value($issued_date));
                            $MortgageDeedSelf->setValue('issued_district', value(District::find($borrower->issued_district)->name));


                            $MortgageDeedSelf->cloneRow('llocalbody', $ld);
                            $i = 1;
                            foreach ($borrower->personalborrowerpersonalland as $l) {

                                if ($l->property_owner_id == $p->id && $m == $l->malpot) {
                                    $MortgageDeedSelf->setValue('ldistrict', value(District::find($l->district_id)->name));
                                    $MortgageDeedSelf->setValue('lprovince', value(Province::find(District::find($l->district_id)->province_id))->name);
                                    $n = $i++;
                                    $MortgageDeedSelf->setValue('landsn#' . $n, $n);

                                    $MortgageDeedSelf->setValue('llocalbody#' . $n, LocalBodies::find($l->local_bodies_id)->name);
                                    $MortgageDeedSelf->setValue('lwardno#' . $n, $l->wardno);
                                    $MortgageDeedSelf->setValue('sheetno#' . $n, $l->sheet_no);
                                    $MortgageDeedSelf->setValue('kittano#' . $n, $l->kitta_no);
                                    $MortgageDeedSelf->setValue('area#' . $n, $l->area);
                                    $MortgageDeedSelf->setValue('remarks#' . $n, $l->remarks);


                                }
                            }
                            $MortgageDeedSelf->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Mortgage_Deed_Self_malpot' . $mlp++ . '.docx'));
                        }
                    }
                }


            }


        }
    }

    private function old_mortgage_deed_self($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name

        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {

            $borrower = PersonalBorrower::find($bid);
            $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();

            if ($propertyOwner) {
                $landowner = [];
                $malpot = [];
                foreach ($borrower->personalborrowerpersonalland as $land) {
                    if ($propertyOwner->id == $land->property_owner_id) {
                        $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
                        $malpot[] = $land->malpot;
                    }
                }
                $personal_land_owner = array_unique($landowner);
                $malpot = array_unique($malpot);

                $mlp = 1;
                foreach ($malpot as $m) {
                    foreach ($personal_land_owner as $p) {

                        $ld = 0;
                        foreach ($borrower->personalborrowerpersonalland as $l) {
                            if ($l->property_owner_id == $p->id && $m == $l->malpot) {
                                $ld = $ld + 1;
                            }
                        }

                        $malpotland = PersonalLand::where([
                            ['property_owner_id', $p->id],
                            ['malpot', $m]
                        ])->first();

                        if ($malpotland) {
                            //mortgage deed
                            $MortgageDeedSelf = new TemplateProcessor(storage_path('document/personal/old mortgage deed self.docx'));
                            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                            $dob = $borrower->dob_year . '÷' . $borrower->dob_month . '÷' . $borrower->dob_day;
                            $MortgageDeedSelf->setValue('branch', value(Branch::find($loan->branch_id)->location));
                            $MortgageDeedSelf->setValue('offerletterdate', value($offerletterdate));
                            $MortgageDeedSelf->setValue('amount', value($loan->loan_amount));
                            $MortgageDeedSelf->setValue('words', value($loan->loan_amount_words));
                            $MortgageDeedSelf->setValue('grandfather_name', value($borrower->grandfather_name));
                            $MortgageDeedSelf->setValue('phone', value($borrower->phone));
                            $MortgageDeedSelf->setValue('grandfather_relation', value($borrower->grandfather_relation));
                            $MortgageDeedSelf->setValue('father_name', value($borrower->father_name));
                            $MortgageDeedSelf->setValue('dob', value($dob));
                            $MortgageDeedSelf->setValue('father_relation', value($borrower->father_relation));
                            if ($borrower->spouse_name) {
                                $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                                $MortgageDeedSelf->setValue('spouse_name', value($spouse_name));
                                $dspouse_name = $borrower->spouse_name;
                                $MortgageDeedSelf->setValue('dspouse_name', value($dspouse_name));
                            } else {
                                $spouse_name = null;
                                $MortgageDeedSelf->setValue('spouse_name', value($spouse_name));
                                $MortgageDeedSelf->setValue('dspouse_name', value($spouse_name));
                            }
                            $MortgageDeedSelf->setValue('district', value(District::find($borrower->district_id)->name));
                            $MortgageDeedSelf->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
                            $MortgageDeedSelf->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
                            $MortgageDeedSelf->setValue('wardno', value($borrower->wardno));

                            $MortgageDeedSelf->setValue('age', value($cyear - ($borrower->dob_year)));
                            $g = $borrower->gender;
                            if ($g == 1) {
                                $gender = 'sf]';
                                $male = 'k\"?if';
                            } else {
                                $gender = 'sL';
                                $male = 'dlxnf';
                            }
                            $MortgageDeedSelf->setValue('gender', value($gender));
                            $MortgageDeedSelf->setValue('male', value($male));
                            $MortgageDeedSelf->setValue('nepali_name', value($borrower->nepali_name));
                            $MortgageDeedSelf->setValue('dnepali_name', value($borrower->nepali_name));
                            $MortgageDeedSelf->setValue('english_name', value($borrower->english_name));
                            $MortgageDeedSelf->setValue('citizenship_number', value($borrower->citizenship_number));
                            $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                            $MortgageDeedSelf->setValue('issued_date', value($issued_date));
                            $MortgageDeedSelf->setValue('issued_district', value(District::find($borrower->issued_district)->name));


                            $MortgageDeedSelf->cloneRow('llocalbody', $ld);
                            $i = 1;
                            foreach ($borrower->personalborrowerpersonalland as $l) {

                                if ($l->property_owner_id == $p->id && $m == $l->malpot) {
                                    $n = $i++;
                                    $MortgageDeedSelf->setValue('lsn#' . $n, $n);
                                    $MortgageDeedSelf->setValue('ldistrict#' . $n, District::find($l->district_id)->name);
                                    $MortgageDeedSelf->setValue('llocalbody#' . $n, LocalBodies::find($l->local_bodies_id)->name);
                                    $MortgageDeedSelf->setValue('lwardno#' . $n, $l->wardno);
                                    $MortgageDeedSelf->setValue('sheetno#' . $n, $l->sheet_no);
                                    $MortgageDeedSelf->setValue('kittano#' . $n, $l->kitta_no);
                                    $MortgageDeedSelf->setValue('area#' . $n, $l->area);
                                    $MortgageDeedSelf->setValue('remarks#' . $n, $l->remarks);
                                }
                            }
                            $MortgageDeedSelf->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Old Mortgage_Deed_Self_malpot' . $mlp++ . '.docx'));
                        }
                    }
                }


            }


        }
    }

    private function mortgage_deed_third_corporate($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name

        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            $borrower = PersonalBorrower::find($bid);

            $landowner = [];
            $malpot = [];
            foreach ($borrower->personalborrowercorporateland as $land) {

                $landowner[] = CorporatePropertyOwner::find($land->property_owner_id);
                $malpot[] = $land->malpot;
            }
            $corporate_land_owner = array_unique($landowner);
            $malpot = array_unique($malpot);
            $mlp = 1;
            foreach ($malpot as $m) {
                foreach ($corporate_land_owner as $p) {

                    $ld = 0;
                    foreach ($borrower->personalborrowercorporateland as $l) {
                        if ($l->property_owner_id == $p->id && $m == $l->malpot) {
                            $ld = $ld + 1;
                        }
                    }

                    $malpotland = CorporateLand::where([
                        ['property_owner_id', $p->id],
                        ['malpot', $m]
                    ])->first();

                    if ($malpotland) {

                        if (!$ld == 0) {
                            //mortgage deed
                            $MortgageDeedThirdCorporate = new TemplateProcessor(storage_path('document/personal/Mortgage Deed Third Corporate.docx'));
                            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                            $MortgageDeedThirdCorporate->setValue('branch', value(Branch::find($loan->branch_id)->location));
                            $MortgageDeedThirdCorporate->setValue('offerletterdate', value($offerletterdate));
                            $dob = $borrower->dob_year . '÷' . $borrower->dob_month . '÷' . $borrower->dob_day;
                            $MortgageDeedThirdCorporate->setValue('dob', value($dob));
                            $MortgageDeedThirdCorporate->setValue('amount', value($loan->loan_amount));
                            $MortgageDeedThirdCorporate->setValue('words', value($loan->loan_amount_words));
                            $MortgageDeedThirdCorporate->setValue('grandfather_name', value($borrower->grandfather_name));
                            $MortgageDeedThirdCorporate->setValue('phone', value($borrower->phone));
                            $MortgageDeedThirdCorporate->setValue('grandfather_relation', value($borrower->grandfather_relation));
                            $MortgageDeedThirdCorporate->setValue('father_name', value($borrower->father_name));
                            $MortgageDeedThirdCorporate->setValue('father_relation', value($borrower->father_relation));
                            if ($borrower->spouse_name) {
                                $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                                $MortgageDeedThirdCorporate->setValue('spouse_name', value($spouse_name));
                                $dspouse_name = $borrower->spouse_name;
                                $MortgageDeedThirdCorporate->setValue('dspouse_name', value($dspouse_name));
                            } else {
                                $spouse_name = null;
                                $MortgageDeedThirdCorporate->setValue('spouse_name', value($spouse_name));
                                $MortgageDeedThirdCorporate->setValue('dspouse_name', value($spouse_name));
                            }
                            $MortgageDeedThirdCorporate->setValue('district', value(District::find($borrower->district_id)->name));
                            $MortgageDeedThirdCorporate->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
                            $MortgageDeedThirdCorporate->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
                            $MortgageDeedThirdCorporate->setValue('wardno', value($borrower->wardno));

                            $MortgageDeedThirdCorporate->setValue('age', value($cyear - ($borrower->dob_year)));
                            $g = $borrower->gender;
                            if ($g == 1) {
                                $gender = 'sf]';
                                $male = 'k\"?if';
                            } else {
                                $gender = 'sL';
                                $male = 'dlxnf';
                            }
                            $MortgageDeedThirdCorporate->setValue('gender', value($gender));
                            $MortgageDeedThirdCorporate->setValue('male', value($male));
                            $MortgageDeedThirdCorporate->setValue('nepali_name', value($borrower->nepali_name));
                            $MortgageDeedThirdCorporate->setValue('dnepali_name', value($borrower->nepali_name));
                            $MortgageDeedThirdCorporate->setValue('english_name', value($borrower->english_name));
                            $MortgageDeedThirdCorporate->setValue('citizenship_number', value($borrower->citizenship_number));
                            $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                            $MortgageDeedThirdCorporate->setValue('issued_date', value($issued_date));
                            $MortgageDeedThirdCorporate->setValue('issued_district', value(District::find($borrower->issued_district)->name));
//for property owner
//                Property Owner Details
                            $MortgageDeedThirdCorporate->setValue('pcministry', value(Ministry::find($p->ministry_id)->name));
                            $MortgageDeedThirdCorporate->setValue('pcphone', value($p->phone));
                            $MortgageDeedThirdCorporate->setValue('pcdepartment', value(Department::find($p->department_id)->name));
                            $MortgageDeedThirdCorporate->setValue('pcregistrationno', value($p->registration_number));
                            $reg_date = $p->reg_year . '÷' . $p->reg_month . '÷' . $p->reg_day;
                            $MortgageDeedThirdCorporate->setValue('pcregistrationdate', value($reg_date));
                            $MortgageDeedThirdCorporate->setValue('pcdistrict', value(District::find($p->district_id)->name));
                            $MortgageDeedThirdCorporate->setValue('pclocalbody', value(LocalBodies::find($p->local_bodies_id)->name));
                            $MortgageDeedThirdCorporate->setValue('pcbody_type', value(LocalBodies::find($p->local_bodies_id)->body_type));
                            $MortgageDeedThirdCorporate->setValue('pcwardno', value($p->wardno));
                            $MortgageDeedThirdCorporate->setValue('pcname', value($p->nepali_name));
                            $MortgageDeedThirdCorporate->setValue('pcnepali_name', value($p->nepali_name));
                            $MortgageDeedThirdCorporate->setValue('dpcnepali_name', value($p->nepali_name));
                            $MortgageDeedThirdCorporate->setValue('pcenglish_name', value($p->english_name));
                            $pg = AuthorizedPerson::find($p->authorized_person_id);
                            $MortgageDeedThirdCorporate->setValue('apost', value($pg->post));
                            $MortgageDeedThirdCorporate->setValue('agrandfather_name', value($pg->grandfather_name));
                            $MortgageDeedThirdCorporate->setValue('agrandfather_relation', value($pg->grandfather_relation));
                            $MortgageDeedThirdCorporate->setValue('afather_name', value($pg->father_name));
                            $MortgageDeedThirdCorporate->setValue('afather_relation', value($pg->father_relation));
                            if ($pg->spouse_name) {
                                $spouse_name = ' ' . $pg->spouse_name . 'sf] ' . $pg->spouse_relation;
                                $MortgageDeedThirdCorporate->setValue('aspouse_name', value($spouse_name));
                                $dspouse_name = $pg->spouse_name;
                                $MortgageDeedThirdCorporate->setValue('daspouse_name', value($dspouse_name));
                            } else {
                                $spouse_name = null;
                                $MortgageDeedThirdCorporate->setValue('aspouse_name', value($spouse_name));
                                $MortgageDeedThirdCorporate->setValue('daspouse_name', value($spouse_name));
                            }
                            $MortgageDeedThirdCorporate->setValue('adistrict', value(District::find($pg->district_id)->name));
                            $MortgageDeedThirdCorporate->setValue('alocalbody', value(LocalBodies::find($pg->local_bodies_id)->name));
                            $MortgageDeedThirdCorporate->setValue('abody_type', value(LocalBodies::find($pg->local_bodies_id)->body_type));
                            $MortgageDeedThirdCorporate->setValue('awardno', value($pg->wardno));
                            $MortgageDeedThirdCorporate->setValue('aage', value($cyear - ($pg->dob_year)));
                            $g = $pg->gender;
                            if ($g == 1) {
                                $gender = 'sf]';
                                $male = 'k\"?if';
                            } else {
                                $gender = 'sL';
                                $male = 'dlxnf';
                            }
                            $MortgageDeedThirdCorporate->setValue('agender', value($gender));
                            $MortgageDeedThirdCorporate->setValue('anepali_name', value($pg->nepali_name));
                            $MortgageDeedThirdCorporate->setValue('danepali_name', value($pg->nepali_name));
                            $MortgageDeedThirdCorporate->setValue('acitizenship_number', value($pg->citizenship_number));
                            $issued_date = $pg->issued_year . '÷' . $pg->issued_month . '÷' . $pg->issued_day;
                            $MortgageDeedThirdCorporate->setValue('aissued_date', value($issued_date));
                            $MortgageDeedThirdCorporate->setValue('aissued_district', value(District::find($pg->issued_district)->name));
                            $MortgageDeedThirdCorporate->cloneRow('llocalbody', $ld);
                            $i = 1;
                            foreach ($borrower->personalborrowercorporateland as $l) {
                                if ($l->property_owner_id == $p->id && $m == $l->malpot) {
                                    $n = $i++;
                                    $MortgageDeedThirdCorporate->setValue('ldistrict', value(District::find($l->district_id)->name));
                                    $MortgageDeedThirdCorporate->setValue('lprovince', value(Province::find(District::find($l->district_id)->province_id))->name);
                                    $MortgageDeedThirdCorporate->setValue('llocalbody#' . $n, LocalBodies::find($l->local_bodies_id)->name);
                                    $MortgageDeedThirdCorporate->setValue('lwardno#' . $n, $l->wardno);
                                    $MortgageDeedThirdCorporate->setValue('sheetno#' . $n, $l->sheet_no);
                                    $MortgageDeedThirdCorporate->setValue('kittano#' . $n, $l->kitta_no);
                                    $MortgageDeedThirdCorporate->setValue('area#' . $n, $l->area);
                                    $MortgageDeedThirdCorporate->setValue('remarks#' . $n, $l->remarks);
                                }
                            }
                            $MortgageDeedThirdCorporate->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Mortgage_Deed_Third_Corporate_malpot' . $mlp++ . '.docx'));
                        }
                    }
                }


            }


        }
    }

    private function old_mortgage_deed_third_corporate($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name

        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            $borrower = PersonalBorrower::find($bid);

            $landowner = [];
            $malpot = [];
            foreach ($borrower->personalborrowercorporateland as $land) {

                $landowner[] = CorporatePropertyOwner::find($land->property_owner_id);
                $malpot[] = $land->malpot;
            }
            $corporate_land_owner = array_unique($landowner);
            $malpot = array_unique($malpot);
            $mlp = 1;
            foreach ($malpot as $m) {
                foreach ($corporate_land_owner as $p) {

                    $ld = 0;
                    foreach ($borrower->personalborrowercorporateland as $l) {
                        if ($l->property_owner_id == $p->id && $m == $l->malpot) {
                            $ld = $ld + 1;
                        }
                    }

                    $malpotland = CorporateLand::where([
                        ['property_owner_id', $p->id],
                        ['malpot', $m]
                    ])->first();

                    if ($malpotland) {

                        if (!$ld == 0) {
                            //mortgage deed
                            $MortgageDeedThirdCorporate = new TemplateProcessor(storage_path('document/personal/old mortgage deed third corporate.docx'));
                            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                            $MortgageDeedThirdCorporate->setValue('branch', value(Branch::find($loan->branch_id)->location));
                            $MortgageDeedThirdCorporate->setValue('offerletterdate', value($offerletterdate));
                            $dob = $borrower->dob_year . '÷' . $borrower->dob_month . '÷' . $borrower->dob_day;
                            $MortgageDeedThirdCorporate->setValue('dob', value($dob));
                            $MortgageDeedThirdCorporate->setValue('amount', value($loan->loan_amount));
                            $MortgageDeedThirdCorporate->setValue('words', value($loan->loan_amount_words));
                            $MortgageDeedThirdCorporate->setValue('grandfather_name', value($borrower->grandfather_name));
                            $MortgageDeedThirdCorporate->setValue('phone', value($borrower->phone));
                            $MortgageDeedThirdCorporate->setValue('grandfather_relation', value($borrower->grandfather_relation));
                            $MortgageDeedThirdCorporate->setValue('father_name', value($borrower->father_name));
                            $MortgageDeedThirdCorporate->setValue('father_relation', value($borrower->father_relation));
                            if ($borrower->spouse_name) {
                                $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                                $MortgageDeedThirdCorporate->setValue('spouse_name', value($spouse_name));
                                $dspouse_name = $borrower->spouse_name;
                                $MortgageDeedThirdCorporate->setValue('dspouse_name', value($dspouse_name));
                            } else {
                                $spouse_name = null;
                                $MortgageDeedThirdCorporate->setValue('spouse_name', value($spouse_name));
                                $MortgageDeedThirdCorporate->setValue('dspouse_name', value($spouse_name));
                            }
                            $MortgageDeedThirdCorporate->setValue('district', value(District::find($borrower->district_id)->name));
                            $MortgageDeedThirdCorporate->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
                            $MortgageDeedThirdCorporate->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
                            $MortgageDeedThirdCorporate->setValue('wardno', value($borrower->wardno));

                            $MortgageDeedThirdCorporate->setValue('age', value($cyear - ($borrower->dob_year)));
                            $g = $borrower->gender;
                            if ($g == 1) {
                                $gender = 'sf]';
                                $male = 'k\"?if';
                            } else {
                                $gender = 'sL';
                                $male = 'dlxnf';
                            }
                            $MortgageDeedThirdCorporate->setValue('gender', value($gender));
                            $MortgageDeedThirdCorporate->setValue('male', value($male));
                            $MortgageDeedThirdCorporate->setValue('nepali_name', value($borrower->nepali_name));
                            $MortgageDeedThirdCorporate->setValue('dnepali_name', value($borrower->nepali_name));
                            $MortgageDeedThirdCorporate->setValue('english_name', value($borrower->english_name));
                            $MortgageDeedThirdCorporate->setValue('citizenship_number', value($borrower->citizenship_number));
                            $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                            $MortgageDeedThirdCorporate->setValue('issued_date', value($issued_date));
                            $MortgageDeedThirdCorporate->setValue('issued_district', value(District::find($borrower->issued_district)->name));
//for property owner
//                Property Owner Details
                            $MortgageDeedThirdCorporate->setValue('cministry', value(Ministry::find($p->ministry_id)->name));
                            $MortgageDeedThirdCorporate->setValue('cdepartment', value(Department::find($p->department_id)->name));
                            $MortgageDeedThirdCorporate->setValue('cregistrationno', value($p->registration_number));
                            $reg_date = $p->reg_year . '÷' . $p->reg_month . '÷' . $p->reg_day;
                            $MortgageDeedThirdCorporate->setValue('cregistrationdate', value($reg_date));
                            $MortgageDeedThirdCorporate->setValue('cdistrict', value(District::find($p->district_id)->name));
                            $MortgageDeedThirdCorporate->setValue('clocalbody', value(LocalBodies::find($p->local_bodies_id)->name));
                            $MortgageDeedThirdCorporate->setValue('cbodytype', value(LocalBodies::find($p->local_bodies_id)->body_type));
                            $MortgageDeedThirdCorporate->setValue('cwardno', value($p->wardno));
                            $MortgageDeedThirdCorporate->setValue('cname', value($p->nepali_name));

                            $pg = AuthorizedPerson::find($p->authorized_person_id);
                            $MortgageDeedThirdCorporate->setValue('gpost', value($pg->post));
                            $MortgageDeedThirdCorporate->setValue('ggrandfather_name', value($pg->grandfather_name));
                            $MortgageDeedThirdCorporate->setValue('ggrandfather_relation', value($pg->grandfather_relation));
                            $MortgageDeedThirdCorporate->setValue('gfather_name', value($pg->father_name));
                            $MortgageDeedThirdCorporate->setValue('gfather_relation', value($pg->father_relation));
                            if ($pg->spouse_name) {
                                $spouse_name = ' ' . $pg->spouse_name . 'sf] ' . $pg->spouse_relation;
                                $MortgageDeedThirdCorporate->setValue('gspouse_name', value($spouse_name));
                            } else {
                                $spouse_name = null;
                                $MortgageDeedThirdCorporate->setValue('gspouse_name', value($spouse_name));
                            }
                            $MortgageDeedThirdCorporate->setValue('gdistrict', value(District::find($pg->district_id)->name));
                            $MortgageDeedThirdCorporate->setValue('glocalbody', value(LocalBodies::find($pg->local_bodies_id)->name));
                            $MortgageDeedThirdCorporate->setValue('gbody_type', value(LocalBodies::find($pg->local_bodies_id)->body_type));
                            $MortgageDeedThirdCorporate->setValue('gwardno', value($pg->wardno));

                            $MortgageDeedThirdCorporate->setValue('gage', value($cyear - ($pg->dob_year)));
                            $g = $pg->gender;
                            if ($g == 1) {
                                $gender = 'sf]';
                                $male = 'k\"?if';
                            } else {
                                $gender = 'sL';
                                $male = 'dlxnf';
                            }

                            $MortgageDeedThirdCorporate->setValue('ggender', value($gender));
                            $MortgageDeedThirdCorporate->setValue('gnepali_name', value($pg->nepali_name));
                            $MortgageDeedThirdCorporate->setValue('gcitizenship_number', value($pg->citizenship_number));
                            $issued_date = $pg->issued_year . '÷' . $pg->issued_month . '÷' . $pg->issued_day;
                            $MortgageDeedThirdCorporate->setValue('gissued_date', value($issued_date));
                            $MortgageDeedThirdCorporate->setValue('gissued_district', value(District::find($pg->issued_district)->name));

                            $MortgageDeedThirdCorporate->cloneRow('llocalbody', $ld);
                            $i = 1;
                            foreach ($borrower->personalborrowercorporateland as $l) {
                                if ($l->property_owner_id == $p->id && $m == $l->malpot) {
                                    $n = $i++;
                                    $MortgageDeedThirdCorporate->setValue('lsn#' . $n, $n);
                                    $MortgageDeedThirdCorporate->setValue('ldistrict#' . $n, District::find($l->district_id)->name);
                                    $MortgageDeedThirdCorporate->setValue('llocalbody#' . $n, LocalBodies::find($l->local_bodies_id)->name);
                                    $MortgageDeedThirdCorporate->setValue('lwardno#' . $n, $l->wardno);
                                    $MortgageDeedThirdCorporate->setValue('sheetno#' . $n, $l->sheet_no);
                                    $MortgageDeedThirdCorporate->setValue('kittano#' . $n, $l->kitta_no);
                                    $MortgageDeedThirdCorporate->setValue('area#' . $n, $l->area);
                                    $MortgageDeedThirdCorporate->setValue('remarks#' . $n, $l->remarks);
                                }
                            }
                            $MortgageDeedThirdCorporate->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Old Mortgage_Deed_Third_Corporate_malpot' . $mlp++ . '.docx'));
                        }
                    }
                }


            }


        }
    }

    private function mortgage_deed_third($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name

        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {

            $borrower = PersonalBorrower::find($bid);
            $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();

            $landowner = [];
            $malpot = [];
            foreach ($borrower->personalborrowerpersonalland as $land) {

                if ($propertyOwner) {
                    if ($propertyOwner->id == $land->property_owner_id) {

                    } else {
                        $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
                        $malpot[] = $land->malpot;
                    }
                } else {
                    $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
                    $malpot[] = $land->malpot;
                }
            }
            $personal_land_owner = array_unique($landowner);
            $malpot = array_unique($malpot);
            $mlp = 1;
            foreach ($malpot as $m) {
                foreach ($personal_land_owner as $p) {

                    $ld = 0;
                    foreach ($borrower->personalborrowerpersonalland as $l) {
                        if ($l->property_owner_id == $p->id && $m == $l->malpot) {
                            $ld = $ld + 1;
                        }
                    }

                    $malpotland = PersonalLand::where([
                        ['property_owner_id', $p->id],
                        ['malpot', $m]
                    ])->first();

                    if ($malpotland) {

                        if (!$ld == 0) {


                            //mortgage deed
                            $MortgageDeedThird = new TemplateProcessor(storage_path('document/personal/Mortgage Deed Third.docx'));
                            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                            $MortgageDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                            $dob = $borrower->dob_year . '÷' . $borrower->dob_month . '÷' . $borrower->dob_day;
                            $MortgageDeedThird->setValue('offerletterdate', value($offerletterdate));
                            $MortgageDeedThird->setValue('amount', value($loan->loan_amount));
                            $MortgageDeedThird->setValue('dob', value($dob));
                            $MortgageDeedThird->setValue('words', value($loan->loan_amount_words));
                            $MortgageDeedThird->setValue('grandfather_name', value($borrower->grandfather_name));
                            $MortgageDeedThird->setValue('phone', value($borrower->phone));
                            $MortgageDeedThird->setValue('grandfather_relation', value($borrower->grandfather_relation));
                            $MortgageDeedThird->setValue('father_name', value($borrower->father_name));
                            $MortgageDeedThird->setValue('father_relation', value($borrower->father_relation));
                            if ($borrower->spouse_name) {
                                $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                                $MortgageDeedThird->setValue('spouse_name', value($spouse_name));
                                $dspouse_name = $borrower->spouse_name;
                                $MortgageDeedThird->setValue('dspouse_name', value($dspouse_name));
                            } else {
                                $spouse_name = null;
                                $MortgageDeedThird->setValue('spouse_name', value($spouse_name));
                                $MortgageDeedThird->setValue('dspouse_name', value($spouse_name));
                            }
                            $MortgageDeedThird->setValue('district', value(District::find($borrower->district_id)->name));
                            $MortgageDeedThird->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
                            $MortgageDeedThird->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
                            $MortgageDeedThird->setValue('wardno', value($borrower->wardno));


                            $MortgageDeedThird->setValue('age', value($cyear - ($borrower->dob_year)));
                            $g = $borrower->gender;
                            if ($g == 1) {
                                $gender = 'sf]';
                                $male = 'k\"?if';
                            } else {
                                $gender = 'sL';
                                $male = 'dlxnf';
                            }
                            $MortgageDeedThird->setValue('gender', value($gender));
                            $MortgageDeedThird->setValue('male', value($male));
                            $MortgageDeedThird->setValue('nepali_name', value($borrower->nepali_name));
                            $MortgageDeedThird->setValue('dnepali_name', value($borrower->nepali_name));
                            $MortgageDeedThird->setValue('english_name', value($borrower->english_name));
                            $MortgageDeedThird->setValue('citizenship_number', value($borrower->citizenship_number));
                            $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                            $MortgageDeedThird->setValue('issued_date', value($issued_date));
                            $MortgageDeedThird->setValue('issued_district', value(District::find($borrower->issued_district)->name));
//for property owner
//                Property Owner Details
                            $MortgageDeedThird->setValue('pgrandfather_name', value($p->grandfather_name));
                            $MortgageDeedThird->setValue('pphone', value($p->phone));
                            $MortgageDeedThird->setValue('pgrandfather_relation', value($p->grandfather_relation));
                            $MortgageDeedThird->setValue('pfather_name', value($p->father_name));
                            $MortgageDeedThird->setValue('pfather_relation', value($p->father_relation));
                            if ($p->spouse_name) {
                                $spouse_name = ' ' . $p->spouse_name . 'sf] ' . $p->spouse_relation;
                                $MortgageDeedThird->setValue('pspouse_name', value($spouse_name));
                                $dspouse_name = $p->spouse_name;
                                $MortgageDeedThird->setValue('dpspouse_name', value($dspouse_name));
                            } else {
                                $spouse_name = null;
                                $MortgageDeedThird->setValue('pspouse_name', value($spouse_name));
                                $MortgageDeedThird->setValue('dpspouse_name', value($spouse_name));
                            }
                            $MortgageDeedThird->setValue('pdistrict', value(District::find($p->district_id)->name));
                            $MortgageDeedThird->setValue('plocalbody', value(LocalBodies::find($p->local_bodies_id)->name));
                            $MortgageDeedThird->setValue('pbodytype', value(LocalBodies::find($p->local_bodies_id)->body_type));
                            $MortgageDeedThird->setValue('pwardno', value($p->wardno));

                            $MortgageDeedThird->setValue('page', value($cyear - ($p->dob_year)));
                            $g = $p->gender;
                            if ($g == 1) {
                                $gender = 'sf]';
                                $male = 'k\"?if';
                            } else {
                                $gender = 'sL';
                                $male = 'dlxnf';
                            }

                            $MortgageDeedThird->setValue('pgender', value($gender));
                            $MortgageDeedThird->setValue('pmale', value($male));
                            $MortgageDeedThird->setValue('pnepali_name', value($p->nepali_name));
                            $MortgageDeedThird->setValue('dpnepali_name', value($p->nepali_name));
                            $MortgageDeedThird->setValue('penglish_name', value($p->english_name));
                            $MortgageDeedThird->setValue('pcitizenship_number', value($p->citizenship_number));
                            $issued_date = $p->issued_year . '÷' . $p->issued_month . '÷' . $p->issued_day;
                            $pdob = $p->dob_year . '÷' . $p->dob_month . '÷' . $p->dob_day;
                            $MortgageDeedThird->setValue('pissued_date', value($issued_date));
                            $MortgageDeedThird->setValue('pdob', value($pdob));
                            $MortgageDeedThird->setValue('pissued_district', value(District::find($p->issued_district)->name));
                            $paddress = District::find($p->district_id)->name . ' lhNnf ' . LocalBodies::find($p->local_bodies_id)->name . ' ' . LocalBodies::find($p->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $p->wardno;
                            $MortgageDeedThird->setValue('paddress', value($paddress));


                            $MortgageDeedThird->cloneRow('llocalbody', $ld);
                            $i = 1;
                            foreach ($borrower->personalborrowerpersonalland as $l) {

                                if ($l->property_owner_id == $p->id && $m == $l->malpot) {

                                    $n = $i++;
                                    $MortgageDeedThird->setValue('landsn#' . $n, $n);
                                    $MortgageDeedThird->setValue('ldistrict', value(District::find($l->district_id)->name));
                                    $MortgageDeedThird->setValue('lprovince', value(Province::find(District::find($l->district_id)->province_id))->name);
                                    $MortgageDeedThird->setValue('llocalbody#' . $n, LocalBodies::find($l->local_bodies_id)->name);
                                    $MortgageDeedThird->setValue('lwardno#' . $n, $l->wardno);
                                    $MortgageDeedThird->setValue('sheetno#' . $n, $l->sheet_no);
                                    $MortgageDeedThird->setValue('kittano#' . $n, $l->kitta_no);
                                    $MortgageDeedThird->setValue('area#' . $n, $l->area);
                                    $MortgageDeedThird->setValue('remarks#' . $n, $l->remarks);


                                }
                            }
                            $MortgageDeedThird->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Mortgage_Deed_Third_malpot' . $mlp++ . '.docx'));
                        }
                    }
                }


            }


        }
    }

    private function old_mortgage_deed_third($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name

        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {

            $borrower = PersonalBorrower::find($bid);
            $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();

            $landowner = [];
            $malpot = [];
            foreach ($borrower->personalborrowerpersonalland as $land) {

                if ($propertyOwner) {
                    if ($propertyOwner->id == $land->property_owner_id) {

                    } else {
                        $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
                        $malpot[] = $land->malpot;
                    }
                } else {
                    $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
                    $malpot[] = $land->malpot;
                }
            }
            $personal_land_owner = array_unique($landowner);
            $malpot = array_unique($malpot);
            $mlp = 1;
            foreach ($malpot as $m) {
                foreach ($personal_land_owner as $p) {

                    $ld = 0;
                    foreach ($borrower->personalborrowerpersonalland as $l) {
                        if ($l->property_owner_id == $p->id && $m == $l->malpot) {
                            $ld = $ld + 1;
                        }
                    }

                    $malpotland = PersonalLand::where([
                        ['property_owner_id', $p->id],
                        ['malpot', $m]
                    ])->first();

                    if ($malpotland) {

                        if (!$ld == 0) {


                            //mortgage deed
                            $MortgageDeedThird = new TemplateProcessor(storage_path('document/personal/old mortgage deed third.docx'));
                            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                            $MortgageDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                            $dob = $borrower->dob_year . '÷' . $borrower->dob_month . '÷' . $borrower->dob_day;
                            $MortgageDeedThird->setValue('offerletterdate', value($offerletterdate));
                            $MortgageDeedThird->setValue('amount', value($loan->loan_amount));
                            $MortgageDeedThird->setValue('dob', value($dob));
                            $MortgageDeedThird->setValue('words', value($loan->loan_amount_words));
                            $MortgageDeedThird->setValue('grandfather_name', value($borrower->grandfather_name));
                            $MortgageDeedThird->setValue('phone', value($borrower->phone));
                            $MortgageDeedThird->setValue('grandfather_relation', value($borrower->grandfather_relation));
                            $MortgageDeedThird->setValue('father_name', value($borrower->father_name));
                            $MortgageDeedThird->setValue('father_relation', value($borrower->father_relation));
                            if ($borrower->spouse_name) {
                                $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                                $MortgageDeedThird->setValue('spouse_name', value($spouse_name));
                                $dspouse_name = $borrower->spouse_name;
                                $MortgageDeedThird->setValue('dspouse_name', value($dspouse_name));
                            } else {
                                $spouse_name = null;
                                $MortgageDeedThird->setValue('spouse_name', value($spouse_name));
                                $MortgageDeedThird->setValue('dspouse_name', value($spouse_name));
                            }
                            $MortgageDeedThird->setValue('district', value(District::find($borrower->district_id)->name));
                            $MortgageDeedThird->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
                            $MortgageDeedThird->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
                            $MortgageDeedThird->setValue('wardno', value($borrower->wardno));


                            $MortgageDeedThird->setValue('age', value($cyear - ($borrower->dob_year)));
                            $g = $borrower->gender;
                            if ($g == 1) {
                                $gender = 'sf]';
                                $male = 'k\"?if';
                            } else {
                                $gender = 'sL';
                                $male = 'dlxnf';
                            }
                            $MortgageDeedThird->setValue('gender', value($gender));
                            $MortgageDeedThird->setValue('male', value($male));
                            $MortgageDeedThird->setValue('nepali_name', value($borrower->nepali_name));
                            $MortgageDeedThird->setValue('dnepali_name', value($borrower->nepali_name));
                            $MortgageDeedThird->setValue('english_name', value($borrower->english_name));
                            $MortgageDeedThird->setValue('citizenship_number', value($borrower->citizenship_number));
                            $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                            $MortgageDeedThird->setValue('issued_date', value($issued_date));
                            $MortgageDeedThird->setValue('issued_district', value(District::find($borrower->issued_district)->name));
//for property owner
//                Property Owner Details
                            $MortgageDeedThird->setValue('pgrandfather_name', value($p->grandfather_name));
                            $MortgageDeedThird->setValue('pphone', value($p->phone));
                            $MortgageDeedThird->setValue('pgrandfather_relation', value($p->grandfather_relation));
                            $MortgageDeedThird->setValue('pfather_name', value($p->father_name));
                            $MortgageDeedThird->setValue('pfather_relation', value($p->father_relation));
                            if ($p->spouse_name) {
                                $spouse_name = ' ' . $p->spouse_name . 'sf] ' . $p->spouse_relation;
                                $MortgageDeedThird->setValue('pspouse_name', value($spouse_name));
                                $dspouse_name = $p->spouse_name;
                                $MortgageDeedThird->setValue('dpspouse_name', value($dspouse_name));
                            } else {
                                $spouse_name = null;
                                $MortgageDeedThird->setValue('pspouse_name', value($spouse_name));
                                $MortgageDeedThird->setValue('dpspouse_name', value($spouse_name));
                            }
                            $MortgageDeedThird->setValue('pdistrict', value(District::find($p->district_id)->name));
                            $MortgageDeedThird->setValue('plocalbody', value(LocalBodies::find($p->local_bodies_id)->name));
                            $MortgageDeedThird->setValue('pbody_type', value(LocalBodies::find($p->local_bodies_id)->body_type));
                            $MortgageDeedThird->setValue('pwardno', value($p->wardno));

                            $MortgageDeedThird->setValue('page', value($cyear - ($p->dob_year)));
                            $g = $p->gender;
                            if ($g == 1) {
                                $gender = 'sf]';
                                $male = 'k\"?if';
                            } else {
                                $gender = 'sL';
                                $male = 'dlxnf';
                            }

                            $MortgageDeedThird->setValue('pgender', value($gender));
                            $MortgageDeedThird->setValue('pmale', value($male));
                            $MortgageDeedThird->setValue('pnepali_name', value($p->nepali_name));
                            $MortgageDeedThird->setValue('dpnepali_name', value($p->nepali_name));
                            $MortgageDeedThird->setValue('penglish_name', value($p->english_name));
                            $MortgageDeedThird->setValue('pcitizenship_number', value($p->citizenship_number));
                            $issued_date = $p->issued_year . '÷' . $p->issued_month . '÷' . $p->issued_day;
                            $pdob = $p->dob_year . '÷' . $p->dob_month . '÷' . $p->dob_day;
                            $MortgageDeedThird->setValue('pissued_date', value($issued_date));
                            $MortgageDeedThird->setValue('pdob', value($pdob));
                            $MortgageDeedThird->setValue('pissued_district', value(District::find($p->issued_district)->name));
                            $paddress = District::find($p->district_id)->name . ' lhNnf ' . LocalBodies::find($p->local_bodies_id)->name . ' ' . LocalBodies::find($p->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $p->wardno;
                            $MortgageDeedThird->setValue('paddress', value($paddress));


                            $MortgageDeedThird->cloneRow('llocalbody', $ld);
                            $i = 1;
                            foreach ($borrower->personalborrowerpersonalland as $l) {

                                if ($l->property_owner_id == $p->id && $m == $l->malpot) {

                                    $n = $i++;
                                    $MortgageDeedThird->setValue('lsn#' . $n, $n);
                                    $MortgageDeedThird->setValue('ldistrict#' . $n, District::find($l->district_id)->name);
                                    $MortgageDeedThird->setValue('llocalbody#' . $n, LocalBodies::find($l->local_bodies_id)->name);
                                    $MortgageDeedThird->setValue('lwardno#' . $n, $l->wardno);
                                    $MortgageDeedThird->setValue('sheetno#' . $n, $l->sheet_no);
                                    $MortgageDeedThird->setValue('kittano#' . $n, $l->kitta_no);
                                    $MortgageDeedThird->setValue('area#' . $n, $l->area);
                                    $MortgageDeedThird->setValue('remarks#' . $n, $l->remarks);


                                }
                            }
                            $MortgageDeedThird->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Old Mortgage_Deed_Third_malpot' . $mlp++ . '.docx'));
                        }
                    }
                }


            }


        }
    }

    private function pledge_deed_self($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);
//dd($borrower);
//        $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();

 $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();
  


        foreach ($borrower->personalborrowerpersonalshare as $share) {
           
            if ($share->property_owner_id == $propertyOwner->id) {
                $hasshare = PersonalShare::find($share->id);
            }
        }
        if ($hasshare) {
            //File name
            $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $PledgeDeedSelf = new TemplateProcessor(storage_path('document/personal/Pledge Deed Self.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $PledgeDeedSelf->setValue('branch', value(Branch::find($loan->branch_id)->location));
                $PledgeDeedSelf->setValue('offerletterdate', value($offerletterdate));
                $PledgeDeedSelf->setValue('amount', value($loan->loan_amount));
                $PledgeDeedSelf->setValue('words', value($loan->loan_amount_words));
                $PledgeDeedSelf->setValue('grandfather_name', value($borrower->grandfather_name));
                $PledgeDeedSelf->setValue('grandfather_relation', value($borrower->grandfather_relation));
                $PledgeDeedSelf->setValue('father_name', value($borrower->father_name));
                $PledgeDeedSelf->setValue('father_relation', value($borrower->father_relation));
                if ($borrower->spouse_name) {
                    $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                    $PledgeDeedSelf->setValue('spouse_name', value($spouse_name));
                } else {
                    $spouse_name = null;
                    $PledgeDeedSelf->setValue('spouse_name', value($spouse_name));
                }
                $PledgeDeedSelf->setValue('district', value(District::find($borrower->district_id)->name));
                $PledgeDeedSelf->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
                $PledgeDeedSelf->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
                $PledgeDeedSelf->setValue('wardno', value($borrower->wardno));

                $PledgeDeedSelf->setValue('age', value($cyear - ($borrower->dob_year)));
                $g = $borrower->gender;
                if ($g == 1) {
                    $gender = 'sf]';
                    $male = 'k\"?if';
                } else {
                    $gender = 'sL';
                    $male = 'dlxnf';
                }
                $PledgeDeedSelf->setValue('gender', value($gender));
                $PledgeDeedSelf->setValue('nepali_name', value($borrower->nepali_name));
                $PledgeDeedSelf->setValue('citizenship_number', value($borrower->citizenship_number));
                $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                $PledgeDeedSelf->setValue('issued_date', value($issued_date));
                $PledgeDeedSelf->setValue('issued_district', value(District::find($borrower->issued_district)->name));
                $s = [];
                foreach ($borrower->personalborrowerpersonalshare as $share) {
                    if ($share->property_owner_id == $propertyOwner->id) {
                        $s[] = PersonalShare::find($share->id);
                    }
                }

                $count = count($s);
                $PledgeDeedSelf->cloneRow('sharesn', $count);
                $i = 1;
                foreach ($s as $share) {
                    $n = $i++;
                    $PledgeDeedSelf->setValue('sharesn#' . $n, $n);
                    $PledgeDeedSelf->setValue('ownername#' . $n, PersonalPropertyOwner::find($share->property_owner_id)->nepali_name);
                    $PledgeDeedSelf->setValue('dpid#' . $n, $share->dpid);
                    $PledgeDeedSelf->setValue('clientid#' . $n, $share->client_id);
                    $PledgeDeedSelf->setValue('kitta#' . $n, $share->kitta);
                    $PledgeDeedSelf->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                    $PledgeDeedSelf->setValue('share_type#' . $n, $share->share_type);
                }
            }
            $PledgeDeedSelf->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Pledge_Deed_Self.docx'));
        }

    }

    private function pledge_deed_third($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);
        $shareproperty = [];
        $owner = [];
        $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();
        foreach ($borrower->personalborrowerpersonalshare as $share) {
            if ($propertyOwner) {
                if ($share->property_owner_id == $propertyOwner->id) {
                } else {
                    $shareproperty[] = PersonalShare::find($share->id);
                    $owner[] = PersonalPropertyOwner::find($share->property_owner_id);
                }
            } else {
                $shareproperty[] = PersonalShare::find($share->id);
                $owner[] = PersonalPropertyOwner::find($share->property_owner_id);
            }
        }
        $shareowner = (array_unique($owner));

        foreach ($shareowner as $own) {
            //File name
            $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $PledgeDeedThird = new TemplateProcessor(storage_path('document/personal/Pledge Deed Third.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $PledgeDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                $PledgeDeedThird->setValue('offerletterdate', value($offerletterdate));
                $PledgeDeedThird->setValue('amount', value($loan->loan_amount));
                $PledgeDeedThird->setValue('words', value($loan->loan_amount_words));
                $PledgeDeedThird->setValue('grandfather_name', value($borrower->grandfather_name));
                $PledgeDeedThird->setValue('grandfather_relation', value($borrower->grandfather_relation));
                $PledgeDeedThird->setValue('father_name', value($borrower->father_name));
                $PledgeDeedThird->setValue('father_relation', value($borrower->father_relation));
                if ($borrower->spouse_name) {
                    $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                    $PledgeDeedThird->setValue('spouse_name', value($spouse_name));
                } else {
                    $spouse_name = null;
                    $PledgeDeedThird->setValue('spouse_name', value($spouse_name));
                }
                $PledgeDeedThird->setValue('district', value(District::find($borrower->district_id)->name));
                $PledgeDeedThird->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
                $PledgeDeedThird->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
                $PledgeDeedThird->setValue('wardno', value($borrower->wardno));
                $PledgeDeedThird->setValue('age', value($cyear - ($borrower->dob_year)));
                $g = $borrower->gender;
                if ($g == 1) {
                    $gender = 'sf]';
                    $male = 'k\"?if';
                } else {
                    $gender = 'sL';
                    $male = 'dlxnf';
                }
                $PledgeDeedThird->setValue('gender', value($gender));
                $PledgeDeedThird->setValue('nepali_name', value($borrower->nepali_name));
                $PledgeDeedThird->setValue('citizenship_number', value($borrower->citizenship_number));
                $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                $PledgeDeedThird->setValue('issued_date', value($issued_date));
                $PledgeDeedThird->setValue('issued_district', value(District::find($borrower->issued_district)->name));

//                Property Owner Details
                $PledgeDeedThird->setValue('pgrandfather_name', value($own->grandfather_name));
                $PledgeDeedThird->setValue('pgrandfather_relation', value($own->grandfather_relation));
                $PledgeDeedThird->setValue('pfather_name', value($own->father_name));
                $PledgeDeedThird->setValue('pfather_relation', value($own->father_relation));
                if ($own->spouse_name) {
                    $spouse_name = ' ' . $own->spouse_name . 'sf] ' . $own->spouse_relation;
                    $PledgeDeedThird->setValue('pspouse_name', value($spouse_name));
                } else {
                    $spouse_name = null;
                    $PledgeDeedThird->setValue('pspouse_name', value($spouse_name));
                }
                $PledgeDeedThird->setValue('pdistrict', value(District::find($own->district_id)->name));
                $PledgeDeedThird->setValue('plocalbody', value(LocalBodies::find($own->local_bodies_id)->name));
                $PledgeDeedThird->setValue('pbody_type', value(LocalBodies::find($own->local_bodies_id)->body_type));
                $PledgeDeedThird->setValue('pwardno', value($own->wardno));

                $PledgeDeedThird->setValue('page', value($cyear - ($own->dob_year)));
                $g = $own->gender;
                if ($g == 1) {
                    $gender = 'sf]';
                    $male = 'k\"?if';
                } else {
                    $gender = 'sL';
                    $male = 'dlxnf';
                }

                $PledgeDeedThird->setValue('pgender', value($gender));
                $PledgeDeedThird->setValue('pnepali_name', value($own->nepali_name));
                $PledgeDeedThird->setValue('pcitizenship_number', value($own->citizenship_number));
                $issued_date = $own->issued_year . '÷' . $own->issued_month . '÷' . $own->issued_day;
                $PledgeDeedThird->setValue('pissued_date', value($issued_date));
                $PledgeDeedThird->setValue('pissued_district', value(District::find($own->issued_district)->name));

                $sh = [];
                foreach ($shareproperty as $a) {
                    if ($own->id == $a->property_owner_id) {
                        $sh[] = PersonalShare::find($a->id);
                    }
                }


                $count = count($sh);

                $PledgeDeedThird->cloneRow('sharesn', $count);
                $c = 1;

                foreach ($sh as $share) {
                    $n = $c++;
                    $PledgeDeedThird->setValue('sharesn#' . $n, $n);
                    $PledgeDeedThird->setValue('ownername#' . $n, PersonalPropertyOwner::find($share->property_owner_id)->nepali_name);
                    $PledgeDeedThird->setValue('dpid#' . $n, $share->dpid);
                    $PledgeDeedThird->setValue('clientid#' . $n, $share->client_id);
                    $PledgeDeedThird->setValue('kitta#' . $n, $share->kitta);
                    $PledgeDeedThird->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                    $PledgeDeedThird->setValue('share_type#' . $n, $share->share_type);
                }
            }
            $PledgeDeedThird->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Pledge_Deed_Third_' . $own->english_name . '.docx'));
        }

    }

    private function pledge_deed_third_corporate($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);
        $shareproperty = [];
        $owner = [];
        foreach ($borrower->personalborrowercorporateshare as $share) {
            $shareproperty[] = CorporateShare::find($share->id);
            $owner[] = CorporatePropertyOwner::find($share->property_owner_id);
        }
        $shareowner = (array_unique($owner));

        foreach ($shareowner as $p) {
            //File name
            $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $PledgeDeedThird = new TemplateProcessor(storage_path('document/personal/Pledge Deed Third Corporate.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $PledgeDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                $PledgeDeedThird->setValue('offerletterdate', value($offerletterdate));
                $PledgeDeedThird->setValue('amount', value($loan->loan_amount));
                $PledgeDeedThird->setValue('words', value($loan->loan_amount_words));
                $PledgeDeedThird->setValue('grandfather_name', value($borrower->grandfather_name));
                $PledgeDeedThird->setValue('grandfather_relation', value($borrower->grandfather_relation));
                $PledgeDeedThird->setValue('father_name', value($borrower->father_name));
                $PledgeDeedThird->setValue('father_relation', value($borrower->father_relation));
                if ($borrower->spouse_name) {
                    $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                    $PledgeDeedThird->setValue('spouse_name', value($spouse_name));
                } else {
                    $spouse_name = null;
                    $PledgeDeedThird->setValue('spouse_name', value($spouse_name));
                }
                $PledgeDeedThird->setValue('district', value(District::find($borrower->district_id)->name));
                $PledgeDeedThird->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
                $PledgeDeedThird->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
                $PledgeDeedThird->setValue('wardno', value($borrower->wardno));
                $PledgeDeedThird->setValue('age', value($cyear - ($borrower->dob_year)));
                $g = $borrower->gender;
                if ($g == 1) {
                    $gender = 'sf]';
                    $male = 'k\"?if';
                } else {
                    $gender = 'sL';
                    $male = 'dlxnf';
                }
                $PledgeDeedThird->setValue('gender', value($gender));
                $PledgeDeedThird->setValue('nepali_name', value($borrower->nepali_name));
                $PledgeDeedThird->setValue('citizenship_number', value($borrower->citizenship_number));
                $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                $PledgeDeedThird->setValue('issued_date', value($issued_date));
                $PledgeDeedThird->setValue('issued_district', value(District::find($borrower->issued_district)->name));

//                Property Owner Details
                $PledgeDeedThird->setValue('cministry', value(Ministry::find($p->ministry_id)->name));
                $PledgeDeedThird->setValue('cdepartment', value(Department::find($p->department_id)->name));
                $PledgeDeedThird->setValue('cregistrationno', value($p->registration_number));
                $reg_date = $p->reg_year . '÷' . $p->reg_month . '÷' . $p->reg_day;
                $PledgeDeedThird->setValue('cregistrationdate', value($reg_date));
                $PledgeDeedThird->setValue('cdistrict', value(District::find($p->district_id)->name));
                $PledgeDeedThird->setValue('clocalbody', value(LocalBodies::find($p->local_bodies_id)->name));
                $PledgeDeedThird->setValue('cbodytype', value(LocalBodies::find($p->local_bodies_id)->body_type));
                $PledgeDeedThird->setValue('cwardno', value($p->wardno));
                $PledgeDeedThird->setValue('cname', value($p->nepali_name));

                $own = AuthorizedPerson::find($p->authorized_person_id);
                $PledgeDeedThird->setValue('gpost', value($own->post));
                $PledgeDeedThird->setValue('ggrandfather_name', value($own->grandfather_name));
                $PledgeDeedThird->setValue('ggrandfather_relation', value($own->grandfather_relation));
                $PledgeDeedThird->setValue('gfather_name', value($own->father_name));
                $PledgeDeedThird->setValue('gfather_relation', value($own->father_relation));
                if ($own->spouse_name) {
                    $spouse_name = ' ' . $own->spouse_name . 'sf] ' . $own->spouse_relation;
                    $PledgeDeedThird->setValue('gspouse_name', value($spouse_name));
                } else {
                    $spouse_name = null;
                    $PledgeDeedThird->setValue('gspouse_name', value($spouse_name));
                }
                $PledgeDeedThird->setValue('gdistrict', value(District::find($own->district_id)->name));
                $PledgeDeedThird->setValue('glocalbody', value(LocalBodies::find($own->local_bodies_id)->name));
                $PledgeDeedThird->setValue('gbody_type', value(LocalBodies::find($own->local_bodies_id)->body_type));
                $PledgeDeedThird->setValue('gwardno', value($own->wardno));

                $PledgeDeedThird->setValue('gage', value($cyear - ($own->dob_year)));
                $g = $own->gender;
                if ($g == 1) {
                    $gender = 'sf]';
                    $male = 'k\"?if';
                } else {
                    $gender = 'sL';
                    $male = 'dlxnf';
                }

                $PledgeDeedThird->setValue('ggender', value($gender));
                $PledgeDeedThird->setValue('gnepali_name', value($own->nepali_name));
                $PledgeDeedThird->setValue('gcitizenship_number', value($own->citizenship_number));
                $issued_date = $own->issued_year . '÷' . $own->issued_month . '÷' . $own->issued_day;
                $PledgeDeedThird->setValue('gissued_date', value($issued_date));
                $PledgeDeedThird->setValue('gissued_district', value(District::find($own->issued_district)->name));

                $sh = [];
                foreach ($shareproperty as $a) {
                    if ($p->id == $a->property_owner_id) {
                        $sh[] = CorporateShare::find($a->id);
                    }
                }


                $count = count($sh);

                $PledgeDeedThird->cloneRow('sharesn', $count);
                $c = 1;

                foreach ($sh as $share) {
                    $n = $c++;
                    $PledgeDeedThird->setValue('sharesn#' . $n, $n);
                    $PledgeDeedThird->setValue('ownername#' . $n, CorporatePropertyOwner::find($share->property_owner_id)->nepali_name);
                    $PledgeDeedThird->setValue('dpid#' . $n, $share->dpid);
                    $PledgeDeedThird->setValue('clientid#' . $n, $share->client_id);
                    $PledgeDeedThird->setValue('kitta#' . $n, $share->kitta);
                    $PledgeDeedThird->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                    $PledgeDeedThird->setValue('share_type#' . $n, $share->share_type);
                }
            }
            $PledgeDeedThird->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Pledge_Deed_Third_Corporate_' . $p->english_name . '.docx'));
        }

    }

    private function vehicle_transfer_letter_bank_favour($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        foreach (PersonalBorrower::find($bid)->personal_hire_purchase as $vehicle) {


            //File name
            $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $VehicleRegistrationLetter = new TemplateProcessor(storage_path('document/personal/Vehicle Transfer Letter Favour of Bank.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document

                if ($borrower->spouse_name) {
                    $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                    $VehicleRegistrationLetter->setValue('spouse_name', value($spouse_name));
                } else {
                    $spouse_name = null;
                    $VehicleRegistrationLetter->setValue('spouse_name', value($spouse_name));
                }
                $VehicleRegistrationLetter->setValue('district', value(District::find($borrower->district_id)->name));
                $VehicleRegistrationLetter->setValue('branch', value(Branch::find($loan->branch_id)->location));
                $VehicleRegistrationLetter->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
                $VehicleRegistrationLetter->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
                $VehicleRegistrationLetter->setValue('wardno', value($borrower->wardno));

                $VehicleRegistrationLetter->setValue('nepali_name', value($borrower->nepali_name));


                $VehicleRegistrationLetter->setValue('model_number', value($vehicle->model_number));
                $VehicleRegistrationLetter->setValue('registration_number', value($vehicle->registration_number));
                $VehicleRegistrationLetter->setValue('engine_number', value($vehicle->engine_number));
                $VehicleRegistrationLetter->setValue('chassis_number', value($vehicle->chassis_number));


            }
            $VehicleRegistrationLetter->saveAs(storage_path('results/' . $filename . '_Documents/' . '_Letter_For_Vehicle_Registration_' . $vehicle->model_number . '.docx'));
        }
    }

    private function vehicle_fukka_letter($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        foreach (PersonalBorrower::find($bid)->personal_hire_purchase as $vehicle) {


            //File name
            $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $VehicleRegistrationLetter = new TemplateProcessor(storage_path('document/personal/Vehicle Fukka Letter.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document

                if ($borrower->spouse_name) {
                    $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                    $VehicleRegistrationLetter->setValue('spouse_name', value($spouse_name));
                } else {
                    $spouse_name = null;
                    $VehicleRegistrationLetter->setValue('spouse_name', value($spouse_name));
                }
                $VehicleRegistrationLetter->setValue('district', value(District::find($borrower->district_id)->name));
                $VehicleRegistrationLetter->setValue('branch', value(Branch::find($loan->branch_id)->location));
                $VehicleRegistrationLetter->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
                $VehicleRegistrationLetter->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
                $VehicleRegistrationLetter->setValue('wardno', value($borrower->wardno));

                $VehicleRegistrationLetter->setValue('nepali_name', value($borrower->nepali_name));


                $VehicleRegistrationLetter->setValue('model_number', value($vehicle->model_number));
                $VehicleRegistrationLetter->setValue('registration_number', value($vehicle->registration_number));
                $VehicleRegistrationLetter->setValue('engine_number', value($vehicle->engine_number));
                $VehicleRegistrationLetter->setValue('chassis_number', value($vehicle->chassis_number));


            }
            $VehicleRegistrationLetter->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Vehicle_Fukka_Letter_' . $vehicle->model_number . '.docx'));
        }
    }

    private function manjurinama_of_hirepurchase($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);
        $guarantor = PersonalBorrower::find($bid)->personal_guarantor_borrower;
        //File name
        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {


            $manjurinama = new TemplateProcessor(storage_path('document/personal/Manjurinama_ of_Hire_Purchase.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $manjurinama->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $manjurinama->setValue('district', value(District::find($borrower->district_id)->name));
            $manjurinama->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
            $manjurinama->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
            $manjurinama->setValue('wardno', value($borrower->wardno));
            $manjurinama->setValue('nepali_name', value($borrower->nepali_name));
            $manjurinama->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Manjurinama_of_Hirepurchase.docx'));
        }
    }

    private function land_rokka_letter_self($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name

        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {

            $borrower = PersonalBorrower::find($bid);
            $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();
            if ($propertyOwner) {
                $landowner = [];
                $malpot = [];
                foreach ($borrower->personalborrowerpersonalland as $land) {
                    if ($propertyOwner->id == $land->property_owner_id) {
                        $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
                        $malpot[] = $land->malpot;
                    }
                }
                $personal_land_owner = array_unique($landowner);
                $malpot = array_unique($malpot);

                $mlp = 1;
                foreach ($malpot as $m) {
                    foreach ($personal_land_owner as $p) {

                        $ld = 0;
                        foreach ($borrower->personalborrowerpersonalland as $l) {
                            if ($l->property_owner_id == $p->id && $m == $l->malpot) {
                                $ld = $ld + 1;
                            }
                        }

                        $malpotland = PersonalLand::where([
                            ['property_owner_id', $p->id],
                            ['malpot', $m]
                        ])->first();

                        if ($malpotland) {


                            //mortgage deed
                            $MortgageDeedSelf = new TemplateProcessor(storage_path('document/personal/Rokka Letter Malpot Self.docx'));
                            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                            $MortgageDeedSelf->setValue('branch', value(Branch::find($loan->branch_id)->location));
                            $MortgageDeedSelf->setValue('offerletterdate', value($offerletterdate));
                            $MortgageDeedSelf->setValue('amount', value($loan->loan_amount));
                            $MortgageDeedSelf->setValue('words', value($loan->loan_amount_words));
                            $MortgageDeedSelf->setValue('grandfather_name', value($borrower->grandfather_name));
                            $MortgageDeedSelf->setValue('grandfather_relation', value($borrower->grandfather_relation));
                            $MortgageDeedSelf->setValue('father_name', value($borrower->father_name));
                            $MortgageDeedSelf->setValue('father_relation', value($borrower->father_relation));
                            if ($borrower->spouse_name) {
                                $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                                $MortgageDeedSelf->setValue('spouse_name', value($spouse_name));
                            } else {
                                $spouse_name = null;
                                $MortgageDeedSelf->setValue('spouse_name', value($spouse_name));
                            }
                            $MortgageDeedSelf->setValue('district', value(District::find($borrower->district_id)->name));
                            $MortgageDeedSelf->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
                            $MortgageDeedSelf->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
                            $MortgageDeedSelf->setValue('wardno', value($borrower->wardno));

                            $MortgageDeedSelf->setValue('age', value($cyear - ($borrower->dob_year)));
                            $g = $borrower->gender;
                            if ($g == 1) {
                                $gender = 'sf]';
                                $male = 'k\"?if';
                            } else {
                                $gender = 'sL';
                                $male = 'dlxnf';
                            }
                            $MortgageDeedSelf->setValue('gender', value($gender));
                            $MortgageDeedSelf->setValue('nepali_name', value($borrower->nepali_name));
                            $MortgageDeedSelf->setValue('citizenship_number', value($borrower->citizenship_number));
                            $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                            $MortgageDeedSelf->setValue('issued_date', value($issued_date));
                            $MortgageDeedSelf->setValue('issued_district', value(District::find($borrower->issued_district)->name));
                            $MortgageDeedSelf->setValue('lmalpot', value($m));

                            $MortgageDeedSelf->cloneRow('landsn', $ld);
                            $i = 1;
                            foreach ($borrower->personalborrowerpersonalland as $l) {

                                if ($l->property_owner_id == $p->id && $m == $l->malpot) {

                                    $n = $i++;
                                    $MortgageDeedSelf->setValue('landsn#' . $n, $n);
                                    $MortgageDeedSelf->setValue('ldistrict#' . $n, District::find($l->district_id)->name);
                                    $MortgageDeedSelf->setValue('ldistrict', value(District::find($l->district_id)->name));
                                    $MortgageDeedSelf->setValue('llocalbody#' . $n, LocalBodies::find($l->local_bodies_id)->name);
                                    $MortgageDeedSelf->setValue('lwardno#' . $n, $l->wardno);
                                    $MortgageDeedSelf->setValue('sheetno#' . $n, $l->sheet_no);
                                    $MortgageDeedSelf->setValue('kittano#' . $n, $l->kitta_no);
                                    $MortgageDeedSelf->setValue('area#' . $n, $l->area);
                                    $MortgageDeedSelf->setValue('remarks#' . $n, $l->remarks);


                                }
                            }
                            $MortgageDeedSelf->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Rokka_Letter_Self_malpot' . $mlp++ . '.docx'));
                        }
                    }
                }


            }


        }
    }

    private function land_rokka_letter_third($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name

        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {

            $borrower = PersonalBorrower::find($bid);
            $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();

            $landowner = [];
            $malpot = [];
            foreach ($borrower->personalborrowerpersonalland as $land) {

                if ($propertyOwner) {
                    if ($propertyOwner->id == $land->property_owner_id) {

                    } else {
                        $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
                        $malpot[] = $land->malpot;
                    }
                } else {
                    $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
                    $malpot[] = $land->malpot;
                }
            }
            $personal_land_owner = array_unique($landowner);
            $malpot = array_unique($malpot);
            $mlp = 1;
            foreach ($malpot as $m) {
                foreach ($personal_land_owner as $p) {

                    $ld = 0;
                    foreach ($borrower->personalborrowerpersonalland as $l) {
                        if ($l->property_owner_id == $p->id && $m == $l->malpot) {
                            $ld = $ld + 1;
                        }
                    }

                    $malpotland = PersonalLand::where([
                        ['property_owner_id', $p->id],
                        ['malpot', $m]
                    ])->first();

                    if ($malpotland) {

                        if (!$ld == 0) {


                            //mortgage deed
                            $MortgageDeedThird = new TemplateProcessor(storage_path('document/personal/Rokka Letter Malpot Person to Person.docx'));
                            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                            $MortgageDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                            $MortgageDeedThird->setValue('amount', value($loan->loan_amount));
                            $MortgageDeedThird->setValue('words', value($loan->loan_amount_words));

                            $MortgageDeedThird->setValue('nepali_name', value($borrower->nepali_name));

//                Property Owner Details
                            $MortgageDeedThird->setValue('pgrandfather_name', value($p->grandfather_name));
                            $MortgageDeedThird->setValue('pgrandfather_relation', value($p->grandfather_relation));
                            $MortgageDeedThird->setValue('pfather_name', value($p->father_name));
                            $MortgageDeedThird->setValue('pfather_relation', value($p->father_relation));
                            if ($p->spouse_name) {
                                $spouse_name = ' ' . $p->spouse_name . 'sf] ' . $p->spouse_relation;
                                $MortgageDeedThird->setValue('pspouse_name', value($spouse_name));
                            } else {
                                $spouse_name = null;
                                $MortgageDeedThird->setValue('pspouse_name', value($spouse_name));
                            }
                            $MortgageDeedThird->setValue('pdistrict', value(District::find($p->district_id)->name));
                            $MortgageDeedThird->setValue('plocalbody', value(LocalBodies::find($p->local_bodies_id)->name));
                            $MortgageDeedThird->setValue('pbody_type', value(LocalBodies::find($p->local_bodies_id)->body_type));
                            $MortgageDeedThird->setValue('pwardno', value($p->wardno));

                            $MortgageDeedThird->setValue('page', value($cyear - ($p->dob_year)));
                            $g = $p->gender;
                            if ($g == 1) {
                                $gender = 'sf]';
                                $male = 'k\"?if';
                            } else {
                                $gender = 'sL';
                                $male = 'dlxnf';
                            }

                            $MortgageDeedThird->setValue('pgender', value($gender));
                            $MortgageDeedThird->setValue('pnepali_name', value($p->nepali_name));
                            $MortgageDeedThird->setValue('pcitizenship_number', value($p->citizenship_number));
                            $issued_date = $p->issued_year . '÷' . $p->issued_month . '÷' . $p->issued_day;
                            $MortgageDeedThird->setValue('pissued_date', value($issued_date));
                            $MortgageDeedThird->setValue('pissued_district', value(District::find($p->issued_district)->name));

                            $MortgageDeedThird->setValue('lmalpot', value($m));
                            $MortgageDeedThird->cloneRow('landsn', $ld);
                            $i = 1;
                            foreach ($borrower->personalborrowerpersonalland as $l) {

                                if ($l->property_owner_id == $p->id && $m == $l->malpot) {

                                    $n = $i++;
                                    $MortgageDeedThird->setValue('landsn#' . $n, $n);
                                    $MortgageDeedThird->setValue('ldistrict#' . $n, District::find($l->district_id)->name);
                                    $MortgageDeedThird->setValue('llocalbody#' . $n, LocalBodies::find($l->local_bodies_id)->name);
                                    $MortgageDeedThird->setValue('ldistrict', value(District::find($l->district_id)->name));

                                    $MortgageDeedThird->setValue('lwardno#' . $n, $l->wardno);
                                    $MortgageDeedThird->setValue('sheetno#' . $n, $l->sheet_no);
                                    $MortgageDeedThird->setValue('kittano#' . $n, $l->kitta_no);
                                    $MortgageDeedThird->setValue('area#' . $n, $l->area);
                                    $MortgageDeedThird->setValue('remarks#' . $n, $l->remarks);


                                }
                            }
                            $MortgageDeedThird->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Land_Rokka_Letter_Third_' . $mlp++ . '.docx'));
                        }
                    }
                }


            }


        }
    }

    private function land_rokka_letter_third_corporate($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name

        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            $borrower = PersonalBorrower::find($bid);

            $landowner = [];
            $malpot = [];
            foreach ($borrower->personalborrowercorporateland as $land) {

                $landowner[] = CorporatePropertyOwner::find($land->property_owner_id);
                $malpot[] = $land->malpot;
            }
            $corporate_land_owner = array_unique($landowner);
            $malpot = array_unique($malpot);
            $mlp = 1;
            foreach ($malpot as $m) {
                foreach ($corporate_land_owner as $p) {

                    $ld = 0;
                    foreach ($borrower->personalborrowercorporateland as $l) {
                        if ($l->property_owner_id == $p->id && $m == $l->malpot) {
                            $ld = $ld + 1;
                        }
                    }

                    $malpotland = CorporateLand::where([
                        ['property_owner_id', $p->id],
                        ['malpot', $m]
                    ])->first();

                    if ($malpotland) {

                        if (!$ld == 0) {
                            //mortgage deed
                            $MortgageDeedThirdCorporate = new TemplateProcessor(storage_path('document/personal/Rokka Letter Malpot Corporate.docx'));
                            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                            $MortgageDeedThirdCorporate->setValue('branch', value(Branch::find($loan->branch_id)->location));
                            $MortgageDeedThirdCorporate->setValue('nepali_name', value($borrower->nepali_name));
                            $MortgageDeedThirdCorporate->setValue('amount', value($loan->loan_amount));
                            $MortgageDeedThirdCorporate->setValue('words', value($loan->loan_amount_words));

//                Property Owner Details
                            $MortgageDeedThirdCorporate->setValue('cname', value($p->nepali_name));
                            $MortgageDeedThirdCorporate->setValue('lmalpot', value($m));

                            $MortgageDeedThirdCorporate->cloneRow('landsn', $ld);
                            $i = 1;
                            foreach ($borrower->personalborrowercorporateland as $l) {

                                if ($l->property_owner_id == $p->id && $m == $l->malpot) {

                                    $n = $i++;
                                    $MortgageDeedThirdCorporate->setValue('landsn#' . $n, $n);
                                    $MortgageDeedThirdCorporate->setValue('ldistrict#' . $n, District::find($l->district_id)->name);
                                    $MortgageDeedThirdCorporate->setValue('ldistrict', value(District::find($l->district_id)->name));
                                    $MortgageDeedThirdCorporate->setValue('llocalbody#' . $n, LocalBodies::find($l->local_bodies_id)->name);
                                    $MortgageDeedThirdCorporate->setValue('lwardno#' . $n, $l->wardno);
                                    $MortgageDeedThirdCorporate->setValue('sheetno#' . $n, $l->sheet_no);
                                    $MortgageDeedThirdCorporate->setValue('kittano#' . $n, $l->kitta_no);
                                    $MortgageDeedThirdCorporate->setValue('area#' . $n, $l->area);
                                    $MortgageDeedThirdCorporate->setValue('remarks#' . $n, $l->remarks);


                                }
                            }
                            $MortgageDeedThirdCorporate->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Rokka_Letter_Corporate_malpot' . $mlp++ . '.docx'));
                        }
                    }
                }


            }


        }
    }

    private function land_rokka_letter_third_joint($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name

        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {

            $borrower = PersonalBorrower::find($bid);
            $joint = PersonalBorrower::find($bid)->personal_joint_land()->first();
            if ($joint) {
                $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
            } else {
                $jointp = [];
            }


            if ($joint->joint1 && $joint->joint2) {
                $p1 = PersonalPropertyOwner::find($joint->joint1)->nepali_name;
                $p2 = PersonalPropertyOwner::find($joint->joint2)->nepali_name;
                $nepali_name = $p1 . ' / ' . $p2 . ' ;+o"Qm';
            } elseif ($joint->joint3 && $joint->joint2) {
                $p1 = PersonalPropertyOwner::find($joint->joint2)->nepali_name;
                $p2 = PersonalPropertyOwner::find($joint->joint3)->nepali_name;
                $nepali_name = $p1 . ' / ' . $p2 . ' ;+o"Qm';
            } elseif ($joint->joint1 && $joint->joint3) {
                $p1 = PersonalPropertyOwner::find($joint->joint1)->nepali_name;
                $p2 = PersonalPropertyOwner::find($joint->joint3)->nepali_name;
                $nepali_name = $p1 . ' / ' . $p2 . ' ;+o"Qm';
            } elseif ($joint->joint1 && $joint->joint3 && $joint->joint2) {
                $p1 = PersonalPropertyOwner::find($joint->joint1)->nepali_name;
                $p2 = PersonalPropertyOwner::find($joint->joint2)->nepali_name;
                $p3 = PersonalPropertyOwner::find($joint->joint3)->nepali_name;
                $nepali_name = $p1 . ', ' . $p2 . ' / ' . $p3 . ' ;+o"Qm';
            }
            $malpot = [];
            foreach ($jointp as $land) {
                $malpot[] = $land->malpot;
            }

            $malpot = array_unique($malpot);
            $mlp = 1;
            foreach ($malpot as $m) {
                $ld = 0;
                foreach ($jointp as $l) {
                    if ($m == $l->malpot) {
                        $ld = $ld + 1;
                    }
                }

                $malpotland = JointLand::where([
                    ['joint_id', $joint->id],
                    ['malpot', $m]
                ])->first();

                if ($malpotland) {

                    if (!$ld == 0) {


                        //mortgage deed
                        $MortgageDeedThird = new TemplateProcessor(storage_path('document/personal/Rokka Letter Malpot Corporate.docx'));
                        Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                        $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                        $MortgageDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                        $MortgageDeedThird->setValue('offerletterdate', value($offerletterdate));
                        $MortgageDeedThird->setValue('amount', value($loan->loan_amount));
                        $MortgageDeedThird->setValue('words', value($loan->loan_amount_words));

                        $MortgageDeedThird->setValue('nepali_name', value($borrower->nepali_name));
                        $MortgageDeedThird->setValue('citizenship_number', value($borrower->citizenship_number));
                        $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                        $MortgageDeedThird->setValue('issued_date', value($issued_date));
                        $MortgageDeedThird->setValue('issued_district', value(District::find($borrower->issued_district)->name));
//for property owner
//                Property Owner Details
                        $MortgageDeedThird->setValue('cname', value($nepali_name));
                        $MortgageDeedThird->setValue('lmalpot', value($m));

                        $MortgageDeedThird->cloneRow('landsn', $ld);
                        $i = 1;

                        foreach ($jointp as $l) {
                            if ($m == $l->malpot) {
                                $n = $i++;
                                $MortgageDeedThird->setValue('landsn#' . $n, $n);
                                $MortgageDeedThird->setValue('ldistrict#' . $n, District::find($l->district_id)->name);
                                $MortgageDeedThird->setValue('ldistrict', value(District::find($l->district_id)->name));

                                $MortgageDeedThird->setValue('llocalbody#' . $n, LocalBodies::find($l->local_bodies_id)->name);
                                $MortgageDeedThird->setValue('lwardno#' . $n, $l->wardno);
                                $MortgageDeedThird->setValue('sheetno#' . $n, $l->sheet_no);
                                $MortgageDeedThird->setValue('kittano#' . $n, $l->kitta_no);
                                $MortgageDeedThird->setValue('area#' . $n, $l->area);
                                $MortgageDeedThird->setValue('remarks#' . $n, $l->remarks);
                            }
                        }
                        $MortgageDeedThird->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Rokka_Letter_Joint2_malpot_' . $mlp++ . '.docx'));
                    }
                }
            }


        }

    }

    private function land_fukka_letter_self($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name

        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {

            $borrower = PersonalBorrower::find($bid);
            $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();
            if ($propertyOwner) {
                $landowner = [];
                $malpot = [];
                foreach ($borrower->personalborrowerpersonalland as $land) {
                    if ($propertyOwner->id == $land->property_owner_id) {
                        $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
                        $malpot[] = $land->malpot;
                    }
                }
                $personal_land_owner = array_unique($landowner);
                $malpot = array_unique($malpot);

                $mlp = 1;
                foreach ($malpot as $m) {
                    foreach ($personal_land_owner as $p) {

                        $ld = 0;
                        foreach ($borrower->personalborrowerpersonalland as $l) {
                            if ($l->property_owner_id == $p->id && $m == $l->malpot) {
                                $ld = $ld + 1;
                            }
                        }

                        $malpotland = PersonalLand::where([
                            ['property_owner_id', $p->id],
                            ['malpot', $m]
                        ])->first();

                        if ($malpotland) {


                            //mortgage deed
                            $MortgageDeedSelf = new TemplateProcessor(storage_path('document/personal/Fukka Letter Malpot Self.docx'));
                            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                            $MortgageDeedSelf->setValue('branch', value(Branch::find($loan->branch_id)->location));
                            $MortgageDeedSelf->setValue('nepali_name', value($borrower->nepali_name));
                            $MortgageDeedSelf->setValue('lmalpot', value($m));
                            $MortgageDeedSelf->cloneRow('landsn', $ld);
                            $i = 1;
                            foreach ($borrower->personalborrowerpersonalland as $l) {

                                if ($l->property_owner_id == $p->id && $m == $l->malpot) {

                                    $n = $i++;
                                    $MortgageDeedSelf->setValue('landsn#' . $n, $n);
                                    $MortgageDeedSelf->setValue('ldistrict#' . $n, District::find($l->district_id)->name);
                                    $MortgageDeedSelf->setValue('ldistrict', value(District::find($l->district_id)->name));
                                    $MortgageDeedSelf->setValue('llocalbody#' . $n, LocalBodies::find($l->local_bodies_id)->name);
                                    $MortgageDeedSelf->setValue('lwardno#' . $n, $l->wardno);
                                    $MortgageDeedSelf->setValue('sheetno#' . $n, $l->sheet_no);
                                    $MortgageDeedSelf->setValue('kittano#' . $n, $l->kitta_no);
                                    $MortgageDeedSelf->setValue('area#' . $n, $l->area);
                                    $MortgageDeedSelf->setValue('remarks#' . $n, $l->remarks);


                                }
                            }
                            $MortgageDeedSelf->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Fukka_Letter_Self_malpot' . $mlp++ . '.docx'));
                        }
                    }
                }


            }


        }
    }

    private function land_fukka_letter_third($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name

        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {

            $borrower = PersonalBorrower::find($bid);
            $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();

            $landowner = [];
            $malpot = [];
            foreach ($borrower->personalborrowerpersonalland as $land) {

                if ($propertyOwner) {
                    if ($propertyOwner->id == $land->property_owner_id) {

                    } else {
                        $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
                        $malpot[] = $land->malpot;
                    }
                } else {
                    $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
                    $malpot[] = $land->malpot;
                }
            }
            $personal_land_owner = array_unique($landowner);
            $malpot = array_unique($malpot);
            $mlp = 1;
            foreach ($malpot as $m) {
                foreach ($personal_land_owner as $p) {

                    $ld = 0;
                    foreach ($borrower->personalborrowerpersonalland as $l) {
                        if ($l->property_owner_id == $p->id && $m == $l->malpot) {
                            $ld = $ld + 1;
                        }
                    }

                    $malpotland = PersonalLand::where([
                        ['property_owner_id', $p->id],
                        ['malpot', $m]
                    ])->first();

                    if ($malpotland) {

                        if (!$ld == 0) {


                            //mortgage deed
                            $MortgageDeedThird = new TemplateProcessor(storage_path('document/personal/Fukka Letter Malpot Third.docx'));
                            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                            $MortgageDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                            $MortgageDeedThird->setValue('amount', value($loan->loan_amount));
                            $MortgageDeedThird->setValue('words', value($loan->loan_amount_words));

                            $MortgageDeedThird->setValue('nepali_name', value($borrower->nepali_name));

//                Property Owner Details
                            $MortgageDeedThird->setValue('pnepali_name', value($p->nepali_name));

                            $MortgageDeedThird->setValue('lmalpot', value($m));
                            $MortgageDeedThird->cloneRow('landsn', $ld);
                            $i = 1;
                            foreach ($borrower->personalborrowerpersonalland as $l) {

                                if ($l->property_owner_id == $p->id && $m == $l->malpot) {

                                    $n = $i++;
                                    $MortgageDeedThird->setValue('landsn#' . $n, $n);
                                    $MortgageDeedThird->setValue('ldistrict#' . $n, District::find($l->district_id)->name);
                                    $MortgageDeedThird->setValue('llocalbody#' . $n, LocalBodies::find($l->local_bodies_id)->name);
                                    $MortgageDeedThird->setValue('ldistrict', value(District::find($l->district_id)->name));

                                    $MortgageDeedThird->setValue('lwardno#' . $n, $l->wardno);
                                    $MortgageDeedThird->setValue('sheetno#' . $n, $l->sheet_no);
                                    $MortgageDeedThird->setValue('kittano#' . $n, $l->kitta_no);
                                    $MortgageDeedThird->setValue('area#' . $n, $l->area);
                                    $MortgageDeedThird->setValue('remarks#' . $n, $l->remarks);


                                }
                            }
                            $MortgageDeedThird->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Land_Fukka_Letter_Third_' . $mlp++ . '.docx'));
                        }
                    }
                }


            }


        }
    }

    private function land_fukka_letter_third_joint($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name

        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {

            $borrower = PersonalBorrower::find($bid);
            $joint = PersonalBorrower::find($bid)->personal_joint_land()->first();
            if ($joint) {
                $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
            } else {
                $jointp = [];
            }

            if ($joint->joint1 && $joint->joint2) {
                $p1 = PersonalPropertyOwner::find($joint->joint1)->nepali_name;
                $p2 = PersonalPropertyOwner::find($joint->joint2)->nepali_name;
                $nepali_name = $p1 . ' / ' . $p2;
            } elseif ($joint->joint3 && $joint->joint2) {
                $p1 = PersonalPropertyOwner::find($joint->joint2)->nepali_name;
                $p2 = PersonalPropertyOwner::find($joint->joint3)->nepali_name;
                $nepali_name = $p1 . ' / ' . $p2;
            } elseif ($joint->joint1 && $joint->joint3) {
                $p1 = PersonalPropertyOwner::find($joint->joint1)->nepali_name;
                $p2 = PersonalPropertyOwner::find($joint->joint3)->nepali_name;
                $nepali_name = $p1 . ' / ' . $p2;
            } elseif ($joint->joint1 && $joint->joint3 && $joint->joint2) {
                $p1 = PersonalPropertyOwner::find($joint->joint1)->nepali_name;
                $p2 = PersonalPropertyOwner::find($joint->joint2)->nepali_name;
                $p3 = PersonalPropertyOwner::find($joint->joint3)->nepali_name;
                $nepali_name = $p1 . ', ' . $p2 . ' / ' . $p3;
            }
            $malpot = [];
            foreach ($jointp as $land) {
                $malpot[] = $land->malpot;
            }

            $malpot = array_unique($malpot);
            $mlp = 1;
            foreach ($malpot as $m) {
                $ld = 0;
                foreach ($jointp as $l) {
                    if ($m == $l->malpot) {
                        $ld = $ld + 1;
                    }
                }

                $malpotland = JointLand::where([
                    ['joint_id', $joint->id],
                    ['malpot', $m]
                ])->first();

                if ($malpotland) {

                    if (!$ld == 0) {


                        //mortgage deed
                        $MortgageDeedThird = new TemplateProcessor(storage_path('document/personal/Fukka Letter Malpot Third joint.docx'));
                        Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                        $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                        $MortgageDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                        $MortgageDeedThird->setValue('offerletterdate', value($offerletterdate));
                        $MortgageDeedThird->setValue('amount', value($loan->loan_amount));
                        $MortgageDeedThird->setValue('words', value($loan->loan_amount_words));

                        $MortgageDeedThird->setValue('nepali_name', value($borrower->nepali_name));
                        $MortgageDeedThird->setValue('citizenship_number', value($borrower->citizenship_number));
                        $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                        $MortgageDeedThird->setValue('issued_date', value($issued_date));
                        $MortgageDeedThird->setValue('issued_district', value(District::find($borrower->issued_district)->name));
//for property owner
//                Property Owner Details
                        $MortgageDeedThird->setValue('pnepali_name', value($nepali_name));
                        $MortgageDeedThird->setValue('lmalpot', value($m));

                        $MortgageDeedThird->cloneRow('landsn', $ld);
                        $i = 1;

                        foreach ($jointp as $l) {
                            if ($m == $l->malpot) {
                                $n = $i++;
                                $MortgageDeedThird->setValue('landsn#' . $n, $n);
                                $MortgageDeedThird->setValue('ldistrict#' . $n, District::find($l->district_id)->name);
                                $MortgageDeedThird->setValue('ldistrict', value(District::find($l->district_id)->name));

                                $MortgageDeedThird->setValue('llocalbody#' . $n, LocalBodies::find($l->local_bodies_id)->name);
                                $MortgageDeedThird->setValue('lwardno#' . $n, $l->wardno);
                                $MortgageDeedThird->setValue('sheetno#' . $n, $l->sheet_no);
                                $MortgageDeedThird->setValue('kittano#' . $n, $l->kitta_no);
                                $MortgageDeedThird->setValue('area#' . $n, $l->area);
                                $MortgageDeedThird->setValue('remarks#' . $n, $l->remarks);
                            }
                        }
                        $MortgageDeedThird->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Fukka_Letter_Joint2_malpot_' . $mlp++ . '.docx'));
                    }
                }
            }


        }

    }

    private function share_rokka_self($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();
        $hasshare=null;
if($propertyOwner){       
	   foreach ($borrower->personalborrowerpersonalshare as $share) {
            if ($share->property_owner_id == $propertyOwner->id) {
                $hasshare = PersonalShare::find($share->id);
            }
}}
        if ($hasshare) {
            //File name
            $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $ShareRokkaSelf = new TemplateProcessor(storage_path('document/personal/Share Rokka Self.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $ShareRokkaSelf->setValue('branch', value(Branch::find($loan->branch_id)->location));

                $ShareRokkaSelf->setValue('nepali_name', value($borrower->nepali_name));
                $ShareRokkaSelf->setValue('citizenship_number', value($borrower->citizenship_number));
                $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                $ShareRokkaSelf->setValue('issued_date', value($issued_date));
                $ShareRokkaSelf->setValue('issued_district', value(District::find($borrower->issued_district)->name));
                $s = [];
                foreach ($borrower->personalborrowerpersonalshare as $share) {
                    if ($share->property_owner_id == $propertyOwner->id) {
                        $s[] = PersonalShare::find($share->id);
                    }
                }

                $count = count($s);

                $total = 0;
                $allfacilities = PersonalFacilities::where([['borrower_id', $borrower->id]])->get();
                foreach ($allfacilities as $af) {
                    $facility_name = Facility::find($af->facility_id)->name;
                    $contains = str_contains($facility_name, 'z]o/');
                    if ($contains) {
                        $a = $af->amount;
                        $b = str_replace(',', '', $a);
                        if (is_numeric($b)) {
                            $total = $total + $b;
                        }
                    }
                }


                $ShareRokkaSelf->cloneRow('sharesn', $count);
                $i = 1;
                foreach ($s as $share) {
                    $n = $i++;
//                    $total = ($share->kitta) + $total;
                    $ShareRokkaSelf->setValue('sharesn#' . $n, $n);
                    $ShareRokkaSelf->setValue('ownername#' . $n, PersonalPropertyOwner::find($share->property_owner_id)->nepali_name);
                    $ShareRokkaSelf->setValue('dpid#' . $n, $share->dpid);
                    $ShareRokkaSelf->setValue('clientid#' . $n, $share->client_id);
                    $ShareRokkaSelf->setValue('kitta#' . $n, $share->kitta);
                    $ShareRokkaSelf->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                    $ShareRokkaSelf->setValue('isin_name#' . $n, RegisteredCompany::find($share->isin)->name);
                    $ShareRokkaSelf->setValue('share_type#' . $n, $share->share_type);
                }
                $ShareRokkaSelf->setValue('share_total', value($total));
            }
            $ShareRokkaSelf->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Share_Rokka_Self.docx'));
        }

    }

    private function share_rokka_third($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);
        $shareproperty = [];
        $owner = [];
        $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();
        foreach ($borrower->personalborrowerpersonalshare as $share) {
            if ($propertyOwner) {
                if ($share->property_owner_id == $propertyOwner->id) {
                } else {
                    $shareproperty[] = PersonalShare::find($share->id);
                    $owner[] = PersonalPropertyOwner::find($share->property_owner_id);
                }
            } else {
                $shareproperty[] = PersonalShare::find($share->id);
                $owner[] = PersonalPropertyOwner::find($share->property_owner_id);
            }
        }
        $shareowner = (array_unique($owner));

        foreach ($shareowner as $own) {
            //File name
            $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $ShareRokkaThird = new TemplateProcessor(storage_path('document/personal/Share Rokka Third.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $ShareRokkaThird->setValue('branch', value(Branch::find($loan->branch_id)->location));

                $ShareRokkaThird->setValue('amount', value($loan->loan_amount));
                $ShareRokkaThird->setValue('nepali_name', value($borrower->nepali_name));
//                Property Owner Details
                $ShareRokkaThird->setValue('pnepali_name', value($own->nepali_name));

                $sh = [];
                foreach ($shareproperty as $a) {
                    if ($own->id == $a->property_owner_id) {
                        $sh[] = PersonalShare::find($a->id);
                    }
                }


                $count = count($sh);

                $ShareRokkaThird->cloneRow('sharesn', $count);

                $total = 0;
                $allfacilities = PersonalFacilities::where([['borrower_id', $borrower->id]])->get();
                foreach ($allfacilities as $af) {
                    $facility_name = Facility::find($af->facility_id)->name;
                    $contains = str_contains($facility_name, 'z]o/');
                    if ($contains) {
                        $a = $af->amount;
                        $b = str_replace(',', '', $a);
                        if (is_numeric($b)) {
                            $total = $total + $b;
                        }
                    }
                }
                $c = 1;
                foreach ($sh as $share) {
                    $n = $c++;
                    $ShareRokkaThird->setValue('sharesn#' . $n, $n);
                    $ShareRokkaThird->setValue('ownername#' . $n, PersonalPropertyOwner::find($share->property_owner_id)->nepali_name);
                    $ShareRokkaThird->setValue('dpid#' . $n, $share->dpid);
                    $ShareRokkaThird->setValue('clientid#' . $n, $share->client_id);
                    $ShareRokkaThird->setValue('kitta#' . $n, $share->kitta);
                    $ShareRokkaThird->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                    $ShareRokkaThird->setValue('isin_name#' . $n, RegisteredCompany::find($share->isin)->name);
                    $ShareRokkaThird->setValue('share_type#' . $n, $share->share_type);
                }
                $ShareRokkaThird->setValue('share_total', value($total));

            }
            $ShareRokkaThird->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Share_Rokka_Third_' . $own->english_name . '.docx'));
        }

    }

    private function share_rokka_third_corporate($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);
        $shareproperty = [];
        $owner = [];
        foreach ($borrower->personalborrowercorporateshare as $share) {
            $shareproperty[] = CorporateShare::find($share->id);
            $owner[] = CorporatePropertyOwner::find($share->property_owner_id);
        }
        $shareowner = (array_unique($owner));

        foreach ($shareowner as $p) {
            //File name
            $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $PledgeDeedThird = new TemplateProcessor(storage_path('document/personal/Share Rokka Third.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $PledgeDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                $PledgeDeedThird->setValue('nepali_name', value($borrower->nepali_name));
//                Property Owner Details

                $PledgeDeedThird->setValue('pnepali_name', value($p->nepali_name));

                $sh = [];
                foreach ($shareproperty as $a) {
                    if ($p->id == $a->property_owner_id) {
                        $sh[] = CorporateShare::find($a->id);
                    }
                }


                $count = count($sh);

                $PledgeDeedThird->cloneRow('sharesn', $count);
                $c = 1;
                $total = 0;
                $allfacilities = PersonalFacilities::where([['borrower_id', $borrower->id]])->get();
                foreach ($allfacilities as $af) {
                    $facility_name = Facility::find($af->facility_id)->name;
                    $contains = str_contains($facility_name, 'z]o/');
                    if ($contains) {
                        $a = $af->amount;
                        $b = str_replace(',', '', $a);
                        if (is_numeric($b)) {
                            $total = $total + $b;
                        }
                    }
                }

                foreach ($sh as $share) {
                    $n = $c++;
                    $PledgeDeedThird->setValue('sharesn#' . $n, $n);
                    $PledgeDeedThird->setValue('ownername#' . $n, CorporatePropertyOwner::find($share->property_owner_id)->nepali_name);
                    $PledgeDeedThird->setValue('dpid#' . $n, $share->dpid);
                    $PledgeDeedThird->setValue('clientid#' . $n, $share->client_id);
                    $PledgeDeedThird->setValue('kitta#' . $n, $share->kitta);
                    $PledgeDeedThird->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                    $PledgeDeedThird->setValue('isin_name#' . $n, RegisteredCompany::find($share->isin)->name);
                    $PledgeDeedThird->setValue('share_type#' . $n, $share->share_type);
                }
                $PledgeDeedThird->setValue('share_total', value($total));
            }
            $PledgeDeedThird->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Share_Rokka_Third_Corporate_' . $p->english_name . '.docx'));

        }
    }

    private function share_fukka_self($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();
        if ($propertyOwner) {
            //File name
            $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $ShareRokkaSelf = new TemplateProcessor(storage_path('document/personal/Share Fukka Self.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $ShareRokkaSelf->setValue('branch', value(Branch::find($loan->branch_id)->location));

                $ShareRokkaSelf->setValue('nepali_name', value($borrower->nepali_name));
                $ShareRokkaSelf->setValue('citizenship_number', value($borrower->citizenship_number));
                $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                $ShareRokkaSelf->setValue('issued_date', value($issued_date));
                $ShareRokkaSelf->setValue('issued_district', value(District::find($borrower->issued_district)->name));
                $s = [];
                foreach ($borrower->personalborrowerpersonalshare as $share) {
                    if ($share->property_owner_id == $propertyOwner->id) {
                        $s[] = PersonalShare::find($share->id);
                    }
                }

                $count = count($s);
                $total = 0;
                $ShareRokkaSelf->cloneRow('sharesn', $count);
                $i = 1;
                foreach ($s as $share) {
                    $n = $i++;
                    $total = ($share->kitta) + $total;
                    $ShareRokkaSelf->setValue('sharesn#' . $n, $n);
                    $ShareRokkaSelf->setValue('ownername#' . $n, PersonalPropertyOwner::find($share->property_owner_id)->nepali_name);
                    $ShareRokkaSelf->setValue('dpid#' . $n, $share->dpid);
                    $ShareRokkaSelf->setValue('clientid#' . $n, $share->client_id);
                    $ShareRokkaSelf->setValue('kitta#' . $n, $share->kitta);
                    $ShareRokkaSelf->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                    $ShareRokkaSelf->setValue('isin_name#' . $n, RegisteredCompany::find($share->isin)->name);
                    $ShareRokkaSelf->setValue('share_type#' . $n, $share->share_type);
                }
                $ShareRokkaSelf->setValue('share_total', value($total));
            }
            $ShareRokkaSelf->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Share_Fukka_Self.docx'));
        }

    }

    private function tieup_deed_self($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name

        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {

            $borrower = PersonalBorrower::find($bid);
            $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();
            if ($propertyOwner) {
                $landowner = [];
                foreach ($borrower->personalborrowerpersonalland as $land) {
                    if ($propertyOwner->id == $land->property_owner_id) {
                        $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
                    }
                }
                $personal_land_owner = array_unique($landowner);

                $mlp = 1;

                foreach ($personal_land_owner as $p) {

                    $ld = 0;
                    foreach ($borrower->personalborrowerpersonalland as $l) {
                        if ($l->property_owner_id == $p->id) {
                            $ld = $ld + 1;
                        }
                    }


                    //mortgage deed
                    $TieupDeedSelfEnhancement = new TemplateProcessor(storage_path('document/personal/Tieup Deed Self for Enhancement.docx'));
                    Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                    $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                    $TieupDeedSelfEnhancement->setValue('branch', value(Branch::find($loan->branch_id)->location));
                    $TieupDeedSelfEnhancement->setValue('offerletterdate', value($offerletterdate));
                    $TieupDeedSelfEnhancement->setValue('amount', value($loan->loan_amount));
                    $TieupDeedSelfEnhancement->setValue('words', value($loan->loan_amount_words));
                    $TieupDeedSelfEnhancement->setValue('grandfather_name', value($borrower->grandfather_name));
                    $TieupDeedSelfEnhancement->setValue('grandfather_relation', value($borrower->grandfather_relation));
                    $TieupDeedSelfEnhancement->setValue('father_name', value($borrower->father_name));
                    $TieupDeedSelfEnhancement->setValue('father_relation', value($borrower->father_relation));
                    if ($borrower->spouse_name) {
                        $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                        $TieupDeedSelfEnhancement->setValue('spouse_name', value($spouse_name));
                    } else {
                        $spouse_name = null;
                        $TieupDeedSelfEnhancement->setValue('spouse_name', value($spouse_name));
                    }
                    $TieupDeedSelfEnhancement->setValue('district', value(District::find($borrower->district_id)->name));
                    $TieupDeedSelfEnhancement->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
                    $TieupDeedSelfEnhancement->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
                    $TieupDeedSelfEnhancement->setValue('wardno', value($borrower->wardno));

                    $TieupDeedSelfEnhancement->setValue('age', value($cyear - ($borrower->dob_year)));
                    $g = $borrower->gender;
                    if ($g == 1) {
                        $gender = 'sf]';
                        $male = 'k\"?if';
                    } else {
                        $gender = 'sL';
                        $male = 'dlxnf';
                    }
                    $TieupDeedSelfEnhancement->setValue('gender', value($gender));
                    $TieupDeedSelfEnhancement->setValue('nepali_name', value($borrower->nepali_name));
                    $TieupDeedSelfEnhancement->setValue('citizenship_number', value($borrower->citizenship_number));
                    $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                    $TieupDeedSelfEnhancement->setValue('issued_date', value($issued_date));
                    $TieupDeedSelfEnhancement->setValue('issued_district', value(District::find($borrower->issued_district)->name));


                    $TieupDeedSelfEnhancement->cloneRow('landsn', $ld);
                    $i = 1;
                    foreach ($borrower->personalborrowerpersonalland as $l) {

                        if ($l->property_owner_id == $p->id) {

                            $n = $i++;
                            $TieupDeedSelfEnhancement->setValue('landsn#' . $n, $n);
                            $TieupDeedSelfEnhancement->setValue('ldistrict#' . $n, District::find($l->district_id)->name);
                            $TieupDeedSelfEnhancement->setValue('llocalbody#' . $n, LocalBodies::find($l->local_bodies_id)->name);
                            $TieupDeedSelfEnhancement->setValue('lwardno#' . $n, $l->wardno);
                            $TieupDeedSelfEnhancement->setValue('sheetno#' . $n, $l->sheet_no);
                            $TieupDeedSelfEnhancement->setValue('kittano#' . $n, $l->kitta_no);
                            $TieupDeedSelfEnhancement->setValue('area#' . $n, $l->area);
                            $TieupDeedSelfEnhancement->setValue('remarks#' . $n, $l->remarks);


                        }

                    }
                    $TieupDeedSelfEnhancement->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Tieup_Deed_Self_Enhancement_' . $mlp++ . '.docx'));

                }
            }


        }
    }

    private function tieup_deed_third($bid)
    {

        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name

        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }


        if ($s == true) {

            $borrower = PersonalBorrower::find($bid);
            $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();

            $landowner = [];

            foreach ($borrower->personalborrowerpersonalland as $land) {

                if ($propertyOwner) {
                    if ($propertyOwner->id == $land->property_owner_id) {

                    } else {
                        $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);

                    }
                } else {
                    $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
                }
            }
            $personal_land_owner = array_unique($landowner);
            $mlp = 1;
            foreach ($personal_land_owner as $p) {

                $ld = 0;
                foreach ($borrower->personalborrowerpersonalland as $l) {
                    if ($l->property_owner_id == $p->id) {
                        $ld = $ld + 1;
                    }
                }


                //mortgage deed
                $MortgageDeedThird = new TemplateProcessor(storage_path('document/personal/Tieup Deed Third for Enhancement.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $MortgageDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                $MortgageDeedThird->setValue('offerletterdate', value($offerletterdate));
                $MortgageDeedThird->setValue('amount', value($loan->loan_amount));
                $MortgageDeedThird->setValue('words', value($loan->loan_amount_words));
                $MortgageDeedThird->setValue('grandfather_name', value($borrower->grandfather_name));
                $MortgageDeedThird->setValue('grandfather_relation', value($borrower->grandfather_relation));
                $MortgageDeedThird->setValue('father_name', value($borrower->father_name));
                $MortgageDeedThird->setValue('father_relation', value($borrower->father_relation));
                if ($borrower->spouse_name) {
                    $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                    $MortgageDeedThird->setValue('spouse_name', value($spouse_name));
                } else {
                    $spouse_name = null;
                    $MortgageDeedThird->setValue('spouse_name', value($spouse_name));
                }
                $MortgageDeedThird->setValue('district', value(District::find($borrower->district_id)->name));
                $MortgageDeedThird->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
                $MortgageDeedThird->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
                $MortgageDeedThird->setValue('wardno', value($borrower->wardno));

                $MortgageDeedThird->setValue('age', value($cyear - ($borrower->dob_year)));
                $g = $borrower->gender;
                if ($g == 1) {
                    $gender = 'sf]';
                    $male = 'k\"?if';
                } else {
                    $gender = 'sL';
                    $male = 'dlxnf';
                }
                $MortgageDeedThird->setValue('gender', value($gender));
                $MortgageDeedThird->setValue('nepali_name', value($borrower->nepali_name));
                $MortgageDeedThird->setValue('citizenship_number', value($borrower->citizenship_number));
                $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
                $MortgageDeedThird->setValue('issued_date', value($issued_date));
                $MortgageDeedThird->setValue('issued_district', value(District::find($borrower->issued_district)->name));
//for property owner
//                Property Owner Details
                $MortgageDeedThird->setValue('pgrandfather_name', value($p->grandfather_name));
                $MortgageDeedThird->setValue('pgrandfather_relation', value($p->grandfather_relation));
                $MortgageDeedThird->setValue('pfather_name', value($p->father_name));
                $MortgageDeedThird->setValue('pfather_relation', value($p->father_relation));
                if ($p->spouse_name) {
                    $spouse_name = ' ' . $p->spouse_name . 'sf] ' . $p->spouse_relation;
                    $MortgageDeedThird->setValue('pspouse_name', value($spouse_name));
                } else {
                    $spouse_name = null;
                    $MortgageDeedThird->setValue('pspouse_name', value($spouse_name));
                }
                $MortgageDeedThird->setValue('pdistrict', value(District::find($p->district_id)->name));
                $MortgageDeedThird->setValue('plocalbody', value(LocalBodies::find($p->local_bodies_id)->name));
                $MortgageDeedThird->setValue('pbody_type', value(LocalBodies::find($p->local_bodies_id)->body_type));
                $MortgageDeedThird->setValue('pwardno', value($p->wardno));

                $MortgageDeedThird->setValue('page', value($cyear - ($p->dob_year)));
                $g = $p->gender;
                if ($g == 1) {
                    $gender = 'sf]';
                    $male = 'k\"?if';
                } else {
                    $gender = 'sL';
                    $male = 'dlxnf';
                }

                $MortgageDeedThird->setValue('pgender', value($gender));
                $MortgageDeedThird->setValue('pnepali_name', value($p->nepali_name));
                $MortgageDeedThird->setValue('pcitizenship_number', value($p->citizenship_number));
                $issued_date = $p->issued_year . '÷' . $p->issued_month . '÷' . $p->issued_day;
                $MortgageDeedThird->setValue('pissued_date', value($issued_date));
                $MortgageDeedThird->setValue('pissued_district', value(District::find($p->issued_district)->name));


                $MortgageDeedThird->cloneRow('landsn', $ld);
                $i = 1;
                foreach ($borrower->personalborrowerpersonalland as $l) {

                    if ($l->property_owner_id == $p->id) {

                        $n = $i++;
                        $MortgageDeedThird->setValue('landsn#' . $n, $n);
                        $MortgageDeedThird->setValue('ldistrict#' . $n, District::find($l->district_id)->name);
                        $MortgageDeedThird->setValue('llocalbody#' . $n, LocalBodies::find($l->local_bodies_id)->name);
                        $MortgageDeedThird->setValue('lwardno#' . $n, $l->wardno);
                        $MortgageDeedThird->setValue('sheetno#' . $n, $l->sheet_no);
                        $MortgageDeedThird->setValue('kittano#' . $n, $l->kitta_no);
                        $MortgageDeedThird->setValue('area#' . $n, $l->area);
                        $MortgageDeedThird->setValue('remarks#' . $n, $l->remarks);


                    }
                }
                $MortgageDeedThird->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Tieup_Deed_Third_for_Enhancement_' . $mlp++ . '.docx'));

            }


        }
    }

    private function consent_of_property_owner_self($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name

        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            $borrower = PersonalBorrower::find($bid);
            $landowner = [];
            foreach ($borrower->personalborrowerpersonalland as $land) {
                $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
            }
            $personal_land_owner = array_unique($landowner);

            $mlp = 1;
            foreach ($personal_land_owner as $p) {
                $ld = 0;
                foreach ($borrower->personalborrowerpersonalland as $l) {
                    if ($l->property_owner_id == $p->id) {
                        $ld = $ld + 1;
                    }
                }

                //mortgage deed
                $MortgageDeedSelf = new TemplateProcessor(storage_path('document/personal/Consent of Property Owner.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $MortgageDeedSelf->setValue('branch', value(Branch::find($loan->branch_id)->location));
                $MortgageDeedSelf->setValue('offerletterdate', value($offerletterdate));
                $MortgageDeedSelf->setValue('amount', value($loan->loan_amount));
                $MortgageDeedSelf->setValue('nepali_name', value($borrower->nepali_name));
                $MortgageDeedSelf->setValue('citizenship_number', value($borrower->citizenship_number));
                $MortgageDeedSelf->setValue('issued_district', value(District::find($borrower->issued_district)->name));
                $issued_date = $p->issued_year . '÷' . $p->issued_month . '÷' . $p->issued_day;
                $MortgageDeedSelf->setValue('pissued_date', value($issued_date));
                $MortgageDeedSelf->setValue('pissued_district', value(District::find($p->issued_district)->name));
                $MortgageDeedSelf->setValue('pnepali_name', value($p->nepali_name));
                $MortgageDeedSelf->setValue('pcitizenship_number', value($p->citizenship_number));
                $MortgageDeedSelf->setValue('pdistrict', value(District::find($p->district_id)->name));
                $MortgageDeedSelf->setValue('plocalbody', value(LocalBodies::find($p->local_bodies_id)->name));
                $MortgageDeedSelf->setValue('pbody_type', value(LocalBodies::find($p->local_bodies_id)->body_type));
                $MortgageDeedSelf->setValue('pwardno', value($p->wardno));

                $MortgageDeedSelf->cloneRow('landsn', $ld);
                $i = 1;
                foreach ($borrower->personalborrowerpersonalland as $l) {
                    if ($l->property_owner_id == $p->id) {
                        $n = $i++;
                        $MortgageDeedSelf->setValue('landsn#' . $n, $n);
                        $MortgageDeedSelf->setValue('ldistrict#' . $n, District::find($l->district_id)->name);
                        $MortgageDeedSelf->setValue('llocalbody#' . $n, LocalBodies::find($l->local_bodies_id)->name);
                        $MortgageDeedSelf->setValue('lwardno#' . $n, $l->wardno);
                        $MortgageDeedSelf->setValue('sheetno#' . $n, $l->sheet_no);
                        $MortgageDeedSelf->setValue('kittano#' . $n, $l->kitta_no);
                        $MortgageDeedSelf->setValue('area#' . $n, $l->area);
                        $MortgageDeedSelf->setValue('remarks#' . $n, $l->remarks);
                    }
                }
                $MortgageDeedSelf->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Consent_of_Property_Owner_' . $mlp++ . '.docx'));
            }
        }
    }

    private function consent_of_property_owner_joint($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name

        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            $borrower = PersonalBorrower::find($bid);
            $landowner = [];

            $joint = PersonalBorrower::find($bid)->personal_joint_land()->first();
            if ($joint) {
                $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
            } else {
                $jointp = [];
            }

            $jointown[] = $joint->joint1;
            $jointown[] = $joint->joint2;
            $jointown[] = $joint->joint3;


            foreach ($jointown as $j) {
                $landowner[] = PersonalPropertyOwner::find($j);
            }


            $mlp = 1;
            foreach ($landowner as $p) {
                $ld = 0;
                foreach ($jointp as $l) {
                    $ld = $ld + 1;
                }
                if ($p) {

                    //mortgage deed
                    $MortgageDeedSelf = new TemplateProcessor(storage_path('document/personal/Consent of Property Owner.docx'));
                    Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                    $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                    $MortgageDeedSelf->setValue('branch', value(Branch::find($loan->branch_id)->location));
                    $MortgageDeedSelf->setValue('offerletterdate', value($offerletterdate));
                    $MortgageDeedSelf->setValue('amount', value($loan->loan_amount));
                    $MortgageDeedSelf->setValue('words', value($loan->loan_amount_words));
                    $MortgageDeedSelf->setValue('grandfather_name', value($borrower->grandfather_name));
                    $MortgageDeedSelf->setValue('grandfather_relation', value($borrower->grandfather_relation));
                    $MortgageDeedSelf->setValue('father_name', value($borrower->father_name));
                    $MortgageDeedSelf->setValue('father_relation', value($borrower->father_relation));
                    if ($borrower->spouse_name) {
                        $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                        $MortgageDeedSelf->setValue('spouse_name', value($spouse_name));
                    } else {
                        $spouse_name = null;
                        $MortgageDeedSelf->setValue('spouse_name', value($spouse_name));
                    }
                    $MortgageDeedSelf->setValue('district', value(District::find($borrower->district_id)->name));
                    $MortgageDeedSelf->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
                    $MortgageDeedSelf->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
                    $MortgageDeedSelf->setValue('wardno', value($borrower->wardno));
                    $MortgageDeedSelf->setValue('age', value($cyear - ($borrower->dob_year)));
                    $g = $borrower->gender;
                    if ($g == 1) {
                        $gender = 'sf]';
                        $male = 'k\"?if';
                    } else {
                        $gender = 'sL';
                        $male = 'dlxnf';
                    }
                    $MortgageDeedSelf->setValue('gender', value($gender));
                    $MortgageDeedSelf->setValue('nepali_name', value($borrower->nepali_name));
                    $MortgageDeedSelf->setValue('citizenship_number', value($borrower->citizenship_number));
                    $MortgageDeedSelf->setValue('issued_district', value(District::find($borrower->issued_district)->name));
                    $issued_date = $p->issued_year . '÷' . $p->issued_month . '÷' . $p->issued_day;

                    $MortgageDeedSelf->setValue('pissued_date', value($issued_date));
                    $MortgageDeedSelf->setValue('pissued_district', value(District::find($p->issued_district)->name));
                    $MortgageDeedSelf->setValue('pnepali_name', value($p->nepali_name));
                    $MortgageDeedSelf->setValue('pcitizenship_number', value($p->citizenship_number));
                    $MortgageDeedSelf->setValue('pdistrict', value(District::find($p->district_id)->name));
                    $MortgageDeedSelf->setValue('plocalbody', value(LocalBodies::find($p->local_bodies_id)->name));
                    $MortgageDeedSelf->setValue('pbody_type', value(LocalBodies::find($p->local_bodies_id)->body_type));
                    $MortgageDeedSelf->setValue('pwardno', value($p->wardno));

                    $MortgageDeedSelf->cloneRow('landsn', $ld);
                    $i = 1;
                    foreach ($jointp as $l) {
                        $n = $i++;
                        $MortgageDeedSelf->setValue('landsn#' . $n, $n);
                        $MortgageDeedSelf->setValue('ldistrict#' . $n, District::find($l->district_id)->name);
                        $MortgageDeedSelf->setValue('llocalbody#' . $n, LocalBodies::find($l->local_bodies_id)->name);
                        $MortgageDeedSelf->setValue('lwardno#' . $n, $l->wardno);
                        $MortgageDeedSelf->setValue('sheetno#' . $n, $l->sheet_no);
                        $MortgageDeedSelf->setValue('kittano#' . $n, $l->kitta_no);
                        $MortgageDeedSelf->setValue('area#' . $n, $l->area);
                        $MortgageDeedSelf->setValue('remarks#' . $n, $l->remarks);
                    }
                    $MortgageDeedSelf->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Consent_of_Property_Owner_Joint_' . $mlp++ . '.docx'));
                }
            }
        }
    }

    private function anusuchi_18($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);
        $shareproperty = [];
        $owner = [];

        foreach ($borrower->personalborrowerpersonalshare as $share) {
            $shareproperty[] = PersonalShare::find($share->id);
            $owner[] = PersonalPropertyOwner::find($share->property_owner_id);
        }
        $shareowner = (array_unique($owner));

        foreach ($shareowner as $own) {
            //File name
            $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $Anushchi18 = new TemplateProcessor(storage_path('document/personal/Anusuchi 18.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $Anushchi18->setValue('branch', value(Branch::find($loan->branch_id)->location));

//                Property Owner Details
                $Anushchi18->setValue('pgrandfather_name', value($own->grandfather_name));
                $Anushchi18->setValue('pgrandfather_relation', value($own->grandfather_relation));
                $Anushchi18->setValue('pfather_name', value($own->father_name));
                $Anushchi18->setValue('pfather_relation', value($own->father_relation));
                if ($own->spouse_name) {
                    $spouse_name = $own->spouse_name;
                    $Anushchi18->setValue('pspouse_name', value($spouse_name));
                } else {
                    $spouse_name = null;
                    $Anushchi18->setValue('pspouse_name', value($spouse_name));
                }
                $Anushchi18->setValue('pdistrict', value(District::find($own->district_id)->name));
                $Anushchi18->setValue('plocalbody', value(LocalBodies::find($own->local_bodies_id)->name));
                $Anushchi18->setValue('pbody_type', value(LocalBodies::find($own->local_bodies_id)->body_type));
                $Anushchi18->setValue('pwardno', value($own->wardno));

                $Anushchi18->setValue('page', value($cyear - ($own->dob_year)));
                $g = $own->gender;
                if ($g == 1) {
                    $gender = 'sf]';
                    $male = 'k\"?if';
                } else {
                    $gender = 'sL';
                    $male = 'dlxnf';
                }
                $Anushchi18->setValue('pnepali_name', value($own->nepali_name));
                $Anushchi18->setValue('phone', value($own->phone));
                $Anushchi18->setValue('pcitizenship_number', value($own->citizenship_number));
                $issued_date = $own->issued_year . '÷' . $own->issued_month . '÷' . $own->issued_day;
                $Anushchi18->setValue('pissued_date', value($issued_date));
                $Anushchi18->setValue('pissued_district', value(District::find($own->issued_district)->name));

                $sh = [];
                foreach ($shareproperty as $a) {
                    if ($own->id == $a->property_owner_id) {
                        $sh[] = PersonalShare::find($a->id);
                    }
                }


                $count = count($sh);

                $Anushchi18->cloneRow('sharesn', $count);
                $c = 1;

                foreach ($sh as $share) {
                    $n = $c++;
                    $Anushchi18->setValue('sharesn#' . $n, $n);
                    $Anushchi18->setValue('ownername#' . $n, PersonalPropertyOwner::find($share->property_owner_id)->nepali_name);
                    $Anushchi18->setValue('dpid#' . $n, $share->dpid);
                    $Anushchi18->setValue('clientid#' . $n, $share->client_id);
                    $Anushchi18->setValue('kitta#' . $n, $share->kitta);
                    $Anushchi18->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                    $dpid = $share->dpid;
                    $client_id = $share->client_id;
                    $Anushchi18->setValue('isin_name#' . $n, RegisteredCompany::find($share->isin)->name);
                    $Anushchi18->setValue('share_type#' . $n, $share->share_type);
                }
                $Anushchi18->setValue('pdpid', $dpid);
                $Anushchi18->setValue('pclientid', $client_id);
            }
            $Anushchi18->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Anusuchi_18_' . $own->english_name . '.docx'));
        }

    }

    private function anusuchi_18_corporate($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);
        $shareproperty = [];
        $owner = [];

        foreach ($borrower->personalborrowercorporateshare as $share) {
            $shareproperty[] = CorporateShare::find($share->id);
            $owner[] = CorporatePropertyOwner::find($share->property_owner_id);
        }
        $shareowner = (array_unique($owner));

        foreach ($shareowner as $own) {
            //File name
            $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $Anushchi18Corporate = new TemplateProcessor(storage_path('document/personal/Anusuchi 18 Corporate.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $Anushchi18Corporate->setValue('branch', value(Branch::find($loan->branch_id)->location));
                //                Property Owner Details
                $pg = AuthorizedPerson::find($own->authorized_person_id);
                $Anushchi18Corporate->setValue('anepali_name', value($pg->nepali_name));
                $Anushchi18Corporate->setValue('pdistrict', value(District::find($own->district_id)->name));
                $Anushchi18Corporate->setValue('plocalbody', value(LocalBodies::find($own->local_bodies_id)->name));
                $Anushchi18Corporate->setValue('pbodytype', value(LocalBodies::find($own->local_bodies_id)->body_type));
                $Anushchi18Corporate->setValue('pwardno', value($own->wardno));
                $Anushchi18Corporate->setValue('pnepali_name', value($own->nepali_name));
                $Anushchi18Corporate->setValue('pregistrationno', value($own->registration_number));
                $Anushchi18Corporate->setValue('phone', value($own->phone));
                $issued_date = $own->reg_year . '÷' . $own->reg_month . '÷' . $own->reg_day;
                $Anushchi18Corporate->setValue('pregistrationdate', value($issued_date));
                $Anushchi18Corporate->setValue('pissued_district', value(District::find($own->district_id)->name));

                $sh = [];
                foreach ($shareproperty as $a) {
                    if ($own->id == $a->property_owner_id) {
                        $sh[] = CorporateShare::find($a->id);
                    }
                }


                $count = count($sh);

                $Anushchi18Corporate->cloneRow('sharesn', $count);
                $c = 1;

                foreach ($sh as $share) {
                    $n = $c++;
                    $Anushchi18Corporate->setValue('sharesn#' . $n, $n);
                    $Anushchi18Corporate->setValue('ownername#' . $n, CorporatePropertyOwner::find($share->property_owner_id)->nepali_name);
                    $Anushchi18Corporate->setValue('dpid#' . $n, $share->dpid);
                    $Anushchi18Corporate->setValue('clientid#' . $n, $share->client_id);
                    $Anushchi18Corporate->setValue('kitta#' . $n, $share->kitta);
                    $Anushchi18Corporate->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                    $dpid = $share->dpid;
                    $client_id = $share->client_id;
                    $Anushchi18Corporate->setValue('isin_name#' . $n, RegisteredCompany::find($share->isin)->name);
                    $Anushchi18Corporate->setValue('share_type#' . $n, $share->share_type);
                }
                $Anushchi18Corporate->setValue('pdpid', $dpid);
                $Anushchi18Corporate->setValue('pclientid', $client_id);
            }
            $Anushchi18Corporate->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Anusuchi_18_Corporate_' . $own->english_name . '.docx'));
        }

    }

    private function anusuchi_19($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);
        $shareproperty = [];
        $owner = [];

        foreach ($borrower->personalborrowerpersonalshare as $share) {
            $shareproperty[] = PersonalShare::find($share->id);
            $owner[] = PersonalPropertyOwner::find($share->property_owner_id);
        }
        $shareowner = (array_unique($owner));

        foreach ($shareowner as $own) {
            //File name
            $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $Anushchi19 = new TemplateProcessor(storage_path('document/personal/Anusuchi 19.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $Anushchi19->setValue('branch', value(Branch::find($loan->branch_id)->location));

//                Property Owner Details

                $sh = [];
                foreach ($shareproperty as $a) {
                    if ($own->id == $a->property_owner_id) {
                        $sh[] = PersonalShare::find($a->id);
                    }
                }


                $count = count($sh);

                $Anushchi19->cloneRow('sharesn', $count);
                $c = 1;

                foreach ($sh as $share) {
                    $n = $c++;
                    $Anushchi19->setValue('sharesn#' . $n, $n);
                    $Anushchi19->setValue('ownername#' . $n, PersonalPropertyOwner::find($share->property_owner_id)->nepali_name);
                    $Anushchi19->setValue('dpid#' . $n, $share->dpid);
                    $Anushchi19->setValue('clientid#' . $n, $share->client_id);
                    $Anushchi19->setValue('kitta#' . $n, $share->kitta);
                    $Anushchi19->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                    $Anushchi19->setValue('isin_name#' . $n, RegisteredCompany::find($share->isin)->name);
                    $Anushchi19->setValue('share_type#' . $n, $share->share_type);
                }

            }
            $Anushchi19->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Anusuchi_19_' . $own->english_name . '.docx'));
        }

    }

    private function anusuchi_19_corporate($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);
        $shareproperty = [];
        $owner = [];

        foreach ($borrower->personalborrowercorporateshare as $share) {
            $shareproperty[] = CorporateShare::find($share->id);
            $owner[] = CorporatePropertyOwner::find($share->property_owner_id);
        }
        $shareowner = (array_unique($owner));

        foreach ($shareowner as $own) {
            //File name
            $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $Anushchi19Corporate = new TemplateProcessor(storage_path('document/personal/Anusuchi 19 Corporate.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $Anushchi19Corporate->setValue('branch', value(Branch::find($loan->branch_id)->location));

//                Property Owner Details

                $sh = [];
                foreach ($shareproperty as $a) {
                    if ($own->id == $a->property_owner_id) {
                        $sh[] = CorporateShare::find($a->id);
                    }
                }


                $count = count($sh);

                $Anushchi19Corporate->cloneRow('sharesn', $count);
                $c = 1;

                foreach ($sh as $share) {
                    $n = $c++;
                    $Anushchi19Corporate->setValue('sharesn#' . $n, $n);
                    $Anushchi19Corporate->setValue('ownername#' . $n, CorporatePropertyOwner::find($share->property_owner_id)->nepali_name);
                    $Anushchi19Corporate->setValue('dpid#' . $n, $share->dpid);
                    $Anushchi19Corporate->setValue('clientid#' . $n, $share->client_id);
                    $Anushchi19Corporate->setValue('kitta#' . $n, $share->kitta);
                    $Anushchi19Corporate->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                    $Anushchi19Corporate->setValue('isin_name#' . $n, RegisteredCompany::find($share->isin)->name);
                    $Anushchi19Corporate->setValue('share_type#' . $n, $share->share_type);
                }

            }
            $Anushchi19Corporate->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Anusuchi_19_Corporate_' . $own->english_name . '.docx'));
        }

    }

    private function swap_commitment_letter($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        //File name
        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            //share loan deed
            $SwapCommitmentLetter = new TemplateProcessor(storage_path('document/personal/Swap Commitment Letter.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
            $SwapCommitmentLetter->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $SwapCommitmentLetter->setValue('offerletterdate', value($offerletterdate));
            $SwapCommitmentLetter->setValue('amount', value($loan->loan_amount));
            $SwapCommitmentLetter->setValue('words', value($loan->loan_amount_words));
            $SwapCommitmentLetter->setValue('grandfather_name', value($borrower->grandfather_name));
            $SwapCommitmentLetter->setValue('grandfather_relation', value($borrower->grandfather_relation));
            $SwapCommitmentLetter->setValue('father_name', value($borrower->father_name));
            $SwapCommitmentLetter->setValue('father_relation', value($borrower->father_relation));
            if ($borrower->spouse_name) {
                $spouse_name = ' ' . $borrower->spouse_name . 'sf] ' . $borrower->spouse_relation;
                $SwapCommitmentLetter->setValue('spouse_name', value($spouse_name));
            } else {
                $spouse_name = null;
                $SwapCommitmentLetter->setValue('spouse_name', value($spouse_name));
            }
            $SwapCommitmentLetter->setValue('district', value(District::find($borrower->district_id)->name));
            $SwapCommitmentLetter->setValue('localbody', value(LocalBodies::find($borrower->local_bodies_id)->name));
            $SwapCommitmentLetter->setValue('body_type', value(LocalBodies::find($borrower->local_bodies_id)->body_type));
            $SwapCommitmentLetter->setValue('wardno', value($borrower->wardno));

            $SwapCommitmentLetter->setValue('age', value($cyear - ($borrower->dob_year)));
            $g = $borrower->gender;
            if ($g == 1) {
                $gender = 'sf]';
                $male = 'k\"?if';
            } else {
                $gender = 'sL';
                $male = 'dlxnf';
            }
            $SwapCommitmentLetter->setValue('gender', value($gender));
            $SwapCommitmentLetter->setValue('nepali_name', value($borrower->nepali_name));
            $SwapCommitmentLetter->setValue('citizenship_number', value($borrower->citizenship_number));
            $issued_date = $borrower->issued_year . '÷' . $borrower->issued_month . '÷' . $borrower->issued_day;
            $SwapCommitmentLetter->setValue('issued_date', value($issued_date));
            $SwapCommitmentLetter->setValue('issued_district', value(District::find($borrower->issued_district)->name));


            $SwapCommitmentLetter->cloneRow('gsn', count($borrower->personalborrowerpersonalguarantor));
            $i = 1;
            foreach ($borrower->personalborrowerpersonalguarantor as $g) {
                $n = $i++;
                $SwapCommitmentLetter->setValue('gsn#' . $n, $n);

                if ($g->spouse_name) {
                    $gspouse_name = ' ' . $g->spouse_name . 'sf] ' . $g->spouse_relation;
                } else {
                    $gspouse_name = null;
                }
                $gend = $g->gender;
                if ($gend == 1) {
                    $ggender = 'sf]';
                    $male = 'k\"?if';
                } else {
                    $ggender = 'sL';
                    $male = 'dlxnf';
                }
                $gissued_date = $g->issued_year . '÷' . $g->issued_month . '÷' . $g->issued_day;
                $guarantor = $g->grandfather_name . 'sf] ' . $g->grandfather_relation . ' ' . $g->father_name . 'sf] ' . $g->father_relation . $gspouse_name . ' ' . District::find($g->district_id)->name . ' lhNnf ' . LocalBodies::find($g->local_bodies_id)->name . ' ' . LocalBodies::find($g->local_bodies_id)->body_type .
                    ' j*f g+=' . $g->wardno . ' a:g] aif{ ' . ($cyear - ($g->dob_year)) . ' ' . $ggender . ' ' . $g->nepali_name . ' -gf=k|=k=g+=' . $g->citizenship_number . ' hf/L ldlt ' . $gissued_date . ' lhNnf ' . District::find($g->issued_district)->name . '_';
                $SwapCommitmentLetter->setValue('guarantor#' . $n, $guarantor);

            }

            $landowner = [];
            $clandowner = [];
            foreach ($borrower->personalborrowerpersonalland as $land) {
                $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
            }
            $personal_land_owner = array_unique($landowner);
            $joint = PersonalBorrower::find($bid)->personal_joint_land()->first();
            if ($joint) {
                $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
            } else {
                $jointp = [];
            }
            foreach ($borrower->personalborrowercorporateland as $land) {
                $clandowner[] = CorporatePropertyOwner::find($land->property_owner_id);
            }
            $corporate_land_owner = array_unique($clandowner);


            $SwapCommitmentLetter->cloneRow('psn', count($personal_land_owner) + count($jointp) + count($corporate_land_owner));
            $i = 1;
            foreach ($personal_land_owner as $p) {
                $n = $i++;
                $SwapCommitmentLetter->setValue('psn#' . $n, $n);
                if ($p->spouse_name) {
                    $pspouse_name = ' ' . $p->spouse_name . 'sf] ' . $p->spouse_relation;
                } else {
                    $pspouse_name = null;
                }
                $pend = $p->gender;
                if ($pend == 1) {
                    $pgender = 'sf]';
                    $male = 'k\"?if';
                } else {
                    $pgender = 'sL';
                    $male = 'dlxnf';
                }
                $pissued_date = $p->issued_year . '÷' . $p->issued_month . '÷' . $p->issued_day;
                $property = $p->grandfather_name . 'sf] ' . $p->grandfather_relation . ' ' . $p->father_name . 'sf] ' . $p->father_relation . $pspouse_name . ' ' . District::find($p->district_id)->name . ' lhNnf ' . LocalBodies::find($p->local_bodies_id)->name . ' ' . LocalBodies::find($p->local_bodies_id)->body_type .
                    ' j*f g+=' . $p->wardno . ' a:g] aif{ ' . ($cyear - ($p->dob_year)) . ' ' . $pgender . ' ' . $p->nepali_name . ' -gf=k|=k=g+=' . $p->citizenship_number . ' hf/L ldlt ' . $pissued_date . ' lhNnf ' . District::find($p->issued_district)->name . '_';

                $SwapCommitmentLetter->setValue('property_owner#' . $n, $property);

            }


            if ($jointp) {
                $property = null;
                $b1 = null;
                $b2 = null;
                $b3 = null;
                if ($joint->joint1 && $joint->joint2 && $joint->joint3) {
                    $b1 = PersonalPropertyOwner::find($joint->joint1);
                    $b2 = PersonalPropertyOwner::find($joint->joint2);
                    $b3 = PersonalPropertyOwner::find($joint->joint3);
                } elseif ($joint->joint1 && $joint->joint2) {
                    $b1 = PersonalPropertyOwner::find($joint->joint1);
                    $b2 = PersonalPropertyOwner::find($joint->joint2);

                } elseif ($joint->joint3 && $joint->joint2) {
                    $b1 = PersonalPropertyOwner::find($joint->joint2);
                    $b2 = PersonalPropertyOwner::find($joint->joint3);

                } elseif ($joint->joint1 && $joint->joint3) {
                    $b1 = PersonalPropertyOwner::find($joint->joint1);
                    $b2 = PersonalPropertyOwner::find($joint->joint3);
                }

                if ($b1 && $b2 && $b3) {

                    if ($b1->spouse_name) {
                        $b1spouse_name = ' ' . $b1->spouse_name . 'sf] ' . $b1->spouse_relation . null;
                    } else {
                        $b1spouse_name = null;
                    }
                    $g = $b1->gender;
                    if ($g == 1) {
                        $b1gender = 'sf]';
                        $male = 'k\"?if';
                    } else {
                        $b1gender = 'sL';
                        $male = 'dlxnf';
                    }
                    $b1issued_date = $b1->issued_year . '÷' . $b1->issued_month . '÷' . $b1->issued_day;
                    if ($b2->spouse_name) {
                        $b2spouse_name = ' ' . $b2->spouse_name . 'sf] ' . $b2->spouse_relation . null;
                    } else {
                        $b2spouse_name = null;
                    }
                    $g = $b2->gender;
                    if ($g == 1) {
                        $b2gender = 'sf]';
                        $male = 'k\"?if';
                    } else {
                        $b2gender = 'sL';
                        $male = 'dlxnf';
                    }
                    $b2issued_date = $b2->issued_year . '÷' . $b2->issued_month . '÷' . $b2->issued_day;
                    if ($b3->spouse_name) {
                        $b3spouse_name = ' ' . $b3->spouse_name . 'sf] ' . $b3->spouse_relation . null;
                    } else {
                        $b3spouse_name = null;
                    }
                    $g = $b3->gender;
                    if ($g == 1) {
                        $b3gender = 'sf]';
                        $male = 'k\"?if';
                    } else {
                        $b3gender = 'sL';
                        $male = 'dlxnf';
                    }
                    $b3issued_date = $b3->issued_year . '÷' . $b3->issued_month . '÷' . $b3->issued_day;

                    $property = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                        $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                        ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                        $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                        ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                        $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf ltg ;+o"Qm';
                } elseif ($b1 && $b2) {

                    if ($b1->spouse_name) {
                        $b1spouse_name = ' ' . $b1->spouse_name . 'sf] ' . $b1->spouse_relation . null;
                    } else {
                        $b1spouse_name = null;
                    }
                    $g = $b1->gender;
                    if ($g == 1) {
                        $b1gender = 'sf]';
                        $male = 'k\"?if';
                    } else {
                        $b1gender = 'sL';
                        $male = 'dlxnf';
                    }
                    $b1issued_date = $b1->issued_year . '÷' . $b1->issued_month . '÷' . $b1->issued_day;
                    if ($b2->spouse_name) {
                        $b2spouse_name = ' ' . $b2->spouse_name . 'sf] ' . $b2->spouse_relation . null;
                    } else {
                        $b2spouse_name = null;
                    }
                    $g = $b2->gender;
                    if ($g == 1) {
                        $b2gender = 'sf]';
                        $male = 'k\"?if';
                    } else {
                        $b2gender = 'sL';
                        $male = 'dlxnf';
                    }
                    $b2issued_date = $b2->issued_year . '÷' . $b2->issued_month . '÷' . $b2->issued_day;

                    $property = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                        ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                        $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                        ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf b"O ;+o"Qm';
                }


                $n = $i++;
                $SwapCommitmentLetter->setValue('psn#' . $n, $n);
                $SwapCommitmentLetter->setValue('property_owner#' . $n, $property);

            }


            foreach ($corporate_land_owner as $corporate) {
                $n = $i++;
//                dd($corporate);
                $SwapCommitmentLetter->setValue('psn#' . $n, $n);
                $reg_date = $corporate->reg_year . '÷' . $corporate->reg_month . '÷' . $corporate->reg_day;
                $auth = AuthorizedPerson::find($corporate->authorized_person_id);
                if ($auth->spouse_name) {
                    $spouse_name = ' ' . $auth->spouse_name . 'sf] ' . $auth->spouse_relation;
                } else {
                    $spouse_name = null;
                }
                $g = $auth->gender;
                if ($g == 1) {
                    $gender = 'sf]';
                    $male = 'k\"?if';
                } else {
                    $gender = 'sL';
                    $male = 'dlxnf';
                }
                $age = $cyear - $auth->dob_year;
                $issued_date = $auth->issued_year . '÷' . $auth->issued_month . '÷' . $auth->issued_day;
                $property = 'g]kfn ;/sf/, ' . Ministry::find($corporate->ministry_id)->name . ' ' . Department::find($corporate->department_id)->name . 'df btf{ g+ ' . $corporate->registration_number . ' ldlt ' . $reg_date . ' df btf{ eO{ ' . District::find($corporate->district_id)->name . ' lhNnf ' . LocalBodies::find($corporate->local_bodies_id)->name . ' ' . LocalBodies::find($corporate->local_bodies_id)->body_type . ' j*f g+=' . $corporate->wardno . ' df /lhi^*{ sfof{no /x]sf] ' . $corporate->nepali_name . ' sf tkm{af^ clVtof/ k|fKt P]=sf ' . $auth->post . ', ' . $auth->grandfather_name . 'sf] ' . $auth->grandfather_relation . ' ' . $auth->father_name . 'sf] ' . $auth->father_relation . $spouse_name . ' ' . District::find($auth->district_id)->name . ' lhNnf ' . LocalBodies::find($auth->local_bodies_id)->name . ' ' . LocalBodies::find($auth->local_bodies_id)->body_type . ' j*f g+=' . $auth->wardno . ' a:g] aif{ ' . $age . ' ' . $gender . ' ' . $auth->nepali_name . ' -gf=k|=k=g+= ' . $auth->citizenship_number . ' hf/L ldlt ' . $issued_date . ' lhNnf ' . District::find($auth->issued_district)->name . '_ ';


                $SwapCommitmentLetter->setValue('property_owner#' . $n, $property);
            }


            $joint = PersonalBorrower::find($bid)->personal_joint_land()->first();
            if ($joint) {
                $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
            } else {
                $jointp = [];
            }
            $count = count($borrower->personalborrowerpersonalland) + count($borrower->personalborrowercorporateland) + count($jointp);
            $SwapCommitmentLetter->cloneRow('landsn', $count);
            $i = 1;
            foreach ($borrower->personalborrowerpersonalland as $land) {
                $n = $i++;
                $SwapCommitmentLetter->setValue('landsn#' . $n, $n);
                $SwapCommitmentLetter->setValue('ldistrict#' . $n, District::find($land->district_id)->name);
                $SwapCommitmentLetter->setValue('llocalbody#' . $n, LocalBodies::find($land->local_bodies_id)->name);
                $SwapCommitmentLetter->setValue('lwardno#' . $n, $land->wardno);
                $SwapCommitmentLetter->setValue('sheetno#' . $n, $land->sheet_no);
                $SwapCommitmentLetter->setValue('kittano#' . $n, $land->kitta_no);
                $SwapCommitmentLetter->setValue('area#' . $n, $land->area);
                $SwapCommitmentLetter->setValue('remarks#' . $n, $land->remarks);
            }
            foreach ($borrower->personalborrowercorporateland as $land) {

                $n = $i++;
                $SwapCommitmentLetter->setValue('landsn#' . $n, $n);
                $SwapCommitmentLetter->setValue('ldistrict#' . $n, District::find($land->district_id)->name);
                $SwapCommitmentLetter->setValue('llocalbody#' . $n, LocalBodies::find($land->local_bodies_id)->name);
                $SwapCommitmentLetter->setValue('lwardno#' . $n, $land->wardno);
                $SwapCommitmentLetter->setValue('sheetno#' . $n, $land->sheet_no);
                $SwapCommitmentLetter->setValue('kittano#' . $n, $land->kitta_no);
                $SwapCommitmentLetter->setValue('area#' . $n, $land->area);
                $SwapCommitmentLetter->setValue('remarks#' . $n, $land->remarks);
            }
            foreach ($jointp as $land) {
                $n = $i++;
                $SwapCommitmentLetter->setValue('landsn#' . $n, $n);
                $SwapCommitmentLetter->setValue('ldistrict#' . $n, District::find($land->district_id)->name);
                $SwapCommitmentLetter->setValue('llocalbody#' . $n, LocalBodies::find($land->local_bodies_id)->name);
                $SwapCommitmentLetter->setValue('lwardno#' . $n, $land->wardno);
                $SwapCommitmentLetter->setValue('sheetno#' . $n, $land->sheet_no);
                $SwapCommitmentLetter->setValue('kittano#' . $n, $land->kitta_no);
                $SwapCommitmentLetter->setValue('area#' . $n, $land->area);
                $SwapCommitmentLetter->setValue('remarks#' . $n, $land->remarks);
            }


        }
        $SwapCommitmentLetter->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Swap_Commitment_Letter.docx'));

    }

    private function bonus_right_cash_divident_self($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        foreach ($borrower->personalborrowerpersonalshare as $share) {
            //File name
            $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $BonusRishgCashDivident = new TemplateProcessor(storage_path('document/personal/Bonus Right.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $BonusRishgCashDivident->setValue('branch', value(Branch::find($loan->branch_id)->location));

//                Property Owner Details
                $BonusRishgCashDivident->setValue('ownername', value(PersonalPropertyOwner::find($share->property_owner_id)->nepali_name));
                $BonusRishgCashDivident->setValue('nepali_name', value($borrower->nepali_name));
                $BonusRishgCashDivident->setValue('dpid', $share->dpid);
                $BonusRishgCashDivident->setValue('clientid', $share->client_id);
                $BonusRishgCashDivident->setValue('kitta', $share->kitta);
                $BonusRishgCashDivident->setValue('isinname', RegisteredCompany::find($share->isin)->name);

            }
            $BonusRishgCashDivident->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Bonus_Right_Cash_Divident_Self_' . RegisteredCompany::find($share->isin)->isin . '.docx'));
        }

    }

    private function bonus_right_cash_divident_third($bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//obtaining values
        $borrower = PersonalBorrower::find($bid);

        foreach ($borrower->personalborrowercorporateshare as $share) {
            //File name
            $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $BonusRightCashDivident = new TemplateProcessor(storage_path('document/personal/Bonus Right.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $BonusRightCashDivident->setValue('branch', value(Branch::find($loan->branch_id)->location));

//                Property Owner Details
                $BonusRightCashDivident->setValue('ownername', value(CorporatePropertyOwner::find($share->property_owner_id)->nepali_name));
                $BonusRightCashDivident->setValue('nepali_name', value($borrower->nepali_name));
                $BonusRightCashDivident->setValue('dpid', $share->dpid);
                $BonusRightCashDivident->setValue('clientid', $share->client_id);
                $BonusRightCashDivident->setValue('kitta', $share->kitta);
                $BonusRightCashDivident->setValue('isinname', RegisteredCompany::find($share->isin)->name);
            }
            $BonusRightCashDivident->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Bonus_Right_Cash_Divident_Third_' . RegisteredCompany::find($share->isin)->isin . '.docx'));
        }

    }

    public function document(Request $request, $bid)
    {
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
        $land = null;
        $share = null;

        $borrower = PersonalBorrower::find($bid);
        $vehicles = PersonalBorrower::find($bid)->personal_hire_purchase;
        $lands = $borrower->personalborrowerpersonalland->first();
        if ($lands) {
            $land = 1;
        }

        $lands = $borrower->personalborrowercorporateland->first();
        if ($lands) {
            $land = 1;
        }
        $lands = $borrower->personalborrowerjointland;
        $joint = $borrower->personal_joint_land()->first();
        if ($joint) {
            $lands = JointPropertyOwner::find($joint->id)->joint_land()->first();
            if ($lands) {
                $land = 1;
            }
        }
        $shares = $borrower->personalborrowerpersonalshare->first();
        if ($shares) {
            $share = 1;
        }
        $shares = $borrower->personalborrowercorporateshare->first();
        if ($shares) {
            $share = 1;
        }
        $guarantor = PersonalBorrower::find($bid)->personal_guarantor_borrower;
        $facilities = PersonalBorrower::find($bid)->personal_facilities;


        $vehicle = null;
        foreach ($vehicles as $v) {
            $vehicle = 1;
        }


        $joint = PersonalBorrower::find($bid)->personal_joint_land()->first();
        if ($joint) {
            $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
        } else {
            $jointp = [];
        }


        $cpromissory_note = $request->input('promissory_note');
        $cloan_deed = $request->input('loan_deed');
        $cguarantor = $request->input('guarantor');
        $cmortgage_deed = $request->input('mortgage_deed');
        $cshare_pledge_deed = $request->input('share_pledge_deed');
        $cmanjurinama_of_hire_purchase = $request->input('manjurinama_of_hire_purchase');
        $cvehicle_registration_letter = $request->input('vehicle_registration_letter');
        $crokka_letter_malpot = $request->input('rokka_letter_malpot');
        $crelease_letter_malpot = $request->input('release_letter_malpot');
        $cshare_rokka_letter = $request->input('share_rokka_letter');
        $cshare_release_letter = $request->input('share_release_letter');
        $ctieup_deed = $request->input('tieup_deed');
        $cconsent_of_property_owner = $request->input('consent_of_property_owner');
        $canusuchi18 = $request->input('anusuchi18');
        $canusuchi19 = $request->input('anusuchi19');
        $cswap_commitment_letter = $request->input('swap_commitment_letter');
        $cbonus_right_cash_divident = $request->input('bonus_right_cash_divident');
        $cvehicle_fukka_letter = $request->input('vehicle_fukka_letter');

        if ($cpromissory_note == 1) {
            $this->Promissory_Note($bid);
        }

        if ($cloan_deed == 1) {
            if ($land && $share && $vehicle) {
                $this->loan_deed_land_share_vehicle($bid);
            } elseif ($land && $share) {
                $this->loan_deed_land_share($bid);
            } elseif ($land && $vehicle) {
                $this->loan_deed_land_vehicle($bid);
            } elseif ($share && $vehicle) {
                $this->loan_deed_share_vehicle($bid);
            } elseif ($share) {
                $this->loan_deed_share_only($bid);
            } elseif ($vehicle) {
                $this->loan_deed_vehicle_only($bid);
            } elseif ($land) {
                $this->loan_deed_land_only($bid);
            } else {
                $this->loan_deed_facilities_only($bid);
            }
        }

        if ($cguarantor == 1) {
            $per = null;
            $cor = null;
            foreach ($borrower->personalborrowerpersonalguarantor as $pg) {
                $per = 0;
            }

            foreach ($borrower->personalborrowercorporateguarantor as $pg) {
                $cor = 0;
            }

            if ($per == 0) {
                $this->personal_guarantor($bid);
            }

            if ($cor == 0) {
                $this->corporate_guarantor($bid);
            }
        }

        if ($cmortgage_deed == 1) {
            $self = null;
            $third = null;
            if ($jointp) {
                if ($joint->joint1 && $joint->joint2 && $joint->joint3) {
                    $this->mortgage_deed_third_joint3($bid);
                    $this->old_mortgage_deed_third_joint3($bid);
                } else {
                    $this->mortgage_deed_third_joint2($bid);
                    $this->old_mortgage_deed_third_joint2($bid);
                }

            }


            $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();

            foreach ($borrower->personalborrowerpersonalland as $l) {
                if ($propertyOwner) {
                    if ($l->property_owner_id == $propertyOwner->id) {
                        $self = 0;
                    } else {
                        $third = 0;
                    }
                } else {
                    $third = 0;
                }
            }
            if ($self == 0) {
                $this->mortgage_deed_self($bid);
                $this->old_mortgage_deed_self($bid);

            }

            if ($third == 0) {
                $this->mortgage_deed_third_corporate($bid);
                $this->old_mortgage_deed_third_corporate($bid);
                $this->old_mortgage_deed_third($bid);
                $this->mortgage_deed_third($bid);

            }
        }

        if ($cshare_pledge_deed == 1) {
           
			$self = null;
            $third = null;
			
            $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();
            foreach ($borrower->personalborrowerpersonalshare as $share) {
                if ($propertyOwner) {
                    if ($share->property_owner_id == $propertyOwner->id) {                       
					   $self = 1;
                    } else {
                        $third = 1;
                    }
                } else {
                    $third = 1;
                }

            }
			
            if ($self == 1) {
			
                $this->pledge_deed_self($bid);
            }

            if ($third == 1) {
                $this->pledge_deed_third($bid);
                $this->pledge_deed_third_corporate($bid);
            }
        }

        if ($cmanjurinama_of_hire_purchase == 1) {
            if ($vehicle) {
                $this->manjurinama_of_hirepurchase($bid);
            }
        }

        if ($cvehicle_registration_letter == 1) {
            if ($vehicle) {
                $this->vehicle_transfer_letter_bank_favour($bid);
            }

        }
        if ($cvehicle_fukka_letter == 1) {
            if ($vehicle) {
                $this->vehicle_fukka_letter($bid);
            }
        }

        if ($crokka_letter_malpot == 1) {
            $this->land_rokka_letter_self($bid);
            $this->land_rokka_letter_third($bid);
            $this->land_rokka_letter_third_corporate($bid);
            $joint = PersonalBorrower::find($bid)->personal_joint_land()->first();
            if ($joint) {
                $this->land_rokka_letter_third_joint($bid);
            }
        }
        if ($crelease_letter_malpot == 1) {
            $this->land_fukka_letter_self($bid);
            $this->land_fukka_letter_third($bid);
            $joint = PersonalBorrower::find($bid)->personal_joint_land()->first();
            if ($joint) {

                $this->land_fukka_letter_third_joint($bid);
            }
        }

        if ($cshare_rokka_letter == 1) {
            $this->share_rokka_self($bid);
            $this->share_rokka_third($bid);
            $this->share_rokka_third_corporate($bid);
        }
        if ($cbonus_right_cash_divident == 1) {
            $this->bonus_right_cash_divident_self($bid);
            $this->bonus_right_cash_divident_third($bid);
        }
        if ($cshare_release_letter == 1) {
            $this->share_fukka_self($bid);
            $this->share_fukka_third($bid);
            $this->share_fukka_third_corporate($bid);
        }
        if ($ctieup_deed == 1) {
            $this->tieup_deed_self($bid);
            $this->tieup_deed_third($bid);
        }

        if ($cconsent_of_property_owner == 1) {
            $this->consent_of_property_owner_self($bid);

            $joint = PersonalBorrower::find($bid)->personal_joint_land()->first();
            if ($joint) {
                $this->consent_of_property_owner_joint($bid);
            }
        }

        if ($canusuchi18 == 1) {
            $this->anusuchi_18($bid);
            $this->anusuchi_18_corporate($bid);
        }
        if ($canusuchi19 == 1) {
            $this->anusuchi_19($bid);
            $this->anusuchi_19_corporate($bid);
        }

        if ($cswap_commitment_letter == 1) {
            $this->swap_commitment_letter($bid);
        }


//File name
        $filename = $borrower->english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');
        $zipper = new  \Chumper\Zipper\Zipper;

        if (File::exists(storage_path('results/' . $filename . '_Documents.zip'))) {
            $s = unlink(storage_path('results/' . $filename . '_Documents.zip'));
            if ($s == true) {
                $files = glob(storage_path('results/' . $filename . '_Documents/*'));
                Zipper::make(storage_path('results/' . $filename . '_Documents.zip'))->add($files)->close();
            } else {
                Session::flash('danger', 'Undefined Error in generating zip document');
                return redirect()->route('home');
            }
        } else {
            $files = glob(storage_path('results/' . $filename . '_Documents/*'));
            Zipper::make(storage_path('results/' . $filename . '_Documents.zip'))->add($files)->close();
        }


        if (File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::deleteDirectory(storage_path('results/' . $filename . '_Documents/'));
            if ($s == true) {
                Session::flash('success', 'Document Created Successfully. Now You can Download your documents.');
            } else {
                Session::flash('danger', 'Undefined Error in generating zip document');
                return redirect()->route('home');
            }

        } else {
            Session::flash('danger', 'Undefined Error in generating document');
            return redirect()->route('home');
        }

        if (Auth::user()->user_type == 'admin') {

        } else {

            $loan->document_status = 'Downloaded';
            $loan->document_remarks = 'Downloaded by' . Auth::user()->name;
           // $loan->approved_by = Auth::user()->id;
            try {
                $loan->update();

            } catch (\Exception $e) {

            }

        }
        return redirect()->route('document.index');
    }

    public
    function choose($bid)
    {
        $status = 'personal';
        $promissory_note = 1;
        $loan_deed = 1;
        $guarantor = null;
        $mortgage_deed = null;
        $share_pledge_deed = null;
        $manjurinama_of_hire_purchase = null;
        $vehicle_registration_letter = null;
        $vehicle_fukka_letter = null;
        $rokka_letter_malpot = null;
        $release_letter_malpot = null;
        $share_rokka_letter = null;
        $share_release_letter = null;
        $tieup_deed = null;
        $consent_of_property_owner = null;
        $anusuchi18 = null;
        $anusuchi19 = null;
        $hypo_of_stock = null;
        $swap_commitment_letter = null;
        $bonus_right_cash_divident = null;
        $borrower = PersonalBorrower::find($bid);
        $vehicles = PersonalBorrower::find($bid)->personal_hire_purchase;

        $land = null;
        $share = null;
        $lands = $borrower->personalborrowerpersonalland->first();
        if ($lands) {
            $land = 1;
        }
        $lands = $borrower->personalborrowercorporateland->first();
        if ($lands) {
            $land = 1;
        }
        $shares = $borrower->personalborrowerpersonalshare->first();
        if ($shares) {
            $share = 1;
        }
        $shares = $borrower->personalborrowercorporateshare->first();
        if ($shares) {
            $share = 1;
        }

        $joint = $borrower->personal_joint_land()->first();
        if ($joint) {
            $lands = JointPropertyOwner::find($joint->id)->joint_land()->first();
            if ($lands) {
                $land = 1;
            }
        }


        $guarantor = PersonalBorrower::find($bid)->personal_guarantor_borrower;
        $loan = PersonalLoan::where([['borrower_id', $bid]])->first();
        if ($land == 1) {
            $mortgage_deed = 1;
            $rokka_letter_malpot = 1;
            $release_letter_malpot = 1;
            $tieup_deed = 1;
            $consent_of_property_owner = 1;
            $swap_commitment_letter = 1;
        }

        if ($share == 1) {
            $share_pledge_deed = 1;
            $share_rokka_letter = 1;
            $share_release_letter = 1;
            $consent_of_property_owner = 1;
            $anusuchi18 = 1;
            $anusuchi19 = 1;
            $bonus_right_cash_divident = 1;
        }

        foreach ($vehicles as $vehicle) {
            $manjurinama_of_hire_purchase = 1;
            $vehicle_registration_letter = 1;
            $vehicle_fukka_letter = 1;
        }

        foreach ($borrower->personalborrowerpersonalguarantor as $pg) {
            $guarantor = 1;
            $swap_commitment_letter = 1;
        }

        foreach ($borrower->personalborrowercorporateguarantor as $pg) {
            $guarantor = 1;
        }
        return view('document.personal_choose', compact('tieup_deed', 'vehicle_fukka_letter', 'bonus_right_cash_divident', 'rokka_letter_malpot', 'release_letter_malpot', 'share_rokka_letter', 'share_release_letter', 'consent_of_property_owner', 'swap_commitment_letter', 'anusuchi19', 'anusuchi18', 'status', 'bid', 'loan', 'borrower', 'promissory_note', 'loan_deed', 'guarantor', 'manjurinama_of_hire_purchase', 'mortgage_deed', 'share_pledge_deed', 'vehicle_registration_letter', 'hypo_of_stock'));
    }

    public
    function approve_request($bid)
    {
        $borrower = PersonalBorrower::find($bid);
        //year for current age
        $year = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($year) {
            if ($year->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $year->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }
//personal guarantor and corporate guarantor
        $personal_guarantor = [];
        $corporate_guarantor = [];
        $corporatelandowner = [];
        $corporateshareowner = [];

        $p_guarantor = PersonalGuarantorBorrower::where([
            ['borrower_id', $bid],
            ['guarantor_type', 'personal'],
            ['status', '1']
        ])->get();
        foreach ($p_guarantor as $p) {
            $personal_guarantor[] = PersonalGuarantor::find($p->personal_guarantor_id);
        }
        $c_guarantor = PersonalGuarantorBorrower::where([
            ['borrower_id', $bid],
            ['guarantor_type', 'corporate'],
            ['status', '1']
        ])->get();
        foreach ($c_guarantor as $c) {
            $corporate_guarantor[] = CorporateGuarantor::find($c->corporate_guarantor_id);
        }
//        facilities
        $facilities = PersonalFacilities::where([
            ['borrower_id', $bid]
        ])->get();
//loan
        $shareowner = [];
        $landowner = [];
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        $hirepurchase = PersonalHirePurchase::where([['borrower_id', $bid]])->get();

        foreach ($borrower->personalborrowerpersonalland as $land) {
            $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
        }
        $personal_land_owner = array_unique($landowner);

        foreach ($borrower->personalborrowerpersonalshare as $share) {
            $shareowner[] = PersonalPropertyOwner::find($share->property_owner_id);
        }

        $personal_share_owner = array_unique($shareowner);

        foreach ($borrower->personalborrowercorporateland as $land) {
            $corporatelandowner[] = CorporatePropertyOwner::find($land->property_owner_id);
        }
        $corporate_land_owner = array_unique($corporatelandowner);

        foreach ($borrower->personalborrowercorporateshare as $share) {
            $corporateshareowner[] = CorporatePropertyOwner::find($share->property_owner_id);
        }
        $corporate_share_owner = array_unique($corporateshareowner);
        $jointp = [];
        $joint_property_owner = [];
        $joint = $borrower->personal_joint_land()->first();
        if ($joint) {
            $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
            $joint_property_owner[] = PersonalPropertyOwner::find($joint->joint1);
            $joint_property_owner[] = PersonalPropertyOwner::find($joint->joint2);
            $joint_property_owner [] = PersonalPropertyOwner::find($joint->joint3);
        }


        return view('document.personal_approve', compact('jointp', 'joint_property_owner', 'corporate_share_owner', 'corporate_land_owner', 'cyear', 'personal_share_owner', 'bid', 'borrower', 'corporate_guarantor', 'personal_guarantor', 'facilities', 'loan', 'personal_land_owner', 'hirepurchase'));


    }

    public function approve($bid)
    {
        $loan = PersonalLoan::where([['borrower_id', $bid]])->first();
        $loan->document_status = 'Approved';
        $loan->document_remarks = 'Approved';
        $loan->approved_at=Carbon::now();
        $loan->approvedby=Auth::user()->id;

        $loan->approved_by = Auth::user()->id;
        $status = null;
        try {
            $status = $loan->update();

        } catch (\Exception $e) {

        }
        if ($status) {
            Session::flash('success', 'Approved Successfully');
        } else {
            Session::flash('danger', 'Unable to Approve Please Try again.');
        }
        return redirect()->route('home');
    }

    public
    function reject(Reject $request, $bid)
    {
        $reason = $request->input('reject');
        $loan = PersonalLoan::where([['borrower_id', $bid]])->first();
        $loan->document_status = 'Rejected';
        $loan->rejected_at=Carbon::now();
        $loan->rejected_by=Auth::user()->id;
        $loan->document_remarks = $reason . ' (Rejected By:' . Auth::user()->name . ')';
        $status = null;
        try {
            $status = $loan->update();

        } catch (\Exception $e) {

        }
        if ($status) {
            Session::flash('success', 'Document Rejected Successfully.');
        } else {
            Session::flash('danger', 'Unable to Reject Document. Please Try again.');
        }
        return redirect()->route('home');
    }

    public
    function rejected($bid)
    {
        $borrower = PersonalBorrower::find($bid);
        $loan = PersonalLoan::where([['borrower_id', $bid]])->first();
        return view('document.rejected', compact('bid', 'borrower', 'loan'));
    }

    public
    function create($bid)
    {
        //   borrower info
        $borrower = PersonalBorrower::find($bid);
//        personal guarantor
        $personal_guarantor = [];
        $corporate_guarantor = [];
        $personal_guarantor_assigned = PersonalGuarantorBorrower::where([
            ['borrower_id', $bid], ['guarantor_type', 'personal'], ['status', '1']
        ])->get();
        foreach ($personal_guarantor_assigned as $pga) {
            $personal_guarantor = PersonalGuarantor::find($pga->personal_guarantor_id)->get();
        }
        $corporate_guarantor_assigned = PersonalGuarantorBorrower::where([
            ['borrower_id', $bid], ['guarantor_type', 'corporate'], ['status', '1']
        ])->get();
        foreach ($corporate_guarantor_assigned as $cga) {
            $corporate_guarantor = CorporateGuarantor::find($cga->corporate_guarantor_id)->get();
        }


//        banking facilities
        $facility = PersonalFacilities::where([
            ['borrower_id', $bid], ['status', '1']
        ])->get();
        $countpf = PersonalFacilities::where([
            ['borrower_id', $bid], ['status', '1']
        ])->count();


        $personal_land = [];
        $corporate_land = [];
        //getting assigned land
        $personal_land_id = PersonalLandBorrower::where([
            ['borrower_id', $bid],
            ['property_type', 'personal'], ['status', '1']
        ])->get();
//getting land details
        foreach ($personal_land_id as $pl) {
            $personal_land[] = PersonalLand::find($pl->personal_land_id);
        }
        $countpl = count($personal_land);
//corporate land
        $corporate_land_id = PersonalLandBorrower::where([
            ['borrower_id', $bid],
            ['property_type', 'corporate'], ['status', '1']
        ])->get();
//getting land details
        foreach ($corporate_land_id as $cl) {
            $corporate_land[] = CorporateLand::find($cl->corporate_land_id);
        }
        $countcl = count($corporate_land);

        $personal_share = [];
        $corporate_share = [];
        //getting assigned share
        $personal_share_id = PersonalShareBorrower::where([
            ['borrower_id', $bid],
            ['property_type', 'personal'], ['status', '1']
        ])->get();

//getting share details
        foreach ($personal_share_id as $ps) {
            $personal_share[] = PersonalShare::find($ps->personal_share_id);
        }
        $countps = count($personal_share);
//corporate share
        $corporate_share_id = PersonalShareBorrower::where([
            ['borrower_id', $bid],
            ['property_type', 'corporate'], ['status', '1']
        ])->get();
//getting share details
        foreach ($corporate_share_id as $cs) {
            $corporate_share[] = CorporateShare::find($cs->corporate_share_id);
        }
        $countcs = count($corporate_share);

        //loan info
        $loan = PersonalLoan::where([
            ['borrower_id', $bid]
        ])->first();
        if ($loan) {
            if ($loan->offerletter_year < '2075') {
                $cyear = 2075;
            } else {
                $cyear = $loan->offerletter_year;
            }
        } else {
            $cyear = 2075;
        }


        $english_name = $borrower->english_name;
        $nepali_name = $borrower->nepali_name;

        $g = $borrower->gender;
        if ($g == 1) {
            $gender = 'sf]';
            $male = 'k\"?if';
        } else {
            $gender = 'sL';
            $male = 'dlxnf';
        }

//personal Details
        $grandfather_name = $borrower->grandfather_name;
        $grandfather_relation = $borrower->grandfather_relation;
        $father_name = $borrower->father_name;
        $father_relation = $borrower->father_relation;
        $spouse_name = $borrower->spouse_name;
        $spouse_relation = $borrower->spouse_relation;
        $district = District::find($borrower->district_id)->name;
        $local_body = LocalBodies::find($borrower->local_bodies_id)->name;
        $body_type = LocalBodies::find($borrower->local_bodies_id)->body_type;
        $wardno = $borrower->wardno;
        $dob_year = $borrower->dob_year;
        $dob_month = $borrower->dob_month;
        $dob_day = $borrower->dob_day;
        $citizenship_number = $borrower->citizenship_number;
        $issued_year = $borrower->issued_year;
        $issued_month = $borrower->issued_month;
        $issued_day = $borrower->issued_day;
        $issued_district = District::find($borrower->issued_district)->name;
        $dob = $dob_year . '÷' . $dob_month . '÷' . $dob_day;
        $issued_date = $issued_year . '÷' . $issued_month . '÷' . $issued_day;

        $age = $cyear - $dob_year;

//        facilities
        $loan_type = Facility::find($loan->loan_type_id)->name;
        $amount = $loan->loan_amount;
        $words = $loan->loan_amount_words;
        $offerday = $loan->offerletter_day;
        $offermonth = $loan->offerletter_month;
        $offeryear = $loan->offerletter_year;
        $branch = Branch::find($loan->branch_id)->location;
        $loan_purpose = $loan->loan_purpose;
        $offerletterdate = $offeryear . '÷' . $offermonth . '÷' . $offerday;

//for file name


        //File name
        $filename = $english_name . '_' . $offeryear . '_' . $offermonth . '_' . $offerday;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }


        if ($s == true) {
            //promissory note
            $templateProcessor = new TemplateProcessor(storage_path('Personal_Promissory_Note.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $templateProcessor->setValue('amount', value($amount));
            $templateProcessor->setValue('words', value($words));
            $templateProcessor->setValue('grandfather_name', value($grandfather_name));
            $templateProcessor->setValue('grandfather_relation', value($grandfather_relation));
            $templateProcessor->setValue('father_name', value($father_name));
            $templateProcessor->setValue('father_relation', value($father_relation));
            $templateProcessor->setValue('spouse_name', value($spouse_name));
            $templateProcessor->setValue('spouse_relation', value($spouse_relation));
            $templateProcessor->setValue('district', value($district));
            $templateProcessor->setValue('localbody', value($local_body));
            $templateProcessor->setValue('body_type', value($body_type));
            $templateProcessor->setValue('wardno', value($wardno));
            $templateProcessor->setValue('age', value($age));
            $templateProcessor->setValue('gender', value($gender));
            $templateProcessor->setValue('nepali_name', value($nepali_name));
            $templateProcessor->setValue('citizenship_number', value($citizenship_number));
            $templateProcessor->setValue('issued_date', value($issued_date));
            $templateProcessor->setValue('issued_district', value($issued_district));

            $templateProcessor->saveAs(storage_path('results/' . $filename . '_Documents/' . $english_name . '_Personal_Promissory_Note.docx'));
//personal manjurinama hirepurchase
            $templateProcessor = new TemplateProcessor(storage_path('Personal_Manjurinama_Hire_Purchase.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $templateProcessor->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $templateProcessor->setValue('district', value($district));
            $templateProcessor->setValue('localbody', value($local_body));
            $templateProcessor->setValue('body_type', value($body_type));
            $templateProcessor->setValue('wardno', value($wardno));
            $templateProcessor->setValue('nepali_name', value($nepali_name));

            $templateProcessor->saveAs(storage_path('results/' . $filename . '_Documents/' . $english_name . '_Personal_Manjurinama_Hire_Purchase.docx'));


// loan deed
            $templateProcessor = new TemplateProcessor(storage_path('Personal_Loan_Deed.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $templateProcessor->setValue('amount', value($amount));
            $templateProcessor->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $templateProcessor->setValue('words', value($words));
            $templateProcessor->setValue('grandfather_name', value($grandfather_name));
            $templateProcessor->setValue('grandfather_relation', value($grandfather_relation));
            $templateProcessor->setValue('father_name', value($father_name));
            $templateProcessor->setValue('father_relation', value($father_relation));
            $templateProcessor->setValue('spouse_name', value($spouse_name));
            $templateProcessor->setValue('spouse_relation', value($spouse_relation));
            $templateProcessor->setValue('district', value($district));
            $templateProcessor->setValue('localbody', value($local_body));
            $templateProcessor->setValue('body_type', value($body_type));
            $templateProcessor->setValue('wardno', value($wardno));
            $templateProcessor->setValue('age', value($age));
            $templateProcessor->setValue('gender', value($gender));
            $templateProcessor->setValue('nepali_name', value($nepali_name));
            $templateProcessor->setValue('citizenship_number', value($citizenship_number));
            $templateProcessor->setValue('issued_date', value($issued_date));
            $templateProcessor->setValue('issued_district', value($issued_district));
            $templateProcessor->setValue('branch', value($branch));
            $templateProcessor->setValue('offerletterdate', value($offerletterdate));

//personal facilities
            $templateProcessor->cloneRow('rownumber', $countpf);
            $i = 1;
            foreach ($facility as $f) {
                $n = $i++;
                $templateProcessor->setValue('rownumber#' . $n, $n);
                $templateProcessor->setValue('rowfacility#' . $n, Facility::find($f->facility_id)->name);
                $templateProcessor->setValue('rowfacility_amount#' . $n, $f->amount);
                $templateProcessor->setValue('rowrate#' . $n, $f->rate);

                if ($f->tyear) {
                    $templateProcessor->setValue('rowtime#' . $n, $f->tyear . '÷' . $f->tmonth . '÷' . $f->tday);
                } else {
                    $templateProcessor->setValue('rowtime#' . $n, $f->tenure);
                }
                $templateProcessor->setValue('rowremarks#' . $n, $f->remarks);
            }


//            land details


            $templateProcessor->cloneRow('rowsn', $countpl + $countcl);
            $i = 1;
            foreach ($personal_land as $pl) {
                $n = $i++;
                $templateProcessor->setValue('rowsn#' . $n, $n);
                $templateProcessor->setValue('rowlandowner#' . $n, PersonalPropertyOwner::find($pl->property_owner_id)->nepali_name);
                $templateProcessor->setValue('rowdistrict#' . $n, District::find($pl->district_id)->name);
                $templateProcessor->setValue('rowlocalbody#' . $n, LocalBodies::find($pl->local_bodies_id)->name);
                $templateProcessor->setValue('rowward#' . $n, $pl->wardno);
                $templateProcessor->setValue('rowsheetno#' . $n, $pl->sheet_no);
                $templateProcessor->setValue('rowkittano#' . $n, $pl->kitta_no);
                $templateProcessor->setValue('rowarea#' . $n, $pl->area);
                $templateProcessor->setValue('rowlandremarks#' . $n, $pl->remarks);
            }
            foreach ($corporate_land as $cl) {
                $n = $i++;
                $templateProcessor->setValue('rowsn#' . $n, $n);
                $templateProcessor->setValue('rowlandowner#' . $n, CorporatePropertyOwner::find($cl->property_owner_id)->nepali_name);
                $templateProcessor->setValue('rowdistrict#' . $n, District::find($cl->district_id)->name);
                $templateProcessor->setValue('rowlocalbody#' . $n, LocalBodies::find($cl->local_bodies_id)->name);
                $templateProcessor->setValue('rowward#' . $n, $cl->wardno);
                $templateProcessor->setValue('rowsheetno#' . $n, $cl->sheet_no);
                $templateProcessor->setValue('rowkittano#' . $n, $cl->kitta_no);
                $templateProcessor->setValue('rowarea#' . $n, $cl->area);
                $templateProcessor->setValue('rowlandremarks#' . $n, $cl->remarks);
            }

//          $templateProcessor->cloneRow('rowsharesn', $countps + $countcs);
            $i = 1;
            foreach ($personal_share as $ps) {
                $n = $i++;
                $templateProcessor->setValue('rowsharesn#' . $n, $n);
                $templateProcessor->setValue('rowdpid#' . $n, $ps->dpid);
                $templateProcessor->setValue('rowclientid#' . $n, $ps->client_id);
                $templateProcessor->setValue('rowkitta#' . $n, $ps->kitta);
            }
            foreach ($corporate_share as $cs) {
                $n = $i++;
                $templateProcessor->setValue('rowsharesn#' . $n, $n);
                $templateProcessor->setValue('rowdpid#' . $n, $cs->dpid);
                $templateProcessor->setValue('rowclientid#' . $n, $cs->client_id);
                $templateProcessor->setValue('rowkitta#' . $n, $cs->kitta);
            }
            $templateProcessor->saveAs(storage_path('results/' . $filename . '_Documents/' . $english_name . '_Personal_Loan_Deed.docx'));


//         personal to    personal Guarantor

            if ($personal_guarantor) {
                $i = 1;

                foreach ($personal_guarantor as $pg) {
//                 start
                    $templateProcessor = new TemplateProcessor(storage_path('Personal_Personal_Guarantor.docx'));
                    Settings::setOutputEscapingEnabled(true);
                    $templateProcessor->setValue('branch', value(Branch::find($loan->branch_id)->location));
                    $templateProcessor->setValue('amount', value($amount));
                    $templateProcessor->setValue('words', value($words));
                    $templateProcessor->setValue('grandfather_name', value($grandfather_name));
                    $templateProcessor->setValue('grandfather_relation', value($grandfather_relation));
                    $templateProcessor->setValue('father_name', value($father_name));
                    $templateProcessor->setValue('father_relation', value($father_relation));
                    $templateProcessor->setValue('spouse_name', value($spouse_name));
                    $templateProcessor->setValue('spouse_relation', value($spouse_relation));
                    $templateProcessor->setValue('district', value($district));
                    $templateProcessor->setValue('localbody', value($local_body));
                    $templateProcessor->setValue('body_type', value($body_type));
                    $templateProcessor->setValue('wardno', value($wardno));
                    $templateProcessor->setValue('age', value($age));
                    $templateProcessor->setValue('gender', value($gender));
                    $templateProcessor->setValue('nepali_name', value($nepali_name));
                    $templateProcessor->setValue('citizenship_number', value($citizenship_number));
                    $templateProcessor->setValue('issued_date', value($issued_date));
                    $templateProcessor->setValue('issued_district', value($issued_district));
                    $templateProcessor->setValue('branch', value($branch));
                    $templateProcessor->setValue('offerletterdate', value($offerletterdate));

                    if ($pg->gender == '1') {
                        $g_gender = 'sf]';
                    } else {
                        $g_gender = 'sL';
                    }
//                    guarantor
                    $templateProcessor->setValue('g_grandfather_name', value($pg->grandfather_name));
                    $templateProcessor->setValue('g_grandfather_relation', value($pg->grandfather_relation));
                    $templateProcessor->setValue('g_father_name', value($pg->father_name));
                    $templateProcessor->setValue('g_father_relation', value($pg->father_relation));
                    $templateProcessor->setValue('g_spouse_name', value($pg->spouse_name));
                    $templateProcessor->setValue('g_spouse_relation', value($pg->spouse_relation));
                    $templateProcessor->setValue('g_district', value(District::find($pg->district_id)->name));
                    $templateProcessor->setValue('g_localbody', value(LocalBodies::find($pg->local_bodies_id)->name));
                    $templateProcessor->setValue('g_body_type', value(LocalBodies::find($pg->local_bodies_id)->body_type));
                    $templateProcessor->setValue('g_wardno', value($pg->wardno));
                    $templateProcessor->setValue('g_age', value($cyear - $pg->dob_year));
                    $templateProcessor->setValue('g_gender', value($g_gender));
                    $templateProcessor->setValue('g_nepali_name', value($pg->nepali_name));
                    $templateProcessor->setValue('g_citizenship_number', value($pg->citizenship_number));
                    $g_issued_date = $pg->issued_year . '÷' . $pg->issued_month . '÷' . $pg->issued_day;
                    $templateProcessor->setValue('g_issued_date', value($g_issued_date));
                    $templateProcessor->setValue('g_issued_district', value(District::find($pg->issued_district)->name));

                    $templateProcessor->saveAs(storage_path('results/' . $filename . '_Documents/' . $english_name . '_Personal_Guarantor_' . $i++ . '.docx'));
                }
            }

//Personal_Mortgage_Deed_Self
            $borrower_property_owner = PersonalPropertyOwner::where([
                ['citizenship_number', $borrower->citizenship_number], ['english_name', $borrower->english_name]])->first();
//property owner
            if ($borrower_property_owner) {
                $borrower_land = PersonalLand::where([
                    ['property_owner_id', $borrower_property_owner->id]
                ])->get();
//                personal release letter self
                $release = new TemplateProcessor(storage_path('Personal_Release_Letter_Self.docx'));
                Settings::setOutputEscapingEnabled(true);


//personal rokka letter self
                $rokka = new TemplateProcessor(storage_path('Personal_Rokka_Letter_Self.docx'));
                Settings::setOutputEscapingEnabled(true);
//personal mortgage deed
                $templateProcessor = new TemplateProcessor(storage_path('Personal_Mortgage_Deed_Self.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $rokka->setValue('amount', value($amount));
                $rokka->setValue('words', value($words));
                $rokka->setValue('grandfather_name', value($grandfather_name));
                $rokka->setValue('grandfather_relation', value($grandfather_relation));
                $rokka->setValue('father_name', value($father_name));
                $rokka->setValue('father_relation', value($father_relation));
                $rokka->setValue('spouse_name', value($spouse_name));
                $rokka->setValue('spouse_relation', value($spouse_relation));
                $rokka->setValue('district', value($district));
                $rokka->setValue('localbody', value($local_body));
                $rokka->setValue('body_type', value($body_type));
                $rokka->setValue('wardno', value($wardno));
                $rokka->setValue('age', value($age));
                $rokka->setValue('gender', value($gender));
                $rokka->setValue('nepali_name', value($nepali_name));


                $release->setValue('nepali_name', value($nepali_name));


                $templateProcessor->setValue('amount', value($amount));
                $templateProcessor->setValue('words', value($words));
                $templateProcessor->setValue('grandfather_name', value($grandfather_name));
                $templateProcessor->setValue('father_name', value($father_name));
                $templateProcessor->setValue('spouse_name', value($spouse_name));
                $templateProcessor->setValue('district', value($district));
                $templateProcessor->setValue('localbody', value($local_body));
                $templateProcessor->setValue('body_type', value($body_type));
                $templateProcessor->setValue('wardno', value($wardno));
                $templateProcessor->setValue('age', value($age));


                if ($gender == '1') {
                    $male = 'k"?if';
                } else {
                    $male = 'dlxnf';
                }
                $templateProcessor->setValue('male', value($male));
                $templateProcessor->setValue('nepali_name', value($nepali_name));
                $templateProcessor->setValue('english_name', value($english_name));
                $templateProcessor->setValue('citizenship_number', value($citizenship_number));
                $templateProcessor->setValue('issued_date', value($issued_date));
                $templateProcessor->setValue('issued_district', value($issued_district));
                $templateProcessor->setValue('branch', value($branch));
                $templateProcessor->setValue('offerletterdate', value($offerletterdate));
                $templateProcessor->setValue('dob', value($dob));

//                $templateProcessor->setValue('land_province', value(District::find($bl->district_id)->name));
//                $templateProcessor->setValue('land_district', value(Province::find(District::find($bl->district_id)->province_id)));


                foreach ($borrower_land as $bl) {
                    $land_borrower[] = PersonalLandBorrower::where([
                        ['personal_land_id', $bl->id], ['property_type', 'personal'], ['status', '1']
                    ])->get();
                }

                $i = 1;
                $templateProcessor->cloneRow('land_local_body', count($land_borrower));
                $rokka->cloneRow('land_local_body', count($land_borrower));
                $release->cloneRow('land_local_body', count($land_borrower));

                foreach ($land_borrower as $lb) {


                    foreach ($lb as $l) {
                        $n = $i++;
                        $templateProcessor->setValue('land_local_body#' . $n, LocalBodies::find(PersonalLand::find($l->personal_land_id)->local_bodies_id)->name);
                        $templateProcessor->setValue('land_wardno#' . $n, PersonalLand::find($l->personal_land_id)->wardno);
                        $templateProcessor->setValue('land_sheetno#' . $n, PersonalLand::find($l->personal_land_id)->sheet_no);
                        $templateProcessor->setValue('land_kittano#' . $n, PersonalLand::find($l->personal_land_id)->kitta_no);
                        $templateProcessor->setValue('land_area#' . $n, PersonalLand::find($l->personal_land_id)->area);
                        $templateProcessor->setValue('land_remarks#' . $n, PersonalLand::find($l->personal_land_id)->remarks);

                        $rokka->setValue('land_district#' . $n, District::find(PersonalLand::find($l->personal_land_id)->district_id)->name);
                        $rokka->setValue('land_local_body#' . $n, LocalBodies::find(PersonalLand::find($l->personal_land_id)->local_bodies_id)->name);
                        $rokka->setValue('land_wardno#' . $n, PersonalLand::find($l->personal_land_id)->wardno);
                        $rokka->setValue('land_sheetno#' . $n, PersonalLand::find($l->personal_land_id)->sheet_no);
                        $rokka->setValue('land_kittano#' . $n, PersonalLand::find($l->personal_land_id)->kitta_no);
                        $rokka->setValue('land_area#' . $n, PersonalLand::find($l->personal_land_id)->area);
                        $rokka->setValue('land_remarks#' . $n, PersonalLand::find($l->personal_land_id)->remarks);


                        $release->setValue('land_district#' . $n, District::find(PersonalLand::find($l->personal_land_id)->district_id)->name);
                        $release->setValue('land_local_body#' . $n, LocalBodies::find(PersonalLand::find($l->personal_land_id)->local_bodies_id)->name);
                        $release->setValue('land_wardno#' . $n, PersonalLand::find($l->personal_land_id)->wardno);
                        $release->setValue('land_sheetno#' . $n, PersonalLand::find($l->personal_land_id)->sheet_no);
                        $release->setValue('land_kittano#' . $n, PersonalLand::find($l->personal_land_id)->kitta_no);
                        $release->setValue('land_area#' . $n, PersonalLand::find($l->personal_land_id)->area);
                        $release->setValue('land_remarks#' . $n, PersonalLand::find($l->personal_land_id)->remarks);

                    }
                }
                $release->saveAs(storage_path('results/' . $filename . '_Documents/' . $english_name . '_Personal_Release_Letter_Self.docx'));
                $rokka->saveAs(storage_path('results/' . $filename . '_Documents/' . $english_name . '_Personal_Rokka_Letter_Self.docx'));
                $templateProcessor->saveAs(storage_path('results/' . $filename . '_Documents/' . $english_name . '_Personal_Mortgage_Deed_Self.docx'));
            }


            //Third Party_Mortgage_Deed
            $property_third = PersonalLandBorrower::where([
                ['borrower_id', $bid], ['property_type', 'personal'], ['status', '1']
            ])->get();

            foreach ($property_third as $pt) {
                $citizen = PersonalPropertyOwner::find(PersonalLand::find($pt->personal_land_id)->property_owner_id)->citizenship_number;
                $eng_name = PersonalPropertyOwner::find(PersonalLand::find($pt->personal_land_id)->property_owner_id)->english_name;

                if (!($borrower->citizenship_number == $citizen && $borrower->english_name == $eng_name)) {
                    $property_owner_id = PersonalPropertyOwner::find(PersonalLand::find($pt->personal_land_id)->property_owner_id)->id;
                }


//                echo PersonalPropertyOwner::find(PersonalLand::find($pt->personal_land_id)->property_owner_id)->english_name;
                $pid = PersonalPropertyOwner::where('id', $property_owner_id)->distinct('id')->pluck('id');


                foreach ($pid as $p) {
                    $powner = PersonalPropertyOwner::find($p);
                    $p_dob = $powner->dob_year . '÷' . $powner->dob_month . '÷' . $powner->dob_day;
                    $p_issued_date = $powner->issued_year . '÷' . $powner->issued_month . '÷' . $powner->issued_day;
                    $land = PersonalLand::where([
                        ['property_owner_id', $p]
                    ])->get();

//                    personal rokka letter person to person
                    $person_rokka = new TemplateProcessor(storage_path('Personal_Rokka_Letter_Person_To_Person.docx'));
                    Settings::setOutputEscapingEnabled(true);
//presonal release
                    $release = new TemplateProcessor(storage_path('Personal_Release_Letter_Person_To_Person.docx'));
                    Settings::setOutputEscapingEnabled(true);
                    //personal mortgage deed third
                    $templateProcessor = new TemplateProcessor(storage_path('Personal_Mortgage_Deed_Third.docx'));
                    Settings::setOutputEscapingEnabled(true);

                    $person_rokka->setValue('amount', value($amount));
                    $person_rokka->setValue('words', value($words));
                    $person_rokka->setValue('nepali_name', value($nepali_name));
                    $person_rokka->setValue('branch', value($branch));


                    $release->setValue('nepali_name', value($nepali_name));
                    $release->setValue('branch', value($branch));

                    $templateProcessor->setValue('amount', value($amount));
                    $templateProcessor->setValue('words', value($words));
                    $templateProcessor->setValue('grandfather_name', value($grandfather_name));
                    $templateProcessor->setValue('grandfather_relation', value($grandfather_relation));
                    $templateProcessor->setValue('father_name', value($father_name));
                    $templateProcessor->setValue('father_relation', value($father_relation));
                    $templateProcessor->setValue('spouse_name', value($spouse_name));
                    $templateProcessor->setValue('spouse_relation', value($spouse_relation));
                    $templateProcessor->setValue('district', value($district));
                    $templateProcessor->setValue('localbody', value($local_body));
                    $templateProcessor->setValue('body_type', value($body_type));
                    $templateProcessor->setValue('wardno', value($wardno));
                    $templateProcessor->setValue('age', value($age));
                    $templateProcessor->setValue('gender', value($gender));
                    $templateProcessor->setValue('nepali_name', value($nepali_name));
                    $templateProcessor->setValue('citizenship_number', value($citizenship_number));
                    $templateProcessor->setValue('issued_date', value($issued_date));
                    $templateProcessor->setValue('issued_district', value($issued_district));
                    $templateProcessor->setValue('branch', value($branch));
                    $templateProcessor->setValue('offerletterdate', value($offerletterdate));

                    if ($gender == '1') {
                        $male = 'k"?if';
                    } else {
                        $male = 'dlxnf';
                    }
                    $templateProcessor->setValue('male', value($male));
                    $templateProcessor->setValue('english_name', value($english_name));
                    $templateProcessor->setValue('dob', value($dob));

                    //                    property owner
                    $templateProcessor->setValue('p_grandfather_name', value($powner->grandfather_name));
                    $templateProcessor->setValue('p_father_name', value($powner->father_name));
                    $templateProcessor->setValue('p_spouse_name', value($powner->spouse_name));
                    $templateProcessor->setValue('p_district', value(District::find($powner->district_id)->name));
                    $templateProcessor->setValue('p_localbody', value(LocalBodies::find($powner->local_bodies_id)->name));
                    $templateProcessor->setValue('p_bodytype', value(LocalBodies::find($powner->local_bodies_id)->body_type));
                    $templateProcessor->setValue('p_wardno', value($powner->wardno));
                    $templateProcessor->setValue('p_issued_date', value($p_issued_date));
                    $templateProcessor->setValue('p_age', value($cyear - ($powner->dob_year)));
                    if ($powner->gender == '1') {
                        $p_male = 'k"?if';
                    } else {
                        $p_male = 'dlxnf';
                    }
                    $templateProcessor->setValue('p_male', value($p_male));
                    $templateProcessor->setValue('p_nepali_name', value($powner->nepali_name));
                    $templateProcessor->setValue('p_english_name', value($powner->english_name));
                    $templateProcessor->setValue('p_citizenship_number', value($powner->citizenship_number));
                    $templateProcessor->setValue('p_issued_date', value($powner->issued_date));
                    $templateProcessor->setValue('p_issued_district', value($powner->issued_district));
                    $templateProcessor->setValue('p_dob', value($p_dob));
                    $release->setValue('p_nepali_name', value($powner->nepali_name));

                    $person_rokka->setValue('p_grandfather_name', value($powner->grandfather_name));
                    $person_rokka->setValue('p_grandfather_relation', value($powner->grandfather_relation));
                    $person_rokka->setValue('p_father_name', value($powner->father_name));
                    $person_rokka->setValue('p_father_relation', value($powner->father_relation));
                    $person_rokka->setValue('p_spouse_name', value($powner->spouse_name));
                    $person_rokka->setValue('p_spouse_relation', value($powner->spouse_relation));
                    $person_rokka->setValue('p_district', value(District::find($powner->district_id)->name));
                    $person_rokka->setValue('p_localbody', value(LocalBodies::find($powner->local_bodies_id)->name));
                    $person_rokka->setValue('p_body_type', value(LocalBodies::find($powner->local_bodies_id)->body_type));
                    $person_rokka->setValue('p_wardno', value($powner->wardno));
                    $person_rokka->setValue('p_issued_date', value($p_issued_date));
                    $person_rokka->setValue('p_age', value($cyear - ($powner->dob_year)));
                    if ($powner->gender == 1) {
                        $p_gender = 'sf]';
                    } else {
                        $p_gender = 'sL';
                    }

                    if ($powner->gender == '1') {
                        $p_male = 'k"?if';
                    } else {
                        $p_male = 'dlxnf';
                    }
                    $person_rokka->setValue('p_male', value($p_male));
                    $person_rokka->setValue('p_gender', value($p_gender));
                    $person_rokka->setValue('p_nepali_name', value($powner->nepali_name));
                    $person_rokka->setValue('p_english_name', value($powner->english_name));
                    $person_rokka->setValue('p_citizenship_number', value($powner->citizenship_number));
                    $person_rokka->setValue('p_issued_date', value($powner->issued_date));
                    $person_rokka->setValue('p_issued_district', value($powner->issued_district));
                    $person_rokka->setValue('p_dob', value($p_dob));


//                $templateProcessor->setValue('land_province', value(District::find($bl->district_id)->name));
//                $templateProcessor->setValue('land_district', value(Province::find(District::find($bl->district_id)->province_id)));
                    $i = 1;
                    $templateProcessor->cloneRow('land_body_name', 2);
                    $release->cloneRow('land_local_body', 2);
                    $person_rokka->cloneRow('land_local_body', 2);
                    foreach ($land as $l) {
                        $n = $i++;
                        $lborrower = PersonalLandBorrower::where([
                            ['personal_land_id', $l->id], ['borrower_id', $bid], ['property_type', 'personal'], ['status', '1']
                        ])->get();

                        if ($lborrower) {
                            foreach ($lborrower as $lb) {
                                $templateProcessor->setValue('land_body_name#' . $n, LocalBodies::find(PersonalLand::find($lb->personal_land_id)->local_bodies_id)->name);
                                $templateProcessor->setValue('land_wardno#' . $n, PersonalLand::find($lb->personal_land_id)->wardno);
                                $templateProcessor->setValue('land_sheetno#' . $n, PersonalLand::find($lb->personal_land_id)->sheet_no);
                                $templateProcessor->setValue('land_kittano#' . $n, PersonalLand::find($lb->personal_land_id)->kitta_no);
                                $templateProcessor->setValue('land_area#' . $n, PersonalLand::find($lb->personal_land_id)->area);
                                $templateProcessor->setValue('land_remarks#' . $n, PersonalLand::find($lb->personal_land_id)->remarks);


                                $person_rokka->setValue('land_district#' . $n, District::find(PersonalLand::find($lb->personal_land_id)->district_id)->name);
                                $person_rokka->setValue('land_local_body#' . $n, LocalBodies::find(PersonalLand::find($lb->personal_land_id)->local_bodies_id)->name);
                                $person_rokka->setValue('land_wardno#' . $n, PersonalLand::find($lb->personal_land_id)->wardno);
                                $person_rokka->setValue('land_sheetno#' . $n, PersonalLand::find($lb->personal_land_id)->sheet_no);
                                $person_rokka->setValue('land_kittano#' . $n, PersonalLand::find($lb->personal_land_id)->kitta_no);
                                $person_rokka->setValue('land_area#' . $n, PersonalLand::find($lb->personal_land_id)->area);
                                $person_rokka->setValue('land_remarks#' . $n, PersonalLand::find($lb->personal_land_id)->remarks);


                                $release->setValue('land_district#' . $n, District::find(PersonalLand::find($lb->personal_land_id)->district_id)->name);
                                $release->setValue('land_local_body#' . $n, LocalBodies::find(PersonalLand::find($lb->personal_land_id)->local_bodies_id)->name);
                                $release->setValue('land_wardno#' . $n, PersonalLand::find($lb->personal_land_id)->wardno);
                                $release->setValue('land_sheetno#' . $n, PersonalLand::find($lb->personal_land_id)->sheet_no);
                                $release->setValue('land_kittano#' . $n, PersonalLand::find($lb->personal_land_id)->kitta_no);
                                $release->setValue('land_area#' . $n, PersonalLand::find($lb->personal_land_id)->area);
                                $release->setValue('land_remarks#' . $n, PersonalLand::find($lb->personal_land_id)->remarks);


                            }

                        }


                    }

                    $templateProcessor->saveAs(storage_path('results/' . $filename . '_Documents/' . $english_name . '_Personal_Mortgage_Deed_Third.docx'));
                    $release->saveAs(storage_path('results/' . $filename . '_Documents/' . $english_name . '_Personal_Release_Letter_Person_To_Person.docx'));
                    $person_rokka->saveAs(storage_path('results/' . $filename . '_Documents/' . $english_name . '_Personal_Rokka_Letter_Person_To_Person.docx'));
                }

            }
            $zipper = new  \Chumper\Zipper\Zipper;
            $files = glob(storage_path('results/' . $filename . '_Documents/*'));
            Zipper::make(storage_path('results/' . $filename . '_Documents.zip'))->add($files)->close();

            return response()->download(storage_path('results/' . $filename . '_Documents.zip'));


        } else {
            dd('Problem in generating document Please check all the details like client name, address.');
        }

//deletes the file after download
        if (File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::deleteDirectory(storage_path('results/' . $filename . '_Documents/'));
            dd($s . 'hehe');
        }


    }
}
