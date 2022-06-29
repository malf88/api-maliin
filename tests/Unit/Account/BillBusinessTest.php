<?php

namespace Tests\Unit\Account;


use App\Models\Bill;
use App\Models\Category;
use App\Modules\Account\Business\AccountBusiness;
use App\Modules\Account\Business\BillBusiness;
use App\Modules\Account\Business\CreditCardBusiness;
use App\Modules\Account\Repository\AccountRepository;
use App\Modules\Account\Repository\BillRepository;
use App\Modules\Account\Services\BillStandarizedService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;
use Tests\Unit\Account\Factory\DataFactory;

class BillBusinessTest extends TestCase
{
    private DataFactory $accountFactory;
    private BillStandarizedService $billStandarizedService;

    public function setUp(): void
    {
        parent::setUp();
        $this->accountFactory = new DataFactory();
        $user = $this->accountFactory->factoryUser(1);
        $this->billStandarizedService = $this->createMock(BillStandarizedService::class);

        $list = Collection::make([
            $this->accountFactory->factoryBill(2,100,1,portion:2),
            $this->accountFactory->factoryBill(3,200,1,portion:3),
            $this->accountFactory->factoryBill(4,-100,1,portion:4)
        ]);
        $this->billStandarizedService->method('normalizeListBills')
            ->willReturn(
                new LengthAwarePaginator($list, $list->count(),5)
            );
    }

    public function getMockRepository(){
        $mock = $this->createMock(BillRepository::class);
        $collectionBill = Collection::make([
            $this->accountFactory->factoryBill(2,300,1,portion:2),
            $this->accountFactory->factoryBill(3,200,1,portion:3),
            $this->accountFactory->factoryBill(4,-100,1,portion:4)
        ]);
        $mock->method('getChildBill')
            ->willReturn($collectionBill);
        $mock->method('getTotalEstimated')
            ->willReturn((float) $collectionBill->sum('amount'));
        $mock->method('getTotalPaid')
            ->willReturn((float) $collectionBill->whereNotNull('pay_day')->sum('amount'));
        $mock->method('getCategory')
            ->willReturn(new Category(['name' => 'Transporte']));
        $mock->method('getTotalCashIn')
            ->willReturn((float) $collectionBill->where('amount','>=',0)->sum('amount'));
        $mock->method('getTotalCashOut')
            ->willReturn((float) $collectionBill->where('amount','<',0)->sum('amount'));

        return $mock;

    }
    public function getMockAccountBusiness(){
        $accountRepository = $this->createMock(AccountRepository::class);
        return new AccountBusiness($accountRepository);
    }

    public function getMockCreditCardBusiness(){
        $creditCardBusiness = $this->createMock(CreditCardBusiness::class);
        $creditCardBusiness
            ->method('getCreditCardById')
            ->willReturn($this->accountFactory->factoryCreditCards()->get(0));
        $creditCardBusiness
            ->method('generateInvoiceByBill');
        return $creditCardBusiness;
    }
    public function factoryBillData():array
    {
        return [
            'description'   => 'Compra no supermercado',
            'amount'        => 160.00,
            'date'          => Carbon::today()->format('Y-m-d'),
            'due_date'      => Carbon::today()->addDays(30)->format('Y-m-d'),
            'pay_day'       => null,
            'barcode'       => '',
            'category_id'   => 1,
            'account_id'    => 1,
            'portion'       => 1,
            'credit_card_id'=> null
        ];
    }
    /**
     * @test
     */
    public function deveListarContasAPagarDeUmaConta(){
        $this->accountFactory->configureUserSession();
        $accountId = 1;
        $accounts = $this->accountFactory->factoryAccount();
        $billRepository = $this->getMockRepository();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billRepository
            ->method('getBillsByAccount')
            ->with($accountId)
            ->willReturn($accounts->get(0)->bills());

        $billBusiness = new BillBusiness($billRepository,$creditCardBusiness, $this->billStandarizedService);
        $bills = $billBusiness->getBillsByAccount($accountId);

        $this->assertIsIterable($bills);
        $this->assertCount(3,$bills);
        $this->assertEquals(200.00,$bills->sum('amount'));

    }

