<?php

namespace App\Modules\Auth\Business;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ItemNotFoundException;
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
            $user = $this->insertUserGoogle($socialiteUser);
            //throw new AccessDeniedHttpException('Usuário não encontrado');

        Auth::login($user);
        $token = Auth::user()->createToken('auth_token');
        return new UserTokenDTO([
            'token' => $token->plainTextToken,
            'token_type' => TokenTypeEnum::TOKEN_BEARER->value,
            'user' => Auth::user()
        ]);
    }

    public function updateUserGoogle(SocialiteUser $socialiteUser): User
    {
        $user = $this->userRepository->findUserByEmail($socialiteUser->getEmail());
        if(!$user)
            throw new ItemNotFoundException('Usuário não encontrado.');

        $userData = [
            'first_name' => $socialiteUser->getName(),
            'google_id' => $socialiteUser->getId()
        ];
        $this->userRepository->updateUser($user->id, $userData);
        return $user;
    }

    public function insertUserGoogle(SocialiteUser $socialiteUser): User
    {
        $user = [
            'first_name' => $socialiteUser->getName(),
            'last_name'  => '',
            'email' => $socialiteUser->getEmail(),
            'google_id'=> $socialiteUser->getId(),
            'password' => Hash::make(uniqid())
        ];
        return $this->userRepository->saveUser($user);
    }

    /**
     * @param Request $request
     * @return UserTokenDTO
     * @deprecated
     */
    public function addOrUpdateUserAndReturnToken(Request $request):UserTokenDTO
    {
        $socialiteUser = $this->getSocialiteUserByToken($request->header('Authorization'));
        try{
            $this->updateUserGoogle($socialiteUser);
        }catch (ItemNotFoundException $e){
            $this->insertUserGoogle($socialiteUser);
        }
        return $this->authUserAndReturnToken($request);
    }

    public function updateEmailUser(string $email): User
    {
        // TODO: Implement updateEmailUser() method.
    }
}
