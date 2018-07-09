<?php

namespace App\Controllers;

use App\Core\Hash;
use App\Core\Token;
use App\Core\Config;
use App\Core\Session;
use App\Core\Controller;

class HomeController extends Controller
{
    protected $model = "user";

    public function index($response)
    {
        // // $user1 = $this->get();
        // // $user2 = $this->getModel()->find('deveplon');
        // $mail = new Mail;
        // $message = $mail->send('deveplon@gmail.com', 'Say Hello');
        $token = new Hash;
        $test = $token->make('vale1988');
        var_dump($test);die;
        return $response->view('home')
            ->with([
                'greetings' => 'Hello World From MiniPublc!',
                'message' => "Yeaaaaah loooooad views baby"
            ])
            ->layout('home');
    }
}
