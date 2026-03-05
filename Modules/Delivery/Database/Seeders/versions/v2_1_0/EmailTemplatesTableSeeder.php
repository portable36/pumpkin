<?php

namespace Modules\Delivery\Database\Seeders\versions\v2_1_0;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplatesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        if (! DB::table('email_templates')->where('slug', 'delivery-boy-assigned')->first()) {
            DB::table('email_templates')->insert([
                [
                    'parent_id' => null,
                    'name' => 'Delivery Boy Assigned',
                    'slug' => 'delivery-boy-assigned',
                    'subject' => 'Assigned #Order Code: {order_no}',
                    'body' => '<!DOCTYPE html>
                        <html>
    
                        <head>
                            <meta charset="utf-8" />
                            <meta http-equiv="x-ua-compatible" content="ie=edge" />
                            <title>Ticket-template-design</title>
                            <meta name="viewport" content="width=device-width, initial-scale=1" />
                            <style type="text/css">
                                @media screen {
                                    @font-face {
                                        font-family: "DM Sans";
                                        font-weight: 700;
                                        src: url(https://fonts.gstatic.com/s/dmsans/v11/rP2Cp2ywxg089UriASitCBimCw.woff2) format("woff2");
                                    }
    
                                    @font-face {
                                        font-family: "DM Sans";
                                        font-weight: 500;
                                        font-style: normal;
                                        src: url(https://fonts.gstatic.com/s/dmsans/v11/rP2Cp2ywxg089UriAWCrCBimCw.woff2) format("woff2");
                                    }
                                }
    
                                .bodys,
                                .tables,
                                td,
                                .anchor-tag a {
                                    -ms-text-size-adjust: 100%;
                                    -webkit-text-size-adjust: 100%;
                                }
    
                                .tables,
                                td {
                                    mso-table-rspace: 0pt;
                                    mso-table-lspace: 0pt;
                                }
    
                                .anchor-tag a {
                                    padding: 1px;
                                    margin: 1px;
                                }
    
                                .anchor-tag a[x-apple-data-detectors] {
                                    font-family: inherit !important;
                                    font-size: inherit !important;
                                    font-weight: inherit !important;
                                    line-height: inherit !important;
                                    color: inherit !important;
                                    text-decoration: none !important;
                                }
    
                                .bodys {
                                    width: 100% !important;
                                    height: 100% !important;
                                    padding: 0 !important;
                                    margin: 0 !important;
                                }
    
                                .tables {
                                    border-collapse: collapse !important;
                                }
    
                                .logo-img {
                                    margin: 26px 0px 19px 0px;
                                    padding: 0px;
                                    width: 207.98px;
                                    height: 56px;
                                }
    
                                .actives {
                                    box-sizing: border-box;
                                    text-decoration: none;
                                    -webkit-text-size-adjust: none;
                                    text-align: center;
                                    border-radius: 2px;
                                    -webkit-border-radius: 2px;
                                    -moz-border-radius: 2px;
                                    -khtml-border-radius: 2px;
                                    -o-border-radius: 2px;
                                    -ms-border-radius: 2px;
                                    padding: 10px 38px;
                                    cursor: pointer;
                                    background: #fcca19;
                                }
    
                                .anchor-tag a:focus,
                                .anchor-tag a:hover {
                                    text-decoration: underline;
                                    text-decoration-color: #fcca19;
                                }
    
                                .anchor-tag a:-webkit-any-link {
                                    color: -webkit-link;
                                    cursor: pointer;
                                    text-decoration: underline;
                                    text-decoration-color: #fcca19;
                                }
    
                                .anchor-tag a:-webkit-any-link {
                                    color: -webkit-link;
                                    cursor: pointer;
                                    text-decoration: none;
                                    text-decoration-color: #fcca19;
                                }
                            </style>
                        </head>
    
                        <body class="bodys" style="background-color: #e9ecef">
                            <div class="preheader"
                                style="display: none; max-width: 0; max-height: 0; margin: 0px; overflow: hidden; color: #fff; opacity: 0;">
                            </div>
                            <table class="tables" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center" bgcolor="#e9ecef">
                                        <table class="tables" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 640px">
                                            <tr>
                                                <td align="center" valign="top" style="padding: 36px 24px"></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" bgcolor="#e9ecef">
                                        <table class="tables" border="0" cellpadding="0" cellspacing="0" width="100%"
                                            style="max-width: 640px; margin-top: 100px">
                                            <tr>
                                                <td align="center" bgcolor="#ffffff">
                                                    <img class="logo-img" src="{logo}" alt="logo" />
                                                    <p style="border-top: 1px solid #dfdfdf; margin: 1px 20px 0px 20px;"></p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" bgcolor="#e9ecef">
                                        <table class="tables" border="0" cellpadding="0" cellspacing="0" width="100%"
                                            style="max-width: 640px; font-family: \'DM Sans\', sans-serif; font-weight: 500;">
                                            <tr>
                                                <td align="center" bgcolor="#fff">
                                                    <p
                                                        style="font-family: \'DM Sans\', sans-serif; letter-spacing: 0.255em; text-transform: uppercase; margin: 26px 0px; line-height: 25px;
                                                        font-size:0.80em!important; color: rgb(44, 44, 44); font-weight: 500 !important; cursor: default !important;">
                                                        </p>
                                                    <p
                                                        style="margin: 0px; text-align: center; line-height: 24px; font-size: 16px; color: #2c2c2c;">
                                                        Hello {assignee_name}</p>
                                                    <p
                                                        style="margin: 0px; color: #898989; font-size: 14px; margin: 7px 54px 0px; text-align: center; line-height: 24px;">
                                                        A new order has been assigned to you.
                                                    </p>
                                                    <div style="background-color: #F3F3F3; border-radius:4px ; margin: 25px 78px">
                                                    </div>
                                                </td>
    
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" bgcolor="#e9ecef">
                                        <table class="tables" border="0" cellpadding="0" cellspacing="0" width="100%"
                                            style=" max-width: 640px; font-family: \'DM Sans\', sans-serif; font-weight: 500;">
                                            <tr>
                                                <td align="center" bgcolor="#ffffff">
                                                    <div>
                                                        <a href="{details}" aria-pressed="true" class="actives anchor-tag">
                                                            <span style="text-decoration: none; color: #2C2C2C;">View Order</span>
                                                        </a>
                                                    </div>
                                                    <p
                                                        style="font-family: \'DM Sans\', sans-serif; font-style: normal; font-weight: 500; font-size: 13px; line-height: 17px; text-align: center; color: #2C2C2C; margin-top: 62px;">
                                                        Thank You,</p>
                                                    <p
                                                        style="font-family: \'DM Sans\', sans-serif; font-style: normal; font-weight: 500;padding-top: 2px; font-size: 13px;line-height: 17px; text-align: center;color: #898989;margin:0; margin-top: 12px; margin-bottom: 20px;">
                                                        {company_name}</p>
                                                    <p style="border-top: 1px solid #dfdfdf; margin: 1px 20px 0px 20px;"></p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" bgcolor="#e9ecef">
                                        <table class="tables" border="0" cellpadding="0" cellspacing="0" width="100%"
                                            style=" max-width: 640px; font-family: \'DM Sans\', sans-serif; font-weight: 500; margin-bottom: 200px; ">
                                            <tr>
                                                <td align="center" bgcolor="#ffffff">
                                                    <p
                                                        style="text-align: center; line-height: 16px; color: #898989; font-size: 12px; margin: 13px 0px;">
                                                        &copy 2022, {company_name}. All rights reserved.</p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </body>
    
                        </html>',
                    'language_id' => 1,
                    'status' => 'Active',
                    'variables' => 'order_no,company_name,details,project_name,logo',
                ],
                [
                    'parent_id' => null,
                    'name' => 'New delivery boy email notification',
                    'slug' => 'new-delivery-boy-email-notification',
                    'subject' => 'New delivery boy email notification',
                    'body' => '<!DOCTYPE html>
                    <html>
                    
                    <head>
                        <meta charset="utf-8" />
                        <meta http-equiv="x-ua-compatible" content="ie=edge" />
                        <title>1.Password Reset</title>
                        <meta name="viewport" content="width=device-width, initial-scale=1" />
                        <style type="text/css">
                            @media screen {
                                @font-face {
                                    font-family: "DM Sans";
                                    font-weight: 700;
                                    src: url(https://fonts.gstatic.com/s/dmsans/v11/rP2Cp2ywxg089UriASitCBimCw.woff2) format("woff2");
                                }
                    
                                @font-face {
                                    font-family: "DM Sans";
                                    font-weight: 500;
                                    font-style: normal;
                                    src: url(https://fonts.gstatic.com/s/dmsans/v11/rP2Cp2ywxg089UriAWCrCBimCw.woff2) format("woff2");
                                }
                            }
                    
                            .bodys,
                            .tables,
                            td,
                            .anchor-tag a {
                                -ms-text-size-adjust: 100%;
                                -webkit-text-size-adjust: 100%;
                            }
                    
                            .tables,
                            td {
                                mso-table-rspace: 0pt;
                                mso-table-lspace: 0pt;
                            }
                    
                            .anchor-tag a {
                                padding: 1px;
                                margin: 1px;
                            }
                    
                            .anchor-tag a[x-apple-data-detectors] {
                                font-family: inherit !important;
                                font-size: inherit !important;
                                font-weight: inherit !important;
                                line-height: inherit !important;
                                color: inherit !important;
                                text-decoration: none !important;
                            }
                    
                            .bodys {
                                width: 100% !important;
                                height: 100% !important;
                                padding: 0 !important;
                                margin: 0 !important;
                            }
                    
                            .tables {
                                border-collapse: collapse !important;
                            }
                    
                            .logo-img {
                                margin: 26px 0px 19px 0px;
                                padding: 0px;
                                width: 207.98px;
                                height: 56px;
                            }
                    
                            .actives {
                                box-sizing: border-box;
                                text-decoration: none;
                                -webkit-text-size-adjust: none;
                                text-align: center;
                                border-radius: 2px;
                                -webkit-border-radius: 2px;
                                -moz-border-radius: 2px;
                                -khtml-border-radius: 2px;
                                -o-border-radius: 2px;
                                -ms-border-radius: 2px;
                                padding: 10px 31px;
                                cursor: pointer;
                                background: #fcca19;
                            }
                    
                            .anchor-tag a:focus,
                            .anchor-tag a:hover {
                                text-decoration: underline;
                                text-decoration-color: #fcca19;
                            }
                    
                            .anchor-tag a:-webkit-any-link {
                                color: -webkit-link;
                                cursor: pointer;
                                text-decoration: underline;
                                text-decoration-color: #fcca19;
                            }
                    
                            .anchor-tag a:-webkit-any-link {
                                color: -webkit-link;
                                cursor: pointer;
                                text-decoration: none;
                                text-decoration-color: #fcca19;
                            }
                        </style>
                    </head>
                    
                    <body class="bodys" style="background-color: #e9ecef">
                        <div class="preheader"
                            style="display: none; max-width: 0; max-height: 0; margin: 0px; overflow: hidden; color: #fff; opacity: 0;">
                        </div>
                        <table class="tables" border="0" cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td align="center" bgcolor="#e9ecef">
                                    <table class="tables" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 640px">
                                        <tr>
                                            <td align="center" valign="top" style="padding: 36px 24px"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" bgcolor="#e9ecef">
                                    <table class="tables" border="0" cellpadding="0" cellspacing="0" width="100%"
                                        style="max-width: 640px; margin-top: 100px">
                                        <tr>
                                            <td align="center" bgcolor="#ffffff">
                                                <img class="logo-img" src="{logo}" alt="logo" />
                                                <p style="border-top: 1px solid #dfdfdf; margin: 1px 20px 0px 20px;"></p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" bgcolor="#e9ecef">
                                    <table class="tables" border="0" cellpadding="0" cellspacing="0" width="100%"
                                        style="max-width: 640px; font-family: \'DM Sans\', sans-serif; font-weight: 500;">
                                        <tr>
                                            <td align="center" bgcolor="#fff">
                                                <p
                                                    style="font-family: \'DM Sans\', sans-serif; letter-spacing: 0.255em; text-transform: uppercase; margin: 26px 0px; line-height: 25px;
                                        font-size:0.80em!important; color: rgb(44, 44, 44); font-weight: 500 !important; cursor: default !important;">
                                                    Your Credentials</p>
                                                <p
                                                    style="margin: 0px; text-align: center; line-height: 24px; font-size: 16px; color: #2c2c2c;">
                                                    Dear {user_name}</p>
                                                <p
                                                    style="margin: 0px; color: #898989; font-size: 14px; margin: 15px 54px 42px; text-align: center; line-height: 24px;">
                                                    A warm welcome to {company_name} family, please login to the <a href="{company_url}"
                                                        style="text-decoration: underline; cursor: pointer; color: #0060a9;">portal</a> to
                                                    see the details of your account.
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" bgcolor="#e9ecef">
                                    <table class="tables" border="0" cellpadding="0" cellspacing="0" width="100%"
                                        style=" max-width: 640px; font-family: \'DM Sans\', sans-serif; font-weight: 500;">
                                        <tr>
                                            <td align="center" bgcolor="#ffffff">
                    
                                                <div>
                                                    <p style="color: #2C2C2C;">Credentials:</p>
                                                    <p
                                                        style="margin: 0px; color: #898989; font-size: 14px; text-align: center; line-height: 24px;">
                                                        <span style="color: #2c2c2c; padding-right: 2px;">Email:</span>{user_email}
                                                    </p>
                                                    <p
                                                        style="margin: 0px; color: #898989; font-size: 14px; text-align: center; line-height: 24px;">
                                                        <span style="color: #2c2c2c; padding-right: 2px;">Password:</span>{user_pass}
                                                    </p>
                                                </div>
                    
                                                <div
                                                    style="font-size: 14px; text-align: center; color: #898989; line-height: 22px; margin: 1px;">
                                                    <p style="margin-top: 54px"> If you have any queries, concerns or suggestions,</p>
                                                    <p style="margin: 0px; margin-top: 1px"> please email us:
                                                        <span
                                                            style="text-decoration: underline; cursor: pointer; color: #0060a9;">{support_mail}</span>
                                                    </p>
                                                </div>                                           
                                                <p style="border-top: 1px solid #dfdfdf; margin: 1px 20px 0px 20px;"></p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" bgcolor="#e9ecef">
                                    <table class="tables" border="0" cellpadding="0" cellspacing="0" width="100%"
                                        style=" max-width: 640px; font-family: \'DM Sans\', sans-serif; font-weight: 500; margin-bottom: 200px; ">
                                        <tr>
                                            <td align="center" bgcolor="#ffffff">
                                                <p
                                                    style="text-align: center; line-height: 16px; color: #898989; font-size: 12px; margin: 13px 0px;">
                                                    &copy 2022, {company_name}. All rights reserved.</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </body>
                    
                    </html>',
                    'language_id' => 1,
                    'status' => 'Active',
                    'variables' => 'user_name, company_url, user_email, user_pass, company_name,logo,support_mail',
                ],
            ]);
        }
    }
}
