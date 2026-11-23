<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="color-scheme" content="light">
        <title>Balasan baru untuk konsultasi Anda</title>
    </head>
    <body>
        <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">
            Tim Empirise telah mengirim balasan untuk konsultasi {{ $reply->consultation->nomor_konsultasi }}.
        </div>
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;background-color:#f5f7f6;font-family:Arial,Helvetica,sans-serif;color:#20312b;">
            <tr>
                <td align="center" style="padding:32px 16px;">
                    <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;border-collapse:separate;background-color:#ffffff;border:1px solid #e3e9e5;border-radius:16px;">
                        <tr>
                            <td style="padding:32px 32px 12px;">
                                <p style="margin:0;color:#34775d;font-size:13px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;">Empirise Jalan-Jalan</p>
                                <h1 style="margin:16px 0 8px;font-size:26px;line-height:1.3;color:#20312b;">Ada balasan untuk Anda</h1>
                                <p style="margin:0;color:#53635c;font-size:16px;line-height:1.6;">
                                    Tim kami membalas konsultasi
                                    <strong style="color:#20312b;">{{ $reply->consultation->nomor_konsultasi }}</strong>.
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:12px 32px 24px;">
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;background-color:#f5f8f6;border-left:4px solid #34775d;">
                                    <tr>
                                        <td style="padding:18px 20px;color:#34453e;font-size:15px;line-height:1.7;white-space:pre-line;">{{ $replyExcerpt }}</td>
                                    </tr>
                                </table>
                                @if ($replyIsTruncated)
                                    <p style="margin:10px 0 0;color:#68776f;font-size:13px;line-height:1.5;">Buka percakapan untuk membaca balasan selengkapnya.</p>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:0 32px 32px;">
                                <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                                    <tr>
                                        <td align="center" bgcolor="#34775d" style="border-radius:999px;">
                                            <a href="{{ $consultationUrl }}" style="display:inline-block;padding:14px 24px;border:1px solid #34775d;border-radius:999px;color:#ffffff;font-size:15px;font-weight:bold;text-decoration:none;">Buka percakapan</a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:20px 32px;border-top:1px solid #e3e9e5;color:#68776f;font-size:13px;line-height:1.6;">
                                <p style="margin:0 0 8px;">Salam hangat,<br><strong style="color:#34453e;">Tim Empirise Jalan-Jalan</strong></p>
                                <p style="margin:0;">Email ini dikirim karena Anda memiliki konsultasi bersama Empirise.</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
