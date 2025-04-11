<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">

<head>
    <title>CSU-Aparri-SDG - Email Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
            
            .content {
                width: 100% !important;
                margin: 0 !important;
                border-radius: 0 !important;
            }
            
            .card-body {
                padding: 20px !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
                text-align: center !important;
                padding: 15px 10px !important;
            }
            
            .header img {
                max-width: 160px !important;
            }
        }

        body {
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8f9fa;
            color: #495057;
            margin: 0;
            padding: 0;
            width: 100% !important;
            line-height: 1.6;
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
        }

        .wrapper {
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            width: 100%;
        }

        .header {
            text-align: center;
            padding: 25px 0;
        }

        .header img {
            max-width: 200px;
            height: auto;
            margin-bottom: 15px;
        }

        .content {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin: 0 auto;
            max-width: 570px;
            overflow: hidden;
        }

        .card-header {
            text-align: center;
            padding: 30px 30px 20px;
            border-bottom: 1px solid #e9ecef;
            background-color: #f8f9fa;
        }

        .card-header h3 {
            color: #212529;
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 5px;
        }

        .card-header p {
            color: #6c757d;
            font-size: 16px;
            margin-top: 10px;
            margin-bottom: 0;
        }

        .card-body {
            padding: 35px 30px;
        }

        .button {
            display: inline-block;
            background-color: #0d6efd;
            border-radius: 6px;
            color: #ffffff !important;
            font-weight: 600;
            font-size: 16px;
            padding: 14px 30px;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s ease;
            box-shadow: 0 2px 5px rgba(13, 110, 253, 0.3);
        }

        .button:hover {
            background-color: #0b5ed7;
            box-shadow: 0 4px 8px rgba(13, 110, 253, 0.4);
        }

        .subcopy {
            border-top: 1px solid #e9ecef;
            margin-top: 30px;
            padding: 20px 0 0;
            font-size: 14px;
            color: #6c757d;
        }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 13px;
            color: #6c757d;
        }

        a {
            color: #0d6efd;
            text-decoration: none;
        }
        
        p {
            margin-top: 0;
            margin-bottom: 15px;
        }
        
        .button-container {
            margin: 35px 0;
        }
        
        .url-container {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 12px;
            margin-top: 10px;
            word-break: break-all;
        }
        
        .verification-icon {
            width: 60px;
            height: 60px;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    <!-- Header -->
                    <tr>
                        <td class="header">
                            <img src="{{ $logoSrc }}"
                                alt="CSU-Aparri-SDG Logo">
                        </td>
                    </tr>

                    <!-- Email Body -->
                    <tr>
                        <td>
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td class="card-header">
                                        <div align="center">
                                            <svg class="verification-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#0d6efd">
                                                <path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-.997-4L6.76 11.757l1.414-1.414 2.829 2.829 5.656-5.657 1.415 1.414L11.003 16z"/>
                                            </svg>
                                        </div>
                                        <h3>Verify Your Email Address</h3>
                                        <p>Thank you for registering with CSU-Aparri-SDG!</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="card-body">
                                        <p>Hello,</p>
                                        <p>Thank you for creating an account with CSU-Aparri-SDG. To complete your registration and activate your account, please verify your email address by clicking the button below:</p>

                                        <div class="button-container" align="center">
                                            <a href="{{ $url }}" class="button" target="_blank" rel="noopener">
                                                Verify Email Address
                                            </a>
                                        </div>

                                        <p>This verification link will expire in 60 minutes for security reasons.</p>
                                        
                                        <p>If you didn't create this account, please disregard this email or contact our support team if you have concerns.</p>
                                        
                                        <p>Best regards,<br>The CSU-Aparri-SDG Team</p>

                                        <div class="subcopy">
                                            <p>If you're having trouble clicking the "Verify Email Address" button, copy and paste the URL below into your web browser:</p>
                                            <div class="url-container">
                                                <a href="{{ $url }}">{{ $url }}</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td>
                            <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td align="center">
                                        <p>© {{ date('Y') }} CSU-Aparri-SDG. All rights reserved.</p>
                                        <p>Cagayan State University - Aparri Campus<br>Sustainable Development Goals Initiative</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>