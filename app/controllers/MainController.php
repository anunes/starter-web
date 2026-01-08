<?php

namespace app\controllers;

class MainController
{
    function home(): void
    {
        view('main/home');
    }

    function guest(): void
    {
        view('main/guest');
    }

    function about(): void
    {
        view('main/about');
    }

    function contact(): void
    {
        view('main/contact');
    }
}
