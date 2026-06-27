<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    // Method untuk menampilkan halaman utama
    public function index()
    {
        // Kode ini akan mencari dan menampilkan file 'index.blade.php'
        // yang ada di dalam folder resources/views
        return view('index');
    }
}