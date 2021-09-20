<?php

namespace Tests\Unit\Account;

use App\Models\Account;
use App\Models\Bill;
use App\Models\User;
use App\Modules\Account\Business\AccountBusiness;
use App\Modules\Account\Business\BillBusiness;
use App\Modules\Account\Business\CreditCardBusiness;
use App\Modules\Account\Repository\AccountRepository;
use App\Modules\Account\Repository\BillRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ItemNotFoundException;
use Tests\TestCase;
use Tests\Unit\Account\Factory\AccountFactory;
use const OpenApi\COLOR_RED;

class BillBusinessTest extends TestCase
{
    private AccountFactory $accountFactory;
    public function setUp(): void
    {
        parent::setUp();
        $this->accountFactory = new AccountFactory();
        $user = $this->accountFactory->factoryUser(1);
        Auth::shouldReceive('user')
            ->andReturn($user);
    }

    public function getMockRepository(){
        $mock = $this->createMock(BillRepository::class);
        $mock->method('getChildBill')
            ->willReturn(Collection::make([
                $this->accountFactory->factoryBill(2,300,1,portion:2),
                $this->accountFactory->factoryBill(3,200,1,portion:3),
                $this->accountFactory->factoryBill(4,-100,1,portion:4)
            ]));
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
        $accountId = 1;
        $accounts = $this->accountFactory->factoryAccount();
        $billRepository = $this->getMockRepository();
        $accountBusiness = $this->getMockAccountBusiness();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billRepository
            ->method('getBillsByAccount')
            ->with($accountId)
            ->willReturn($accounts->get(0)->bills());

        $billBusiness = new BillBusiness($accountBusiness,$billRepository,$creditCardBusiness);
        $bills = $billBusiness->getBillsByAccount($accountId);

        $this->assertIsIterable($bills);
        $this->assertCount(3,$bills);
        $this->assertEquals(200.00,$bills->sum('amount'));

    }

    /**
     * @test
     */
    public function deveDispararExcecaoAoListarContasAPagarDeUmaConta(){
        $accountId = 2;
        $billRepository = $this->getMockRepository();
        $accountBusiness = $this->getMockAccountBusiness();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billBusiness = new BillBusiness($accountBusiness,$billRepository,$creditCardBusiness);
        $this->expectException(ItemNotFoundException::class);
        $bills = $billBusiness->getBillsByAccount($accountId);

    }

    /**
     * @test
     */
    public function deveListarContasAPagarDeUmaContaPaginada(){
        $accountId = 1;
        $accounts = $this->accountFactory->factoryAccount();
        $billRepository = $this->getMockRepository();
        $accountBusiness = $this->getMockAccountBusiness();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billsPaginados = new LengthAwarePaginator($accounts->get(0)->bills(),3,15);
        $billRepository
            ->method('getBillsByAccount')
            ->with($accountId,true)
            ->willReturn($billsPaginados);

        $billBusiness = new BillBusiness($accountBusiness,$billRepository,$creditCardBusiness);
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
        $accountId = 2;
        $billRepository = $this->getMockRepository();
        $accountBusiness = $this->getMockAccountBusiness();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billBusiness = new BillBusiness($accountBusiness,$billRepository,$creditCardBusiness);
        $this->expectException(ItemNotFoundException::class);
        $bills = $billBusiness->getBillsByAccountPaginate($accountId);

    }
    /**
     * @test
     */
    public function deveSalvarContasAPagarEmUmaConta(){
        $accountId = 1;
        $billData = $this->factoryBillData();
        $bill = new Bill();
        $bill->fill($billData);
        $bill->id = 1;

        $billRepository = $this->getMockRepository();
        $accountBusiness = $this->getMockAccountBusiness();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billRepository
            ->method('saveBill')
            ->with($accountId,$billData)
            ->willReturn($bill);
        $billBusiness = new BillBusiness($accountBusiness,$billRepository,$creditCardBusiness);
        $bill = $billBusiness->insertBill($accountId,$billData);
        $this->assertEquals('Compra no supermercado',$bill->description);
        $this->assertEquals(160.00,$bill->amount);
        $this->assertEquals(1,$bill->account_id);

    }

    /**
     * @test
     */
    public function deveSalvarContasAPagarEmUmaContaComParcelas(){
        $accountId = 1;
        $billData = $this->factoryBillData();
        $billData['portion'] = 3;

        $bill = new Bill();
        $bill->fill($billData);
        $bill->id = 1;
        $billRepository = $this->getMockRepository();
        $accountBusiness = $this->getMockAccountBusiness();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billRepository
            ->method('saveBill')
            ->willReturn($bill);
        $billBusiness = new BillBusiness($accountBusiness,$billRepository,$creditCardBusiness);
        $bills = $billBusiness->insertBill($accountId,$billData);
        $this->assertIsIterable($bills);
        $this->assertCount(3,$bills);
        $this->assertEquals(480.00,$bills->sum('amount'));

    }
    /**
     * @test
     */
    public function deveDispararExcecaoAoSalvarContasAPagar(){
        $accountId = 2;

        $billData = $this->factoryBillData();
        $billRepository = $this->getMockRepository();
        $accountBusiness = $this->getMockAccountBusiness();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billBusiness = new BillBusiness($accountBusiness,$billRepository,$creditCardBusiness);
        $this->expectException(ItemNotFoundException::class);
        $bills = $billBusiness->insertBill($accountId,$billData);
    }

