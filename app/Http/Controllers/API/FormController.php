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
        // Validate form data
        $validatedData = $request->validate([
            'conference_id' => 'required|exists:conferences,id',
            'questions.*.description' => 'required|string',
            'questions.*.point' => 'required|integer',
            'questions.*.coefficient' => 'required|integer',
        ]);

        // Create a new form
        $form = Form::create([
            'conference_id' => $validatedData['conference_id'],
        ]);

        // Create questions for the form
        foreach ($validatedData['questions'] as $questionData) {
            Question::create([
                'form_id' => $form->id,
                'description' => $questionData['description'],
                'point' => $questionData['point'],
                'coefficient' => $questionData['coefficient'],
            ]);
        }

        return response()->json(['message' => 'Form submitted successfully']);
    }
}
