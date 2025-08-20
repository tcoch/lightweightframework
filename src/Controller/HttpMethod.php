<?php

use LightWeightFramework\Http\Request\Request;

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    echo '$_SERVER GET';
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    echo '$_SERVER POST';
}

if (Request::createFromGlobals()->getRequestMethod() === "GET") {
    echo 'Request GET';
}

if (Request::createFromGlobals()->getRequestMethod() === "POST") {
    echo 'Request POST';
}

if ($_POST) {
    print_r($_POST);
}

if (Request::createFromGlobals()->hasPostData()) {
    echo 'Request value: '.  Request::createFromGlobals()->getPostData()->getValue("foo");
}
