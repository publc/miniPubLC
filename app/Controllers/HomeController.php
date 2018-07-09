<?php

namespace App\Controllers;

use App\Core\Token;
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
        $token = new Token;
        $test = $token->check('d78ef77d9a8d8c6827e71f35a104e50c');
        var_dump($test);die;
        return $response->view('home')
            ->with([
                'greetings' => 'Hello World From MiniPublc!',
                'message' => "Yeaaaaah loooooad views baby"
            ])
            ->layout('home');
    }
}
