<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class ReportPdfController extends Controller
{
    public function casesList()
    {
        return response('PDF Casos');
    }

    public function statistics()
    {
        return response('PDF Estadisticas');
    }

    public function studentFile(Student $student)
    {
        return response('PDF Alumno ' . $student->id);
    }
}
