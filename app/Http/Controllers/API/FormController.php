<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\Question;

class FormController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'conference_id' => 'required|exists:conferences,id',
            'finalDecisionCoefficient' => 'required|integer',
            'confidentialRemarksCoefficient' => 'required|integer',
            'eligibleCoefficient' => 'required|integer',
        ]);

        // Create a new form record
        $form = Form::create($validated);

        return response()->json(['message' => 'Form Created Successfully']);
    }
}
