<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Student Allocation</title>
    <style>
        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
                padding: 20px !important;
            }
            .button {
                width: 100% !important;
                text-align: center !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f7f7f7; color: #333333;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
        <tr>
            <td style="padding: 20px 0;">
                <table align="center" role="presentation" cellpadding="0" cellspacing="0" width="600" border="0" style="background-color: #ffffff; border-radius: 6px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); margin: 0 auto;" class="container">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 30px 40px 20px 40px; border-bottom: 1px solid #eeeeee;">
                            <h1 style="margin: 0; font-size: 24px; color: #4a4a4a; font-weight: 600;">New Student Allocation</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #4a4a4a;">
                                Good news! You have been allocated a new student:
                            </p>
                            <p style="margin: 0 0 30px; font-size: 18px; font-weight: 600; color: #2c3e50;">
                                {{$student_name}}
                            </p>
                            <p style="margin: 0 0 30px; font-size: 16px; line-height: 1.6; color: #4a4a4a;">
                                Please check your application dashboard for more details about your new student and upcoming sessions.
                            </p>
                            
                            <!-- Button -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin: 0 0 20px 0;" class="button">
                                <tr>
                                    <td align="left" style="padding: 0;">
                                        <a href="{{$url}}" target="_blank" style="background-color: #3498db; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 4px; display: inline-block; font-weight: 500; font-size: 16px; border: none;">
                                            VIEW STUDENT DETAILS
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="margin: 30px 0 0; font-size: 15px; line-height: 1.6; color: #7f8c8d;">
                                If you have any questions or need assistance, please contact the administrative team.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                </table>
            </td>
        </tr>
    </table>
</body>
</html>