<?php

namespace App\Modules\Account\Controllers;

use App\Exceptions\ExistsException;
use App\Http\Controllers\Controller;
use App\Modules\Account\Impl\Business\AccountBusinessInterface;
use App\Modules\Account\ServicesLocal\AccountServiceLocal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ItemNotFoundException;

class AccountController extends Controller
{
    public function __construct(
        private AccountBusinessInterface $accountServices
    )
    {
    }
    /**
     * @OA\Get(
     *     tags={"Accounts"},
     *     summary="Retorna uma lista de contas",
     *     description="Retornará uma lista de cursos do usuário logado",
     *     path="/account",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Response(
     *          response="200",
     *          description="Uma lista de contas"
     *     ),
     * )
     *
     */
    public function index(Request $request)
    {
        return response($this->accountServices->getListAllAccountFromLoggedUser(),200);
    }
    /**
     * @OA\Get(
     *     tags={"Accounts"},
     *     summary="Retorna a conta com o {id}",
     *     description="Retorna a conta com o {id} informado",
     *     path="/account/{id}",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do registro buscado",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *         ),
     *         style="form"
     *     ),
     *     @OA\Response(response="200", description="Um objeto de curso"),
     *     @OA\Response(response="404", description="Conta não encontrada")
     * ),
     *
     */
    public function show(Request $request, int $id)
    {
        try{
            return response($this->accountServices->getAccountById($id),200);
        }catch (ItemNotFoundException $e){
            return response($e->getMessage(),404);
        }

    }
    /**
     * @OA\Post(
     *     tags={"Accounts"},
     *     summary="Insere uma conta",
     *     description="Insere uma nova conta",
     *     path="/account",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="bank",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="agency",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="account",
     *                     type="string"
     *                 ),
     *                 example={"name": "Nubank", "bank": "260", "account": "1234567-8", "agency": "0001"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response="201", description="Objeto inserido com sucesso"),
     * ),
     *
     */
    public function insert(Request $request)
    {
        return response($this->accountServices->insertAccount(Auth::user(),$request->all()),201);

    }
    /**
     * @OA\Put(
     *     tags={"Accounts"},
     *     summary="Altera uma conta",
     *     description="Altera uma conta existente",
     *     path="/account/{id}",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do registro buscado",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *         ),
     *         style="form"
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="bank",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="agency",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="account",
     *                     type="string"
     *                 ),
     *                 example={"name": "Nubank", "bank": "260", "acount": "1234567-8", "agency": "0001"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Objeto alterado com sucesso"),
     *     @OA\Response(response="404", description="Objeto não encontrado"),
     * ),
     *
     */
    public function update(Request $request,int $id)
    {
        try{
            return response($this->accountServices->updateAccount($id,$request->all()),200);
        }catch (ItemNotFoundException $e){
            return response($e->getMessage(),404);
        }
    }
    /**
     * @OA\Delete(
     *     tags={"Accounts"},
     *     summary="Exclui a conta com o {id}",
     *     description="Exclui a conta com o {id} informado",
     *     path="/account/{id}",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do registro buscado",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *         ),
     *         style="form"
     *     ),
     *     @OA\Response(response="200", description="Excluído com sucesso"),
     *     @OA\Response(response="404", description="Conta não encontrada")
     * ),
     *
     */
    public function delete(Request $request,int $id)
    {
        try{
            return response($this->accountServices->deleteAccount($id),200);
        }catch (ItemNotFoundException $e){
            return esponse($e->getMessage(),404);
        }

    }

    /**
     * @OA\Put(
     *     tags={"Accounts"},
     *     summary="Adiciona um usuário a conta",
     *     description="Adiciona um usuário a conta",
     *     path="/account/{account_id}/user/{user_id}",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="account_id",
     *         in="path",
     *         description="Id da conta",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *         ),
     *         style="form"
     *     ),
     *  @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="Id do usuário a ser vinculado à conta",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *         ),
     *         style="form"
     *     ),
     *     @OA\Response(response="200", description="Excluído com sucesso"),
     *     @OA\Response(response="404", description="Conta não encontrada")
     * ),
     *
     */
    public function addUserToAccount(int $account_id, int $user_id)
    {
        try{
            return response($this->accountServices->addUserToAccount($account_id, $user_id),200);
        }catch (ItemNotFoundException|ExistsException $e){
            return response($e->getMessage(),404);
        }

    }

    /**
     * @OA\Delete(
     *     tags={"Accounts"},
     *     summary="Exclui o usuário da conta",
     *     description="Exclui o usuário da conta",
     *     path="/account/{account_id}/user/{user_id}",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="account_id",
     *         in="path",
     *         description="Id da conta",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *         ),
     *         style="form"
     *     ),
     *  @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="Id do usuário a ser removido da conta",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *         ),
     *         style="form"
     *     ),
     *     @OA\Response(response="200", description="Excluído com sucesso"),
     *     @OA\Response(response="404", description="Conta não encontrada")
     * ),
     *
     */
    public function removeUserToAccount(int $account_id, int $user_id)
    {
        try{
            return response($this->accountServices->removeUserToAccount($account_id, $user_id),200);
        }catch (ItemNotFoundException|ExistsException $e){
            return response($e->getMessage(),404);
        }

    }
}
