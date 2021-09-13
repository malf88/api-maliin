<?php

namespace App\Modules\Account\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CreditCard;
use App\Modules\Account\ServicesLocal\CreditCardServiceLocal;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class CreditCardController extends Controller
{
    private CreditCardServiceLocal $creditCardServices;
    public function __construct(CreditCardServiceLocal $creditCardServices)
    {
        $this->creditCardServices = $creditCardServices;
    }
     /**
      * @OA\Get(
      *     tags={"Credit Cards"},
      *     summary="Uma lista de cartões de crédito de uma conta",
      *     description="Uma lista de cartões de crédito da conta informada",
      *     path="/creditcard/account/{accountId}",
      *     security={
      *         {"bearerAuth": {}}
      *     },
      *     @OA\Parameter(
      *         name="accountId",
      *         in="path",
      *         description="Id da conta",
      *         required=true,
      *         @OA\Schema(
      *           type="integer",
      *         ),
      *         style="form"
      *     ),
      *     @OA\Response(response="200", description="Uma lista de cartão de crédito"),
      *     @OA\Response(response="404", description="Conta não encontrada")
      * ),
      */
    public function index($accountId):Collection
    {
        return $this->creditCardServices->getListCreditCardByAccount($accountId);
    }
    /**
     * @OA\Get(
     *     tags={"Credit Cards"},
     *     summary="Retorna o cartão de crédito com o {id}",
     *     description="Retornada o cartão de crédito com o {id} informado",
     *     path="/creditcard/{id}",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do cartão de crédito",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *         ),
     *         style="form"
     *     ),
     *     @OA\Response(response="200", description="Retorna um objeto de cartão de crédito"),
     *     @OA\Response(response="404", description="Conta não encontrada")
     * ),
     */
    public function show(Request $request):CreditCard
    {
        return $this->creditCardServices->getCreditCardbyId($request->id);
    }

    /**
     * @OA\Post(
     *     tags={"Credit Cards"},
     *     summary="Insere um cartão de crédito",
     *     description="Insere um novo cartão de crédito",
     *     path="/creditcard/account/{accountId}",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="accountId",
     *         in="path",
     *         description="Id da conta",
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
     *                     property="due_day",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="close_day",
     *                     type="int"
     *                 ),
     *                 example={"name": "Itaú", "due_day": 10, "close_day": 16}
     *             )
     *         )
     *     ),
     *     @OA\Response(response="201", description="Objeto inserido com sucesso"),
     *     @OA\Response(response="404", description="Conta não encontrada"),
     * ),
     *
     */
    public function insert(Request $request,int $accountId):CreditCard
    {
        return $this->creditCardServices->insertCreditCard($accountId,$request->all());
    }

    /**
     * @OA\Put(
     *     tags={"Credit Cards"},
     *     summary="Altera um cartão de crédito",
     *     description="Altera um cartão de crédito existente",
     *     path="/creditcard/{id}",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do registro a ser alterado",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *         ),
     *         style="form"
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="text/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="due_day",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="close_day",
     *                     type="int"
     *                 ),
     *                 example={"name": "Itaú", "due_day": 10, "close_day": 16}
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Objeto alterado com sucesso"),
     *     @OA\Response(response="404", description="Objeto não encontrado"),
     * ),
     *
     */
    public function update(Request $request, int $id)
    {
        return $this->creditCardServices->updateCreditCard($id,$request->all());
    }
    /**
     * @OA\Delete(
     *     tags={"Credit Cards"},
     *     summary="Exclui o cartão de crédito com o {id}",
     *     description="Exclui uma categoria",
     *     path="/creditcard/{id}",
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
    public function delete(int $id):int
    {
        return $this->creditCardServices->removeCreditCard($id);
    }
    /**
     * @OA\Get(
     *     tags={"Credit Cards"},
     *     summary="Retorna uma lista de faturas",
     *     description="Retornada uma lista de faturas do cartão com o {id} informado",
     *     path="/creditcard/{id}/invoices",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id do cartão de crédito",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *         ),
     *         style="form"
     *     ),
     *     @OA\Response(response="200", description="Retorna uma lista de faturas do cartão de crédito"),
     *     @OA\Response(response="404", description="Conta não encontrada")
     * ),
     */
    public function invoices(int $id):Collection
    {
        return $this->creditCardServices->getInvoicesByCreditCard($id);
    }
}
