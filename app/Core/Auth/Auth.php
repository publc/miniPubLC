<?php

namespace App\Core\Auth;

use Exception;
use App\Core\App;
use App\Core\JWT;
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

    protected $jwt;

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
            },
            'jwt' => function () {
                return new JWT;
            }
        ]);

        $this->app = $this->container->app;
        $this->config = $this->container->config;
        $this->request = $this->container->request;
        $this->cookie = $this->container->cookie;
        $this->session = $this->container->session;
        $this->hash = $this->container->hash;
        $this->response = $this->container->response;
        $this->jwt = $this->container->jwt;
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

        if (!empty($params['remember']) && $params['remember'] === 'on') {
            $this->remember($user);
        }

        $token = $this->generateToken($user);

        $response = [
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'token' => $token
        ];

        return $response;
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

        $token = $this->generateToken($user);

        $response = [
            'name' => $user->name,
            'email' => $user->email,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
            'token' => $token
        ];

        return $response;
    }

    public function patch()
    {
        $checkToken = $this->checkToken();
        if ($checkToken !== true) {
            return $checkToken;
        }

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
            parent::update($user->email, 'email', $dbParams);
        } catch (Exception $e) {
            return $this->app->respond(['errors' => 'Register proccess failed, try again later']);
        }

        return ['errors' => false];
    }

    public function delete($params = array())
    {
        $checkToken = $this->checkToken();
        if ($checkToken !== true) {
            return $checkToken;
        }

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
        $token = $this->generateToken($user, false, true);
        return [
            'token' => $token
        ];
    }

    protected function remember($user)
    {
        $token = $this->generateToken($user, true);
        $cookieName = $this->config->get('remember')->name;

        $params = [
            'remember_token' => $token
        ];

        parent::update($user->email, $params);
        $this->cookie->put($cookieName, $token);
        return;
    }

    protected function checkToken($remember = false)
    {
        if ($remember === false) {
            return $this->request->validateToken();
        }

        $config = $this->config->get('remember');
        $cookie = $this->cookie->get($config->name);

        if (!$cookie) {
            return;
        }

        return $this->validateToken($cookie);
    }

    protected function generateToken($user, $remember = false, $logout = false)
    {
        $jwt = $this->config->get('jwt');
        $exp = $this->config->get('remember');
        $payload = (array) $jwt->payload;
        $payload['userId'] = $user->id;
        $payload['exp'] = ($logout === true) ? time() - 60 : (($remember === true) ? $exp->exp : $payload['exp']);
        return $this->jwt::encode($payload, $jwt->secret);
    }

    protected function validateToken($token)
    {
        try {
            $this->jwt::decode($token, $this->config->get('jwt')->secret, ['HS256']);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
