<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSuratIzin extends Model
{
    use HasFactory;

    protected $table = 'db_izin_kebun';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id',
        'list_units_id',
        'tanggal_keluar',
        'tanggal_kembali',
        'kendaraan',
        'plat_nomor',
        'lokasi_tujuan',
        'keperluan',
        'atasan_1',
        'atasan_2',
        'status',
        'catatan',
        'status_bot'
    ];
}
