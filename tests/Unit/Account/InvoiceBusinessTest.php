<?php

namespace Tests\Unit\Account;

use App\Models\Bill;
use App\Models\Category;
use App\Models\Invoice;
use App\Modules\Account\Business\AccountBusiness;
use App\Modules\Account\Business\CreditCardBusiness;
use App\Modules\Account\Business\InvoiceBusiness;
use App\Modules\Account\DTO\InvoiceDTO;
use App\Modules\Account\Repository\BillRepository;
use App\Modules\Account\Repository\CreditCardRepository;
use App\Modules\Account\Repository\InvoiceRepository;
use App\Modules\Account\Services\BillStandarizedService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ItemNotFoundException;
use Mockery;
use Tests\TestCase;
use Tests\Unit\Account\Factory\DataFactory;

class InvoiceBusinessTest extends TestCase
{
    private DataFactory $factory;
    private Mockery\MockInterface $invoiceRepository;
    private CreditCardRepository $creditCardRepository;
    private BillStandarizedService $billStandarizedService;

    private int $creditCardId = 1;
    public function setUp(): void
    {
        parent::setUp();
        $this->creditCardId = 1;
        $this->invoiceRepository = Mockery::mock(InvoiceRepository::class);
        $this->creditCardRepository = $this->createMock(CreditCardRepository::class);
        $this->accountBusiness = $this->createMock(AccountBusiness::class);
        $this->billStandarizedService = new BillStandarizedService($this->createMock(BillRepository::class));
        $this->factory = new DataFactory();
        $user = $this->factory->factoryUser(1);
        Auth::shouldReceive('user')->andReturn($user);
    }
    private function configureMockRepository(string $date){

        $creditCards = $this->factory->factoryCreditCards();
        $this->creditCardRepository
            ->method('getCreditCardById')
            ->willReturn($creditCards->find($this->creditCardId));
        $invoice = $creditCards
            ->get(0)
            ->invoices()
            ->where('start_date','<=',$date)
            ->where('end_date','>=',$date)
            ->first();
        $this->invoiceRepository->shouldReceive('getInvoiceByCreditCardAndDate')
            ->andReturn($invoice != null ? new InvoiceDTO($invoice->toArray()) : null);

    }

    private function configureCreditCardBusiness(){
        $invoiceBusiness = $this->createMock(InvoiceBusiness::class);
        $this->creditCardBusiness = new CreditCardBusiness($this->creditCardRepository,$invoiceBusiness);
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
        $invoiceBusiness = new InvoiceBusiness($this->invoiceRepository, $this->billStandarizedService);

        $invoice = $invoiceBusiness->getInvoiceByCreditCardAndDate($this->creditCardId,Carbon::make($date));

        $this->assertEquals('2021-08-01',Carbon::make($invoice->start_date)->format('Y-m-d'));
        $this->assertEquals('2021-08-30',Carbon::make($invoice->end_date)->format('Y-m-d'));
        $this->assertEquals('2021-09-15',Carbon::make($invoice->due_date)->format('Y-m-d'));
        $this->assertEquals(8,$invoice->month_reference);
        $this->assertEquals($this->creditCardId, $invoice->credit_card_id);
    }

    /**
     * @test
     */
    public function deveRetornarInvoiceParaOCartaoDeCreditoParaData()
    {
        $this->creditCardId = 1;
        $date = '2021-08-15';
        $this->configureMockRepository($date);
        $this->configureCreditCardBusiness();
        $creditCards = $this->factory->factoryCreditCards();
        $invoiceBusiness = new InvoiceBusiness($this->invoiceRepository, $this->billStandarizedService);

        $invoice = $invoiceBusiness->createInvoiceForCreditCardByDate($creditCards->get(0),Carbon::make($date));

        $this->assertEquals('2021-08-01',Carbon::make($invoice->start_date)->format('Y-m-d'));
        $this->assertEquals('2021-08-30',Carbon::make($invoice->end_date)->format('Y-m-d'));
        $this->assertEquals('2021-09-15',Carbon::make($invoice->due_date)->format('Y-m-d'));
        $this->assertEquals(8,$invoice->month_reference);
        $this->assertEquals($this->creditCardId, $invoice->credit_card_id);
    }
    /**
     * @test
     */
    public function deveCriarInvoiceParaOCartaoDeCreditoParaData()
    {
        $this->creditCardId = 1;
        $date = '2021-09-15';
        $creditCards = $this->factory->factoryCreditCards();

        $this->configureMockRepository($date);
        $this->invoiceRepository->shouldReceive('insertInvoice')
            ->once()
            ->andReturnArg(0);
        $this->configureCreditCardBusiness();
        $invoiceBusiness = new InvoiceBusiness($this->invoiceRepository, $this->billStandarizedService);

        $invoice = $invoiceBusiness->createInvoiceForCreditCardByDate($creditCards->get(0),Carbon::make($date));

        $this->assertEquals('2021-09-01',$invoice->start_date->format('Y-m-d'));
        $this->assertEquals('2021-09-30',$invoice->end_date->format('Y-m-d'));
        $this->assertEquals('2021-10-03',$invoice->due_date->format('Y-m-d'));
        $this->assertEquals(10,$invoice->month_reference);
        $this->assertEquals($this->creditCardId, $invoice->credit_card_id);
    }

