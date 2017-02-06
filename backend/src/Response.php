<?php

namespace pronata;

class Response
{
    private $content;

    private $statusCode;

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    public function send()
    {
        http_response_code($this->statusCode);
        print json_encode($this->content, JSON_UNESCAPED_UNICODE);

    }


}