<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Tutor Allocation</title>
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
                            <h1 style="margin: 0; font-size: 24px; color: #4a4a4a; font-weight: 600;">Your Tutor Assignment</h1>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #4a4a4a;">
                                Good news! You have been assigned to a tutor who will guide your learning journey:
                            </p>
                            <p style="margin: 0 0 30px; font-size: 18px; font-weight: 600; color: #2c3e50;">
                                {{$tutor_name}}
                            </p>
                            <p style="margin: 0 0 30px; font-size: 16px; line-height: 1.6; color: #4a4a4a;">
                                Please check the application for your tutor's details and upcoming session schedule.
                            </p>
                            
                            <!-- Button -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin: 0 0 20px 0;" class="button">
                                <tr>
                                    <td align="left" style="padding: 0;">
                                        <a href="{{$url}}" target="_blank" style="background-color: #0066ff; color: #ffffff; text-decoration: none; padding: 12px 25px; border-radius: 4px; display: inline-block; font-weight: 500; font-size: 16px; border: none;">
                                            VIEW TUTOR DETAILS
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="margin: 30px 0 0; font-size: 15px; line-height: 1.6; color: #7f8c8d;">
                                We're excited for you to begin learning with your new tutor. If you have any questions, please contact our support team.
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