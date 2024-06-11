<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Subtopic;


use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function getTopicsAndSubtopic(Request $request, $conference_id)
{
    // Fetch topics for the specified conference ID
    $topics = Topic::where('conference_id', $conference_id)->get();

    // Fetch subtopics related to the fetched topics
    $subtopics = Subtopic::whereIn('topic_id', $topics->pluck('id'))->get();

    return response()->json([
        'topics' => $topics,
        'subtopics' => $subtopics
    ]);
}

public function deleteTopic(Request $request, $conferenceId, $topicId)
{
    // Find the topic by ID
    $topic = Topic::findOrFail($topicId);

    // Ensure the topic belongs to the specified conference
    if ($topic->conference_id != $conferenceId) {
        return response()->json(['error' => 'Topic not found in conference'], 404);
    }

    // Delete the topic and its associated subtopics
    $topic->delete();
    Subtopic::where('topic_id', $topicId)->delete();

    return response()->json(['message' => 'Topic and subtopics deleted successfully'], 200);
}
}
