<?php

namespace pronata;

use pronata\Error\InvalidParamsException;

class Request
{
    private $content;
    private $objectUriPart;
    private $idUriPart;
    private $user;

    public function __construct()
    {
        $requestUri = htmlentities($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8');
        $requestUriParts = explode("/", $requestUri);
        $this->objectUriPart = isset($requestUriParts[1]) ? $requestUriParts[1] : NULL;
        $this->idUriPart = isset($requestUriParts[2]) ? $requestUriParts[2] : NULL;
        if (($this->getHttpMethod() == 'GET') && isset($requestUriParts[1])){
            $objectUriPartWithGetParams = explode("?", $requestUriParts[1]);
            $this->objectUriPart = $objectUriPartWithGetParams[0];
        }

    }

    public function setUser($user)
    {
        $this->user = $user;

    }

    public function getUser()
    {
        return $this->user;
    }

    public function getBearerToken()
    {
        $authorizationHeaders = NULL;
        if (isset($_SERVER['Authorization'])) {
            $authorizationHeaders = trim(strip_tags($_SERVER["Authorization"]));
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $authorizationHeaders = trim(strip_tags($_SERVER["HTTP_AUTHORIZATION"]));
        }
        elseif (function_exists('apache_request_headers')) {
                $requestHeaders = apache_request_headers();
                // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
                $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
                if (isset($requestHeaders['Authorization'])) {
                    $authorizationHeaders = trim($requestHeaders['Authorization']);
                }
        }
        if (!empty($authorizationHeaders)) {
            if (preg_match('/Bearer\s(\S+)/', $authorizationHeaders, $matches)) {
                return $matches[1];
            }
        }

        throw new InvalidParamsException('Not set authorization header');

    }

    public function getIdUriPart() {
        return $this->idUriPart;
    }

    public function getObjectUriPart()
    {
        return $this->objectUriPart;
    }

    public function getContent()
    {
        if (null === $this->content)
        {
            if (0 === strlen(trim($this->content = file_get_contents('php://input'))))
            {
                $this->content = false;
            }
        }
        return $this->content;
    }

    public function getPathInfo()
    {
        return htmlentities($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8');
    }

    public function getContentType()
    {
        return $_SERVER['CONTENT_TYPE'];
    }

    public function getHttpMethod()
    {
        return htmlentities($_SERVER['REQUEST_METHOD'], ENT_QUOTES, 'UTF-8');
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        $params = [];
        switch($this->getHttpMethod()) {
            case 'POST':
                $params = $_POST;
                break;
            case 'GET':
                $params = $_GET;
                break;
            case 'DELETE':
                break;
            case 'PATCH':
                parse_str(file_get_contents('php://input'), $params);
                break;
            case 'PUT':
                parse_str(file_get_contents('php://input'), $params);
                break;
            default:
                break;
        }
        return $params;
    }
}