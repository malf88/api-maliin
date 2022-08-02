<?php

namespace App\Modules\Auth\DTO;

use App\Models\User;

class UserTokenDTO extends \App\Abstracts\DTOAbstract
{
    public string $token;
    public string $token_type;
    public User $user;
}
