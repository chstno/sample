<?php


namespace Core\Database;

enum FetchTypes: int
{
    public const FETCH          = 1;
    public const FETCH_ALL      = 2;
    public const AFFECTED       = 3;
    public const LAST_INSERT_ID = 4;
}