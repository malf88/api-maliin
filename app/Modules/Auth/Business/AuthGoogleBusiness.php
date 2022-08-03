<?php

namespace App\Modules\Auth\Business;

use App\Exceptions\ExistsException;
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
        return $this->generateToken($user);

    }

    private function generateToken(User $user):UserTokenDTO
    {
        Auth::setUser($user);
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
            'email' => '',
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

    /**
     * @throws ExistsException
     */
    public function updateEmailUserAndReturnNewToken(string $email): UserTokenDTO
    {
        $user = $this->authRepository->findUserByGoogleId(Auth::user()->google_id);
        if (!empty($user->email))
            throw new ExistsException('O usuário já está vinculado ao email: ' . $user->email);

        $userEmailExists = $this->userRepository->findUserByEmail($email);

        $this->updateExistingUser($userEmailExists, $user->google_id);

        if (!$this->removeUserDuplicated($userEmailExists, $user))
            return $this->generateToken(
                $this->userRepository->updateUser($user->id, ['email' => $email])
            );
        return $this->generateToken($userEmailExists);

    }

    private function updateExistingUser(User|null $userEmailExists, string $googleId):void
    {
        if ($userEmailExists != null) {
            $userEmailExists = $this->userRepository->updateUser(
                $userEmailExists->id,
                ['google_id' => $googleId]
            );
        }
    }

    private function removeUserDuplicated(User|null $existingUser, User $newUser):bool
    {
        if($existingUser && $existingUser->id != $newUser->id){
            return $this->userRepository->deleteUserById($newUser->id);
        }
        return false;
    }
}
