<?php

namespace App\Modules\Auth\DTO;

class UserTokenDTO extends \App\Abstracts\DTOAbstract
{
    public string $token;
    public string $token_type;
}
