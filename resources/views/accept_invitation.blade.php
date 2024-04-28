<!DOCTYPE html>
<html>
<head>
    <title>Accept Invitation</title>
</head>
<body>
    <script>
        function handleAccept() {
            // Make an AJAX request to accept the invitation
            fetch('http://127.0.0.1:8000/api/invitation/{{ $invitation->id }}/accept', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                throw new Error('Acceptance failed');
            })
            .then(data => {
                alert(data.message); // Display confirmation message
                window.close(); // Close the window after showing the alert
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to accept invitation');
            });
        }

        function handleDecline() {
            // Add logic here to handle declination
            alert("Invitation Cancelled!");
            window.close(); // Close the window after showing the alert
        }
    </script>
    <h2>Invitation Confirmation</h2>
    <p>Do you want to accept or decline the invitation?</p>
    <button onclick="handleAccept()">Accept</button>
    <button onclick="handleDecline()">Decline</button>
</body>
</html>
