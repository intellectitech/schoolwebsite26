<?php

namespace App\Http\Controllers;

use App\Models\FacultyMember;
use App\Models\Pathway;

class AcademicsController extends Controller
{
    public function index()
    {
        $pathways = Pathway::ordered()->get();
        $faculty = FacultyMember::spotlighted()->get();

        return view('academics.index', compact('pathways', 'faculty'));
    }
}