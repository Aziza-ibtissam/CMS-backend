<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/node_modules/primeicons/primeicons.css">

    <title>Email Verification</title>
</head>
<body>
    <div class="verification-container">
        <img src="{{ asset('storage/image/emailVerification.png') }}" alt="Verification Image" class="verification-image">
        <div class="verification-text">
            
            </div>
            <div id="success-message text-center mt-3">
            <p><i class=" bi bi-check2-circle" style="color: #28a745;"></i> Email verified successfully!</p>
                <div class="d-inline-block w-50 text-center">
                    <a href="http://localhost:8080/SignIn" >
                    Sing IN
                    </a>
                </div>
        </div>
    </div>

    <style>
        .verification-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }

        .verification-image {
            height: 500px;
            width: 500px;
            margin-bottom: 20px;
        }

        .verification-text {
            padding: 20px;
            border-radius: 8px;
        }

        .verification-text h1 {
            margin-bottom: 20px;
        }

        .verification-text button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        .verification-text button:hover {
            background-color: #0056b3;
        }
    </style>
</body>
</html>
