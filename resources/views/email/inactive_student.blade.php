<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Tutor is Waiting for You!</title>
    <style>
        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
            }
            .content {
                padding: 20px !important;
            }
            .cta-button {
                width: 100% !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
        <tr>
            <td style="padding: 20px 0;">
                <table align="center" role="presentation" cellpadding="0" cellspacing="0" width="600" border="0" class="container" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 30px 0; text-align: center; background-color: #3498db; border-top-left-radius: 8px; border-top-right-radius: 8px;">
                            <h2 style="margin: 0; color: #ffffff; font-size: 28px;">Your Tutor Misses You!</h2>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td class="content" style="padding: 40px 30px;">
                            <h1 style="margin: 0 0 20px; color: #333333; font-size: 24px;">We've Missed You!</h1>
                            <p style="margin: 0 0 20px; color: #666666; font-size: 16px; line-height: 1.5;">Hi {{$user_name}},</p>
                            <p style="margin: 0 0 20px; color: #666666; font-size: 16px; line-height: 1.5;">We noticed it's been 28 days since you last logged in to your tutoring account.</p>
                            <p style="margin: 0 0 30px; color: #666666; font-size: 16px; line-height: 1.5;"><strong>Your tutor is waiting for you!</strong> Your learning journey isn't complete, and we're here to help you continue making progress.</p>
                            <p style="margin: 0 0 30px; color: #666666; font-size: 16px; line-height: 1.5;">Remember, consistent learning leads to the best results. Even a short session can make a big difference in your progress.</p>
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto 30px;">
                                <tr>
                                    <td class="cta-button" align="center" style="background-color: #3498db; border-radius: 4px; padding: 0;">
                                        <a href="{{$login_url}}" target="_blank" style="display: inline-block; padding: 12px 30px; color: #ffffff; text-decoration: none; font-weight: bold; font-size: 16px;">RESUME YOUR LEARNING</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="margin: 0; color: #666666; font-size: 16px; line-height: 1.5;">We're looking forward to seeing you again!<br>The {{$tutoring_system_name}} Team</p>
                        </td>
                    </tr>
                    <!-- Footer -->
                </table>
            </td>
        </tr>
    </table>
</body>
</html>