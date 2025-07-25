<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Modules\Account\DTO\BillDTO;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MigrateSisGesDom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sisgesdom:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrar as compras para a base do sisgesdom';
    private string $urlService = 'https://sisgesdom.malf88.xyz/api/';
    private string $projectId = '9eedf474-9f9c-433f-bdc5-dbae4163d4be';
    private array $categorias = [
        2 => '9eedf345-1510-453a-8b72-e2ad1f482225', // Investimentos
        3 => '9eedf32a-a83f-4b02-a1ae-33bb9df7fe31', // Mercado
        4 => '9eedf362-e4f6-4ad6-b5bf-d9b319047cb9', // Contas domésticas
        5 => '9eedf374-9d39-4a01-bd53-fa99d6609844', // Escola Maya
        6 => '9eedf407-f68a-4317-af68-51d31cf306b5', // Saúde
        7 => '9eedf416-8fa1-4423-98e9-cd10c71e45ca', // Salão de beleza
        8 => '9eedf352-6911-4232-8c6a-757a58cd85a3', // Rendimentos
        9 => '9eedf407-f68a-4317-af68-51d31cf306b5', // Saúde
        10 => '9eedf37f-020b-4e65-abfb-3c0343cd1b53', // Carro
        11 => '9eedf391-337c-4a6a-9601-3095107eac7f', // Transporte
        12 => '9eedf3c1-54c8-4b61-8f77-54f1e31149b3', // Banco
        13 => '9eedf3f8-ebc2-45b0-94ed-c7e4d7340ba0', // Servidor
        14 => '9eedf335-fad3-4623-87f8-1335cb4d8d21', // Lanches
        15 => '9eedf335-fad3-4623-87f8-1335cb4d8d21', // Besteirinhas (usando o mesmo de Lanches)
    ];
    private array $cartoes = [
        2 => '9eedf4a5-5b21-4a18-a8aa-4bab4589f8fa', // Nubank - Marco
        3 => '9eedf4c8-29e9-4c87-898b-03541b2beba1', // Nubank - Dani
    ];
    private string $familyId = '9eedf2fd-488b-49e8-aad1-312cca830725';
    protected string $directoryModules = 'Modules';
    private string $authorization = 'Bearer ';

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {
        //http://localhost:4000/api/finance/project/9eedf474-9f9c-433f-bdc5-dbae4163d4be/transaction/?familyId=9eedf2fd-488b-49e8-aad1-312cca830725

        $bills = Bill::where('account_id', 2)
            ->where('deleted_at', null)
            ->where(function ($query){
                $query->whereBetween('due_date', ['2025-07-01', '2026-12-31'])
                    ->orWhereBetween('date', ['2025-07-01', '2026-12-31']);
            })
            ->get()
            ->map(function ($bill) {
                return new BillDTO($bill->toArray());
            });
//        $bills = $billBusiness->getBillsByAccountBetween(2, ['2025-08-01','2026-12-31']);

        $bills->each(function (BillDTO $bill) {
            $payLoad = [
                "projectId" => $this->projectId,
                "amount" => $bill->amount,
                "type" => $bill->amount > 0 ? 'CREDIT' : 'DEBIT',
                "date" => $bill->date,
                "due" => $bill->due_date,
                "totalPortion" => $bill->bill_parent ? $bill->bill_parent->count(): 1,
                "category_id" => $this->categorias[$bill->category_id], // Contas domésticas
                "credit_card_id" => $bill->credit_card_id ? $this->cartoes[$bill->credit_card_id] : null,
                "description" => $bill->description,
                "payment_date" => $bill->pay_day,
                "observation" => "Conta migrada do Maliin: "."#". $bill->id . $bill->barcode,
            ];
            dump($payLoad);

            $clienGuzzle = new Client(['base_uri' => $this->urlService]);
            $clienGuzzle->post('finance/project/'.$this->projectId.'/transaction/?familyId='.$this->familyId, [
                'json' => $payLoad,
                'headers' => [
                    'Authorization' => 'Bearer '.$this->authorization
                ]
            ]);

            $this->info("Migrada a compra: #".$bill->id);
        });



    }




}