    /**
     * @test
     */
    public function deveListarContasAPagarDeUmaContaComData(){
        $this->accountFactory->configureUserSession();
        $accountId = 1;
        $accounts = $this->accountFactory->factoryAccount();
        $billRepository = $this->getMockRepository();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $interval = ['2021-01-01','2021-01-31'];
        $billRepository
            ->method('getBillsByAccountWithRangeDate')
            ->with($accountId)
            ->willReturn($accounts->get(0)->bills()->whereBetween('due_date', $interval));

        $billBusiness = new BillBusiness($billRepository,$creditCardBusiness, $this->billStandarizedService);
        $bills = $billBusiness->getBillsByAccountBetween($accountId, $interval);
        $this->assertIsIterable($bills);
        $this->assertCount(3,$bills['bills']);
        $this->assertEquals(200.00,$bills['bills']->sum('amount'));
        $this->assertEquals(500,$bills['total']['total_cash_in']);
        $this->assertEquals(-100,$bills['total']['total_cash_out']);
        $this->assertEquals(400,$bills['total']['total_estimated']);
        $this->assertEquals(0,$bills['total']['total_paid']);

    }

    /**
     * @test
     */
    public function deveDispararExcecaoAoListarContasAPagarDeUmaConta(){
        $this->accountFactory->configureUserSession(true);
        $accountId = 2;
        $billRepository = $this->getMockRepository();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billBusiness = new BillBusiness($billRepository,$creditCardBusiness, $this->billStandarizedService);
        $this->expectException(NotFoundHttpException::class);
        $bills = $billBusiness->getBillsByAccount($accountId);

    }

    /**
     * @test
     */
    public function deveListarContasAPagarDeUmaContaPaginada(){
        $this->accountFactory->configureUserSession();
        $accountId = 1;
        $accounts = $this->accountFactory->factoryAccount();
        $billRepository = $this->getMockRepository();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billsPaginados = new LengthAwarePaginator($accounts->get(0)->bills(),3,15);
        $billRepository
            ->method('getBillsByAccount')
            ->with($accountId,true)
            ->willReturn($billsPaginados);

        $billBusiness = new BillBusiness($billRepository,$creditCardBusiness, $this->billStandarizedService);
        $bills = $billBusiness->getBillsByAccountPaginate($accountId);

        $this->assertCount(3,$bills->items());
        $this->assertEquals(1,$bills->currentPage());
        $this->assertEquals(3,$bills->total());
        $this->assertEquals(200.00,$bills->sum('amount'));

    }

    /**
     * @test
     */
    public function deveDispararExcecaoAoListarContasAPagarDeUmaContaPaginada(){
        $this->accountFactory->configureUserSession(true);
        $accountId = 2;
        $billRepository = $this->getMockRepository();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billBusiness = new BillBusiness($billRepository,$creditCardBusiness, $this->billStandarizedService);
        $this->expectException(NotFoundHttpException::class);
        $bills = $billBusiness->getBillsByAccountPaginate($accountId);

    }
    /**
     * @test
     */
    public function deveSalvarContasAPagarEmUmaConta(){
        $this->accountFactory->configureUserSession();
        $accountId = 1;
        $billData = $this->factoryBillData();
        $bill = new Bill();
        $bill->fill($billData);
        $bill->id = 1;

        $billRepository = $this->getMockRepository();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billRepository
            ->method('saveBill')
            ->with($accountId,$billData)
            ->willReturn($bill);
        $billBusiness = new BillBusiness($billRepository,$creditCardBusiness, $this->billStandarizedService);
        $bill = $billBusiness->insertBill($accountId,$billData);
        $this->assertEquals('Compra no supermercado',$bill->description);
        $this->assertEquals(160.00,$bill->amount);
        $this->assertEquals(1,$bill->account_id);

    }

    /**
     * @test
     */
    public function deveSalvarContasAPagarEmUmaContaComParcelas(){
        $this->accountFactory->configureUserSession();
        $accountId = 1;
        $billData = $this->factoryBillData();
        $billData['portion'] = 3;

        $bill = new Bill();
        $bill->fill($billData);
        $bill->id = 1;
        $billRepository = $this->getMockRepository();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billRepository
            ->method('saveBill')
            ->willReturn($bill);
        $billBusiness = new BillBusiness($billRepository,$creditCardBusiness, $this->billStandarizedService);
        $bills = $billBusiness->insertBill($accountId,$billData);
        $this->assertIsIterable($bills);
        $this->assertCount(3,$bills);
        $this->assertEquals(480.00,$bills->sum('amount'));

    }
    /**
     * @test
     */
    public function deveDispararExcecaoAoSalvarContasAPagar(){
        $this->accountFactory->configureUserSession(true);
        $accountId = 2;

        $billData = $this->factoryBillData();
        $billRepository = $this->getMockRepository();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billBusiness = new BillBusiness($billRepository,$creditCardBusiness, $this->billStandarizedService);
        $this->expectException(NotFoundHttpException::class);
        $bills = $billBusiness->insertBill($accountId,$billData);
    }

