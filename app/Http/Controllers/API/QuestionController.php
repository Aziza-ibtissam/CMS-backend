<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Questions;

class QuestionController extends Controller
{
    public function store(Request $request)
    {
        // Validate incoming request data
        $validated = $request->validate([
            'form_id' => 'required|exists:forms,id',
            'description' => 'required|string',
            'coefficient' => 'required|integer',
            'point' => 'required|integer',
        ]);

        // Create a new question record
        $question = Question::create($validated);

        // Return a response indicating success
        return response()->json(['message' => 'Question created successfully', 'question' => $question], 201);
    }
}
