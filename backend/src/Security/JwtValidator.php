<?php

namespace pronata\Security;


use pronata\Config\EntityManagerCreator;
use pronata\Entity\User;
use pronata\Error\InvalidParamsException;
use pronata\Request;

class JwtValidator
{

    public  function __construct()
    {
        $emCreator = new EntityManagerCreator();
        $this->em = $emCreator->getOrmEntityManager();
    }

    public function getUserFromJwtPayload($authJwtToken)
    {
        $payload = JWT::decode($authJwtToken, null, false);
        $user = $this->em->getRepository('pronata\Entity\User')
            ->findOneBy(array('username' => $payload->username), null);
        if (isset($user) && ($user instanceof User)) {
            $signKey = $user->getSignKey();
            $payload = JWT::decode($authJwtToken, $signKey, true);
            if (isset($payload->username) && isset($payload->iat) && isset($payload->iss) &&
                $payload->iss == 'todo-api') {
                return $user;
            }
            else {
                throw new InvalidParamsException('Authorization failed');
            }
        }
        else {
            throw new InvalidParamsException('Authorization failed');
        }
    }

}