<?php

namespace App\Modules\Account\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Account\ServicesLocal\InvoiceServiceLocal;
use Illuminate\Http\Request;


class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceServiceLocal $invoiceServices)
    {
    }
    /**
     * @OA\Patch(
     *     tags={"Invoice"},
     *     summary="Uma lista de contas a pagar/receber",
     *     description="Uma lista de contas a pagar/receber da conta informada",
     *     path="/invoice/pay/{invoiceId}",
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Parameter(
     *         name="invoiceId",
     *         in="path",
     *         description="Id da fatura",
     *         required=true,
     *         @OA\Schema(
     *           type="integer",
     *         ),
     *         style="form"
     *     ),
     *     @OA\Response(response="200", description="Uma fatura"),
     *     @OA\Response(response="404", description="Conta não encontrada")
     * )
     */
    public function pay(Request $request, int $invoiceId)
    {
        return $this->invoiceServices->payInvoiceAndBill($invoiceId);
    }
}
