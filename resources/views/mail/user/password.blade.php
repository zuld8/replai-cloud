<!DOCTYPE html>
<html lang="id" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml">

<head>
    <title>{{$setting->app_name}} - Reset Password</title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: inherit !important;
        }

        #MessageViewBody a {
            color: inherit;
            text-decoration: none;
        }

        p {
            line-height: inherit;
            margin: 0;
        }

        .desktop_hide,
        .desktop_hide table {
            display: none;
            max-height: 0px;
            overflow: hidden;
        }

        @media (max-width:600px) {
            .desktop_hide table.icons-inner {
                display: inline-block !important;
            }

            .mobile_hide {
                display: none !important;
            }

            .row-content {
                width: 100% !important;
            }

            .stack .column {
                width: 100%;
                display: block;
            }

            .desktop_hide,
            .desktop_hide table {
                display: table !important;
                max-height: none !important;
            }

            .email-container {
                width: 100% !important;
                margin: 0 !important;
            }

            .button-link {
                font-size: 16px !important;
                padding: 14px 30px !important;
            }
        }
    </style>
</head>

<body style="margin: 0; padding: 0; background-color: #f4f7f6;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; background-color: #f4f7f6;" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                
                <!-- Main Container -->
                <table class="email-container" role="presentation" style="width: 600px; max-width: 600px; background-color: #ffffff; border-radius: 16px; box-shadow: 0 4px 24px rgba(69, 135, 87, 0.12); overflow: hidden;" width="600" cellspacing="0" cellpadding="0" border="0">
                    
                    <!-- Header with Brand -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #458757 0%, #5fa973 100%); padding: 40px 40px 100px 40px; text-align: center; position: relative;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center">
                                        <!-- Logo -->
                                        <img src="<?= asset($setting->logo); ?>" alt="{{$setting->app_name}}" style="width: 140px; height: auto; display: block; margin: 0 auto 20px auto;" />
                                        
                                        <!-- Title -->
                                        <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700; line-height: 1.3;">
                                            Reset Password
                                        </h1>
                                        <p style="margin: 10px 0 0 0; color: rgba(255, 255, 255, 0.9); font-size: 15px; line-height: 1.5;">
                                            Permintaan untuk mengatur ulang password
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Icon Container (Overlapping) -->
                    <tr>
                        <td style="padding: 0; position: relative;">
                            <div style="width: 100%; text-align: center; margin-top: -60px; position: relative; z-index: 10;">
                                <div style="display: inline-block; background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%); border-radius: 20px; padding: 20px; box-shadow: 0 8px 32px rgba(69, 135, 87, 0.2);">
                                    <img src="<?= asset('assets/img/icons/reset-password.png'); ?>" alt="Reset Password" style="width: 120px; height: auto; display: block;" />
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 40px 30px 40px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center">
                                        <h2 style="margin: 0 0 16px 0; color: #1f2937; font-size: 22px; font-weight: 600; line-height: 1.3;">
                                            Halo, <?= $user->name; ?>! 👋
                                        </h2>
                                        <p style="margin: 0 0 20px 0; color: #4b5563; font-size: 15px; line-height: 1.6;">
                                            Kami menerima permintaan untuk mereset password akun Anda di <strong style="color: #458757;">{{$setting->app_name}}</strong>. 
                                            Klik tombol di bawah ini untuk melanjutkan proses reset password.
                                        </p>
                                        
                                        <!-- Security Notice -->
                                        <div style="background-color: #fef2f2; border-left: 4px solid #ef4444; border-radius: 8px; padding: 16px; margin: 20px 0; text-align: left;">
                                            <p style="margin: 0; color: #991b1b; font-size: 13px; line-height: 1.5;">
                                                <strong>🔒 Perhatian Keamanan:</strong> Jangan bagikan email ini kepada siapapun untuk menghindari pembajakan akun. Link reset password hanya berlaku untuk satu kali penggunaan.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Button -->
                    <tr>
                        <td style="padding: 0 40px 40px 40px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center">
                                        <a href="{{config('app.url')}}<?= route('password.reset', $token, false); ?>?email=<?= $user->email; ?>" class="button-link" style="display: inline-block; background: linear-gradient(135deg, #458757 0%, #5fa973 100%); color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; padding: 16px 48px; border-radius: 12px; box-shadow: 0 4px 16px rgba(69, 135, 87, 0.3); transition: all 0.3s ease;" target="_blank">
                                            🔑 Reset Password Sekarang
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Token Expiry Info -->
                    <tr>
                        <td style="padding: 0 40px 30px 40px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center">
                                        <div style="background-color: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; padding: 12px; margin-bottom: 20px;">
                                            <p style="margin: 0; color: #166534; font-size: 13px; line-height: 1.5;">
                                                ⏰ Link ini akan kadaluarsa dalam <strong>60 menit</strong> untuk keamanan akun Anda.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Alternative Link -->
                    <tr>
                        <td style="padding: 0 40px 40px 40px; border-top: 1px solid #e5e7eb;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td style="padding-top: 30px;">
                                        <p style="margin: 0 0 12px 0; color: #6b7280; font-size: 13px; line-height: 1.6; text-align: center;">
                                            Tombol tidak berfungsi? Salin dan tempel link berikut ke browser Anda:
                                        </p>
                                        <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; margin-top: 12px; word-break: break-all;">
                                            <a href="{{config('app.url')}}<?= route('password.reset', $token, false); ?>?email=<?= $user->email; ?>" style="color: #458757; font-size: 12px; text-decoration: none; word-break: break-all;">
                                                {{config('app.url')}}<?= route('password.reset', $token, false); ?>?email=<?= $user->email; ?>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; padding: 30px 40px; border-top: 1px solid #e5e7eb;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
                                <tr>
                                    <td align="center">
                                        <p style="margin: 0 0 12px 0; color: #6b7280; font-size: 13px; line-height: 1.6;">
                                            <strong>Tidak merasa meminta reset password?</strong>
                                        </p>
                                        <p style="margin: 0 0 12px 0; color: #6b7280; font-size: 13px; line-height: 1.6;">
                                            Abaikan email ini. Password Anda tidak akan berubah dan akun Anda tetap aman. Jika Anda mengalami masalah keamanan, segera hubungi tim support kami.
                                        </p>
                                        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                                © 2024 {{$setting->app_name}}. All rights reserved.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
                <!-- End Main Container -->

            </td>
        </tr>
    </table>
</body>

</html>