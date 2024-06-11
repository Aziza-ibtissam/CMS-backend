<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conference Created</title>
    <style>
        /* Paste your CSS styles here */
    </style>
</head>
<body>
    <div class="container">
        <h1>Conference Created</h1>
        <p>Hello <strong>{{ $user->firstName && $user->lastName ? $user->firstName . ' ' . $user->lastName : '' }}</strong>,</p>
        <p>A new conference has been created:</p>
        <ul>
            <li><strong>Title:</strong> {{ $conference->title }}</li>
            <li><strong>Start Date:</strong> {{ $conference->start_at }}</li>
            <li><strong>End Date:</strong> {{ $conference->end_at }}</li>
            <li><strong>Webpage:</strong> {{ $conference->webpage }}</li>
        </ul>
        <a href="{{ $confirmedUrl }}"><button style="background-color: green; color: white;">Confirm</button></a>        <p>Thank you for using our conference management system.</p>
    </div>

   
</body>
</html>
