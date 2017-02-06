<?php

namespace pronata\Controller;


use pronata\Request;

interface CrudControllerInterface
{
    public function postAction(Request $request);

    public function deleteAction(Request $request);

    public function getAction(Request $request);

    public function putAction(Request $request);

    public function patchAction(Request $request);


}