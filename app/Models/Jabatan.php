<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    // Tentukan nama tabel yang benar jika tidak mengikuti konvensi penamaan Laravel
    protected $table = 'tbl_data_jabatan';

    // Tentukan kolom yang dapat diisi
    protected $fillable = [
        'nama_jabatan',
        // tambahkan kolom lain yang relevan
    ];
} 