    /**
     * @test
     */
    public function deveRetornarUmaContaAPagar(){
        $this->accountFactory->configureUserSession();
        $billId = 1;
        $accounts = $this->accountFactory->factoryAccount();
        $billRepository = $this->getMockRepository();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $user = $this->accountFactory->factoryUser(1);
        $account = $accounts->get(0);
        $account->user = $user;
        $bill = $this->createPartialMock(Bill::class,['account']);
        $bill->method('account')
            ->willReturn($account);

        $bill->account = $account;
        $bill->fill($this->factoryBillData());

        $this->billStandarizedService->method('normalizeBill')
            ->willReturn($bill);

        $billBusiness = new BillBusiness($billRepository,$creditCardBusiness, $this->billStandarizedService);
        $bill = $billBusiness->getBillById($billId);

        $this->assertEquals(160.00,$bill->amount);

    }
    /**
     * @test
     */
    public function deveDispararExcecaoAoRetornarUmaContaAPagar(){
        $this->accountFactory->configureUserSession(true);
        $billId = 5;
        $accounts = $this->accountFactory->factoryAccount();
        $billRepository = $this->getMockRepository();
        $creditCardBusiness = $this->getMockCreditCardBusiness();

        $account = $accounts->get(0);
        $account->id = 10;
        $bill = $this->createPartialMock(Bill::class,['account','load']);
        $bill->method('account')
            ->willReturn($account);
        $bill->method('load');

        $bill->account = $account;
        $bill->fill($this->factoryBillData());
        $bill->account_id = 15;
        $billRepository
            ->method('getBillById')
            ->with($billId)
            ->willReturn($bill);
        $this->billStandarizedService->method('normalizeBill')
            ->willReturn($bill);

        $billBusiness = new BillBusiness($billRepository,$creditCardBusiness, $this->billStandarizedService);
        $this->expectException(NotFoundHttpException::class);
        $bill = $billBusiness->getBillById($billId);
    }

    /**
     * @test
     */
    public function deveAlterarUmaContaAPagar(){
        $this->accountFactory->configureUserSession();
        $billId = 1;
        $accounts = $this->accountFactory->factoryAccount();
        $billData = $this->factoryBillData();

        $user = $this->accountFactory->factoryUser(1);
        $account = $accounts->get(0);
        $account->user = $user;
        $bill = $this->createPartialMock(Bill::class,['account','load']);
        $bill->method('load');
        $bill->method('account')
            ->willReturn($account);

        $bill->account = $account;
        $bill->fill($this->factoryBillData());

        $billRepository = $this->getMockRepository();
        $creditCardBusiness = $this->getMockCreditCardBusiness();

        $billRepository
            ->method('updateBill')
            ->willReturn($bill);
        $this->billStandarizedService->method('normalizeBill')
            ->willReturn($bill);
        $billBusiness = new BillBusiness($billRepository,$creditCardBusiness, $this->billStandarizedService);
        $bill = $billBusiness->updateBill($billId,$billData);

        $this->assertEquals(160.00,$bill->amount);

    }
    /**
     * @test
     */
    public function deveAlterarUmaContaAPagarESeusIrmaos(){
        $this->accountFactory->configureUserSession();
        $billId = 2;
        $accounts = $this->accountFactory->factoryAccount();
        $billData = $this->factoryBillData();
        $billData['update_childs'] = true;

        $user = $this->accountFactory->factoryUser(1);
        $account = $accounts->get(0);
        $account->user = $user;
        $bill = $this->createPartialMock(Bill::class,['account']);
        $bill->method('account')
            ->willReturn($account);
        $bill->bill_parent_id = 1;
        $bill->id = 1;
        $bill->account = $account;
        $bill->bill_parent =
            Collection::make([
                $this->accountFactory->factoryBill(2,300,1,portion:2),
                $this->accountFactory->factoryBill(3,200,1,portion:3),
                $this->accountFactory->factoryBill(4,-100,1,portion:4)
            ]);

        $bill->fill($this->factoryBillData());

        $billRepository = $this->getMockRepository();
        $creditCardBusiness = $this->getMockCreditCardBusiness();

        $billRepository
            ->method('updateBill')
            ->willReturn($bill);
        $billRepository
            ->method('getBillById')
            ->willReturn($bill);
        $this->billStandarizedService->method('normalizeBill')
            ->willReturn($bill);


        $billBusiness = new BillBusiness($billRepository,$creditCardBusiness, $this->billStandarizedService);
        $bills = $billBusiness->updateBill($billId,$billData);

        $this->assertCount(3,$bills->bill_parent);
        $this->assertEquals(1,$bills->id);

    }

