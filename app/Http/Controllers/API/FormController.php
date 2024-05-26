<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Form;
use App\Models\Question;

class FormController extends Controller
{
    public function saveForm(Request $request)
    {
        $request->validate([
            'conference_id' => 'required|integer',
            'finalDecisionCoefficient' => 'nullable|integer',
            'confidentialRemarksCoefficient' => 'nullable|integer',
            'eligibleCoefficient' => 'nullable|integer',
            'questions' => 'required|array',
            'questions.*.description' => 'required|string',
            'questions.*.point' => 'required|integer',
        ]);

        // Update or create the form based on the conference_id
        $form = Form::updateOrCreate(
            ['conference_id' => $request->conference_id],
            [
                'finalDecisionCoefficient' => $request->finalDecisionCoefficient,
                'confidentialRemarksCoefficient' => $request->confidentialRemarksCoefficient,
                'eligibleCoefficient' => $request->eligibleCoefficient,
            ]
        );

        // Sync the questions with the form
        $form->questions()->delete(); // Delete existing questions
        foreach ($request->questions as $questionData) {
            $form->questions()->create($questionData);
        }

        return response()->json(['message' => 'Form and questions saved successfully.']);
    }

    public function getFormForReview($conferenceId)
    {
        
        // Retrieve form data based on the conference ID
        $formData = Form::with('questions')->where('conference_id', $conferenceId)->firstOrFail();

        // Return the form data
        return response()->json(['formData' => $formData], 200);
       
    }
}