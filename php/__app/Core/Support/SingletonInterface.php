<?php


namespace Core\Support;


interface SingletonInterface
{
    public static function getInstance(): static;
}