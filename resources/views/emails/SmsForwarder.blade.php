<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
    <meta charset="utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings xmlns:o="urn:schemas-microsoft-com:office:office">
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <style>
        td, th, div, p, a, h1, h2, h3, h4, h5, h6 {
            font-family: "Segoe UI", sans-serif;
            mso-line-height-rule: exactly;
        }
    </style>
    <![endif]-->
    <title>Form Submitted Email</title>
    <style>
        .hover-underline:hover {
            text-decoration: underline !important;
        }

        @media (max-width: 600px) {
            .sm-w-full {
                width: 100% !important;
            }

            .sm-px-24 {
                padding-left: 24px !important;
                padding-right: 24px !important;
            }
        }
    </style>
</head>
<body
    style="margin: 0; width: 100%; padding: 0; word-break: break-word; -webkit-font-smoothing: antialiased; background-color: #f3f4f6;">
<div role="article" aria-roledescription="email" aria-label="Form Submitted Email" lang="en">
    <table style="width: 100%; font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;"
           cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center" style="background-color: #f3f4f6;">
                <table class="sm-w-full" style="margin-top: 20px; width: 600px;" cellpadding="0" cellspacing="0"
                       role="presentation">
                    <tr>
                        <td align="center" class="sm-px-24">
                            <table style="width: 100%;" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td class="sm-px-24"
                                        style="border-radius: 4px; background-color: #ffffff; padding: 48px; text-align: left; font-size: 24px; font-weight: 700; color: #374151;">
                                        <table style="width: 100%;" cellpadding="0" cellspacing="0" role="presentation">
                                            <tr>
                                                <td style="padding-top: 20px;">
                                                    <div
                                                        style="height: 1px; background-color: #e5e7eb; line-height: 1px;">
                                                        &zwnj;
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <table class="sm-w-full"
                                               style="width: 600px; font-size: 16px; font-weight: 400;" cellpadding="0"
                                               cellspacing="0" role="presentation">
                                            @foreach($smsData as $data)
                                                <tr>
                                                    <td>
                                                        @if( $data['title'] != 'Updates' || $hasUpdate)
                                                            <div
                                                                style="margin-top: 20px; margin-bottom: 5px; color: #6b7280;">
                                                                {{$data['title']}}
                                                            </div>
                                                        @endif
                                                        @if($data['title'] == 'Link')
                                                            <div><a
                                                                    href="http://{{$data['value']}}">{{$data['value']}}</a>
                                                            </div>
                                                        @elseif($data['title'] == 'Updates')
                                                            @if($hasUpdate)
                                                                @foreach($data['value'] as $index1 => $update)
                                                                    @if($index1 < (count($data['value'])-1))
                                                                        <div
                                                                            style="margin-top: 8px; margin-bottom: 8px;">
                                                                            <div>{!! nl2br(e($update))!!}</div>
                                                                        </div>
                                                                    @else
                                                                        <div
                                                                            style="margin-top: 8px; margin-bottom: 8px; color: #b91c1c;">
                                                                            <div>{!! nl2br(e($update))!!}</div>
                                                                        </div>
                                                                    @endif

                                                                @endforeach
                                                            @endif
                                                        @else
                                                            <div>{!! nl2br(e($data['value']))!!}</div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @if($mapsImageUrl)
                                                <tr>
                                                    <td style="text-align: center;">
                                                        <div style="margin-top: 20px; margin-bottom: 5px;">
                                                            <img
                                                                src="{{$mapsImageUrl}}"
                                                                style="max-width: 100%; vertical-align: middle; line-height: 100%; border: 0;"
                                                                alt="">
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($mapsLink)
                                                <tr>
                                                    <td>
                                                        <div>
                                                            <a href="{{$mapsLink}}">
                                                                {{$mapsLink}}
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>
                                        <table style="width: 100%;" cellpadding="0" cellspacing="0" role="presentation">
                                            <tr>
                                                <td style="padding-top: 32px; padding-bottom: 32px;">
                                                    <div
                                                        style="height: 1px; background-color: #e5e7eb; line-height: 1px;">
                                                        &zwnj;
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <p style="margin: 0; margin-bottom: 16px; font-size: 18px;">Thanks,<br>
                                            <span style="font-size: 16px; font-style: italic;">programming@manwithawrench.com</span>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="height: 48px;"></td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 24px; padding-right: 24px; text-align: center; font-size: 12px; color: #4b5563;">
                                        <p style="margin: 0; margin-bottom: 4px; text-transform: uppercase;">
                                            Manwithawrench.com</p>
                                        <p style="margin: 0; font-style: italic;">programming@manwithawrench.com</p>
                                        <p style="cursor: default;">
                                            <a href="https://www.manwithawrench.com" class="hover-underline"
                                               style="color: #3b82f6; text-decoration: none;">www.manwithawrench.com</a>
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
