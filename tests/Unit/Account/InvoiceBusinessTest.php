<?php

namespace Tests\Unit\Account;

use App\Models\Invoice;
use App\Modules\Account\Business\AccountBusiness;
use App\Modules\Account\Business\CreditCardBusiness;
use App\Modules\Account\Business\InvoiceBusiness;
use App\Modules\Account\Repository\CreditCardRepository;
use App\Modules\Account\Repository\InvoiceRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ItemNotFoundException;
use Tests\TestCase;
use Tests\Unit\Account\Factory\AccountFactory;

class InvoiceBusinessTest extends TestCase
{
    private AccountFactory $factory;
    private InvoiceRepository $invoiceRepository;
    private CreditCardRepository $creditCardRepository;
    private AccountBusiness $accountBusiness;
    private CreditCardBusiness $creditCardBusiness;
    private int $creditCardId = 1;
    public function setUp(): void
    {
        parent::setUp();
        $this->creditCardId = 1;
        $this->invoiceRepository = $this->createMock(InvoiceRepository::class);
        $this->creditCardRepository = $this->createMock(CreditCardRepository::class);
        $this->accountBusiness = $this->createMock(AccountBusiness::class);

        $this->invoiceRepository = $this->createMock(InvoiceRepository::class);
        $this->factory = new AccountFactory();
        $user = $this->factory->factoryUser(1);
        Auth::shouldReceive('user')->andReturn($user);
    }
    private function configureMockRepository(string $date){

        $creditCards = $this->factory->factoryCreditCards();
        $this->creditCardRepository
            ->method('getCreditCardById')
            ->willReturn($creditCards->find($this->creditCardId));

        $this->invoiceRepository->method('getInvoiceByCreditCardAndDate')
            ->willReturn($creditCards
                ->get(0)
                ->invoices()
                ->where('start_date','<=',$date)
                ->where('end_date','>=',$date)
                ->first());
    }

    private function configureCreditCardBusiness(){
        $invoiceBusiness = $this->createMock(InvoiceBusiness::class);
        $this->creditCardBusiness = new CreditCardBusiness($this->creditCardRepository,$this->accountBusiness,$invoiceBusiness);
    }
    /**
     * @test
     */
    public function deveRetornarInvoiceDeUmCartaoPorPeriodo()
    {
        $this->creditCardId = 1;
        $date = '2021-08-10';
        $this->configureMockRepository($date);
        $creditCards = $this->factory->factoryCreditCards();
        $this->configureCreditCardBusiness();
        $invoiceBusiness = new InvoiceBusiness($this->invoiceRepository,$this->creditCardBusiness);

        $invoice = $invoiceBusiness->getInvoiceByCreditCardAndDate($this->creditCardId,Carbon::make($date));

        $this->assertEquals('2021-08-01',$invoice->start_date->format('Y-m-d'));
        $this->assertEquals('2021-08-30',$invoice->end_date->format('Y-m-d'));
        $this->assertEquals('2021-09-15',$invoice->due_date->format('Y-m-d'));
        $this->assertEquals(8,$invoice->month_reference);
    }

    /**
     * @test
     */
    public function deveRetornarExcecaoAoBuscaInvoiceDeUmCartaoPorData()
    {
        $this->creditCardId = 5;
        $date = '2021-08-10';
        $this->configureMockRepository($date);
        $creditCards = $this->factory->factoryCreditCards();

        $this->configureCreditCardBusiness();

        $invoiceBusiness = new InvoiceBusiness($this->invoiceRepository,$this->creditCardBusiness);
        $this->expectException(ItemNotFoundException::class);
        $invoice = $invoiceBusiness->getInvoiceByCreditCardAndDate($this->creditCardId,Carbon::make($date));

    }

    /**
     * @test
     */
    public function deveRetornarInvoiceParaOCartaoDeCreditoParaData()
    {
        $this->creditCardId = 1;
        $date = '2021-08-16';
        $this->configureMockRepository($date);
        $this->configureCreditCardBusiness();
        $invoiceBusiness = new InvoiceBusiness($this->invoiceRepository,$this->creditCardBusiness);

        $invoice = $invoiceBusiness->createInvoiceForCreditCardByDate($this->creditCardId,Carbon::make($date));
        $this->assertEquals('2021-08-01',$invoice->start_date->format('Y-m-d'));
        $this->assertEquals('2021-08-30',$invoice->end_date->format('Y-m-d'));
        $this->assertEquals('2021-09-15',$invoice->due_date->format('Y-m-d'));
        $this->assertEquals(8,$invoice->month_reference);
    }
    /**
     * @test
     */
    public function deveCriarInvoiceParaOCartaoDeCreditoParaData()
    {
        $this->creditCardId = 1;
        $date = '2021-09-15';
        $invoiceData =  [
            'start_date'        =>  '2021-08-31',
            'end_date'          =>  '2021-09-30',
            'due_date'          =>  '2021-10-07',
            'month_reference'   =>  10,
            'credit_card_id'    => $this->creditCardId
        ];

        $invoice = new Invoice();
        $invoice->fill($invoiceData);
        $this->configureMockRepository($date);

        $this->invoiceRepository
            ->method('insertInvoice')
            ->willReturn($invoice);
        $this->configureCreditCardBusiness();
        $invoiceBusiness = new InvoiceBusiness($this->invoiceRepository,$this->creditCardBusiness);

        $invoice = $invoiceBusiness->createInvoiceForCreditCardByDate($this->creditCardId,Carbon::make($date));
        $this->assertEquals('2021-08-31',$invoice->start_date->format('Y-m-d'));
        $this->assertEquals('2021-09-30',$invoice->end_date->format('Y-m-d'));
        $this->assertEquals('2021-10-07',$invoice->due_date->format('Y-m-d'));
        $this->assertEquals(10,$invoice->month_reference);
    }
}
