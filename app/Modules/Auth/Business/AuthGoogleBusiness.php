<?php

namespace App\Modules\Auth\Business;

use App\Models\User;
use App\Modules\Auth\DTO\UserTokenDTO;
use App\Modules\Auth\Enum\TokenTypeEnum;
use App\Modules\Auth\Impl\AuthRepositoryInterface;
use App\Modules\Auth\Impl\Business\AuthBusinessInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AuthGoogleBusiness extends AuthBusinessAbstract implements AuthBusinessInterface
{
    public function getSocialiteUserByToken(string $token):\Laravel\Socialite\Two\User
    {
        return Socialite::driver('google')->stateless()->userFromToken($token);

    }
    public function authUserAndReturnToken(Request $request):UserTokenDTO
    {
        $socialiteUser = $this->getSocialiteUserByToken($request->header('Authorization'));
        $user = $this->authRepository->findUserByGoogleId($socialiteUser->id);
        if(!$user)
            throw new AccessDeniedHttpException('Usuário não encontrado');

        Auth::login($user);
        $token = Auth::user()->createToken('auth_token');
        return new UserTokenDTO([
            'token' => $token->plainTextToken,
            'token_type' => TokenTypeEnum::TOKEN_BEARER->value
        ]);
    }
    public function addUserAndReturnToken(Request $request):UserTokenDTO{
        $socialiteUser = $this->getSocialiteUserByToken($request->header('Authorization'));
        $user = new User([
            'first_name' => $socialiteUser->name,
            'last_name'  => '',
            'email' => $socialiteUser->email,
            'google_id'=> $socialiteUser->id,
            'password' => Crypt::encrypt(env(Str::substr(md5(uniqid()),5,6)))
        ]);
        $user = $this->authRepository->insertUser($user);
        return $this->authUserAndReturnToken($request);
    }
}
