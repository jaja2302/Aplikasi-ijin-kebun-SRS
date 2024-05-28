<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historyform extends Model
{
    use HasFactory;
    protected $table = 'form_surat_izin';



    protected $fillable = [
        'user_id',
        'unit_id',
        'tanggal_keluar',
        'tanggal_kembali',
        'lokasi_tujuan',
        'keperluan',
        'atasan_1',
        'atasan_2',
        'status'

    ];

    public function Requestor()
    {
        return $this->belongsTo(Pengguna::class, 'user_id', 'user_id');
    }
    public function Unit()
    {
        return $this->belongsTo(Unitlist::class, 'unit_id', 'id');
    }
    public $timestamps = false;
}
