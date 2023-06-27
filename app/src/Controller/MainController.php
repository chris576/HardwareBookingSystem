<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class MainController
{
    public function default() : Response {
        return new Response("Hello World.");
    }
}