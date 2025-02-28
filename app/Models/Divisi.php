<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    use HasFactory;

    // Tentukan nama tabel yang benar
    protected $table = 'tbl_data_divisi';

    // Tentukan kolom yang dapat diisi
    protected $fillable = [
        'nama_divisi',
        // tambahkan kolom lain yang relevan
    ];
} 