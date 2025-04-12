<?php

namespace App\Helpers;

class UserHelper
{
    public static function canApprovePurchaseInvoice()
    {
        $user = auth()->user();
        
        if (!$user || $user->status !== 'A') {
            return false;
        }

        return ($user->id_jabatan == 160 || $user->id_role == '5af56935b011a');
    }

    public static function canApproveContraBon()
    {
        return self::canApprovePurchaseInvoice(); // Menggunakan kriteria yang sama
    }
} 