    /**
     * @test
     */
    public function deveAlterarUmaContaAPagarESeusFilhos(){
        $this->accountFactory->configureUserSession();
        $billId = 2;
        $accounts = $this->accountFactory->factoryAccount();
        $billData = $this->factoryBillData();
        $billData['update_childs'] = true;

        $user = $this->accountFactory->factoryUser(1);
        $account = $accounts->get(0);
        $account->user = $user;
        $bill = $this->createPartialMock(Bill::class,['account','load']);
        $bill->method('account')
            ->willReturn($account);
        $bill->bill_parent = Collection::make([
           $this->accountFactory->factoryBill(2,-300.00,1,portion:2),
           $this->accountFactory->factoryBill(3,-300.00,1,portion:3),
           $this->accountFactory->factoryBill(4,-300.00,1,portion:4),
        ]);
        $bill->id = 1;
        $bill->account = $account;
        $bill->fill($this->factoryBillData());

        $billRepository = $this->getMockRepository();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $this->billStandarizedService->method('normalizeBill')
            ->willReturn($bill);

        $billRepository
            ->method('updateBill')
            ->willReturn($bill);
        $billBusiness = new BillBusiness($billRepository,$creditCardBusiness, $this->billStandarizedService);
        $bills = $billBusiness->updateBill($billId,$billData);

        $this->assertCount(3,$bills->bill_parent);
        $this->assertEquals(1,$bills->id);

    }

    /**
     * @test
     */
    public function deveDispararExcecaoAoAlterarUmaContaAPagarESeusFilhosQueNaoEDono(){
        $this->accountFactory->configureUserSession(true);
        $billId = 2;
        $accounts = $this->accountFactory->factoryAccount();
        $billData = $this->factoryBillData();
        $billData['update_childs'] = true;

        $user = $this->accountFactory->factoryUser(1);
        $account = $accounts->get(0);
        $account->user = $user;
        $bill = $this->createPartialMock(Bill::class,['account','load']);
        $bill->method('account')
            ->willReturn($account);

        $this->billStandarizedService->method('normalizeBill')
            ->willReturn($bill);

        $bill->id = 1;
        $bill->account = $account;
        $bill->fill($this->factoryBillData());

        $billRepository = $this->getMockRepository();
        $creditCardBusiness = $this->getMockCreditCardBusiness();

        $this->expectException(NotFoundHttpException::class);
        $billBusiness = new BillBusiness($billRepository,$creditCardBusiness, $this->billStandarizedService);
        $bills = $billBusiness->updateBill($billId,$billData);

    }
    /**
     * @test
     */
    public function deveDispararExcecaoAoAlterarUmaContaAPagarESeusFilhos(){
        $this->accountFactory->configureUserSession(true);
        $billId = 1;
        $accounts = $this->accountFactory->factoryAccount();
        $billData = $this->factoryBillData();
        $billData['update_childs'] = true;

        $user = $this->accountFactory->factoryUser(2);
        $account = $accounts->get(0);
        $account->user = $user;
        $bill = $this->createPartialMock(Bill::class,['account','load']);
        $bill->method('load');
        $bill->method('account')
            ->willReturn($account);

        $bill->account = $account;

        $billRepository = $this->getMockRepository();
        $this->billStandarizedService->method('normalizeBill')
            ->willReturn($bill);

        $bill->fill($billData);
        $bill->account_id = 15;
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billBusiness = new BillBusiness($billRepository,$creditCardBusiness, $this->billStandarizedService);
        $this->expectException(NotFoundHttpException::class);
        $bills = $billBusiness->updateBill($billId,$billData);
    }

