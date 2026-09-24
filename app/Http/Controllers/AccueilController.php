<?php

namespace App\Http\Controllers;

class AccueilController extends Controller
{
    public function __construct(private string $title = "Accueil")
    {
        $this->title = $title;
    }

    public function classController()
    {
        return view('classController');
    }
}

