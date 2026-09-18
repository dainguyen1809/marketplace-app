<?php

namespace Domain\User\Enum;

enum Gender: int
{
    case MALE = 0;

    case FEMALE = 1;

    case OTHER = 2;
}
