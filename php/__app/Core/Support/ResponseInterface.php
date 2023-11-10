<?php

namespace Core\Support;

interface ResponseInterface
{
    public function send();
    public function make();
}