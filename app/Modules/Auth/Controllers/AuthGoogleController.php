<?php

namespace App\Modules\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Impl\Business\AuthBusinessInterface;
use Illuminate\Http\Request;

class AuthGoogleController extends Controller
{
    public function __construct(
        private readonly AuthBusinessInterface $authBusiness
    )
    {
    }
    /**
     * @OA\Get (
     *     tags={"Login"},
     *     summary="Realiza o login do usuário google",
     *     description="Realiza o login do usuário e retorna um token",
     *     path="/auth/google",
     *     @OA\Response(
     *          response="201",
     *          description="Login aconteceu com sucesso",
     *          @OA\JsonContent()
     *      ),
     * ),
     *
     */
    public function authenticate(Request $request){
        return response($this->authBusiness->addOrUpdateUserAndReturnToken($request)->toArray(),200);
    }

    /**
     * @OA\Put(
     *     tags={"Login"},
     *     summary="Retorna os dados do usuário logado",
     *     description="Realiza o logout do usuário",
     *     path="/auth/logout",
     *
     *     @OA\Response(
     *          response="200",
     *          description="Usuário deslogado",
     *          @OA\JsonContent()
     *      ),
     *      @OA\Response(
     *          response="401",
     *          description="Acesso não autorizado",
     *          @OA\JsonContent()
     *      ),
     * ),
     *
     */
    public function logout(Request $request){
        return response($this->authBusiness->logout($request->user(),200));
    }
}
