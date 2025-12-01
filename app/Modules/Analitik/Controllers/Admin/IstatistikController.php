<?php

namespace App\Modules\Analitik\Controllers\Admin;

use App\Http\Controllers\Controller;

class IstatistikController extends Controller
{
    public function index()
    {
        return view('admin.analitik.istatistikler.index');
    }

    public function genel()
    {
        return view('admin.analitik.istatistikler.genel');
    }

    public function ilan()
    {
        return view('admin.analitik.istatistikler.ilan');
    }

    public function satis()
    {
        return view('admin.analitik.istatistikler.satis');
    }

    public function finans()
    {
        return view('admin.analitik.istatistikler.finans');
    }

    public function musteri()
    {
        return view('admin.analitik.istatistikler.musteri');
    }
}