    /**
     * @test
     */
    public function deveDeletarContaAPagar(){
        $this->accountFactory->configureUserSession();
        $billId = 1;
        $accounts = $this->accountFactory->factoryAccount();
        $user = $this->accountFactory->factoryUser(1);
        $account = $accounts->get(0);
        $account->user = $user;

        $bill = $this->createPartialMock(Bill::class,['account','load']);
        $bill->method('load');
        $bill->method('account')
            ->willReturn($account);
        $bill->account = $account;
        $bill->fill($this->factoryBillData());

        $billRepository = $this->getMockRepository();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $this->billStandarizedService->method('normalizeBill')
            ->willReturn($bill);
        $billRepository
            ->method('deleteBill')
            ->willReturn(true);

        $billBusiness = new BillBusiness($billRepository,$creditCardBusiness, $this->billStandarizedService);
        $bill = $billBusiness->deleteBill($billId);

        $this->assertEquals(true,$bill);
    }

    /**
     * @test
     */
    public function deveDispararExcecaoDeletarContaAPagarPorNaoSerDonoDoLancamento(){
        $this->accountFactory->configureUserSession(true);
        $billId = 1;
        $accounts = $this->accountFactory->factoryAccount();
        $user = $this->accountFactory->factoryUser(1);
        $account = $accounts->get(0);
        $account->user = $user;

        $bill = $this->createPartialMock(Bill::class,['account','load']);
        $bill->method('load');
        $bill->method('account')
            ->willReturn($account);
        $bill->account = $account;
        $bill->fill($this->factoryBillData());

        $billRepository = $this->getMockRepository();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $this->billStandarizedService->method('normalizeBill')
            ->willReturn($bill);
        $this->expectException(NotFoundHttpException::class);
        $billBusiness = new BillBusiness($billRepository,$creditCardBusiness, $this->billStandarizedService);
        $bill = $billBusiness->deleteBill($billId);

        $this->assertEquals(true,$bill);
    }

    /**
     * @test
     */
    public function deveDispararExcecaoAoDeletarContaAPagar(){
        $this->accountFactory->configureUserSession(true);
        $billId = 1;
        $accounts = $this->accountFactory->factoryAccount();

        $user = $this->accountFactory->factoryUser(2);
        $account = $accounts->get(0);
        $account->user = $user;
        $bill = $this->createPartialMock(Bill::class,['account','load']);
        $bill->method('account')
            ->willReturn($account);
        $bill->method('load');
        $bill->account = $account;
        $bill->fill($this->factoryBillData());
        $bill->account_id = 15;
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billRepository = $this->getMockRepository();
        $this->billStandarizedService->method('normalizeBill')
            ->willReturn($bill);

        $billBusiness = new BillBusiness($billRepository,$creditCardBusiness, $this->billStandarizedService);
        $this->expectException(NotFoundHttpException::class);
        $bill = $billBusiness->deleteBill($billId);
    }

    /**
     * @test
     */
    public function deveTrazerMesesComCompras(){
        $this->accountFactory->configureUserSession();
        $accountId = 1;
        $accounts = $this->accountFactory->factoryAccount();
        $billRepository = $this->getMockRepository();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billRepository
            ->method('getMonthWithBill')
            ->with($accountId)
            ->willReturn(Collection::make([['month' => '01', 'year' => '2018']]));

        $billBusiness = new BillBusiness($billRepository,$creditCardBusiness, $this->billStandarizedService);
        $dates = $billBusiness->getPeriodWithBill($accountId);
        $this->assertIsIterable($dates);

        $this->assertEquals('01',$dates->get(0)['month']);
        $this->assertEquals('2018',$dates->get(0)['year']);
    }
    /**
     * @test
     */
    public function deveDispararExcecaoAoTrazerMesesComComprasDeContaInexistente(){
        $this->accountFactory->configureUserSession(true);
        $accountId = 2;
        $accounts = $this->accountFactory->factoryAccount();
        $billRepository = $this->getMockRepository();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billRepository
            ->method('getMonthWithBill')
            ->with($accountId)
            ->willReturn(Collection::make([['month' => '01', 'year' => '2018']]));

        $billBusiness = new BillBusiness($billRepository,$creditCardBusiness, $this->billStandarizedService);
        $this->expectException(NotFoundHttpException::class);
        $dates = $billBusiness->getPeriodWithBill($accountId);
    }

}
