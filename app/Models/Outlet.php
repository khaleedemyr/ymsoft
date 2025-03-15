<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;

    // Tentukan nama tabel yang benar
    protected $table = 'tbl_data_outlet';
    protected $primaryKey = 'id_outlet';

    // Tentukan kolom yang dapat diisi
    protected $fillable = [
        'nama_outlet',
        // tambahkan kolom lain yang relevan
    ];
} 