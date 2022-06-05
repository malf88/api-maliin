<?php

namespace App\Modules\Account\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Account\Impl\Business\BillBusinessInterface;
use App\Modules\Account\Services\BillPdfService;
use App\Modules\Account\ServicesLocal\BillServiceLocal;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function __construct(
        private BillBusinessInterface $billServices
    )
    {
    }

    /**
     * @OA\Get(
     *     tags={"Bills"},
     *     summary="Uma lista de contas a pagar/receber",
     *     description="Uma lista de contas a pagar/receber da conta informada",
     *     path="/bill/account/{accountId}",
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
     *     @OA\Response(response="200", description="Uma lista de contas a pagar/receber"),
     *     @OA\Response(response="404", description="Conta não encontrada")
     * )
     */

    public function index(int $accountId)
    {
        return $this->billServices->getBillsByAccount($accountId);
    }

    /**
     * @OA\Get(
     *     tags={"Bills"},
     *     summary="Uma lista de contas a pagar/receber",
     *     description="Uma lista de contas a pagar/receber da conta informada",
     *     path="/bill/account/{accountId}/between/{startDate}/{endDate}",
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
     *     @OA\Parameter(
     *         name="startDate",
     *         in="path",
     *         description="Data do início do intervalo",
     *         required=true,
     *         @OA\Schema(
     *           type="date",
     *         ),
     *         style="form"
     *     ),
     *     @OA\Parameter(
     *         name="endDate",
     *         in="path",
     *         description="Data de término do intervalo",
     *         required=true,
     *         @OA\Schema(
     *           type="date",
     *         ),
     *         style="form"
     *     ),
     *     @OA\Response(response="200", description="Uma lista de contas a pagar/receber"),
     *     @OA\Response(response="404", description="Conta não encontrada")
     * )
     */

    public function between(int $accountId, string $startDate, string $endDate)
    {
        return $this->billServices->getBillsByAccountBetween(
            accountId: $accountId,
            rangeDate:[$startDate,$endDate]);
    }
    /**
     * @OA\Get(
     *     tags={"Bills"},
     *     summary="Retorna a conta a pagar/receber com o {id}",
     *     description="Retorna a conta a pagar/receber com o {id} informado",
     *     path="/bill/{id}",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id da conta a pagar/receber",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *         ),
     *         style="form"
     *     ),
     *     @OA\Response(response="200", description="Retorna um objeto de conta a pagar/receber"),
     *     @OA\Response(response="404", description="Conta não encontrada")
     * ),
     */
    public function show(int $id)
    {
        return $this->billServices->getBillById($id);
    }
    /**
     * @OA\Post(
     *     tags={"Bills"},
     *     summary="Insere uma conta a pagar/receber",
     *     description="Insere uma conta a pagar/receber",
     *     path="/bill/account/{accountId}",
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
     *                     property="description",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="amount",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="date",
     *                     type="date"
     *                 ),
     *                 @OA\Property(
     *                     property="due_date",
     *                     type="date"
     *                 ),
     *                 @OA\Property(
     *                     property="pay_day",
     *                     type="date"
     *                 ),
     *                 @OA\Property(
     *                     property="credit_card_id",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="category_id",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="barcode",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="portion",
     *                     type="int"
     *                 ),
     *                 example={
     *                           "description": "Gasolina [1/10]",
     *                           "amount": "-29.10",
     *                           "date": "2019-03-13T00:00:00.000000Z",
     *                           "due_date": null,
     *                           "pay_day": "2020-02-04T00:00:00.000000Z",
     *                           "category_id": 1,
     *                           "portion": 1,
     *                           "account_id": 1,
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(response="201", description="Objeto inserido com sucesso"),
     *     @OA\Response(response="404", description="Conta não encontrada"),
     * ),
     *
     */
    public function insert(Request $request, $accountId)
    {
        return $this->billServices->insertBill($accountId,$request->all());
    }

    /**
     * @OA\Put(
     *     tags={"Bills"},
     *     summary="Altera uma conta a pagar/receber",
     *     description="Altera uma conta a pagar/receber",
     *     path="/bill/{id}",
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
     *                     property="description",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="amount",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="date",
     *                     type="date"
     *                 ),
     *                 @OA\Property(
     *                     property="due_date",
     *                     type="date"
     *                 ),
     *                 @OA\Property(
     *                     property="pay_day",
     *                     type="date"
     *                 ),
     *                 @OA\Property(
     *                     property="credit_card_id",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="category_id",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="barcode",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="portion",
     *                     type="int"
     *                 ),
     *                 @OA\Property(
     *                     property="update_childs",
     *                     type="bool"
     *                 ),
     *                 example={
     *                           "description": "Gasolina [1/10]",
     *                           "amount": "-29.10",
     *                           "date": "2019-03-13T00:00:00.000000Z",
     *                           "due_date": null,
     *                           "pay_day": "2020-02-04T00:00:00.000000Z",
     *                           "category_id": 1,
     *                           "portion": 1,
     *                           "account_id": 1,
     *                           "update_childs": false
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Objeto alterado com sucesso"),
     *     @OA\Response(response="404", description="Objeto não encontrado"),
     * ),
     *
     */
    public function update(Request $request, $id)
    {
        return $this->billServices->updateBill($id,$request->all());
    }

    /**
     * @OA\Delete(
     *     tags={"Bills"},
     *     summary="Exclui uma conta a pagar/receber com o {id}",
     *     description="Exclui uma conta a pagar/receber com o {id}",
     *     path="/bill/{id}",
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
    public function delete(int $id)
    {
        return $this->billServices->deleteBill($id);
    }
    /**
     * @OA\Put(
     *     tags={"Bills"},
     *     summary="Seta a conta a pagar/receber como paga/recebida",
     *     description="Seta a conta a pagar/receber como paga/recebida",
     *     path="/bill/{id}/pay",
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
    public function pay(int $id)
    {
        return $this->billServices->updateBill($id,['pay_day' => Carbon::today()->format('Y/m/d')]);
    }
    /**
     * @OA\Get(
     *     tags={"Bills"},
     *     summary="Uma lista de contas a pagar/receber",
     *     description="Uma lista de contas a pagar/receber da conta informada",
     *     path="/bill/account/{accountId}/periods",
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
     *     @OA\Response(response="200", description="Uma lista de períodos que possuem alguma conta."),
     *     @OA\Response(response="404", description="Conta não encontrada")
     * )
     */

    public function periods(int $accountId)
    {
        return $this->billServices->getPeriodWithBill($accountId);
    }

    /**
     * @OA\Get(
     *     tags={"Bills"},
     *     summary="Uma lista de contas a pagar/receber em formato PDF",
     *     description="Uma lista de contas a pagar/receber da conta informada",
     *     path="/bill/account/{accountId}/between/{startDate}/{endDate}/pdf",
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
     *     @OA\Parameter(
     *         name="startDate",
     *         in="path",
     *         description="Data do início do intervalo",
     *         required=true,
     *         @OA\Schema(
     *           type="date",
     *         ),
     *         style="form"
     *     ),
     *     @OA\Parameter(
     *         name="endDate",
     *         in="path",
     *         description="Data de término do intervalo",
     *         required=true,
     *         @OA\Schema(
     *           type="date",
     *         ),
     *         style="form"
     *     ),
     *     @OA\Response(response="200", description="Uma lista de contas a pagar/receber"),
     *     @OA\Response(response="404", description="Conta não encontrada")
     * )
     */

    public function generatePDFWithBillsBetween(int $accountId, string $startDate, string $endDate)
    {
        return response()
            ->streamDownload(function () use ($accountId, $startDate, $endDate) {
                $this->billServices->generatePdfByPeriod(
                    new BillPdfService(),
                    accountId: $accountId,
                    period: [$startDate, $endDate]);
            });
    }
}
