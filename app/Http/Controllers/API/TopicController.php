<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Subtopic;


use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function index(Topic $topic)
    {
        $subtopics = Subtopic::where('topic_id', $topic->id)->get();
        return response()->json($subtopics);
    }
}
