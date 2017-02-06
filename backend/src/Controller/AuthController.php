<?php

namespace pronata\Controller;

use pronata\Config\EntityManagerCreator;
use pronata\Entity\User;
use pronata\Error\InvalidParamsException;
use pronata\Request;
use pronata\Security\JWT;
use pronata\Response;

class AuthController {

    public function __construct()
    {
        $emCreator = new EntityManagerCreator();
        $this->em = $emCreator->getOrmEntityManager();
    }

    public function getTokenAction(Request $request)
    {
        $params = $request->getParams();
        if (isset($params['username']) && isset($params['password'])) {
            $user = $this->em->getRepository('pronata\Entity\User')
                ->findOneBy(array('username' => $params['username']), null);
            $signKey = $this->generateRandomSecret();
            if (isset($user) && ($user instanceof User)) {
                if (!$user->isValidPassword($params['password'])) {
                    throw new InvalidParamsException('Invalid password');
                }
            }
            else {
                $user = new User();
                $user->setUsername($params['username']);
                $user->setPassword($params['password']);
                $user->setSignKey($signKey);
                $this->em->persist($user);
                $this->em->flush();
            }
            date_default_timezone_set('UTC');
            //iss (issuer), exp (expiration time), sub (subject), aud (audience), and others.
            $reservedClaims = [
                'iss' => 'todo-api',
                'iat' => time()
            ];
            $payload = array_merge($reservedClaims, ['username' => $params['username']]);

            $jwtToken = JWT::encode($payload, $user->getSignKey());
            $content = [
                'access_token' => $jwtToken
            ];
            $response = new Response();
            $response->setStatusCode(200);
            $response->setContent($content);
            $response->send();
        }
        else {
            throw new InvalidParamsException('Invalid payload to get auth token');
        }
    }

    private function generateRandomSecret()
    {
        $length = rand(10, 20);
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomSecret = '';
        for ($i = 0; $i < $length; $i++) {
            $randomSecret .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomSecret;
    }
}