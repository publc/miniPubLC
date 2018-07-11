<?php

namespace App\Controllers;

use App\Core\Hash;
use App\Core\Config;
use App\Core\Session;
use App\Core\Controller;

class HomeController extends Controller
{
    protected $model = "user";

    public function index($response)
    {
        return $response->view('home')
            ->with([
                'greetings' => 'Hello World From MiniPublc!',
                'message' => "Yeaaaaah loooooad views baby"
            ])
            ->layout('home');
    }
}
