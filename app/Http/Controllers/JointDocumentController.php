<?php

namespace App\Http\Controllers;

use App\Branch;
use App\District;
use App\Facility;
use App\Http\Requests\Reject;
use App\JointBorrower;
use App\JointFacilities;
use App\JointGuarantorBorrower;
use App\JointHirePurchase;
use App\JointLand;
use App\JointLoan;
use App\JointPropertyOwner;
use App\LocalBodies;
use App\PersonalBorrower;
use App\PersonalGuarantor;
use App\PersonalLand;
use App\PersonalPropertyOwner;
use App\PersonalShare;
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

class JointDocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function condition($bid)
    {
        $vehicle = PersonalBorrower::find($bid)->joint_hire_purchase;
        $land = PersonalBorrower::find($bid)->personal_land_borrower;
        $share = PersonalBorrower::find($bid)->personal_share_borrower;
        if ($vehicle && $land && $share) {
            return redirect()->route('personal.all_in_one', compact('bid'));
        }
    }

    private function Promissory_Note($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }


        //File name
        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            //promissory note
            $templateProcessor = new TemplateProcessor(storage_path('document/joint/Promissory_Note.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $templateProcessor->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $templateProcessor->setValue('amount', value($loan->loan_amount));
            $templateProcessor->setValue('words', value($loan->loan_amount_words));
            $templateProcessor->setValue('borrower_details', value($borrower_details));
            $templateProcessor->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Joint_Promissory_Note.docx'));

        }
    }

    private function share_fukka_third($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }


        $shareproperty = [];
        $owner = [];


        foreach ($borrower->jointborrowerpersonalshare as $share) {
            $shareproperty[] = PersonalShare::find($share->id);
            $owner[] = PersonalPropertyOwner::find($share->property_owner_id);
        }
        $shareowner = (array_unique($owner));

        foreach ($shareowner as $own) {
            //File name
            $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $ShareFukkaThird = new TemplateProcessor(storage_path('document/joint/Share Fukka.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $ShareFukkaThird->setValue('branch', value(Branch::find($loan->branch_id)->location));

                $ShareFukkaThird->setValue('amount', value($loan->loan_amount));
                $ShareFukkaThird->setValue('nepali_name', value($nepali_name));
//                Property Owner Details
                $ShareFukkaThird->setValue('pnepali_name', value($own->nepali_name));

                $sh = [];
                foreach ($shareproperty as $a) {
                    if ($own->id == $a->property_owner_id) {
                        $sh[] = PersonalShare::find($a->id);
                    }
                }

                $count = count($sh);
                $ShareFukkaThird->cloneRow('sharesn', $count);
                $c = 1;
                $total = 0;
                $allfacilities = JointFacilities::where([['borrower_id', $borrower->id]])->get();
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
                    $ShareFukkaThird->setValue('sharesn#' . $n, $n);
                    $ShareFukkaThird->setValue('ownername#' . $n, PersonalPropertyOwner::find($share->property_owner_id)->nepali_name);
                    $ShareFukkaThird->setValue('dpid#' . $n, $share->dpid);
                    $ShareFukkaThird->setValue('clientid#' . $n, $share->client_id);
                    $ShareFukkaThird->setValue('kitta#' . $n, $share->kitta);
                    $ShareFukkaThird->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                    $ShareFukkaThird->setValue('isin_name#' . $n, RegisteredCompany::find($share->isin)->name);
                    $ShareFukkaThird->setValue('share_type#' . $n, $share->share_type);
                }
                $ShareFukkaThird->setValue('share_total', value($total));

            }
            $ShareFukkaThird->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Share_Fukka_' . $own->english_name . '.docx'));
        }

    }

    private function loan_deed_land_share_vehicle($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }

        //File name
        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            //share loan deed
            $LandShareVehicleLoanDeed = new TemplateProcessor(storage_path('document/joint/Land & Share & vehicle.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
            $LandShareVehicleLoanDeed->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $LandShareVehicleLoanDeed->setValue('offerletterdate', value($offerletterdate));
            $LandShareVehicleLoanDeed->setValue('amount', value($loan->loan_amount));
            $LandShareVehicleLoanDeed->setValue('words', value($loan->loan_amount_words));
            $LandShareVehicleLoanDeed->setValue('borrower_details', value($borrower_details));

            $facility = $borrower->joint_facilities;
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

            $joint = $borrower->joint_joint_land()->first();

            if ($joint) {
                $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
            } else {
                $jointp = [];
            }
            $count = count($borrower->jointborrowerpersonalland) + count($jointp);

            $LandShareVehicleLoanDeed->cloneRow('landsn', $count);

            $i = 1;
            foreach ($borrower->jointborrowerpersonalland as $land) {
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


            $count = count($borrower->jointborrowerpersonalshare);
            $LandShareVehicleLoanDeed->cloneRow('sharesn', $count);
            $i = 1;
            foreach ($borrower->jointborrowerpersonalshare as $share) {
                $n = $i++;
                $LandShareVehicleLoanDeed->setValue('sharesn#' . $n, $n);
                $LandShareVehicleLoanDeed->setValue('ownername#' . $n, PersonalPropertyOwner::find($share->property_owner_id)->nepali_name);
                $LandShareVehicleLoanDeed->setValue('dpid#' . $n, $share->dpid);
                $LandShareVehicleLoanDeed->setValue('clientid#' . $n, $share->client_id);
                $LandShareVehicleLoanDeed->setValue('kitta#' . $n, $share->kitta);
                $LandShareVehicleLoanDeed->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                $LandShareVehicleLoanDeed->setValue('share_type#' . $n, $share->share_type);
            }
            $count = count($borrower->joint_hire_purchase);
            $LandShareVehicleLoanDeed->cloneRow('vehiclesn', $count);

            $i = 1;
            foreach ($borrower->joint_hire_purchase as $vehicle) {
                $n = $i++;
                $LandShareVehicleLoanDeed->setValue('vehiclesn#' . $n, $n);
                $LandShareVehicleLoanDeed->setValue('model_number#' . $n, $vehicle->model_number);
                $LandShareVehicleLoanDeed->setValue('registration_number#' . $n, $vehicle->registration_number);
                $LandShareVehicleLoanDeed->setValue('engine_number#' . $n, $vehicle->engine_number);
                $LandShareVehicleLoanDeed->setValue('chassis_number#' . $n, $vehicle->chassis_number);

            }
        }
        $LandShareVehicleLoanDeed->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Joint_Loan_Deed.docx'));
    }

    private function loan_deed_land_share($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }


        //File name
        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            //share loan deed
            $LandandShareLoanDeed = new TemplateProcessor(storage_path('document/joint/Land & Share.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
            $LandandShareLoanDeed->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $LandandShareLoanDeed->setValue('offerletterdate', value($offerletterdate));
            $LandandShareLoanDeed->setValue('amount', value($loan->loan_amount));
            $LandandShareLoanDeed->setValue('words', value($loan->loan_amount_words));
            $LandandShareLoanDeed->setValue('borrower_details', value($borrower_details));

            $facility = $borrower->joint_facilities;
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
            $joint = $borrower->joint_joint_land()->first();
            if ($joint) {
                $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
            } else {
                $jointp = [];
            }
            $count = count($borrower->jointborrowerpersonalland) + count($jointp);
            $LandandShareLoanDeed->cloneRow('landsn', $count);

            $i = 1;
            foreach ($borrower->jointborrowerpersonalland as $land) {
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


            $count = count($borrower->jointborrowerpersonalshare);
            $LandandShareLoanDeed->cloneRow('sharesn', $count);
            $i = 1;
            foreach ($borrower->jointborrowerpersonalshare as $share) {
                $n = $i++;
                $LandandShareLoanDeed->setValue('sharesn#' . $n, $n);
                $LandandShareLoanDeed->setValue('ownername#' . $n, PersonalPropertyOwner::find($share->property_owner_id)->nepali_name);
                $LandandShareLoanDeed->setValue('dpid#' . $n, $share->dpid);
                $LandandShareLoanDeed->setValue('clientid#' . $n, $share->client_id);
                $LandandShareLoanDeed->setValue('kitta#' . $n, $share->kitta);
                $LandandShareLoanDeed->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                $LandandShareLoanDeed->setValue('share_type#' . $n, $share->share_type);
            }


        }
        $LandandShareLoanDeed->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Joint_Loan_Deed.docx'));

    }

    private function loan_deed_land_vehicle($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }


        //File name
        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            //share loan deed
            $LandvehicleLoanDeed = new TemplateProcessor(storage_path('document/joint/Land & vehicle.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
            $LandvehicleLoanDeed->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $LandvehicleLoanDeed->setValue('offerletterdate', value($offerletterdate));
            $LandvehicleLoanDeed->setValue('amount', value($loan->loan_amount));
            $LandvehicleLoanDeed->setValue('words', value($loan->loan_amount_words));
            $LandvehicleLoanDeed->setValue('borrower_details', value($borrower_details));

            $facility = $borrower->joint_facilities;
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
            $joint = $borrower->joint_joint_land()->first();
            if ($joint) {
                $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
            } else {
                $jointp = [];
            }
            $count = count($borrower->jointborrowerpersonalland) + count($jointp);
            $LandvehicleLoanDeed->cloneRow('landsn', $count);
            $i = 1;
            foreach ($borrower->jointborrowerpersonalland as $land) {
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


            $count = count($borrower->joint_hire_purchase);
            $LandvehicleLoanDeed->cloneRow('vehiclesn', $count);

            $i = 1;
            foreach ($borrower->joint_hire_purchase as $vehicle) {
                $n = $i++;
                $LandvehicleLoanDeed->setValue('vehiclesn#' . $n, $n);
                $LandvehicleLoanDeed->setValue('model_number#' . $n, $vehicle->model_number);
                $LandvehicleLoanDeed->setValue('registration_number#' . $n, $vehicle->registration_number);
                $LandvehicleLoanDeed->setValue('engine_number#' . $n, $vehicle->engine_number);
                $LandvehicleLoanDeed->setValue('chassis_number#' . $n, $vehicle->chassis_number);

            }

        }
        $LandvehicleLoanDeed->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Joint_Loan_Deed.docx'));

    }

    private function loan_deed_share_vehicle($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }


        //File name
        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            //share loan deed
            $ShareVehicleLoanDeed = new TemplateProcessor(storage_path('document/joint/Share & Vehicle.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
            $ShareVehicleLoanDeed->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $ShareVehicleLoanDeed->setValue('offerletterdate', value($offerletterdate));
            $ShareVehicleLoanDeed->setValue('amount', value($loan->loan_amount));
            $ShareVehicleLoanDeed->setValue('words', value($loan->loan_amount_words));
            $ShareVehicleLoanDeed->setValue('borrower_details', value($borrower_details));

            $facility = $borrower->joint_facilities;
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

            $count = count($borrower->jointborrowerpersonalshare);

            $ShareVehicleLoanDeed->cloneRow('sharesn', $count);
            $i = 1;
            foreach ($borrower->jointborrowerpersonalshare as $share) {
                $n = $i++;
                $ShareVehicleLoanDeed->setValue('sharesn#' . $n, $n);
                $ShareVehicleLoanDeed->setValue('ownername#' . $n, PersonalPropertyOwner::find($share->property_owner_id)->nepali_name);
                $ShareVehicleLoanDeed->setValue('dpid#' . $n, $share->dpid);
                $ShareVehicleLoanDeed->setValue('clientid#' . $n, $share->client_id);
                $ShareVehicleLoanDeed->setValue('kitta#' . $n, $share->kitta);
                $ShareVehicleLoanDeed->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                $ShareVehicleLoanDeed->setValue('share_type#' . $n, $share->share_type);
            }


            $count = count($borrower->joint_hire_purchase);
            $ShareVehicleLoanDeed->cloneRow('vehiclesn', $count);

            $i = 1;
            foreach ($borrower->joint_hire_purchase as $vehicle) {
                $n = $i++;
                $ShareVehicleLoanDeed->setValue('vehiclesn#' . $n, $n);
                $ShareVehicleLoanDeed->setValue('model_number#' . $n, $vehicle->model_number);
                $ShareVehicleLoanDeed->setValue('registration_number#' . $n, $vehicle->registration_number);
                $ShareVehicleLoanDeed->setValue('engine_number#' . $n, $vehicle->engine_number);
                $ShareVehicleLoanDeed->setValue('chassis_number#' . $n, $vehicle->chassis_number);

            }


        }
        $ShareVehicleLoanDeed->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Joint_Loan_Deed.docx'));

    }

    private function loan_deed_share_only($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }


        //File name
        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            //share loan deed
            $ShareOnlyLoanDeed = new TemplateProcessor(storage_path('document/joint/Share_Only.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
            $ShareOnlyLoanDeed->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $ShareOnlyLoanDeed->setValue('offerletterdate', value($offerletterdate));
            $ShareOnlyLoanDeed->setValue('amount', value($loan->loan_amount));
            $ShareOnlyLoanDeed->setValue('words', value($loan->loan_amount_words));
            $ShareOnlyLoanDeed->setValue('borrower_details', value($borrower_details));

            $facility = $borrower->joint_facilities;
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

            $count = count($borrower->jointborrowerpersonalshare);
            $ShareOnlyLoanDeed->cloneRow('sharesn', $count);
            $i = 1;
            foreach ($borrower->jointborrowerpersonalshare as $share) {
                $n = $i++;
                $ShareOnlyLoanDeed->setValue('sharesn#' . $n, $n);
                $ShareOnlyLoanDeed->setValue('ownername#' . $n, PersonalPropertyOwner::find($share->property_owner_id)->nepali_name);
                $ShareOnlyLoanDeed->setValue('dpid#' . $n, $share->dpid);
                $ShareOnlyLoanDeed->setValue('clientid#' . $n, $share->client_id);
                $ShareOnlyLoanDeed->setValue('kitta#' . $n, $share->kitta);
                $ShareOnlyLoanDeed->setValue('isin#' . $n, RegisteredCompany::find($share->isin)->isin);
                $ShareOnlyLoanDeed->setValue('share_type#' . $n, $share->share_type);
            }
        }
        $ShareOnlyLoanDeed->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Joint_Loan_Deed.docx'));

    }

    private function loan_deed_vehicle_only($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }


        //File name
        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            //share loan deed
            $VehicleOnlyLoanDeed = new TemplateProcessor(storage_path('document/joint/Vehicle_Only.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
            $VehicleOnlyLoanDeed->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $VehicleOnlyLoanDeed->setValue('offerletterdate', value($offerletterdate));
            $VehicleOnlyLoanDeed->setValue('amount', value($loan->loan_amount));
            $VehicleOnlyLoanDeed->setValue('words', value($loan->loan_amount_words));
            $VehicleOnlyLoanDeed->setValue('borrower_details', value($borrower_details));
            $facility = $borrower->joint_facilities;
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

            $count = count($borrower->joint_hire_purchase);
            $VehicleOnlyLoanDeed->cloneRow('vehiclesn', $count);

            $i = 1;
            foreach ($borrower->joint_hire_purchase as $vehicle) {
                $n = $i++;
                $VehicleOnlyLoanDeed->setValue('vehiclesn#' . $n, $n);
                $VehicleOnlyLoanDeed->setValue('model_number#' . $n, $vehicle->model_number);
                $VehicleOnlyLoanDeed->setValue('registration_number#' . $n, $vehicle->registration_number);
                $VehicleOnlyLoanDeed->setValue('engine_number#' . $n, $vehicle->engine_number);
                $VehicleOnlyLoanDeed->setValue('chassis_number#' . $n, $vehicle->chassis_number);

            }
        }
        $VehicleOnlyLoanDeed->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Joint_Loan_Deed.docx'));

    }

    private function loan_deed_land_only($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }


        //File name
        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            //share loan deed
            $LandOnlyLoanDeed = new TemplateProcessor(storage_path('document/joint/Land_Only.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
            $LandOnlyLoanDeed->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $LandOnlyLoanDeed->setValue('offerletterdate', value($offerletterdate));
            $LandOnlyLoanDeed->setValue('amount', value($loan->loan_amount));
            $LandOnlyLoanDeed->setValue('words', value($loan->loan_amount_words));
            $LandOnlyLoanDeed->setValue('borrower_details', value($borrower_details));

            $facility = $borrower->joint_facilities;
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
            $joint = $borrower->joint_joint_land()->first();
            if ($joint) {
                $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
            } else {
                $jointp = [];
            }
            $count = count($borrower->jointborrowerpersonalland) + count($jointp);
            $LandOnlyLoanDeed->cloneRow('landsn', $count);

            $i = 1;
            foreach ($borrower->jointborrowerpersonalland as $land) {

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

            $LandOnlyLoanDeed->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Joint_Loan_Deed.docx'));
        }
    }

    private function loan_deed_facilities_only($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }


        //File name
        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            //share loan deed
            $FacilitiesOnly = new TemplateProcessor(storage_path('document/joint/Facilities_Only.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
            $FacilitiesOnly->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $FacilitiesOnly->setValue('offerletterdate', value($offerletterdate));
            $FacilitiesOnly->setValue('amount', value($loan->loan_amount));
            $FacilitiesOnly->setValue('words', value($loan->loan_amount_words));
            $FacilitiesOnly->setValue('borrower_details', value($borrower_details));

            $facility = $borrower->joint_facilities;
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
        $FacilitiesOnly->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Joint_Loan_Deed.docx'));

    }

    private function personal_guarantor($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }

        $guarantor = $borrower->personal_guarantor_borrower;
        //File name
        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
//personal guarantor


            foreach ($borrower->jointborrowerpersonalguarantor as $pg) {

                $personalguarantor = new TemplateProcessor(storage_path('document/joint/Personal_Guarantor.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $personalguarantor->setValue('branch', value(Branch::find($loan->branch_id)->location));
                $personalguarantor->setValue('offerletterdate', value($offerletterdate));
                $personalguarantor->setValue('amount', value($loan->loan_amount));
                $personalguarantor->setValue('words', value($loan->loan_amount_words));
                $personalguarantor->setValue('borrower_details', value($borrower_details));

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

    private function bonus_right_cash_divident($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }


        foreach ($borrower->jointborrowerpersonalshare as $share) {
            //File name
            $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $BonusRightCashDivident = new TemplateProcessor(storage_path('document/joint/Bonus Right.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $BonusRightCashDivident->setValue('branch', value(Branch::find($loan->branch_id)->location));

//                Property Owner Details
                $BonusRightCashDivident->setValue('ownername', value(PersonalPropertyOwner::find($share->property_owner_id)->nepali_name));
                $BonusRightCashDivident->setValue('nepali_name', value($nepali_name));
                $BonusRightCashDivident->setValue('dpid', $share->dpid);
                $BonusRightCashDivident->setValue('clientid', $share->client_id);
                $BonusRightCashDivident->setValue('kitta', $share->kitta);
                $BonusRightCashDivident->setValue('isinname', RegisteredCompany::find($share->isin)->name);

            }
            $BonusRightCashDivident->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Bonus_Right_Cash_Divident_' . RegisteredCompany::find($share->isin)->isin . '.docx'));
        }

    }

    private function vehicle_transfer_letter_bank_favour($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }

        foreach ($borrower->joint_hire_purchase as $vehicle) {


            //File name
            $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $VehicleRegistrationLetter = new TemplateProcessor(storage_path('document/joint/Vehicle Transfer Letter Favour of Bank.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document


                $VehicleRegistrationLetter->setValue('nepali_name', value($nepali_name));
                $VehicleRegistrationLetter->setValue('branch', value(Branch::find($loan->branch_id)->location));


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
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }

        foreach ($borrower->joint_hire_purchase as $vehicle) {


            //File name
            $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $VehicleRegistrationLetter = new TemplateProcessor(storage_path('document/joint/Vehicle Fukka Letter.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document


                $VehicleRegistrationLetter->setValue('nepali_name', value($nepali_name));
                $VehicleRegistrationLetter->setValue('branch', value(Branch::find($loan->branch_id)->location));


                $VehicleRegistrationLetter->setValue('model_number', value($vehicle->model_number));
                $VehicleRegistrationLetter->setValue('registration_number', value($vehicle->registration_number));
                $VehicleRegistrationLetter->setValue('engine_number', value($vehicle->engine_number));
                $VehicleRegistrationLetter->setValue('chassis_number', value($vehicle->chassis_number));


            }
            $VehicleRegistrationLetter->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Vehicle_Fukka_Letter_' . $vehicle->model_number . '.docx'));
        }
    }

    private function swap_commitment_letter($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }


        //File name
        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            //share loan deed
            $SwapCommitmentLetter = new TemplateProcessor(storage_path('document/joint/Swap Commitment Letter.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
            $SwapCommitmentLetter->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $SwapCommitmentLetter->setValue('offerletterdate', value($offerletterdate));
            $SwapCommitmentLetter->setValue('amount', value($loan->loan_amount));
            $SwapCommitmentLetter->setValue('words', value($loan->loan_amount_words));
            $SwapCommitmentLetter->setValue('borrower_details', value($borrower_details));

            $SwapCommitmentLetter->cloneRow('gsn', count($borrower->jointborrowerpersonalguarantor));
            $i = 1;
            foreach ($borrower->jointborrowerpersonalguarantor as $g) {
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
            foreach ($borrower->jointborrowerpersonalland as $land) {
                $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
            }
            $personal_land_owner = array_unique($landowner);

            $joint = $borrower->joint_joint_land()->first();
            if ($joint) {
                $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
            } else {
                $jointp = [];
            }


            $SwapCommitmentLetter->cloneRow('psn', count($personal_land_owner) + count($jointp));
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
                        $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
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
                        ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
                }


                $n = $i++;
                $SwapCommitmentLetter->setValue('psn#' . $n, $n);
                $SwapCommitmentLetter->setValue('property_owner#' . $n, $property);

            }


            $joint = $borrower->joint_joint_land()->first();
            if ($joint) {
                $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
            } else {
                $jointp = [];
            }
            $count = count($borrower->jointborrowerpersonalland) + count($jointp);
            $SwapCommitmentLetter->cloneRow('landsn', $count);

            $i = 1;
            foreach ($borrower->jointborrowerpersonalland as $land) {

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

    private function manjurinama_of_hirepurchase($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;
            $address = District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ', ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' / ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type .
                ' j*f g+=' . $b3->wardno;
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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;
            $address = District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' / ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }
        $guarantor = $borrower->personal_guarantor_borrower;
        //File name
        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {


            $manjurinama = new TemplateProcessor(storage_path('document/joint/Manjurinama_ of_Hire_Purchase.docx'));
            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
            $manjurinama->setValue('branch', value(Branch::find($loan->branch_id)->location));
            $manjurinama->setValue('address', value($address));
            $manjurinama->setValue('nepali_name', value($nepali_name));
            $manjurinama->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Manjurinama_of_Hirepurchase.docx'));
        }
    }

    private function pledge_deed_third($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }
        $shareproperty = [];
        $owner = [];
        foreach ($borrower->jointborrowerpersonalshare as $share) {
            $shareproperty[] = PersonalShare::find($share->id);
            $owner[] = PersonalPropertyOwner::find($share->property_owner_id);

        }
        $shareowner = (array_unique($owner));

        foreach ($shareowner as $own) {
            //File name
            $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $PledgeDeedThird = new TemplateProcessor(storage_path('document/joint/Pledge Deed Third.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $PledgeDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                $PledgeDeedThird->setValue('offerletterdate', value($offerletterdate));
                $PledgeDeedThird->setValue('amount', value($loan->loan_amount));
                $PledgeDeedThird->setValue('words', value($loan->loan_amount_words));
                $PledgeDeedThird->setValue('borrower_details', value($borrower_details));

//              Property Owner Details
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

    private function land_rokka_letter_third($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }
        //File name

        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {

            $landowner = [];
            $malpot = [];
            foreach ($borrower->jointborrowerpersonalland as $land) {
                $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
                $malpot[] = $land->malpot;
            }
            $personal_land_owner = array_unique($landowner);
            $malpot = array_unique($malpot);
            $mlp = 1;
            foreach ($malpot as $m) {
                foreach ($personal_land_owner as $p) {

                    $ld = 0;
                    foreach ($borrower->jointborrowerpersonalland as $l) {
                        if ($m == $l->malpot) {
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
                            $MortgageDeedThird = new TemplateProcessor(storage_path('document/joint/Rokka Letter Malpot Person to Person.docx'));
                            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                            $MortgageDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                            $MortgageDeedThird->setValue('amount', value($loan->loan_amount));
                            $MortgageDeedThird->setValue('words', value($loan->loan_amount_words));

                            $MortgageDeedThird->setValue('nepali_name', value($nepali_name));

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
                            foreach ($borrower->jointborrowerpersonalland as $l) {

                                if ($m == $l->malpot) {

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

    private function land_fukka_letter_third($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }

        //File name

        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            $landowner = [];
            $malpot = [];
            foreach ($borrower->jointborrowerpersonalland as $land) {

                $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
                $malpot[] = $land->malpot;
            }
            $personal_land_owner = array_unique($landowner);
            $malpot = array_unique($malpot);
            $mlp = 1;
            foreach ($malpot as $m) {
                foreach ($personal_land_owner as $p) {

                    $ld = 0;
                    foreach ($borrower->jointborrowerpersonalland as $l) {
                        if ($m == $l->malpot) {
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
                            $MortgageDeedThird = new TemplateProcessor(storage_path('document/joint/Fukka Letter Malpot Third.docx'));
                            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                            $MortgageDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                            $MortgageDeedThird->setValue('amount', value($loan->loan_amount));
                            $MortgageDeedThird->setValue('words', value($loan->loan_amount_words));

                            $MortgageDeedThird->setValue('nepali_name', value($nepali_name));

//                Property Owner Details
                            $MortgageDeedThird->setValue('pnepali_name', value($p->nepali_name));

                            $MortgageDeedThird->setValue('lmalpot', value($m));
                            $MortgageDeedThird->cloneRow('landsn', $ld);
                            $i = 1;
                            foreach ($borrower->jointborrowerpersonalland as $l) {

                                if ($m == $l->malpot) {

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

    private function share_rokka_third($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }

        $shareproperty = [];
        $owner = [];
        $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();
        foreach ($borrower->jointborrowerpersonalshare as $share) {

            $shareproperty[] = PersonalShare::find($share->id);
            $owner[] = PersonalPropertyOwner::find($share->property_owner_id);

        }
        $shareowner = (array_unique($owner));

        foreach ($shareowner as $own) {
            //File name
            $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $ShareRokkaThird = new TemplateProcessor(storage_path('document/joint/Share Rokka Third.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $ShareRokkaThird->setValue('branch', value(Branch::find($loan->branch_id)->location));

                $ShareRokkaThird->setValue('amount', value($loan->loan_amount));
                $ShareRokkaThird->setValue('nepali_name', value($nepali_name));
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
                $allfacilities = JointFacilities::where([['borrower_id', $borrower->id]])->get();
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

    private function tieup_deed_third($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }

        //File name

        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            $landowner = [];
            foreach ($borrower->jointborrowerpersonalland as $land) {
                $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
            }
            $personal_land_owner = array_unique($landowner);
            $mlp = 1;
            foreach ($personal_land_owner as $p) {

                $ld = 0;
                foreach ($borrower->jointborrowerpersonalland as $l) {
                    if ($l->property_owner_id == $p->id) {
                        $ld = $ld + 1;
                    }
                }


                //mortgage deed
                $MortgageDeedThird = new TemplateProcessor(storage_path('document/joint/Tieup Deed Third for Enhancement.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $MortgageDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                $MortgageDeedThird->setValue('offerletterdate', value($offerletterdate));
                $MortgageDeedThird->setValue('amount', value($loan->loan_amount));
                $MortgageDeedThird->setValue('words', value($loan->loan_amount_words));
                $MortgageDeedThird->setValue('borrower_details', value($borrower_details));

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
                foreach ($borrower->jointborrowerpersonalland as $l) {

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
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }


        //File name

        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            $landowner = [];
            foreach ($borrower->jointborrowerpersonalland as $land) {
                $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
            }
            $personal_land_owner = array_unique($landowner);

            $mlp = 1;
            foreach ($personal_land_owner as $p) {
                $ld = 0;
                foreach ($borrower->jointborrowerpersonalland as $l) {
                    if ($l->property_owner_id == $p->id) {
                        $ld = $ld + 1;
                    }
                }

                //mortgage deed
                $MortgageDeedSelf = new TemplateProcessor(storage_path('document/joint/Consent of Property Owner.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $MortgageDeedSelf->setValue('branch', value(Branch::find($loan->branch_id)->location));
                $MortgageDeedSelf->setValue('offerletterdate', value($offerletterdate));
                $MortgageDeedSelf->setValue('amount', value($loan->loan_amount));
                $MortgageDeedSelf->setValue('nepali_name', value($nepali_name));
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
                foreach ($borrower->jointborrowerpersonalland as $l) {
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
                $MortgageDeedSelf->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Consent_of_Property_Owner_Joint_' . $mlp++ . '.docx'));
            }
        }
    }

    private function anusuchi_18($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }

        $shareproperty = [];
        $owner = [];

        foreach ($borrower->jointborrowerpersonalshare as $share) {
            $shareproperty[] = PersonalShare::find($share->id);
            $owner[] = PersonalPropertyOwner::find($share->property_owner_id);
        }
        $shareowner = (array_unique($owner));

        foreach ($shareowner as $own) {
            //File name
            $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $Anushchi18 = new TemplateProcessor(storage_path('document/joint/Anusuchi 18.docx'));
                Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                $Anushchi18->setValue('branch', value(Branch::find($loan->branch_id)->location));

//                Property Owner Details
                $Anushchi18->setValue('pgrandfather_name', value($own->grandfather_name));
                $Anushchi18->setValue('pgrandfather_relation', value($own->grandfather_relation));
                $Anushchi18->setValue('pfather_name', value($own->father_name));
                $Anushchi18->setValue('phone', value($own->phone));
                $Anushchi18->setValue('pfather_relation', value($own->father_relation));
                if ($own->spouse_name) {
                    $spouse_name = ' ' . $own->spouse_name . 'sf] ' . $own->spouse_relation;
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

    private function anusuchi_19($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }

        $shareproperty = [];
        $owner = [];

        foreach ($borrower->jointborrowerpersonalshare as $share) {
            $shareproperty[] = PersonalShare::find($share->id);
            $owner[] = PersonalPropertyOwner::find($share->property_owner_id);
        }
        $shareowner = (array_unique($owner));

        foreach ($shareowner as $own) {
            //File name
            $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {
                //share loan deed
                $Anushchi19 = new TemplateProcessor(storage_path('document/joint/Anusuchi 19.docx'));
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

    private function consent_of_property_owner_joint($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }

        //File name

        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {
            $landowner = [];

            $joint = $borrower->joint_joint_land()->first();
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
                    $MortgageDeedSelf = new TemplateProcessor(storage_path('document/joint/Consent of Property Owner.docx'));
                    Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                    $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                    $MortgageDeedSelf->setValue('branch', value(Branch::find($loan->branch_id)->location));
                    $MortgageDeedSelf->setValue('offerletterdate', value($offerletterdate));
                    $MortgageDeedSelf->setValue('amount', value($loan->loan_amount));
                    $MortgageDeedSelf->setValue('words', value($loan->loan_amount_words));
                    $MortgageDeedSelf->setValue('nepali_name', value($nepali_name));

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

    private function mortgage_deed_third($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

            if ($b1->spouse_name) {
                $b1spouse_name = ' ' . $b1->spouse_name . 'sf] ' . $b1->spouse_relation . null;
            } else {
                $b1spouse_name = null;
            }
            $g = $b1->gender;
            if ($g == 1) {
                $b1gender = 'sf]';
                $b1male = 'k\"?if';
            } else {
                $b1gender = 'sL';
                $b1male = 'dlxnf';
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
                $b2male = 'k\"?if';
            } else {
                $b2gender = 'sL';
                $b2male = 'dlxnf';
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
                $b3male = 'k\"?if';
            } else {
                $b3gender = 'sL';
                $b3male = 'dlxnf';
            }
            $b3issued_date = $b3->issued_year . '÷' . $b3->issued_month . '÷' . $b3->issued_day;

            $bnepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b2->nepali_name;
            $benglish_name = $b1->english_name . ', ' . $b2->english_name . ' and ' . $b2->english_name;
            $b1dob = $b1->dob_year . '÷' . $b1->dob_month . '÷' . $b1->dob_day;
            $b2dob = $b2->dob_year . '÷' . $b2->dob_month . '÷' . $b2->dob_day;
            $b3dob = $b3->dob_year . '÷' . $b3->dob_month . '÷' . $b3->dob_day;
            $bdob = $b1dob . ', ' . $b2dob . ' / ' . $b3dob;
            $b1address = District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b1->wardno;
            $b2address = District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b2->wardno;
            $b3address = District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b3->wardno;

            $baddress = $b1address . ', ' . $b2address . ' / ' . $b3address;
            $bissued_district = District::find($b1->issued_district)->name . ', ' . District::find($b2->issued_district)->name . ' / ' . District::find($b3->issued_district)->name;
            $bmale = $b1male . ', ' . $b2male . ' / ' . $b3male;
            if ($b1->spouse_name && $b2->spouse_name && $b3->spouse_name) {
                $bspouse_name = $b1->spouse_name . ', ' . $b2->spouse_name . ' / ' . $b2->spouse_name;
            } elseif ($b1->spouse_name && $b2->spouse_name) {
                $bspouse_name = $b1->spouse_name . ' / ' . $b2->spouse_name;
            } elseif ($b2->spouse_name && $b3->spouse_name) {
                $bspouse_name = $b2->spouse_name . ' / ' . $b3->spouse_name;
            } elseif ($b1->spouse_name && $b3->spouse_name) {
                $bspouse_name = $b1->spouse_name . ' / ' . $b3->spouse_name;
            } else {
                $bspouse_name = null;
            }

            $bfather_name = $b1->father_name . ', ' . $b2->father_name . ' / ' . $b3->father_name;
            $bphone = $b1->phone . ', ' . $b2->phone . ' / ' . $b3->phone;
            $bgrandfather_name = $b1->grandfather_name . ', ' . $b2->grandfather_name . ' / ' . $b3->grandfather_name;
            $citizenship_details = $b1->citizenship_number . $b1issued_date . ', ' . $b2->citizenship_number . $b2issued_date . ' / ' . $b3->citizenship_number . $b3issued_date;

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

            if ($b1->spouse_name) {
                $b1spouse_name = ' ' . $b1->spouse_name . 'sf] ' . $b1->spouse_relation . null;
            } else {
                $b1spouse_name = null;
            }
            $g = $b1->gender;
            if ($g == 1) {
                $b1gender = 'sf]';
                $b1male = 'k\"?if';
            } else {
                $b1gender = 'sL';
                $b1male = 'dlxnf';
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
                $b2male = 'k\"?if';
            } else {
                $b2gender = 'sL';
                $b2male = 'dlxnf';
            }
            $b2issued_date = $b2->issued_year . '÷' . $b2->issued_month . '÷' . $b2->issued_day;

            $bnepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;
            $benglish_name = $b1->english_name . ' and ' . $b2->english_name;
            $b1dob = $b1->dob_year . '÷' . $b1->dob_month . '÷' . $b1->dob_day;
            $b2dob = $b2->dob_year . '÷' . $b2->dob_month . '÷' . $b2->dob_day;
            $bdob = $b1dob . ' / ' . $b2dob;
            $b1address = District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b1->wardno;
            $b2address = District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b2->wardno;
            $baddress = $b1address . ' / ' . $b2address;
            $bissued_district = District::find($b1->issued_district)->name . ' / ' . District::find($b2->issued_district)->name;
            $bmale = $b1male . ' / ' . $b2male;
            if ($b1->spouse_name && $b2->spouse_name) {
                $bspouse_name = $b1->spouse_name . ' / ' . $b2->spouse_name;
            } elseif ($b1->spouse_name) {
                $bspouse_name = $b1->spouse_name;
            } elseif ($b2->spouse_name) {
                $bspouse_name = $b2->spouse_name;
            } else {
                $bspouse_name = null;
            }
            $bfather_name = $b1->father_name . ' / ' . $b2->father_name;
            $bphone = $b1->phone . ' / ' . $b2->phone;
            $bgrandfather_name = $b1->grandfather_name . ' / ' . $b2->grandfather_name;
            $citizenship_details = $b1->citizenship_number . $b1issued_date . ' / ' . $b2->citizenship_number . $b2issued_date;


            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }

        //File name
        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');
//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }
        if ($s == true) {
            $landowner = [];
            $malpot = [];
            foreach ($borrower->jointborrowerpersonalland as $land) {
                $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
                $malpot[] = $land->malpot;
            }
            $personal_land_owner = array_unique($landowner);
            $malpot = array_unique($malpot);
            $mlp = 1;
            foreach ($malpot as $m) {
                foreach ($personal_land_owner as $p) {

                    $ld = 0;
                    foreach ($borrower->jointborrowerpersonalland as $l) {
                        if ($m == $l->malpot) {
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
                            if($b3){
                                $MortgageDeedThird = new TemplateProcessor(storage_path('document/joint/Mortgage Deed Third b3.docx'));
                            }else{
                                $MortgageDeedThird = new TemplateProcessor(storage_path('document/joint/Mortgage Deed Third.docx'));
                            }

                            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                            $MortgageDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                            $MortgageDeedThird->setValue('offerletterdate', value($offerletterdate));
                            $MortgageDeedThird->setValue('amount', value($loan->loan_amount));
                            $MortgageDeedThird->setValue('words', value($loan->loan_amount_words));
                            $MortgageDeedThird->setValue('borrower_details', value($borrower_details));

                            $MortgageDeedThird->setValue('nepali_name', value($bnepali_name));

//borrower details
                            if ($b1->spouse_name) {
                                $b1spouse_name = $b1->spouse_name;
                            } else {
                                $b1spouse_name = null;
                            }
                            $g = $b1->gender;
                            if ($g == 1) {
                                $b1male = 'k\"?if';
                            } else {
                                $b1male = 'dlxnf';
                            }
                            $b1issued_date = $b1->issued_year . '÷' . $b1->issued_month . '÷' . $b1->issued_day;
                            $b1dob = $b1->dob_year . '÷' . $b1->dob_month . '÷' . $b1->dob_day;
                            $b1address = District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b1->wardno;
                            $b1issued_district = District::find($b1->issued_district)->name;
                            $b1citizenship_details = $b1->citizenship_number.' / '.$b1issued_date;

                            $MortgageDeedThird->setValue('b1nepali_name', value($b1->nepali_name));
                            $MortgageDeedThird->setValue('b1phone', value($b1->phone));
                            $MortgageDeedThird->setValue('b1english_name', value($b1->english_name));
                            $MortgageDeedThird->setValue('b1dob', value($b1dob));
                            $MortgageDeedThird->setValue('b1address', value($b1address));
                            $MortgageDeedThird->setValue('b1male', value($b1male));
                            $MortgageDeedThird->setValue('b1spouse_name', value($b1spouse_name));
                            $MortgageDeedThird->setValue('b1father_name', value($b1->father_name));
                            $MortgageDeedThird->setValue('b1grandfather_name', value($b1->grandfather_name));
                            $MortgageDeedThird->setValue('b1citizenship_details', value($b1citizenship_details));
                            $MortgageDeedThird->setValue('b1issued_district', value($b1issued_district));


                            if ($b2->spouse_name) {
                                $b2spouse_name = $b2->spouse_name;
                            } else {
                                $b2spouse_name = null;
                            }
                            $g = $b2->gender;
                            if ($g == 1) {
                                $b2male = 'k\"?if';
                            } else {
                                $b2male = 'dlxnf';
                            }
                            $b2issued_date = $b2->issued_year . '÷' . $b2->issued_month . '÷' . $b2->issued_day;
                            $b2dob = $b2->dob_year . '÷' . $b2->dob_month . '÷' . $b2->dob_day;
                            $b2address = District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b2->wardno;
                            $b2issued_district = District::find($b2->issued_district)->name;
                            $b2citizenship_details = $b2->citizenship_number .' / '. $b2issued_date;

                            $MortgageDeedThird->setValue('b2nepali_name', value($b2->nepali_name));
                            $MortgageDeedThird->setValue('b2phone', value($b2->phone));
                            $MortgageDeedThird->setValue('b2english_name', value($b2->english_name));
                            $MortgageDeedThird->setValue('b2dob', value($b2dob));
                            $MortgageDeedThird->setValue('b2address', value($b2address));
                            $MortgageDeedThird->setValue('b2male', value($b2male));
                            $MortgageDeedThird->setValue('b2spouse_name', value($b2spouse_name));
                            $MortgageDeedThird->setValue('b2father_name', value($b2->father_name));
                            $MortgageDeedThird->setValue('b2grandfather_name', value($b2->grandfather_name));
                            $MortgageDeedThird->setValue('b2citizenship_details', value($b2citizenship_details));
                            $MortgageDeedThird->setValue('b2issued_district', value($b2issued_district));

                            if($b3){
                                if ($b3->spouse_name) {
                                    $b3spouse_name = $b3->spouse_name;
                                } else {
                                    $b3spouse_name = null;
                                }
                                $g = $b3->gender;
                                if ($g == 1) {
                                    $b3male = 'k\"?if';
                                } else {
                                    $b3male = 'dlxnf';
                                }
                                $b3issued_date = $b3->issued_year . '÷' . $b3->issued_month . '÷' . $b3->issued_day;
                                $b3dob = $b3->dob_year . '÷' . $b3->dob_month . '÷' . $b3->dob_day;
                                $b3address = District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b3->wardno;
                                $b3issued_district = District::find($b3->issued_district)->name;
                                $b3citizenship_details = $b3->citizenship_number .' / '. $b3issued_date;

                                $MortgageDeedThird->setValue('b3nepali_name', value($b3->nepali_name));
                                $MortgageDeedThird->setValue('b3phone', value($b3->phone));
                                $MortgageDeedThird->setValue('b3english_name', value($b3->english_name));
                                $MortgageDeedThird->setValue('b3dob', value($b3dob));
                                $MortgageDeedThird->setValue('b3address', value($b3address));
                                $MortgageDeedThird->setValue('b3male', value($b3male));
                                $MortgageDeedThird->setValue('b3spouse_name', value($b3spouse_name));
                                $MortgageDeedThird->setValue('b3father_name', value($b3->father_name));
                                $MortgageDeedThird->setValue('b3grandfather_name', value($b3->grandfather_name));
                                $MortgageDeedThird->setValue('b3citizenship_details', value($b3citizenship_details));
                                $MortgageDeedThird->setValue('b3issued_district', value($b3issued_district));
                            }
                            // Property Owner Details
                            $MortgageDeedThird->setValue('pphone', value($p->phone));
                            $MortgageDeedThird->setValue('pgrandfather_name', value($p->grandfather_name));
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
                            foreach ($borrower->jointborrowerpersonalland as $l) {
                                if ($m == $l->malpot) {
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

    private function mortgage_deed_third_joint2($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

            if ($b1->spouse_name) {
                $b1spouse_name = ' ' . $b1->spouse_name . 'sf] ' . $b1->spouse_relation . null;
            } else {
                $b1spouse_name = null;
            }
            $g = $b1->gender;
            if ($g == 1) {
                $b1gender = 'sf]';
                $b1male = 'k\"?if';
            } else {
                $b1gender = 'sL';
                $b1male = 'dlxnf';
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
                $b2male = 'k\"?if';
            } else {
                $b2gender = 'sL';
                $b2male = 'dlxnf';
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
                $b3male = 'k\"?if';
            } else {
                $b3gender = 'sL';
                $b3male = 'dlxnf';
            }
            $b3issued_date = $b3->issued_year . '÷' . $b3->issued_month . '÷' . $b3->issued_day;

            $bphone = $b1->phone . ', ' . $b2->phone . ' / ' . $b3->phone;
            $bnepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;
            $benglish_name = $b1->english_name . ', ' . $b2->english_name . ' and ' . $b3->english_name;
            $b1dob = $b1->dob_year . '÷' . $b1->dob_month . '÷' . $b1->dob_day;
            $b2dob = $b2->dob_year . '÷' . $b2->dob_month . '÷' . $b2->dob_day;
            $b3dob = $b3->dob_year . '÷' . $b3->dob_month . '÷' . $b3->dob_day;
            $bdob = $b1dob . ', ' . $b2dob . ' / ' . $b3dob;
            $b1address = District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b1->wardno;
            $b2address = District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b2->wardno;
            $b3address = District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b3->wardno;

            $baddress = $b1address . ', ' . $b2address . ' / ' . $b3address;
            $bissued_district = District::find($b1->issued_district)->name . ', ' . District::find($b2->issued_district)->name . ' / ' . District::find($b3->issued_district)->name;
            $bmale = $b1male . ', ' . $b2male . ' / ' . $b3male;
            if ($b1->spouse_name && $b2->spouse_name && $b3->spouse_name) {
                $bspouse_name = $b1->spouse_name . ', ' . $b2->spouse_name . ' / ' . $b2->spouse_name;
            } elseif ($b1->spouse_name && $b2->spouse_name) {
                $bspouse_name = $b1->spouse_name . ' / ' . $b2->spouse_name;
            } elseif ($b2->spouse_name && $b3->spouse_name) {
                $bspouse_name = $b2->spouse_name . ' / ' . $b3->spouse_name;
            } elseif ($b1->spouse_name && $b3->spouse_name) {
                $bspouse_name = $b1->spouse_name . ' / ' . $b3->spouse_name;
            } else {
                $bspouse_name = null;
            }
            $bfather_name = $b1->father_name . ', ' . $b2->father_name . ' / ' . $b3->father_name;
            $bgrandfather_name = $b1->grandfather_name . ', ' . $b2->grandfather_name . ' / ' . $b3->grandfather_name;
            $citizenship_details = $b1->citizenship_number . $b1issued_date . ', ' . $b2->citizenship_number . $b2issued_date . ' / ' . $b3->citizenship_number . $b3issued_date;

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

            if ($b1->spouse_name) {
                $b1spouse_name = ' ' . $b1->spouse_name . 'sf] ' . $b1->spouse_relation . null;
            } else {
                $b1spouse_name = null;
            }
            $g = $b1->gender;
            if ($g == 1) {
                $b1gender = 'sf]';
                $b1male = 'k\"?if';
            } else {
                $b1gender = 'sL';
                $b1male = 'dlxnf';
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
                $b2male = 'k\"?if';
            } else {
                $b2gender = 'sL';
                $b2male = 'dlxnf';
            }
            $b2issued_date = $b2->issued_year . '÷' . $b2->issued_month . '÷' . $b2->issued_day;
            $bphone = $b1->phone . ' / ' . $b2->phone;
            $bnepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;
            $benglish_name = $b1->english_name . ' and ' . $b2->english_name;
            $b1dob = $b1->dob_year . '÷' . $b1->dob_month . '÷' . $b1->dob_day;
            $b2dob = $b2->dob_year . '÷' . $b2->dob_month . '÷' . $b2->dob_day;
            $bdob = $b1dob . ' / ' . $b2dob;
            $b1address = District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b1->wardno;
            $b2address = District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b2->wardno;
            $baddress = $b1address . ' / ' . $b2address;
            $bissued_district = District::find($b1->issued_district)->name . ' / ' . District::find($b2->issued_district)->name;
            $bmale = $b1male . ' / ' . $b2male;
            if ($b1->spouse_name && $b2->spouse_name) {
                $bspouse_name = $b1->spouse_name . ' / ' . $b2->spouse_name;
            } elseif ($b1->spouse_name) {
                $bspouse_name = $b1->spouse_name;
            } elseif ($b2->spouse_name) {
                $bspouse_name = $b2->spouse_name;
            } else {
                $bspouse_name = null;
            }
            $bfather_name = $b1->father_name . ' / ' . $b2->father_name;
            $bgrandfather_name = $b1->grandfather_name . ' / ' . $b2->grandfather_name;
            $citizenship_details = $b1->citizenship_number . $b1issued_date . ' / ' . $b2->citizenship_number . $b2issued_date;


            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }

        $joint = $borrower->joint_joint_land()->first();
        if ($joint) {
            //File name
            $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {

                $joint = $borrower->joint_joint_land()->first();
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

                            if($b3){
                                $MortgageDeedThird = new TemplateProcessor(storage_path('document/joint/Mortgage deed third joint2 b3.docx'));
                            }else{
                                $MortgageDeedThird = new TemplateProcessor(storage_path('document/joint/Mortgage deed third joint2.docx'));
                            }
                            //mortgage deed
                            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                            $MortgageDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                            $MortgageDeedThird->setValue('offerletterdate', value($offerletterdate));
                            $MortgageDeedThird->setValue('amount', value($loan->loan_amount));
                            $MortgageDeedThird->setValue('words', value($loan->loan_amount_words));
                            $MortgageDeedThird->setValue('borrower_details', value($borrower_details));
                            $MortgageDeedThird->setValue('nepali_name', value($bnepali_name));

                            if ($b1->spouse_name) {
                                $b1spouse_name = $b1->spouse_name;
                            } else {
                                $b1spouse_name = null;
                            }
                            $g = $b1->gender;
                            if ($g == 1) {
                                $b1male = 'k\"?if';
                            } else {
                                $b1male = 'dlxnf';
                            }
                            $b1issued_date = $b1->issued_year . '÷' . $b1->issued_month . '÷' . $b1->issued_day;
                            $b1dob = $b1->dob_year . '÷' . $b1->dob_month . '÷' . $b1->dob_day;
                            $b1address = District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b1->wardno;
                            $b1issued_district = District::find($b1->issued_district)->name;
                            $b1citizenship_details = $b1->citizenship_number .' / '. $b1issued_date;

                            $MortgageDeedThird->setValue('b1nepali_name', value($b1->nepali_name));
                            $MortgageDeedThird->setValue('b1phone', value($b1->phone));
                            $MortgageDeedThird->setValue('b1english_name', value($b1->english_name));
                            $MortgageDeedThird->setValue('b1dob', value($b1dob));
                            $MortgageDeedThird->setValue('b1address', value($b1address));
                            $MortgageDeedThird->setValue('b1male', value($b1male));
                            $MortgageDeedThird->setValue('b1spouse_name', value($b1spouse_name));
                            $MortgageDeedThird->setValue('b1father_name', value($b1->father_name));
                            $MortgageDeedThird->setValue('b1grandfather_name', value($b1->grandfather_name));
                            $MortgageDeedThird->setValue('b1citizenship_details', value($b1citizenship_details));
                            $MortgageDeedThird->setValue('b1issued_district', value($b1issued_district));


                            if ($b2->spouse_name) {
                                $b2spouse_name = $b2->spouse_name;
                            } else {
                                $b2spouse_name = null;
                            }
                            $g = $b2->gender;
                            if ($g == 1) {
                                $b2male = 'k\"?if';
                            } else {
                                $b2male = 'dlxnf';
                            }
                            $b2issued_date = $b2->issued_year . '÷' . $b2->issued_month . '÷' . $b2->issued_day;
                            $b2dob = $b2->dob_year . '÷' . $b2->dob_month . '÷' . $b2->dob_day;
                            $b2address = District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b2->wardno;
                            $b2issued_district = District::find($b2->issued_district)->name;
                            $b2citizenship_details = $b2->citizenship_number .' / '. $b2issued_date;

                            $MortgageDeedThird->setValue('b2nepali_name', value($b2->nepali_name));
                            $MortgageDeedThird->setValue('b2phone', value($b2->phone));
                            $MortgageDeedThird->setValue('b2english_name', value($b2->english_name));
                            $MortgageDeedThird->setValue('b2dob', value($b2dob));
                            $MortgageDeedThird->setValue('b2address', value($b2address));
                            $MortgageDeedThird->setValue('b2male', value($b2male));
                            $MortgageDeedThird->setValue('b2spouse_name', value($b2spouse_name));
                            $MortgageDeedThird->setValue('b2father_name', value($b2->father_name));
                            $MortgageDeedThird->setValue('b2grandfather_name', value($b2->grandfather_name));
                            $MortgageDeedThird->setValue('b2citizenship_details', value($b2citizenship_details));
                            $MortgageDeedThird->setValue('b2issued_district', value($b2issued_district));

                            if($b3){
                                if ($b3->spouse_name) {
                                    $b3spouse_name = $b3->spouse_name;
                                } else {
                                    $b3spouse_name = null;
                                }
                                $g = $b3->gender;
                                if ($g == 1) {
                                    $b3male = 'k\"?if';
                                } else {
                                    $b3male = 'dlxnf';
                                }
                                $b3issued_date = $b3->issued_year . '÷' . $b3->issued_month . '÷' . $b3->issued_day;
                                $b3dob = $b3->dob_year . '÷' . $b3->dob_month . '÷' . $b3->dob_day;
                                $b3address = District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b3->wardno;
                                $b3issued_district = District::find($b3->issued_district)->name;
                                $b3citizenship_details = $b3->citizenship_number .' / '. $b3issued_date;

                                $MortgageDeedThird->setValue('b3nepali_name', value($b3->nepali_name));
                                $MortgageDeedThird->setValue('b3phone', value($b3->phone));
                                $MortgageDeedThird->setValue('b3english_name', value($b3->english_name));
                                $MortgageDeedThird->setValue('b3dob', value($b3dob));
                                $MortgageDeedThird->setValue('b3address', value($b3address));
                                $MortgageDeedThird->setValue('b3male', value($b3male));
                                $MortgageDeedThird->setValue('b3spouse_name', value($b3spouse_name));
                                $MortgageDeedThird->setValue('b3father_name', value($b3->father_name));
                                $MortgageDeedThird->setValue('b3grandfather_name', value($b3->grandfather_name));
                                $MortgageDeedThird->setValue('b3citizenship_details', value($b3citizenship_details));
                                $MortgageDeedThird->setValue('b3issued_district', value($b3issued_district));
                            }


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
                            $MortgageDeedThird->saveAs(storage_path('results/' . $filename . '_Documents/' . 'Mortgage_Deed_Third_Joint2_malpot_' . $mlp++ . '.docx'));
                        }
                    }
                }


            }

        }
    }

    private function mortgage_deed_third_joint3($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

            if ($b1->spouse_name) {
                $b1spouse_name = ' ' . $b1->spouse_name . 'sf] ' . $b1->spouse_relation . null;
            } else {
                $b1spouse_name = null;
            }
            $g = $b1->gender;
            if ($g == 1) {
                $b1gender = 'sf]';
                $b1male = 'k\"?if';
            } else {
                $b1gender = 'sL';
                $b1male = 'dlxnf';
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
                $b2male = 'k\"?if';
            } else {
                $b2gender = 'sL';
                $b2male = 'dlxnf';
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
                $b3male = 'k\"?if';
            } else {
                $b3gender = 'sL';
                $b3male = 'dlxnf';
            }
            $b3issued_date = $b3->issued_year . '÷' . $b3->issued_month . '÷' . $b3->issued_day;

            $bphone = $b1->phone . ', ' . $b2->phone . ' / ' . $b3->phone;
            $bnepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;
            $benglish_name = $b1->english_name . ', ' . $b2->english_name . ' and ' . $b3->english_name;
            $b1dob = $b1->dob_year . '÷' . $b1->dob_month . '÷' . $b1->dob_day;
            $b2dob = $b2->dob_year . '÷' . $b2->dob_month . '÷' . $b2->dob_day;
            $b3dob = $b3->dob_year . '÷' . $b3->dob_month . '÷' . $b3->dob_day;
            $bdob = $b1dob . ', ' . $b2dob . ' / ' . $b3dob;
            $b1address = District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b1->wardno;
            $b2address = District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b2->wardno;
            $b3address = District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b3->wardno;

            $baddress = $b1address . ', ' . $b2address . ' / ' . $b3address;
            $bissued_district = District::find($b1->issued_district)->name . ', ' . District::find($b2->issued_district)->name . ' / ' . District::find($b3->issued_district)->name;
            $bmale = $b1male . ', ' . $b2male . ' / ' . $b3male;
            if ($b1->spouse_name && $b2->spouse_name && $b3->spouse_name) {
                $bspouse_name = $b1->spouse_name . ', ' . $b2->spouse_name . ' / ' . $b2->spouse_name;
            } elseif ($b1->spouse_name && $b2->spouse_name) {
                $bspouse_name = $b1->spouse_name . ' / ' . $b2->spouse_name;
            } elseif ($b2->spouse_name && $b3->spouse_name) {
                $bspouse_name = $b2->spouse_name . ' / ' . $b3->spouse_name;
            } elseif ($b1->spouse_name && $b3->spouse_name) {
                $bspouse_name = $b1->spouse_name . ' / ' . $b3->spouse_name;
            } else {
                $bspouse_name = null;
            }
            $bfather_name = $b1->father_name . ', ' . $b2->father_name . ' / ' . $b3->father_name;
            $bgrandfather_name = $b1->grandfather_name . ', ' . $b2->grandfather_name . ' / ' . $b3->grandfather_name;
            $citizenship_details = $b1->citizenship_number . $b1issued_date . ', ' . $b2->citizenship_number . $b2issued_date . ' / ' . $b3->citizenship_number . $b3issued_date;

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;
            $bphone = $b1->phone . ' / ' . $b2->phone;
            if ($b1->spouse_name) {
                $b1spouse_name = ' ' . $b1->spouse_name . 'sf] ' . $b1->spouse_relation . null;
            } else {
                $b1spouse_name = null;
            }
            $g = $b1->gender;
            if ($g == 1) {
                $b1gender = 'sf]';
                $b1male = 'k\"?if';
            } else {
                $b1gender = 'sL';
                $b1male = 'dlxnf';
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
                $b2male = 'k\"?if';
            } else {
                $b2gender = 'sL';
                $b2male = 'dlxnf';
            }
            $b2issued_date = $b2->issued_year . '÷' . $b2->issued_month . '÷' . $b2->issued_day;

            $bnepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;
            $benglish_name = $b1->english_name . ' and ' . $b2->english_name;
            $b1dob = $b1->dob_year . '÷' . $b1->dob_month . '÷' . $b1->dob_day;
            $b2dob = $b2->dob_year . '÷' . $b2->dob_month . '÷' . $b2->dob_day;
            $bdob = $b1dob . ' / ' . $b2dob;
            $b1address = District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b1->wardno;
            $b2address = District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b2->wardno;
            $baddress = $b1address . ' / ' . $b2address;
            $bissued_district = District::find($b1->issued_district)->name . ' / ' . District::find($b2->issued_district)->name;
            $bmale = $b1male . ' / ' . $b2male;
            if ($b1->spouse_name && $b2->spouse_name) {
                $bspouse_name = $b1->spouse_name . ' / ' . $b2->spouse_name;
            } elseif ($b1->spouse_name) {
                $bspouse_name = $b1->spouse_name;
            } elseif ($b2->spouse_name) {
                $bspouse_name = $b2->spouse_name;
            } else {
                $bspouse_name = null;
            }
            $bfather_name = $b1->father_name . ' / ' . $b2->father_name;
            $bgrandfather_name = $b1->grandfather_name . ' / ' . $b2->grandfather_name;
            $citizenship_details = $b1->citizenship_number . $b1issued_date . ' / ' . $b2->citizenship_number . $b2issued_date;


            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }


        $joint = $borrower->joint_joint_land()->first();
        if ($joint) {
            //File name

            $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
            $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
            if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
                $s = File::makeDirectory($path, $mode = 0777, true, true);
            } else {
                $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
            }

            if ($s == true) {

                $joint = $borrower->joint_joint_land()->first();
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

if($b3){

    $MortgageDeedThird = new TemplateProcessor(storage_path('document/joint/Mortgage deed third joint3 b3.docx'));
}else{

    $MortgageDeedThird = new TemplateProcessor(storage_path('document/joint/Mortgage deed third joint3.docx'));
}
                            //mortgage deed

                            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                            $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                            $MortgageDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                            $MortgageDeedThird->setValue('offerletterdate', value($offerletterdate));
                            $MortgageDeedThird->setValue('amount', value($loan->loan_amount));
                            $MortgageDeedThird->setValue('words', value($loan->loan_amount_words));
                            $MortgageDeedThird->setValue('borrower_details', value($borrower_details));
                            $MortgageDeedThird->setValue('nepali_name', value($bnepali_name));
                            if ($b1->spouse_name) {
                                $b1spouse_name = $b1->spouse_name;
                            } else {
                                $b1spouse_name = null;
                            }
                            $g = $b1->gender;
                            if ($g == 1) {
                                $b1male = 'k\"?if';
                            } else {
                                $b1male = 'dlxnf';
                            }
                            $b1issued_date = $b1->issued_year . '÷' . $b1->issued_month . '÷' . $b1->issued_day;
                            $b1dob = $b1->dob_year . '÷' . $b1->dob_month . '÷' . $b1->dob_day;
                            $b1address = District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b1->wardno;
                            $b1issued_district = District::find($b1->issued_district)->name;
                            $b1citizenship_details = $b1->citizenship_number .' / '. $b1issued_date;

                            $MortgageDeedThird->setValue('b1nepali_name', value($b1->nepali_name));
                            $MortgageDeedThird->setValue('b1phone', value($b1->phone));
                            $MortgageDeedThird->setValue('b1english_name', value($b1->english_name));
                            $MortgageDeedThird->setValue('b1dob', value($b1dob));
                            $MortgageDeedThird->setValue('b1address', value($b1address));
                            $MortgageDeedThird->setValue('b1male', value($b1male));
                            $MortgageDeedThird->setValue('b1spouse_name', value($b1spouse_name));
                            $MortgageDeedThird->setValue('b1father_name', value($b1->father_name));
                            $MortgageDeedThird->setValue('b1grandfather_name', value($b1->grandfather_name));
                            $MortgageDeedThird->setValue('b1citizenship_details', value($b1citizenship_details));
                            $MortgageDeedThird->setValue('b1issued_district', value($b1issued_district));


                            if ($b2->spouse_name) {
                                $b2spouse_name = $b2->spouse_name;
                            } else {
                                $b2spouse_name = null;
                            }
                            $g = $b2->gender;
                            if ($g == 1) {
                                $b2male = 'k\"?if';
                            } else {
                                $b2male = 'dlxnf';
                            }
                            $b2issued_date = $b2->issued_year . '÷' . $b2->issued_month . '÷' . $b2->issued_day;
                            $b2dob = $b2->dob_year . '÷' . $b2->dob_month . '÷' . $b2->dob_day;
                            $b2address = District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b2->wardno;
                            $b2issued_district = District::find($b2->issued_district)->name;
                            $b2citizenship_details = $b2->citizenship_number .' / '.$b2issued_date;

                            $MortgageDeedThird->setValue('b2nepali_name', value($b2->nepali_name));
                            $MortgageDeedThird->setValue('b2phone', value($b2->phone));
                            $MortgageDeedThird->setValue('b2english_name', value($b2->english_name));
                            $MortgageDeedThird->setValue('b2dob', value($b2dob));
                            $MortgageDeedThird->setValue('b2address', value($b2address));
                            $MortgageDeedThird->setValue('b2male', value($b2male));
                            $MortgageDeedThird->setValue('b2spouse_name', value($b2spouse_name));
                            $MortgageDeedThird->setValue('b2father_name', value($b2->father_name));
                            $MortgageDeedThird->setValue('b2grandfather_name', value($b2->grandfather_name));
                            $MortgageDeedThird->setValue('b2citizenship_details', value($b2citizenship_details));
                            $MortgageDeedThird->setValue('b2issued_district', value($b2issued_district));

                            if($b3){
                                if ($b3->spouse_name) {
                                    $b3spouse_name = $b3->spouse_name;
                                } else {
                                    $b3spouse_name = null;
                                }
                                $g = $b3->gender;
                                if ($g == 1) {
                                    $b3male = 'k\"?if';
                                } else {
                                    $b3male = 'dlxnf';
                                }
                                $b3issued_date = $b3->issued_year . '÷' . $b3->issued_month . '÷' . $b3->issued_day;
                                $b3dob = $b3->dob_year . '÷' . $b3->dob_month . '÷' . $b3->dob_day;
                                $b3address = District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' ' . ' j*f g+=' . $b3->wardno;
                                $b3issued_district = District::find($b3->issued_district)->name;
                                $b3citizenship_details = $b3->citizenship_number .' / '. $b3issued_date;

                                $MortgageDeedThird->setValue('b3nepali_name', value($b3->nepali_name));
                                $MortgageDeedThird->setValue('b3phone', value($b3->phone));
                                $MortgageDeedThird->setValue('b3english_name', value($b3->english_name));
                                $MortgageDeedThird->setValue('b3dob', value($b3dob));
                                $MortgageDeedThird->setValue('b3address', value($b3address));
                                $MortgageDeedThird->setValue('b3male', value($b3male));
                                $MortgageDeedThird->setValue('b3spouse_name', value($b3spouse_name));
                                $MortgageDeedThird->setValue('b3father_name', value($b3->father_name));
                                $MortgageDeedThird->setValue('b3grandfather_name', value($b3->grandfather_name));
                                $MortgageDeedThird->setValue('b3citizenship_details', value($b3citizenship_details));
                                $MortgageDeedThird->setValue('b3issued_district', value($b3issued_district));
                            }

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

    private function land_rokka_letter_third_joint($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }

        //File name

        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {


            $joint = $borrower->joint_joint_land()->first();
            if ($joint) {
                $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
            } else {
                $jointp = [];
            }


            if ($joint->joint1 && $joint->joint2) {
                $p1 = PersonalPropertyOwner::find($joint->joint1)->nepali_name;
                $p2 = PersonalPropertyOwner::find($joint->joint2)->nepali_name;
                $nepali_namep = $p1 . ' / ' . $p2;
            } elseif ($joint->joint3 && $joint->joint2) {
                $p1 = PersonalPropertyOwner::find($joint->joint2)->nepali_name;
                $p2 = PersonalPropertyOwner::find($joint->joint3)->nepali_name;
                $nepali_name = $p1 . ' / ' . $p2;
            } elseif ($joint->joint1 && $joint->joint3) {
                $p1 = PersonalPropertyOwner::find($joint->joint1)->nepali_name;
                $p2 = PersonalPropertyOwner::find($joint->joint3)->nepali_name;
                $nepali_namep = $p1 . ' / ' . $p2;
            } elseif ($joint->joint1 && $joint->joint3 && $joint->joint2) {
                $p1 = PersonalPropertyOwner::find($joint->joint1)->nepali_name;
                $p2 = PersonalPropertyOwner::find($joint->joint2)->nepali_name;
                $p3 = PersonalPropertyOwner::find($joint->joint3)->nepali_name;
                $nepali_namep = $p1 . ', ' . $p2 . ' / ' . $p3;
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
                        $MortgageDeedThird = new TemplateProcessor(storage_path('document/joint/Rokka Letter Malpot joint.docx'));
                        Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                        $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                        $MortgageDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                        $MortgageDeedThird->setValue('offerletterdate', value($offerletterdate));
                        $MortgageDeedThird->setValue('amount', value($loan->loan_amount));
                        $MortgageDeedThird->setValue('words', value($loan->loan_amount_words));

                        $MortgageDeedThird->setValue('nepali_name', value($nepali_name));

//for property owner
//                Property Owner Details
                        $MortgageDeedThird->setValue('cname', value($nepali_namep));
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

    private function land_fukka_letter_third_joint($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }

        //File name

        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {

            $joint = $borrower->joint_joint_land()->first();
            if ($joint) {
                $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
            } else {
                $jointp = [];
            }

            if ($joint->joint1 && $joint->joint2) {
                $p1 = PersonalPropertyOwner::find($joint->joint1)->nepali_name;
                $p2 = PersonalPropertyOwner::find($joint->joint2)->nepali_name;
                $nepali_namep = $p1 . ' / ' . $p2;
            } elseif ($joint->joint3 && $joint->joint2) {
                $p1 = PersonalPropertyOwner::find($joint->joint2)->nepali_name;
                $p2 = PersonalPropertyOwner::find($joint->joint3)->nepali_name;
                $nepali_namep = $p1 . ' / ' . $p2;
            } elseif ($joint->joint1 && $joint->joint3) {
                $p1 = PersonalPropertyOwner::find($joint->joint1)->nepali_name;
                $p2 = PersonalPropertyOwner::find($joint->joint3)->nepali_name;
                $nepali_namep = $p1 . ' / ' . $p2;
            } elseif ($joint->joint1 && $joint->joint3 && $joint->joint2) {
                $p1 = PersonalPropertyOwner::find($joint->joint1)->nepali_name;
                $p2 = PersonalPropertyOwner::find($joint->joint2)->nepali_name;
                $p3 = PersonalPropertyOwner::find($joint->joint3)->nepali_name;
                $nepali_namep = $p1 . ', ' . $p2 . ' / ' . $p3;
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
                        $MortgageDeedThird = new TemplateProcessor(storage_path('document/joint/Fukka Letter Malpot Third joint.docx'));
                        Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                        $offerletterdate = $loan->offerletter_year . '÷' . $loan->offerletter_month . '÷' . $loan->offerletter_day;
                        $MortgageDeedThird->setValue('branch', value(Branch::find($loan->branch_id)->location));
                        $MortgageDeedThird->setValue('offerletterdate', value($offerletterdate));
                        $MortgageDeedThird->setValue('amount', value($loan->loan_amount));
                        $MortgageDeedThird->setValue('words', value($loan->loan_amount_words));

                        $MortgageDeedThird->setValue('nepali_name', value($nepali_name));
                        //for property owner
//                Property Owner Details
                        $MortgageDeedThird->setValue('pnepali_name', value($nepali_namep));
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

    public function document(Request $request, $bid)
    {
        $loan = JointLoan::where([['borrower_id', $bid]])->first();
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
        $borrower = JointBorrower::find($bid);
        $vehicles = $borrower->joint_hire_purchase;
        $lands = $borrower->jointborrowerpersonalland->first();
        if ($lands) {
            $land = 1;
        }
        $shares = $borrower->jointborrowerpersonalshare->first();
        if ($shares) {
            $share = 1;
        }
        $facilities = $borrower->joint_facilities;
        $joint = $borrower->joint_joint_land()->first();
        if ($joint) {
            $lands = JointPropertyOwner::find($joint->id)->joint_land()->first();
            if ($lands) {
                $land = 1;
            }
        }

        $b1 = null;
        $b2 = null;
        $b3 = null;
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;

        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
        }


        $vehicle = null;
        foreach ($vehicles as $v) {
            $vehicle = 1;
        }


        $joint = $borrower->joint_joint_land()->first();
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
            foreach ($borrower->jointborrowerpersonalguarantor as $pg) {
                $per = 0;
            }
            if ($per == 0) {
                $this->personal_guarantor($bid);
            }


        }

        if ($cmortgage_deed == 1) {
            $third = null;
            if ($jointp) {
                if ($joint->joint1 && $joint->joint2 && $joint->joint3) {
                    $this->mortgage_deed_third_joint3($bid);
                } else {
                    $this->mortgage_deed_third_joint2($bid);
                }

            }

            foreach ($borrower->jointborrowerpersonalland as $l) {
                $third = 0;
            }

            if ($third == 0) {
                $this->mortgage_deed_third($bid);
            }
        }

        if ($cshare_pledge_deed == 1) {
            $third = null;
            foreach ($borrower->jointborrowerpersonalshare as $share) {
                $third = 0;

            }

            if ($third == 0) {
                $this->pledge_deed_third($bid);
            }
        }

        if ($cmanjurinama_of_hire_purchase == 1) {
            if ($vehicle) {
                $this->vehicle_transfer_letter_bank_favour($bid);
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
//            $this->land_rokka_letter_self($bid);
            $this->land_rokka_letter_third($bid);
            $joint = $borrower->joint_joint_land()->first();
            if ($joint) {
                $this->land_rokka_letter_third_joint($bid);
            }
        }
        if ($crelease_letter_malpot == 1) {
//            $this->land_fukka_letter_self($bid);
            $this->land_fukka_letter_third($bid);
            $joint = $borrower->joint_joint_land()->first();
            if ($joint) {
                $this->land_fukka_letter_third_joint($bid);
            }
        }

        if ($cshare_rokka_letter == 1) {
//            $this->share_rokka_self($bid);
            $this->share_rokka_third($bid);
        }
        if ($cbonus_right_cash_divident == 1) {
//            $this->bonus_right_cash_divident_self($bid);
            $this->bonus_right_cash_divident($bid);
        }
        if ($cshare_release_letter == 1) {
//            $this->share_fukka_self($bid);
            $this->share_fukka_third($bid);
        }
        if ($ctieup_deed == 1) {
//            $this->tieup_deed_self($bid);
            $this->tieup_deed_third($bid);
        }

        if ($cconsent_of_property_owner == 1) {
            $this->consent_of_property_owner_self($bid);
            $joint = $borrower->joint_joint_land()->first();
            if ($joint) {
                $this->consent_of_property_owner_joint($bid);
            }
        }

        if ($canusuchi18 == 1) {
            $this->anusuchi_18($bid);
        }
        if ($canusuchi19 == 1) {
            $this->anusuchi_19($bid);
        }

        if ($cswap_commitment_letter == 1) {
            $this->swap_commitment_letter($bid);
        }


//File name
        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
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
        //    $loan->approved_by = Auth::user()->id;
            try {
                $loan->update();

            } catch (\Exception $e) {

            }
        }
        return redirect()->route('document.index');
    }

    public function choose($bid)
    {
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
        $borrower = JointBorrower::find($bid);
        $borrowers = [];
        $borrowers[] = PersonalBorrower::find($borrower->joint1);
        $borrowers[] = PersonalBorrower::find($borrower->joint2);
        $borrowers[] = PersonalBorrower::find($borrower->joint3);
        $land = null;
        $share = null;
        $vehicles = JointHirePurchase::where([['borrower_id', $bid]])->get();
        $lands = $borrower->jointborrowerpersonalland->first();
        if ($lands) {
            $land = 1;
        }
        $joint = $borrower->joint_joint_land()->first();

        if ($joint) {
            $lands = JointPropertyOwner::find($joint->id)->joint_land()->get();
            if ($lands) {
                $land = 1;
            }
        }

        $shares = $borrower->jointborrowerpersonalshare->first();
        if ($shares) {
            $share = 1;
        }
        $guarantor = JointGuarantorBorrower::where([['borrower_id', $bid]])->get();
        $loan = JointLoan::where([['borrower_id', $bid]])->first();
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

        foreach ($guarantor as $pg) {
            $guarantor = 1;
            $swap_commitment_letter = 1;
        }
        return view('document.joint_choose', compact('vehicle_fukka_letter', 'tieup_deed', 'bonus_right_cash_divident', 'rokka_letter_malpot', 'release_letter_malpot', 'share_rokka_letter', 'share_release_letter', 'consent_of_property_owner', 'swap_commitment_letter', 'anusuchi19', 'anusuchi18', 'bid', 'loan', 'borrowers', 'promissory_note', 'loan_deed', 'guarantor', 'manjurinama_of_hire_purchase', 'mortgage_deed', 'share_pledge_deed', 'vehicle_registration_letter', 'hypo_of_stock'));
    }

    public function approve_request($bid)
    {
        $borrower = JointBorrower::find($bid);
        $borrowers = [];
        $borrowers[] = PersonalBorrower::find($borrower->joint1);
        $borrowers[] = PersonalBorrower::find($borrower->joint2);
        $borrowers[] = PersonalBorrower::find($borrower->joint3);


        //year for current age
        $year = JointLoan::where([
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
//personal guarantor
        $personal_guarantor = [];


        $p_guarantor = JointGuarantorBorrower::where([
            ['borrower_id', $bid],
        ])->get();

        foreach ($p_guarantor as $p) {
            $personal_guarantor[] = PersonalGuarantor::find($p->personal_guarantor_id);
        }

//        facilities
        $facilities = JointFacilities::where([
            ['borrower_id', $bid]
        ])->get();
//loan
        $shareowner = [];
        $landowner = [];
        $hirepurchase = JointHirePurchase::where([['borrower_id', $bid]])->get();
        foreach ($borrower->jointborrowerpersonalland as $land) {
            $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
        }
        $personal_land_owner = array_unique($landowner);

        foreach ($borrower->jointborrowerpersonalshare as $share) {
            $shareowner[] = PersonalPropertyOwner::find($share->property_owner_id);
        }
        $personal_share_owner = array_unique($shareowner);

        $jointp = [];
        $joint = $borrower->joint_joint_land()->first();
        if ($joint) {
            $jointp = JointPropertyOwner::find($joint->id)->joint_land()->get();
        }

        $joint_property_owner = [];
        if ($jointp) {
            $joint_property_owner[] = PersonalPropertyOwner::find($joint->joint1);
            $joint_property_owner[] = PersonalPropertyOwner::find($joint->joint2);
            $joint_property_owner[] = PersonalPropertyOwner::find($joint->joint3);
        }

        $loan = JointLoan::where([
            ['borrower_id', $bid]
        ])->first();

        return view('document.joint_approve', compact('jointp', 'joint_property_owner', 'cyear', 'personal_share_owner', 'bid', 'borrower', 'borrowers', 'personal_guarantor', 'facilities', 'loan', 'personal_land_owner', 'hirepurchase'));

    }

    public function approve($bid)
    {
        $loan = JointLoan::where([['borrower_id', $bid]])->first();
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

    public function reject(Reject $request, $bid)
    {
        $reason = $request->input('reject');
        $loan = JointLoan::where([['borrower_id', $bid]])->first();
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

    public function rejected($bid)
    {
        $borrower = JointBorrower::find($bid);
        $borrowers = [];
        $borrowers[] = PersonalBorrower::find($borrower->joint1);
        $borrowers[] = PersonalBorrower::find($borrower->joint2);
        $borrowers[] = PersonalBorrower::find($borrower->joint3);


        $loan = JointLoan::where([['borrower_id', $bid]])->first();
        return view('document.joint_rejected', compact('bid', 'borrowers', 'loan'));
    }


//for self
    private function tieup_deed_self($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }


        //File name

        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {

            $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();
            if ($propertyOwner) {
                $landowner = [];
                foreach ($borrower->jointborrowerpersonalland as $land) {
                    if ($propertyOwner->id == $land->property_owner_id) {
                        $landowner[] = PersonalPropertyOwner::find($land->property_owner_id);
                    }
                }
                $personal_land_owner = array_unique($landowner);

                $mlp = 1;

                foreach ($personal_land_owner as $p) {

                    $ld = 0;
                    foreach ($borrower->jointborrowerpersonalland as $l) {
                        if ($l->property_owner_id == $p->id) {
                            $ld = $ld + 1;
                        }
                    }


                    //mortgage deed
                    $TieupDeedSelfEnhancement = new TemplateProcessor(storage_path('document/joint/Tieup Deed Self for Enhancement.docx'));
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
                    foreach ($borrower->jointborrowerpersonalland as $l) {

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

    private function land_rokka_letter_self($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }

        //File name

        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {

            $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();
            if ($propertyOwner) {
                $landowner = [];
                $malpot = [];
                foreach ($borrower->jointborrowerpersonalland as $land) {
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
                        foreach ($borrower->jointborrowerpersonalland as $l) {
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
                            $MortgageDeedSelf = new TemplateProcessor(storage_path('document/joint/Rokka Letter Malpot Self.docx'));
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
                            foreach ($borrower->jointborrowerpersonalland as $l) {

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

    private function land_fukka_letter_self($bid)
    {
        $loan = JointLoan::where([
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
        $b1 = null;
        $b2 = null;
        $b3 = null;
        $borrower = JointBorrower::find($bid);
        if ($borrower->joint1 && $borrower->joint2 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);
            $b3 = PersonalBorrower::find($borrower->joint3);
        } elseif ($borrower->joint1 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint2);

        } elseif ($borrower->joint3 && $borrower->joint2) {
            $b1 = PersonalBorrower::find($borrower->joint2);
            $b2 = PersonalBorrower::find($borrower->joint3);

        } elseif ($borrower->joint1 && $borrower->joint3) {
            $b1 = PersonalBorrower::find($borrower->joint1);
            $b2 = PersonalBorrower::find($borrower->joint3);
        }

        if ($b1 && $b2 && $b3) {
            $english_name = $b1->english_name . '_' . $b2->english_name . '_and_' . $b3->english_name;
            $nepali_name = $b1->nepali_name . ', ' . $b2->nepali_name . ' / ' . $b3->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' .
                $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ', ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 / ' . $b3->grandfather_name . 'sf] ' . $b3->grandfather_relation . ' ' .
                $b3->father_name . 'sf] ' . $b3->father_relation . $b3spouse_name . ' ' . District::find($b3->district_id)->name . ' lhNnf ' . LocalBodies::find($b3->local_bodies_id)->name . ' ' . LocalBodies::find($b3->local_bodies_id)->body_type . ' j*f g+=' . $b3->wardno . ' a:g] aif{ ' . ($cyear - ($b3->dob_year)) . ' ' . $b3gender . ' ' . $b3->nepali_name . ' -gf=k|=k=g+=' . $b3->citizenship_number . ' hf/L ldlt ' . $b3issued_date . ' lhNnf ' . District::find($b3->issued_district)->name . '_ —1 ;d]t hgf 3 ;+o"Qm';
        } elseif ($b1 && $b2) {
            $english_name = $b1->english_name . '_and_' . $b2->english_name;
            $nepali_name = $b1->nepali_name . ' / ' . $b2->nepali_name;

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

            $borrower_details = $b1->grandfather_name . 'sf] ' . $b1->grandfather_relation . ' ' . $b1->father_name . 'sf] ' . $b1->father_relation . $b1spouse_name . ' ' . District::find($b1->district_id)->name . ' lhNnf ' . LocalBodies::find($b1->local_bodies_id)->name . ' ' . LocalBodies::find($b1->local_bodies_id)->body_type .
                ' j*f g+=' . $b1->wardno . ' a:g] aif{ ' . ($cyear - ($b1->dob_year)) . ' ' . $b1gender . ' ' . $b1->nepali_name . ' -gf=k|=k=g+=' . $b1->citizenship_number . ' hf/L ldlt ' . $b1issued_date . ' lhNnf ' . District::find($b1->issued_district)->name . '_ —1' . ' / ' . $b2->grandfather_name . 'sf] ' . $b2->grandfather_relation . ' ' .
                $b2->father_name . 'sf] ' . $b2->father_relation . $b2spouse_name . ' ' . District::find($b2->district_id)->name . ' lhNnf ' . LocalBodies::find($b2->local_bodies_id)->name . ' ' . LocalBodies::find($b2->local_bodies_id)->body_type .
                ' j*f g+=' . $b2->wardno . ' a:g] aif{ ' . ($cyear - ($b2->dob_year)) . ' ' . $b2gender . ' ' . $b2->nepali_name . ' -gf=k|=k=g+=' . $b2->citizenship_number . ' hf/L ldlt ' . $b2issued_date . ' lhNnf ' . District::find($b2->issued_district)->name . '_ —1 ;d]t hgf 2 ;+o"Qm';
        }


        //File name

        $filename = $english_name . '_' . $loan->offerletter_year . '_' . $loan->offerletter_month . '_' . $loan->offerletter_day;
        $path = storage_path('results/' . $filename . '_Documents/');

//    making folder
        if (!File::exists(storage_path('results/' . $filename . '_Documents/'))) {
            $s = File::makeDirectory($path, $mode = 0777, true, true);
        } else {
            $s = File::exists(storage_path('results/' . $filename . '_Documents/'));
        }

        if ($s == true) {

            $propertyOwner = PersonalPropertyOwner::where([['english_name', $borrower->english_name], ['citizenship_number', $borrower->citizenship_number], ['grandfather_name', $borrower->grandfather_name]])->first();
            if ($propertyOwner) {
                $landowner = [];
                $malpot = [];
                foreach ($borrower->jointborrowerpersonalland as $land) {
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
                        foreach ($borrower->jointborrowerpersonalland as $l) {
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
                            $MortgageDeedSelf = new TemplateProcessor(storage_path('document/joint/Fukka Letter Malpot Self.docx'));
                            Settings::setOutputEscapingEnabled(true);
// Variables on different parts of document
                            $MortgageDeedSelf->setValue('branch', value(Branch::find($loan->branch_id)->location));
                            $MortgageDeedSelf->setValue('nepali_name', value($borrower->nepali_name));
                            $MortgageDeedSelf->setValue('lmalpot', value($m));
                            $MortgageDeedSelf->cloneRow('landsn', $ld);
                            $i = 1;
                            foreach ($borrower->jointborrowerpersonalland as $l) {

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

}