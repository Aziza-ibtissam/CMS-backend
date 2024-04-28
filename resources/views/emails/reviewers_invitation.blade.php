<!DOCTYPE html>
<html>
<head>
    <title>Invitation to Review Conference</title>
</head>
<body>
    <h2>Invitation to Review Conference: {{ $conference->title }}</h2>
    <p>Dear {{ $firstName }} {{ $lastName }},</p>
    <p>You have been invited to review the conference "{{ $conference->title }}".</p>
    <p><strong>Start Date:</strong> {{ $conference->start_at }}</p>
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
    <p>Please click the buttons below to accept or decline the invitation:</p>
    <a href="{{ $acceptUrl }}"><button style="background-color: green; color: white;">Accept</button></a>
    <a href="{{ $declineUrl }}"><button style="background-color: red; color: white;">Decline</button></a>
    <p>Please contact {{ $conference->email }} if you have questions about the conference.</p>
    <p>Thanks <br> <strong>ConfMan</strong></p>
</body>
</html>
