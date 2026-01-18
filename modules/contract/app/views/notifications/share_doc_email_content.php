<?php
// Clean HTML email template with embedded PHP
$email_content = sprintf($this->lang->line("share_doc_email_content"), $user_profile_name, $action);
$user_message = nl2br($this->input->post("message"));
?>

<table style="border-collapse: collapse; table-layout: fixed; border-spacing: 0; vertical-align: top; min-width: 320px; margin: 0 auto; background-color: #ffffff; width: 100%" cellpadding="0" cellspacing="0">
    <tbody>
    <tr style="vertical-align: top">
        <td style="word-break: break-word; border-collapse: collapse !important; vertical-align: top">
            <!--[if (mso)|(IE)]>
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td align="center" style="background-color: #ffffff;">
            <![endif]-->

            <div class="u-row-container" style="padding: 0; background-color: transparent">
                <div class="u-row" style="margin: 0 auto; min-width: 320px; max-width: 500px; overflow-wrap: break-word; background-color: transparent;">
                    <div style="border-collapse: collapse; display: table; width: 100%; background-color: transparent;">
                        <!--[if (mso)|(IE)]>
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="padding: 0; background-color: transparent;" align="center">
                                    <table cellpadding="0" cellspacing="0" border="0" style="width:500px;">
                                        <tr style="background-color: transparent;">
                        <![endif]-->

                        <div class="u-col u-col-100" style="max-width: 320px; min-width: 500px; display: table-cell; vertical-align: top;">
                            <div style="background-color: #ffffff; width: 100% !important; border-radius: 0;">
                                <!--[if (!mso)&(!IE)]><!-->
                                <div style="padding: 0; border: 0 solid transparent; border-radius: 0;">
                                    <!--<![endif]-->

                                    <!-- Email Content -->
                                    <table style="font-family: arial, helvetica, sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                                        <tbody>
                                        <tr>
                                            <td style="overflow-wrap: break-word; word-break: break-word; padding: 0 7px 7px; font-family: arial, helvetica, sans-serif;" align="left">
                                                <div style="line-height: 100%; text-align: left;">
                                                    <p style="font-size: 14px; line-height: 140%;"><?= $email_content ?>:</p>
                                                    <br>
                                                    <p style="font-size: 14px; line-height: 140%;"><?= $user_message ?></p>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <!-- Button -->
                                    <table style="font-family: arial, helvetica, sans-serif;" role="presentation" cellpadding="0" cellspacing="0" width="100%" border="0">
                                        <tbody>
                                        <tr>
                                            <td style="overflow-wrap: break-word; word-break: break-word; padding-top: 18px; font-family: arial, helvetica, sans-serif;" align="left">
                                                <div align="center">
                                                    <!--[if mso]>
                                                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                            <td style="font-family: arial, helvetica, sans-serif;" align="center">
                                                                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="" style="height:34px; v-text-anchor:middle; width:123px;" arcsize="12%" stroke="f" fillcolor="#205081">
                                                                    <w:anchorlock/>
                                                                    <center style="color:#FFFFFF;font-family:arial,helvetica,sans-serif;">
                                                    <![endif]-->
                                                    <a class="mcnButton" title="Review document" href="<?= $url ?>" target="_blank" style="padding: 10px; border-radius: 3px; letter-spacing: normal; text-decoration: none; color: #FFFFFF; background-color: #205081;">Review document</a>
                                                    <!--[if mso]>
                                                    </center>
                                                    </v:roundrect>
                                                    </td>
                                                    </tr>
                                                    </table>
                                                    <![endif]-->
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <!--[if (!mso)&(!IE)]><!-->
                                </div>
                                <!--<![endif]-->
                            </div>
                        </div>

                        <!--[if (mso)|(IE)]>
                        </tr>
                        </table>
                        </td>
                        </tr>
                        </table>
                        <![endif]-->
                    </div>
                </div>
            </div>

            <!--[if (mso)|(IE)]>
            </td>
            </tr>
            </table>
            <![endif]-->
        </td>
    </tr>
    </tbody>
</table>