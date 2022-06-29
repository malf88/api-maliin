<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class ApiAuthController extends Controller
{
    public function jwtToken(Request $request){
        try{
            $user = Socialite::driver('google')->stateless()->userFromToken($request->header('Authorization'));
            $finduser = User::where('google_id', $user->id)->first();

            if($finduser){

                Auth::login($finduser);

            }else{
                $newUser = User::create([
                    'first_name' => $user->name,
                    'last_name'  => '',
                    'email' => $user->email,
                    'google_id'=> $user->id,
                    'password' => ''
                ]);

                Auth::login($newUser);
            }
            $token = Auth::user()->createToken('auth_token');
            return [
                'token' => $token->plainTextToken,
                'token_type' => 'Bearer',
            ];
        }catch (\Exception $e){
            return response($e->getMessage(),401);
        }
    }

    public function token(Request $request){
        try {

            $user = Socialite::driver('google')->user();

            return $user->token;

        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    /**
     * @OA\Post(
     *     tags={"Login"},
     *     summary="Realiza o login do usuário",
     *     description="Realiza o login do usuário e retorna um token",
     *     path="/token",
     *     @OA\Response(
     *          response="201",
     *          description="Login aconteceu com sucesso",
     *          @OA\JsonContent()
     *      ),
     * ),
     *
     */
    public function login(Request $request){
        return Socialite::driver('google')->redirect();
    }
    /**
     * @OA\Get(
     *     tags={"Login"},
     *     summary="Retorna os dados do usuário logado",
     *     description="Realiza o login do usuário e retorna um token",
     *     path="/user",
     *
     *     @OA\Response(
     *          response="200",
     *          description="Usuário logado",
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
    public function getUser(Request $request){
        return $request->user();
    }
    /**
     * @OA\Put(
     *     tags={"Login"},
     *     summary="Retorna os dados do usuário logado",
     *     description="Realiza o logout do usuário",
     *     path="/logout",
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

        return $request->user()->tokens()->where('token', $request->user()->currentAccessToken()->token)->delete();
    }
}
