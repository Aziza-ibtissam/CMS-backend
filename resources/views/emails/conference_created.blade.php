<style>
    body {
        font-family: Arial, sans-serif; /* Change font family as needed */
        background-color: #f8f9fa; /* Change background color as needed */
        color: #333; /* Change text color as needed */
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 600px; /* Adjust container width as needed */
        margin: 50px auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Add box shadow */
        text-align: center; /* Center align text within container */
    }

    h1 {
        color: #007bff; /* Change color as needed */
        font-size: 36px; /* Change font size as needed */
        margin-bottom: 20px;
    }

    p {
        font-size: 16px; /* Change font size as needed */
        line-height: 1.6; /* Adjust line height as needed */
        margin-bottom: 10px;
    }

    ul {
        list-style-type: none; /* Remove bullet points */
        padding: 0;
        margin: 0 auto; /* Center the ul */
        text-align: left; /* Align text to left within ul */
    }

    li {
        margin-bottom: 5px;
    }

    button {
        background-color: #007bff; /* Change background color as needed */
        color: #fff; /* Change text color as needed */
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        border-radius: 5px;
        transition: background-color 0.3s ease; /* Add transition effect */
    }

    button:hover {
        background-color: #0056b3; /* Change hover background color as needed */
    }
</style>

<div class="container">
    <h1>Conference Created</h1>
    <p>Hello <strong>{{ $user->firstName && $user->lastName ? $user->firstName . ' ' . $user->lastName : '' }}</strong>,</p>
    <p>A new conference has been created:</p>
    <ul>
    <p><strong>Title:</strong> {{ $conference->title }}</p>
    <p><strong>Start Date:</strong> {{ $conference->start_at }}</p>
    <p><strong>End Date:</strong> {{ $conference->end_at }}</p>
    <p><strong>Webpage:</strong> {{ $conference->webpage }}</p>
    </ul>
    <button onclick="redirectToLogin()">Confirmed</button>
    <p>Thank you for using our conference management system.</p>

    <!-- Add the button here -->
</div>

<script>
    function redirectToLogin() {
        window.location.href = '{{ route("login") }}';
    }
</script>
