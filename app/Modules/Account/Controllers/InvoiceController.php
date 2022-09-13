<?php

namespace App\Modules\Account\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Account\Impl\Business\InvoiceBusinessInterface;
use App\Modules\Account\Services\InvoicePdfService;
use Illuminate\Http\Request;


class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceBusinessInterface $invoiceServices)
    {
    }
    /**
     * @OA\Get(
     *     tags={"Invoices"},
     *     summary="Retorna uma única fatura indicada pelo invoiceId",
     *     description="Uma lista de contas a pagar/receber da conta informada",
     *     path="/invoice/{invoiceId}",
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
     *     @OA\Response(response="200", description="Uma lista de contas a pagar/receber"),
     *     @OA\Response(response="404", description="Conta não encontrada")
     * )
     */

    public function index(int $invoiceId)
    {
        return response($this->invoiceServices->getInvoiceWithBills($invoiceId)->toArray(), 200);
    }

    /**
     * @OA\Get(
     *     tags={"Invoices"},
     *     summary="Retorna uma única fatura indicada pelo invoiceId em pdf",
     *     description="Uma lista de contas a pagar/receber da conta informada em pdf",
     *     path="/invoice/{invoiceId}/pdf",
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
     *     @OA\Response(response="200", description="Uma lista de contas a pagar/receber"),
     *     @OA\Response(response="404", description="Conta não encontrada")
     * )
     */

    public function indexPdf(int $invoiceId)
    {
        return response()
            ->streamDownload(function () use ($invoiceId) {
                $this->invoiceServices->getInvoiceWithBillsInPDF(new InvoicePdfService(),$invoiceId);
            });
    }
    /**
     * @OA\Patch(
     *     tags={"Invoices"},
     *     summary="Marca uma fatura como paga",
     *     description="Marca uma fatura de cartão de crédito como paga.",
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
        return response($this->invoiceServices->payInvoice($invoiceId)->toArray(), 200);
    }
}
