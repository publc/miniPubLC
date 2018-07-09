<?php

namespace App\Core\Auth;

use Exception;
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

    protected $isLoggedIn = false;

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
            $user = $this->user->find($token, 'remember_token');
            $this->user->store();
        }
    }

    public function login()
    {
        $params = (array) $this->request->getParams();
        $fields = [
            'email' => [
                'required' => true,
                'email' => true,
            ],
            'password' => [
                'required' => true,
                'min' => 6
            ]];

        $validation = $this->request->validate($params, $fields);

        if ($validation !== true) {
            $response = $this->response->withStatus(400)->withJSON(['errors' => $validation]);
            return $this->app->respond($response);
        }

        $user = parent::find('email', $params['email']);

        if (!password_verify($params['password'], $user->password)) {
            $response = $this->response->withStatus(400)->withJSON(['errors' => 'Not matches with our records']);
            return $this->app->respond($response);
        }

        $this->session->put('user', $user);

        $this->isLoggedIn = true;

        if (!empty($params['remember']) && $params['remember'] === 'on') {
            $this->remember();
        }

        return;
    }

    public function register()
    {
        $params = (array) $this->request->getParams();

        $fields = [
            'name' => [
                'required' => true,
                'min' => 2,
                'max' => 45
            ],
            'email' => [
                'required' => true,
                'unique' => 'user',
                'email' => true,
            ],
            'username' => [
                'required' => true,
                'min' => 2,
                'max' => 45,
                'unique' => 'user'
            ],
            'password' => [
                'required' => true,
                'min' => 6
            ],
            'confirm_password' => [
                'required' => true,
                'matches' => 'password'
            ]];

        $validation = $this->request->validate($params, $fields);

        if ($validation !== true) {
            $response = $this->response->withStatus(400)->withJSON(['errors' => $validation]);
            return $this->app->respond($response);
        }

        $password = $this->hash->make($params['password']);
        $dbParams = [
            'name' => $params['name'],
            'email' => $params['email'],
            'username' => $params['username'],
            'password' => $password,
            'active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            parent::create($dbParams);
        } catch (Exception $e) {
            return $this->app->respond(['errors' => 'Register proccess failed, try again later']);
        }

        $user = parent::find('email', $params['email']);

        $this->session->put('user', $user);

        $this->isLoggedIn = true;

        return;
    }

    public function patch()
    {
        $user = $this->session->get('user');

        $params = (array) $this->request->getParams();

        $fields = [
            'name' => [
                'min' => 2,
                'max' => 45
            ],
            'password' => [
                'required' => true,
                'min' => 6
            ],
            'confirm_password' => [
                'required' => true,
                'matches' => 'password'
            ]];

        $validation = $this->request->validate($params, $fields);

        if ($validation !== true) {
            $response = $this->response->withStatus(400)->withJSON(['errors' => $validation]);
            return $this->app->respond($response);
        }

        $password = $this->hash->make($params['password']);

        $dbParams = [
            'name' => $params['name'],
            'password' => $password,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            parent::update($user->email, $dbParams);
        } catch (Exception $e) {
            return $this->app->respond(['errors' => 'Register proccess failed, try again later']);
        }

        return;
    }

    public function delete($params = array())
    {
        $user = $this->session->get('user');

        $params = (array) $this->request->getParams();
        $fields = [
            'email' => [
                'required' => true,
                'email' => true,
            ]];

        $validation = $this->request->validate($params, $fields);
        if ($validation !== true) {
            $response = $this->response->withStatus(400)->withJSON(['errors' => $validation]);
            return $this->app->respond($response);
        }

        $filter = $params['email'];
        try {
            parent::delete($filter);
        } catch (Exception $e) {
            return $this->app->respond(['errors' => 'Register proccess failed, try again later']);
        }

        return;
    }

    public function isLoggedIn()
    {
        return $this->isLoggedIn;
    }

    public function logout()
    {
        $this->session->delete('user');
        return;
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
            'params' => array(
                'remember_token' => $token
            ),
            'filters' => array(
                'filter' => 'email',
                'op' => '=',
                'value' => $email
            )
        );

        parent::update($params);
        $this->cookie->put($cookieName, $token);
        return;
    }
}
