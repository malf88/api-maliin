<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAuthController extends Controller
{
    /**
     * @OA\Post(
     *     tags={"Login"},
     *     summary="Realiza o login do usuário",
     *     description="Realiza o login do usuário e retorna um token",
     *     path="/token",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 example={"email": "teste@teste.com", "password": "12345"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response="201",
     *          description="Login aconteceu com sucesso",
     *          @OA\JsonContent()
     *      ),
     * ),
     *
     */
    public function login(Request $request){
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }
        $user = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('auth_token');

        return [
                'token' => $token->plainTextToken,
                'token_type' => 'Bearer',
            ];
    }

    public function getUser(Request $request){
        return $request->user();
    }
}
