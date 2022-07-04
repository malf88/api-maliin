<?php

namespace App\Modules\Auth\Business;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Two\User as SocialiteUser;
use App\Modules\Auth\DTO\UserTokenDTO;
use App\Modules\Auth\Enum\TokenTypeEnum;
use App\Modules\Auth\Impl\Business\AuthBusinessInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AuthGoogleBusiness
    extends AuthBusinessAbstract
    implements AuthBusinessInterface
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

    public function updateUserGoogleIfExists(SocialiteUser $socialiteUser): User
    {
        $user = $this->userRepository->findUserByEmail($socialiteUser->getEmail());
        if($user){
            $userData = [
                'first_name' => $socialiteUser->getName(),
                'google_id' => $socialiteUser->getId()
            ];
            $this->userRepository->updateUser($user->id, $userData);
        }
        return $user;
    }

    public function addUserAndReturnToken(Request $request):UserTokenDTO{
        $socialiteUser = $this->getSocialiteUserByToken($request->header('Authorization'));
        $user = $this->updateUserGoogleIfExists($socialiteUser);
        if(!$user){
            $user = [
                'first_name' => $socialiteUser->getName(),
                'last_name'  => '',
                'email' => $socialiteUser->getEmail(),
                'google_id'=> $socialiteUser->getId(),
                'password' => Hash::make(uniqid())
            ];
            $user = $this->userRepository->saveUser($user);
        }

        return $this->authUserAndReturnToken($request);
    }
}
