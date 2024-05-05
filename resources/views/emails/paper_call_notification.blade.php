<!DOCTYPE html>
<html>
<head>
    <title> Call For Paper:{{ $conference->title }}</title>
</head>
<body>
    <h2>Call For Paper:{{ $conference->title }}</h2>
    <p>You have been invited to call for your paper submission..</p>
    <p> <b>The Conference Location: </b>:{{ $conference->country }}, {{ $conference->city }}</p>
     <p><b>Starts On: </b> {{ $conference->start_at }}</p>
    <p><strong>Topics:</strong></p>
    <ul>
        @foreach($topics as $topic)
            <li>{{ $topic->name }}</li>
            <ul>
                @foreach($topic->subtopics as $subtopic)
                    <li>{{ $subtopic->name }}</li>
                @endforeach
            </ul>
        @endforeach
    </ul>
    <p><b>Submission Deadline: </b>{{$conference->paper_subm_due_date}}</p>
    <p> If you are interested, you can visit the conference official website.</p>
    <a href="{{ $websiteUrl  }}"><button style="background-color: green; color: white;">Visit WebSite</button></a>
    <p>Please contact {{ $conference->email }} if you have questions about the conference.</p>
    <p>Thanks <br> <strong>ConfMan</strong></p>
</body>
</html>
