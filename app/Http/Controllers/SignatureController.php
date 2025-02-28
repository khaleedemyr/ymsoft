<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SignatureController extends Controller
{
    public function upload(Request $request)
    {
        $user = Auth::user();
        $path = null;

        if ($request->hasFile('signatureFile')) {
            $path = $request->file('signatureFile')->store('signatures', 'public');
        } elseif ($request->has('signatureData')) {
            $data = $request->input('signatureData');
            list($type, $data) = explode(';', $data);
            list(, $data) = explode(',', $data);
            $data = base64_decode($data);
            $path = 'signatures/' . $user->id . '_signature.png';
            Storage::disk('public')->put($path, $data);
        }

        if ($path) {
            $user->signature_path = $path;
            $user->save();
        }

        return redirect()->back()->with('signaturePath', $path);
    }
} 