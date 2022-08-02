<?php

namespace App\Modules\Auth\Impl\Business;

use App\Models\User;
use App\Modules\Auth\DTO\UserTokenDTO;
use Illuminate\Http\Request;

interface AuthBusinessInterface
{
    public function authUserAndReturnToken(Request $request):UserTokenDTO;
    public function updateEmailUser(string $email):User;

    /**
     * @param Request $request
     * @return UserTokenDTO
     * @deprecated
     */
    public function addOrUpdateUserAndReturnToken(Request $request):UserTokenDTO;
    public function getSocialiteUserByToken(string $token):\Laravel\Socialite\Two\User;
    public function logout(User $user):bool;
}