    /**
     * @test
     */
    public function deveRetornarFaturaComListaDeContasAPagarOuReceber()
    {
        $invoice = Mockery::mock('App\Models\Invoice[makeVisible]');
        $invoice->shouldReceive('makeVisible')
            ->once()
            ->andReturnSelf();
        $bill = Mockery::mock('App\Models\Bill[load,getBillParentAttribute,getCategoryAttribute]');
        $bill->shouldReceive('getBillParentAttribute')
            ->andReturn(Collection::empty());
        $bill->shouldReceive('getCategoryAttribute')
            ->andReturn(new Category([
                'id' => 1,
                'name' => 'Alimentação'
            ]));
        $bill->description = "Mercado";
        $bill->id = 1;
        $bill->date = Carbon::now();
        $bill->bill_parent_id = null;
        $bill->pay_day = null;
        $bill->amount = 3.50;
        $bill->portion = 3;
        $bill->due_date = Carbon::make('2023-01-15');
        $bill->account_id = 1;
        $bill->credit_card_id = null;
        $bill->category_id = 1;
        $invoice->bills = Collection::make([$bill, $bill, $bill]);
        $invoiceId = 1;
        $this->invoiceRepository
            ->shouldReceive('getInvoiceWithBills')
            ->andReturn($invoice);
        $this->configureMockRepository('2021-09-01');
        $invoiceBusiness = new InvoiceBusiness($this->invoiceRepository, $this->billStandarizedService);
        $invoice = $invoiceBusiness->getInvoiceWithBills($invoiceId);

        $this->assertCount(3,$invoice->bills);
    }
    /**
     * @test
     */
    public function deveRetornarFaturaComListaDeContasAPagarOuReceberNormalizado()
    {

        $invoiceId = 1;
        $this->invoiceRepository
            ->shouldReceive('getInvoiceWithBills')
            ->andReturn($this->factory->factoryInvoiceList()->get(0));
        $this->configureMockRepository('2021-09-01');
        $invoiceBusiness = new InvoiceBusiness($this->invoiceRepository, $this->billStandarizedService);
        $invoice = $invoiceBusiness->getInvoiceWithBillsNormalized($invoiceId);

        $this->assertCount(3,$invoice->bills);
    }
    /**
     * @test
     */
    public function devePagarFaturaCartaoDeCredito(){
        $invoice = Mockery::mock('App\Models\Invoice[save,with]');
        $invoice->shouldReceive('save')
            ->once()
            ->andReturnSelf();


        $bill = Mockery::mock('App\Models\Bill[refresh,load,save,getBillParentAttribute,getCategoryAttribute]');
        $bill->shouldReceive('getBillParentAttribute')
            ->andReturn(Collection::empty());
        $bill->shouldReceive('save')
            ->times(3);
        $bill->shouldReceive('refresh')
            ->times(3);
        $bill->shouldReceive('getCategoryAttribute')
            ->andReturn(new Category([
                'id' => 1,
                'name' => 'Alimentação'
            ]));
        $bill->description = "Mercado";
        $bill->id = 1;
        $bill->date = Carbon::now();
        $bill->bill_parent_id = null;
        $bill->pay_day = null;
        $bill->amount = 3.50;
        $bill->portion = 3;
        $bill->due_date = Carbon::make('2023-01-15');
        $bill->account_id = 1;
        $bill->credit_card_id = null;
        $bill->category_id = 1;
        $invoice->bills = Collection::make([$bill, $bill, $bill]);
        $invoiceId = 1;
        $this->invoiceRepository
            ->shouldReceive('getInvoiceWithBills')
            ->andReturn($invoice);
        $this->invoiceRepository
            ->shouldReceive('getInvoice')
            ->andReturn($invoice);

        $invoiceBusiness = new InvoiceBusiness($this->invoiceRepository, $this->billStandarizedService);
        $invoice = $invoiceBusiness->payInvoice($invoiceId);
        $invoice
            ->bills
            ->each(function($item,$key){
                $this->assertEquals(Carbon::now()->format('Y-m-d'),$item->pay_day->format('Y-m-d'));
            });

    }

}
