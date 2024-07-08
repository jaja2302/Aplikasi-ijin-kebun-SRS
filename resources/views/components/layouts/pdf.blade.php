<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $name_file }}</title>
    <style>
        @page {
            margin: 100px 30px;
        }

        @font-face {
            font-family: 'Inter-Regular';
            src: url('/fonts/Inter-Regular.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        header {
            position: fixed;
            top: -80px;
            left: 0px;
            right: 0px;
            height: 50px;
            text-align: center;
            align-content: center;
        }

        body {
            font-family: 'Inter-Regular', sans-serif;
        }

        .container {
            width: 100%;
            display: table;
            clear: both;
        }

        .borderedTable,
        .borderedTable td,
        .borderedTable th {
            border: 1px solid;
        }

        .borderedTable {
            width: 100%;
            border-collapse: collapse;
        }

        .no-border-table {
            width: 100%;
            border: 0;
        }

        .no-border-table td,
        .no-border-table th {
            border: 0;
        }

        .info {
            border-bottom: 1px solid black;
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <header>
        <table class="borderedTable" style="width: 100%; font-size: 8px; font-weight: bold;">
            <tr>
                <td style="border-right: none; width: 10%; text-align: center;" rowspan="4">
                    <img width="120" src="{{ asset('images/icons/logo_sss.png') }}" alt="Logo SRS">
                </td>
                <td style="border-left: none; width: 55%; text-align: center;" rowspan="4">
                    <a style="font-size: 25px;">IZIN KELUAR KEBUN</a>
                </td>
                <td style="width: 35%;">
                    <table class="no-border-table">
                        <tr>
                            <td style="width: 40%;">
                                <a>No. Dokumen</a>
                            </td>
                            <td style="width: 60%;">
                                <a>: F - PERS.GN - D21.R0</a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="no-border-table">
                        <tr>
                            <td style="width: 40%;">
                                <a>Revisi</a>
                            </td>
                            <td style="width: 60%;">
                                <a>: 01</a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="no-border-table">
                        <tr>
                            <td style="width: 40%;">
                                <a>Berlaku Efektif</a>
                            </td>
                            <td style="width: 60%;">
                                <a>: 10 Desember 2012</a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="no-border-table">
                        <tr>
                            <td style="width: 40%;">
                                <a>Halaman</a>
                            </td>
                            <td style="width: 60%;">
                                <a>: 1 dari 1</a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </header>

    <div class="container">
        <div style="float: left; width: 65%; margin-top: 50px;">
            <div>Nama Karyawan :</div>
            <div class="info">{{ $allUser[$data['user_id']]['nama_lengkap'] }}</div>
            <div>Unit / Sub. Unit :</div>
            <div class="info">{{ $departement }}</div>
            <div>Jabatan :</div>
            <div class="info">{{ $allUser[$data['user_id']]['jabatan'] }}</div>
        </div>
        <div style="float: left; width: 35%; margin-top: 30px;">
            <div style="width: 100%; display: flex; justify-content: center; align-items: center; text-align: center;">
                <img width="200" height="200" src="{{ asset('images/others/avatar.svg') }}" alt="QR Code Surat">
            </div>
        </div>
    </div>

    <div class="container">
        <div style="float: left; width: 100%; margin-top: 7px;">
            <div>Tujuan :</div>
            <div class="info">{{ $data['lokasi_tujuan'] }}</div>
        </div>
    </div>

    <div class="container">
        <div style="float: left; width: 100%; margin-top: 7px;">
            <div>Keperluan :</div>
            <div
                style="margin-top: 10px; display: flex; justify-content: left; align-items: left; text-align: justify; border:1px solid black; border-radius: 10px; padding: 10px;">
                {{ $data['keperluan'] }}</div>
        </div>
    </div>

    <div class="container">
        <div style="float: left; width: 50%; margin-top: 15px;">
            <div style="display: flex; justify-content: center; align-items: center; text-align: center;">
                <a>Tanggal Keluar</a>
                <div style="border:1px solid black; border-radius: 10px; padding: 10px; margin: 10px 10px 0px 0px;">
                    {{ formattedDate($data['tanggal_keluar'], IntlDateFormatter::LONG) }}
                </div>
            </div>
        </div>
        <div style="float: left; width: 50%; margin-top: 15px;">
            <div style="display: flex; justify-content: center; align-items: center; text-align: center;">
                <a>Tanggal Kembali</a>
                <div style="border:1px solid black; border-radius: 10px; padding: 10px; margin: 10px 0px 0px 10px;">
                    {{ formattedDate($data['tanggal_kembali'], IntlDateFormatter::LONG) }}
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div style="float: left; width: 50%; margin-top: 15px;">
            <div style="display: flex; justify-content: center; align-items: center; text-align: center;">
                <a>Jenis Kendaraan</a>
                <div style="border:1px solid black; border-radius: 10px; padding: 10px; margin: 10px 10px 0px 0px;">
                    {{ $data['kendaraan'] }}
                </div>
            </div>
        </div>
        <div style="float: left; width: 50%; margin-top: 15px;">
            <div style="display: flex; justify-content: center; align-items: center; text-align: center;">
                <a>Plat Nomor</a>
                <div style="border:1px solid black; border-radius: 10px; padding: 10px; margin: 10px 0px 0px 10px;">
                    {{ $data['plat_nomor'] }}
                </div>
            </div>
        </div>
    </div>

    @if (!empty($qrCodeEncrypt))
        <div class="container">
            <div
                style=" width: 100%; margin-top: 30px; display: flex; justify-content: center; align-items: center; text-align: center;">
                <img src="{{ $qrCodeEncrypt }}" alt="QR Code Surat">
            </div>
        </div>
    @endif

    <table style="width: 100%; margin-top: 50px;">
        <tr>
            <td>Approved By / Digital Signed By :</td>
        </tr>
    </table>

    <table style="width: 100%; margin-top: 10px;">
        <tr>
            <td style="border-bottom: 1px solid black;">Atasan 1 - {{ $allUser[$data['atasan_1']]['nama_lengkap'] }}
                ({{ $allUser[$data['atasan_1']]['jabatan'] }})</td>
        </tr>
    </table>

    <table style="width: 100%; margin-top: 7px;">
        <tr>
            <td style="border-bottom: 1px solid black;">Atasan 2 - {{ $allUser[$data['atasan_2']]['nama_lengkap'] }}
                ({{ $allUser[$data['atasan_2']]['jabatan'] }})</td>
        </tr>
    </table>
</body>

</html>
