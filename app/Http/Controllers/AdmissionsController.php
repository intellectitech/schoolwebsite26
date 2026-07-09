<?php

namespace App\Http\Controllers;

use App\Models\AdmissionStep;
use App\Models\FaqItem;

class AdmissionsController extends Controller
{
    public function index()
    {
        $steps = AdmissionStep::ordered()->get();
        $faqs = FaqItem::category('admissions')->get();

        return view('admissions.index', compact('steps', 'faqs'));
    }
}