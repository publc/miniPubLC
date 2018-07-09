<?php

namespace App\Core\Auth;

use App\Core\App;
use App\Core\Hash;
use App\Core\Config;
use App\Core\Cookie;
use App\Core\Request;
use App\Core\Session;
use App\Core\Response;
use App\Core\Container;
use App\Core\Controller;

class Auth extends Controller
{
    protected $model = 'user';

    protected $validation;

    protected $app;

    protected $config;

    protected $cookie;

    protected $request;

    protected $session;

    protected $hash;

    protected $response;

    protected $container;

    public function __construct()
    {
        $this->container = new Container([
            'app' => function () {
                return new App;
            },
            'request' => function () {
                return new Request;
            },
            'config' => function () {
                return new Config;
            },
            'session' => function () {
                return new Session;
            },
            'cookie' => function () {
                return new Cookie;
            },
            'hash' => function () {
                return new Hash;
            },
            'response' => function () {
                return new Response;
            }
        ]);

        $this->app = $this->container->app;
        $this->config = $this->container->config;
        $this->request = $this->container->request;
        $this->cookie = $this->container->cookie;
        $this->session = $this->container->session;
        $this->hash = $this->container->hash;
        $this->response = $this->container->response;
    }

    public function autologin()
    {
        $cookieName = $this->config->get('remember')->cookie_name;
        $sessionName = $this->config->get('session')->session_name;

        if ($this->cookie->exists($cookieName) && !$this->session->exists($sessionName)) {
            $token = $this->cookie->get($cookieName);
            $user = $this->user->find($token, "remember_token");
            $this->user->store();
        }
    }

    public function login()
    {

        $params = $this->request->getParams();
        $fields = [
            "email" => [
                "required" => true,
                "email" => true,
            ],
            "password" => [
                "required" => true,
                "min" => 6
            ]];

        $validation = $this->request->validate($params, $fields);

        if ($validation !== true) {
            $response = $this->response->withStatus(400)->withJSON(['errors' => $validation]);
            return $this->app->respond($response);
        }

        $user = parent::find($params['email']);

        if (!password_verify($params['password'], $user->password)) {
            $response = $this->response->withStatus(400)->withJSON(['errors' => "Not matches with our records"]);
            return $this->app->respond($response);
        }

        $this->session->put('user', $user);

        if (!empty($params['remember']) && $params['remember'] === "on") {
            $this->remember();
        }

        return;
    }

    public function register()
    {
        if (!$this->validation->registration($this->request)) {
            return;
        }

        $fields = $this->registrationData($this->request);

        if ($this->user->create($fields)) {
            Session::flash("message", ["Registration Successfully"]);
        }

        return;
    }

    public function patch()
    {
        if (!$this->validation->update($this->request)) {
            return;
        }

        $fields = $this->updateData($this->request);

        if ($this->user->updateUser($fields)) {
            Session::flash("message", ["Successfully Updated"]);
        }

        return;
    }

    protected function registrationData(Request $request)
    {
        return array(
            "username" => $request->get("username"),
            "email" => $request->get("email"),
            "password" => Hash::make($request->get("password")),
            "name" => $request->get("name"),
            "active" => 1,
            "created_at" => date("Y-m-d H:i:s")
        );
    }

    protected function updateData(Request $request)
    {
        $userData = $this->user->getUserData();
        $username = (!empty($request->get("username"))) ? $request->get("username") : $userData->username;
        $email = (!empty($request->get("email"))) ? $request->get("email") : $userData->email;
        $name = (!empty($request->get("name"))) ? $request->get("name") : $userData->name;
        return array(
            "username" => $username,
            "email" => $email,
            "name" => $name,
            "updated_at" => date("Y-m-d H:i:s")
        );
    }

    public function updatePassword()
    {
        if (!$this->validation->updatePassword($this->request)) {
            return;
        }

        $userData = $this->user->getUserData();

        if (!$userData || !$this->validation->validatePassword($userData, $this->request)) {
            Session::flash("message", ["Invalid params. review and try again"]);
            return false;
        }

        $fields = $this->updatePasswordData($this->request);


        if ($this->user->updatePassword($fields)) {
            Session::flash("message", ["Successfully Updated"]);
        }

        return;
    }

    protected function updatePasswordData(Request $request)
    {
        return array(
            "password" => Hash::make($request->get("new_password")),
            "updated_at" => date("Y-m-d H:i:s")
        );
    }

    public function validate()
    {
        return $this->user->isLoggedIn();
    }

    public function logout()
    {
        $this->user->logout();
        Router::redirect("/");
    }

    public function remember()
    {
        $token = $this->session->get('user')->remember_token;
        $email = $this->session->get('user')->email;
        $cookieName = $this->container->config->get('remember')->cookie_name;

        if (!is_null($token)) {
            $this->cookie->put($cookieName, $token);
            return;
        }

        $token = $this->hash->unique();

        $params = array(
            "params" => array(
                "remember_token" => $token
            ),
            "filters" => array(
                "filter" => "email",
                "op" => "=",
                "value" => $email
            )
        );

        parent::update($params);
        $this->cookie->put($cookieName, $token);
        return;
    }
}