    /**
     * @test
     */
    public function deveRetornarUmaContaAPagar(){
        $billId = 1;
        $accounts = $this->accountFactory->factoryAccount();
        $billRepository = $this->getMockRepository();
        $accountBusiness = $this->getMockAccountBusiness();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $user = $this->accountFactory->factoryUser(1);
        $account = $accounts->get(0);
        $account->user = $user;
        $bill = $this->createPartialMock(Bill::class,['account','load']);
        $bill->method('load');
        $bill->method('account')
            ->willReturn($account);

        $bill->account = $account;
        $bill->fill($this->factoryBillData());
        $billRepository
            ->method('getBillById')
            ->with($billId)
            ->willReturn($bill);

        $billBusiness = new BillBusiness($accountBusiness,$billRepository,$creditCardBusiness);
        $bill = $billBusiness->getBillById($billId);

        $this->assertEquals(160.00,$bill->amount);

    }
    /**
     * @test
     */
    public function deveDispararExcecaoAoRetornarUmaContaAPagar(){
        $billId = 1;
        $accounts = $this->accountFactory->factoryAccount();
        $billRepository = $this->getMockRepository();
        $accountBusiness = $this->getMockAccountBusiness();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $user = $this->accountFactory->factoryUser(3);

        $account = $accounts->get(0);
        $account->user = $user;
        $bill = $this->createPartialMock(Bill::class,['account']);
        $bill->method('account')
            ->willReturn($account);

        $bill->account = $account;
        $bill->fill($this->factoryBillData());
        $billRepository
            ->method('getBillById')
            ->with($billId)
            ->willReturn($bill);

        $billBusiness = new BillBusiness($accountBusiness,$billRepository,$creditCardBusiness);
        $this->expectException(ItemNotFoundException::class);
        $bill = $billBusiness->getBillById($billId);


    }

    /**
     * @test
     */
    public function deveAlterarUmaContaAPagar(){
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
        $accountBusiness = $this->getMockAccountBusiness();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billRepository
            ->method('getBillById')
            ->willReturn($bill);
        $billRepository
            ->method('updateBill')
            ->willReturn($bill);
        $billBusiness = new BillBusiness($accountBusiness,$billRepository,$creditCardBusiness);
        $bill = $billBusiness->updateBill($billId,$billData);

        $this->assertEquals(160.00,$bill->amount);

    }
    /**
     * @test
     */
    public function deveAlterarUmaContaAPagarESeusIrmaos(){
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

        $bill->fill($this->factoryBillData());

        $billRepository = $this->getMockRepository();
        $accountBusiness = $this->getMockAccountBusiness();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billRepository
            ->method('getBillById')
            ->willReturn($bill);
        $billRepository
            ->method('updateBill')
            ->willReturn($bill);
        $billBusiness = new BillBusiness($accountBusiness,$billRepository,$creditCardBusiness);
        $bills = $billBusiness->updateBill($billId,$billData);

        $this->assertCount(3,$bills->bill_parent);
        $this->assertEquals(1,$bills->id);

    }

    /**
     * @test
     */
    public function deveAlterarUmaContaAPagarESeusFilhos(){
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
        $accountBusiness = $this->getMockAccountBusiness();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billRepository
            ->method('getBillById')
            ->willReturn($bill);
        $billRepository
            ->method('updateBill')
            ->willReturn($bill);
        $billBusiness = new BillBusiness($accountBusiness,$billRepository,$creditCardBusiness);
        $bills = $billBusiness->updateBill($billId,$billData);

        $this->assertCount(3,$bills->bill_parent);
        $this->assertEquals(1,$bills->id);

    }
    /**
     * @test
     */
    public function deveDispararExcecaoAoAlterarUmaContaAPagarESeusFilhos(){
        $billId = 1;
        $accounts = $this->accountFactory->factoryAccount();
        $billData = $this->factoryBillData();
        $billData['update_childs'] = true;

        $user = $this->accountFactory->factoryUser(2);
        $account = $accounts->get(0);
        $account->user = $user;
        $bill = $this->createPartialMock(Bill::class,['account']);
        $bill->method('account')
            ->willReturn($account);
        $bill->account = $account;

        $billRepository = $this->getMockRepository();
        $billRepository
            ->method('getBillById')
            ->willReturn($bill);
        $bill->fill($billData);

        $accountBusiness = $this->getMockAccountBusiness();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billBusiness = new BillBusiness($accountBusiness,$billRepository,$creditCardBusiness);
        $this->expectException(ItemNotFoundException::class);
        $bills = $billBusiness->updateBill($billId,$billData);
    }

    /**
     * @test
     */
    public function deveDeletarContaAPagar(){
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

        $billRepository = $this->getMockRepository();
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billRepository
            ->method('getBillById')
            ->willReturn($bill);
        $billRepository
            ->method('deleteBill')
            ->willReturn(true);
        $accountBusiness = $this->getMockAccountBusiness();

        $billBusiness = new BillBusiness($accountBusiness,$billRepository,$creditCardBusiness);
        $bill = $billBusiness->deleteBill($billId);

        $this->assertEquals(true,$bill);
    }

    /**
     * @test
     */
    public function deveDispararExcecaoAoDeletarContaAPagar(){
        $billId = 1;
        $accounts = $this->accountFactory->factoryAccount();

        $user = $this->accountFactory->factoryUser(2);
        $account = $accounts->get(0);
        $account->user = $user;
        $bill = $this->createPartialMock(Bill::class,['account']);
        $bill->method('account')
            ->willReturn($account);
        $bill->account = $account;
        $creditCardBusiness = $this->getMockCreditCardBusiness();
        $billRepository = $this->getMockRepository();
        $billRepository
            ->method('getBillById')
            ->willReturn($bill);
        $accountBusiness = $this->getMockAccountBusiness();
        $billBusiness = new BillBusiness($accountBusiness,$billRepository,$creditCardBusiness);
        $this->expectException(ItemNotFoundException::class);
        $bill = $billBusiness->deleteBill($billId);

        $this->assertEquals(true,$bill);
    }

}
