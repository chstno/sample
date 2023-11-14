<?php


namespace Core\Database;

enum FetchTypes: int
{
    case FETCH          = 1;
    case FETCH_ALL      = 2;
    case AFFECTED       = 3;
    case LAST_INSERT_ID = 4;
}