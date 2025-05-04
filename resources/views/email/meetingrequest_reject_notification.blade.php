<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Request Rejected Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-container {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            background-color: white;
        }
        .email-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .email-subject {
            font-weight: bold;
            color: #333;
        }
        .email-body {
            padding: 10px 0;
        }
        .email-footer {
            border-top: 1px solid #eee;
            padding-top: 10px;
            margin-top: 15px;
            font-size: 0.9em;
            color: #777;
        }
        .notification-bubble {
            background-color: #fff5f5;
            border-left: 4px solid #e74c3c;
            padding: 15px;
            margin: 15px 0;
            text-align: center;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <div class="email-subject">Subject: Tutoring Request Not Approved</div>
            <div>From: {{ $TUTOR_EMAIL }}</div>
            <div>To: {{ $STUDENT_EMAIL }}</div>
        </div>
        <div class="email-body">
            <p>Hello,</p>
            
            <div class="notification-bubble">
                <p><strong>Your tutoring session request was not approved.</strong></p>
                <p>Please check your student portal for alternative options.</p>
            </div>
            
            <p>This is an automated notification. No response is required.</p>
            
            <p>Regards,<br>
            {{ $INSTITUTION_NAME }} Tutoring Services</p>
        </div>
        <div class="email-footer">
            <p>This is an automated message from the tutoring system.</p>
        </div>
    </div>
</body>
</html>