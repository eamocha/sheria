<?php
// Clean HTML email template with embedded PHP
$current_year = date("Y");
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <!--[if gte mso 9]>
    <xml>
        <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
        </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <!--[if !mso]><!--><meta http-equiv="X-UA-Compatible" content="IE=edge"><!--<![endif]-->
    <title>Email Template</title>

    <style type="text/css">
        table, td { color: #000000; }
        a { color: #161a39; text-decoration: underline; }

        @media only screen and (min-width: 620px) {
            .u-row {
                width: 600px !important;
            }
            .u-row .u-col {
                vertical-align: top;
            }
            .u-row .u-col-100 {
                width: 600px !important;
            }
        }

        @media (max-width: 620px) {
            .u-row-container {
                max-width: 100% !important;
                padding-left: 0px !important;
                padding-right: 0px !important;
            }
            .u-row .u-col {
                min-width: 320px !important;
                max-width: 100% !important;
                display: block !important;
            }
            .u-row {
                width: calc(100% - 40px) !important;
            }
            .u-col {
                width: 100% !important;
            }
            .u-col > div {
                margin: 0 auto;
            }
        }

        body {
            margin: 0;
            padding: 0;
        }

        table, tr, td {
            vertical-align: top;
            border-collapse: collapse;
        }

        p {
            margin: 0;
        }

        .ie-container table,
        .mso-container table {
            table-layout: fixed;
        }

        * {
            line-height: inherit;
        }

        a[x-apple-data-detectors='true'] {
            color: inherit !important;
            text-decoration: none !important;
        }
    </style>

    <!--[if !mso]><!-->
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700&display=swap" rel="stylesheet" type="text/css">
    <!--<![endif]-->
</head>

<body class="clean-body u_body" style="margin: 0; padding: 0; background-color: #f9f9f9; color: #000000">
<!--[if IE]><div class="ie-container"><![endif]-->
<!--[if mso]><div class="mso-container"><![endif]-->

<table style="border-collapse: collapse; table-layout: fixed; width: 100%; background-color: #f9f9f9;" cellpadding="0" cellspacing="0">
    <tr>
        <td style="word-break: break-word; vertical-align: top;">
            <!--[if (mso)|(IE)]><table width="100%" cellpadding="0" cellspacing="0"><tr><td style="background-color: #f9f9f9;"><![endif]-->

            <!-- Header Spacer -->
            <div class="u-row-container" style="padding: 0; background-color: #f9f9f9;">
                <div class="u-row" style="margin: 0 auto; max-width: 600px; background-color: #f9f9f9;">
                    <div style="display: table; width: 100%;">
                        <div class="u-col u-col-100" style="display: table-cell; vertical-align: top; min-width: 600px;">
                            <div style="width: 100% !important;">
                                <table style="font-family: 'Lato', sans-serif;" width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="padding: 15px;" align="left">
                                            <table height="0px" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-top: 1px solid #f9f9f9;">
                                                <tr>
                                                    <td style="font-size: 0px; line-height: 0px;">&nbsp;</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logo Section -->
            <div class="u-row-container" style="padding: 0; background-color: transparent;">
                <div class="u-row" style="margin: 0 auto; max-width: 600px; background-color: #ffffff;">
                    <div style="display: table; width: 100%;">
                        <div class="u-col u-col-100" style="display: table-cell; vertical-align: top; min-width: 600px;">
                            <div style="width: 100% !important;">
                                <table style="font-family: 'Lato', sans-serif;" width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="padding: 25px 10px 25px 34px;" align="center">
                                            <img src="https://www.sheria360.com/wp-content/uploads/2020/03/12002.png" alt="Logo" style="width: 36%; max-width: 200px;">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Section -->
            <div class="u-row-container" style="padding: 0; background-color: transparent;">
                <div class="u-row" style="margin: 0 auto; max-width: 600px; background-color: #ffffff;">
                    <div style="display: table; width: 100%;">
                        <div class="u-col u-col-100" style="display: table-cell; vertical-align: top; min-width: 600px;">
                            <div style="width: 100% !important;">
                                <table style="font-family: 'Lato', sans-serif;" width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="padding: 10px 40px; color: #666666; font-size: 14px; line-height: 140%;" align="left">
                                            <?= $content ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="u-row-container" style="padding: 0; background-color: transparent;">
                <div class="u-row" style="margin: 0 auto; max-width: 600px; background-color: #ffffff;">
                    <div style="display: table; width: 100%;">
                        <div class="u-col u-col-100" style="display: table-cell; vertical-align: top; min-width: 600px;">
                            <div style="width: 100% !important;">
                                <table style="font-family: 'Lato', sans-serif;" width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="padding: 10px 10px 9px;" align="left">
                                            <table height="0px" width="69%" align="center" border="0" cellspacing="0" cellpadding="0" style="border-top: 1px solid #ced4d9;">
                                                <tr>
                                                    <td style="font-size: 0px; line-height: 0px;">&nbsp;</td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Media Links -->
            <div class="u-row-container" style="padding: 0; background-color: transparent;">
                <div class="u-row" style="margin: 0 auto; max-width: 600px; background-color: #ffffff;">
                    <div style="display: table; width: 100%;">
                        <div class="u-col u-col-100" style="display: table-cell; vertical-align: top; min-width: 600px;">
                            <div style="width: 100% !important;">
                                <table style="font-family: 'Lato', sans-serif;" width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td style="padding: 0 10px 20px;" align="left">
                                            <div align="center" style="display: table; max-width: 187px; margin: 0 auto;">
                                                <!-- Facebook -->
                                                <table align="left" border="0" cellspacing="0" cellpadding="0" width="32" height="32" style="margin-right: 15px;">
                                                    <tr>
                                                        <td align="left" valign="middle">
                                                            <a href="https://www.facebook.com/sheria360/?ref=settings" target="_blank">
                                                                <img src="https://www.sheria360.com/wp-content/uploads/2020/06/facebook.png" alt="Facebook" width="32" style="display: block;">
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <!-- Twitter -->
                                                <table align="left" border="0" cellspacing="0" cellpadding="0" width="32" height="32" style="margin-right: 15px;">
                                                    <tr>
                                                        <td align="left" valign="middle">
                                                            <a href="https://twitter.com/sheria360" target="_blank">
                                                                <img src="https://www.sheria360.com/wp-content/uploads/2020/06/twitter.png" alt="Twitter" width="32" style="display: block;">
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <!-- Instagram -->
                                                <table align="left" border="0" cellspacing="0" cellpadding="0" width="32" height="32" style="margin-right: 15px;">
                                                    <tr>
                                                        <td align="left" valign="middle">
                                                            <a href="https://www.instagram.com/sheria360/" target="_blank">
                                                                <img src="https://www.sheria360.com/wp-content/uploads/2020/06/instagram.png" alt="Instagram" width="32" style="display: block;">
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </table>

                                                <!-- LinkedIn -->
                                                <table align="left" border="0" cellspacing="0" cellpadding="0" width="32" height="32">
                                                    <tr>
                                                        <td align="left" valign="middle">
                                                            <a href="https://www.linkedin.com/company/2645401" target="_blank">
                                                                <img src="https://www.sheria360.com/wp-content/uploads/2020/06/linkedin.png" alt="LinkedIn" width="32" style="display: block;">
                                                            </a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="u-row-container" style="padding: 0; background-color: transparent;">
                <div class="u-row" style="margin: 0 auto; max-width: 600px; background-color: #ffffff;">
                    <div style="display: table; width: 100%;">
                        <div class="u-col u-col-100" style="display: table-cell; vertical-align: top; min-width: 600px;">
                            <div style="width: 100% !important;">
                                <table style="font-family: 'Lato', sans-serif;" width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <!-- Copyright -->
                                    <tr>
                                        <td style="padding: 5px; color: #808f9b; font-size: 14px; line-height: 140%;" align="center">
                                            <p>@<?= $current_year ?> sheria360</p>
                                        </td>
                                    </tr>

                                    <!-- Address 1 -->
                                    <tr>
                                        <td style="padding: 5px; color: #808f9b; font-size: 14px; line-height: 140%;" align="center">
                                            <p>P.O Box 6030-00200,</p>
                                        </td>
                                    </tr>

                                    <!-- Address 2 -->
                                    <tr>
                                        <td style="padding: 5px; color: #808f9b; font-size: 14px; line-height: 140%;" align="center">
                                            <p>Physical Address</p>
                                        </td>
                                    </tr>

                                    <!-- Contact Email -->
                                    <tr>
                                        <td style="padding: 5px; color: #808f9b; font-size: 14px; line-height: 140%;" align="center">
                                            <p>Let us know if you have any questions. <a href="mailto:info@sheria360.com" style="color: #161a39; text-decoration: underline;">info@sheria360.com</a></p>
                                        </td>
                                    </tr>

                                    <!-- Signature -->
                                    <tr>
                                        <td style="padding: 5px; color: #808f9b; font-size: 14px; line-height: 140%;" align="center">
                                            <p>sheria360 Team</p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Spacer -->
            <div class="u-row-container" style="padding: 0; background-color: transparent;">
                <div class="u-row" style="margin: 0 auto; max-width: 600px; background-color: #ffffff;">
                    <div style="display: table; width: 100%;">
                        <div class="u-col u-col-100" style="display: table-cell; vertical-align: top; min-width: 600px;">
                            <div style="width: 100% !important; padding: 30px 0;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
        </td>
    </tr>
</table>

<!--[if mso]></div><![endif]-->
<!--[if IE]></div><![endif]-->
</body>
</html>