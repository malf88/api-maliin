<?php

namespace App\Modules\Account\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Account\Impl\Business\AccountBusinessInterface;
use App\Modules\Account\ServicesLocal\AccountServiceLocal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return $this->accountServices->getListAllAccountFromLoggedUser();
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
        return $this->accountServices->getAccountById($id);
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
        return $this->accountServices->insertAccount(Auth::user(),$request->all());
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
        return $this->accountServices->updateAccount($id,$request->all());
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
        return $this->accountServices->deleteAccount($id);
    }
}
