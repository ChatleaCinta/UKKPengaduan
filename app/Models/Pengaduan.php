<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    use HasFactory;
    protected $table = 'pengaduan';
    protected $fillable = ['foto','nik','status','laporan'];
    public function user()
    {
        return $this->hasOne(User::class, 'nik', 'nik');
    }
    public function tanggapan()
    {
        return $this->hasMany(tanggapan::class,'id_pengaduan');
    }
}
