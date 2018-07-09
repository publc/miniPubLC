<?php

namespace App\Controllers;

use App\Core\Mail;
use App\Models\User;
use App\Core\Controller;
use App\Core\Storage\FileStorage;

class HomeController extends Controller
{
    protected $model = "user";

    public function index($response)
    {

        // // $user1 = $this->get();
        // // $user2 = $this->getModel()->find('deveplon');
        // $mail = new Mail;
        // $message = $mail->send('deveplon@gmail.com', 'Say Hello');
        // return $response->view('home')
        //     ->with([
        //         'greetings' => 'Hello World From MiniPublc!',
        //         'message' => $message
        //     ])
        //     ->layout('home');
    }
}
