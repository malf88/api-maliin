<?php

namespace Tests\Unit\Account;

use App\Models\Account;
use App\Models\Bill;
use App\Models\User;
use App\Modules\Account\Business\AccountBusiness;
use App\Modules\Account\Business\BillBusiness;
use App\Modules\Account\Repository\AccountRepository;
use App\Modules\Account\Repository\BillRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ItemNotFoundException;
use Tests\TestCase;

class BillBusinessTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $user = $this->factoryUser(1);
        Auth::shouldReceive('user')
            ->andReturn($user);
    }

    public function factoryUser(int $id):User
    {

        $user = $this->createPartialMock(User::class,['accounts']);
        $accounts = $this->factoryAccount();
        $user
            ->method('accounts')
            ->willReturn($accounts);
        $user->id = $id;

        return $user;
    }

    private function factoryAccount():Collection{
        $accountInfo = [
            'name'      => 'João',
            'bank'      => '102 - Nu pagamentos SA',
            'account'   =>  '23423'
        ];

        $billAccount1 = $this->factoryBill(1,100.00);
        $billAccount2 = $this->factoryBill(2,200.00);
        $billAccount3 = $this->factoryBill(3,-100.00);

        $user = new User();
        $user->id = 1;

        $account = $this->createPartialMock(Account::class,['bills','load']);

        $billCollection = Collection::make([$billAccount1,$billAccount2,$billAccount3]);

        $account
            ->method('bills')
            ->willReturn($billCollection);
        $account
            ->method('load')
            ->with(['bills'])
            ->willReturn($account);
        $account->user = $user;
        $account->fill($accountInfo);
        $account->id = 1;
        return Collection::make([$account]);

    }

    public function factoryBill(int $id, float $amount, int $parentId = null,$pay_day = null):Bill
    {
        $bill = $this->createPartialMock(Bill::class,['load']);
        $bill->description = "Mercado";
        $bill->id = $id;
        $bill->bill_parent_id = $parentId;
        $bill->pay_day = $pay_day;
        $bill->amount = $amount;

        return $bill;

    }

    public function getMockRepository(){
        return $this->createMock(BillRepository::class);

    }
    public function getMockAccountBusiness(){
        $accountRepository = $this->createMock(AccountRepository::class);
        return new AccountBusiness($accountRepository);
    }
    public function factoryBillData():array
    {
        return [
            'description'   => 'Compra no supermercado',
            'amount'        => 160.00,
            'date'          => Carbon::today(),
            'due_date'      => Carbon::today()->addDays(30),
            'pay_day'       => null,
            'barcode'       => '',
            'category_id'   => 1,
            'account_id'    => 1,
            'portion'       => 1
        ];
    }
    /**
     * @test
     */
    public function deveListarContasAPagarDeUmaConta(){
        $accountId = 1;
        $accounts = $this->factoryAccount();
        $billRepository = $this->getMockRepository();
        $accountBusiness = $this->getMockAccountBusiness();

        $billRepository
            ->method('getBillsByAccount')
            ->with($accountId)
            ->willReturn($accounts->get(0)->bills());

        $billBusiness = new BillBusiness($billRepository,$accountBusiness);
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
        $billBusiness = new BillBusiness($billRepository,$accountBusiness);
        $this->expectException(ItemNotFoundException::class);
        $bills = $billBusiness->getBillsByAccount($accountId);

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
        $billRepository
            ->method('saveBill')
            ->with($accountId,$billData)
            ->willReturn($bill);
        $billBusiness = new BillBusiness($billRepository,$accountBusiness);
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
        $accounts = $this->factoryAccount();
        $billData = $this->factoryBillData();
        $billData['portion'] = 3;

        $bill = new Bill();
        $bill->fill($billData);
        $bill->id = 1;
        $billRepository = $this->getMockRepository();
        $accountBusiness = $this->getMockAccountBusiness();
        $billRepository
            ->method('saveBill')
            ->willReturn($bill);
        $billBusiness = new BillBusiness($billRepository,$accountBusiness);
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
        $billBusiness = new BillBusiness($billRepository,$accountBusiness);
        $this->expectException(ItemNotFoundException::class);
        $bills = $billBusiness->insertBill($accountId,$billData);
    }

    /**
     * @test
     */
    public function deveRetornarUmaContaAPagar(){
        $billId = 1;
        $accounts = $this->factoryAccount();
        $billRepository = $this->getMockRepository();
        $accountBusiness = $this->getMockAccountBusiness();
        $user = $this->factoryUser(1);
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

        $billBusiness = new BillBusiness($billRepository,$accountBusiness);
        $bill = $billBusiness->getBillById($billId);

        $this->assertEquals(160.00,$bill->amount);

    }
    /**
     * @test
     */
    public function deveDispararExcecaoAoRetornarUmaContaAPagar(){
        $billId = 1;
        $accounts = $this->factoryAccount();
        $billRepository = $this->getMockRepository();
        $accountBusiness = $this->getMockAccountBusiness();
        $user = $this->factoryUser(3);


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

        $billBusiness = new BillBusiness($billRepository,$accountBusiness);
        $this->expectException(ItemNotFoundException::class);
        $bill = $billBusiness->getBillById($billId);


    }

    /**
     * @test
     */
    public function deveAlterarUmaContaAPagar(){
        $billId = 1;
        $accounts = $this->factoryAccount();
        $billData = $this->factoryBillData();

        $user = $this->factoryUser(1);
        $account = $accounts->get(0);
        $account->user = $user;
        $bill = $this->createPartialMock(Bill::class,['account']);
        $bill->method('account')
            ->willReturn($account);

        $bill->account = $account;
        $bill->fill($this->factoryBillData());

        $billRepository = $this->getMockRepository();
        $accountBusiness = $this->getMockAccountBusiness();
        $billRepository
            ->method('getBillById')
            ->willReturn($bill);
        $billRepository
            ->method('updateBill')
            ->willReturn($bill);
        $billBusiness = new BillBusiness($billRepository,$accountBusiness);
        $bill = $billBusiness->updateBill($billId,$billData);

        $this->assertEquals(160.00,$bill->amount);

    }
    /**
     * @test
     */
    public function deveAlterarUmaContaAPagarESeusFilhos(){
        $billId = 1;
        $accounts = $this->factoryAccount();
        $billData = $this->factoryBillData();
        $billData['update_childs'] = true;

        $user = $this->factoryUser(1);
        $account = $accounts->get(0);
        $account->user = $user;
        $bill = $this->createPartialMock(Bill::class,['account']);
        $bill->method('account')
            ->willReturn($account);
        $bill->account = $account;

        $bill->fill($this->factoryBillData());

        $billRepository = $this->getMockRepository();
        $accountBusiness = $this->getMockAccountBusiness();
        $billRepository
            ->method('getChildBill')
            ->willReturn(Collection::make([
                $this->factoryBill(1, 300,pay_day:Carbon::today()),
                $this->factoryBill(2, 350,1),
                $this->factoryBill(3, 350,1),
            ]));
        $billRepository
            ->method('getBillById')
            ->willReturn($bill);
        $billRepository
            ->method('updateBill')
            ->willReturn($bill);
        $billBusiness = new BillBusiness($billRepository,$accountBusiness);
        $bills = $billBusiness->updateBill($billId,$billData);

        $this->assertCount(3,$bills);

    }
    /**
     * @test
     */
    public function deveDispararExcecaoAoAlterarUmaContaAPagarESeusFilhos(){
        $billId = 1;
        $accounts = $this->factoryAccount();
        $billData = $this->factoryBillData();
        $billData['update_childs'] = true;

        $user = $this->factoryUser(2);
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

        $billBusiness = new BillBusiness($billRepository,$accountBusiness);
        $this->expectException(ItemNotFoundException::class);
        $bills = $billBusiness->updateBill($billId,$billData);
    }

    /**
     * @test
     */
    public function deveDeletarContaAPagar(){
        $billId = 1;
        $accounts = $this->factoryAccount();
        $user = $this->factoryUser(1);
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
        $billRepository
            ->method('deleteBill')
            ->willReturn(true);
        $accountBusiness = $this->getMockAccountBusiness();

        $billBusiness = new BillBusiness($billRepository,$accountBusiness);
        $bill = $billBusiness->deleteBill($billId);

        $this->assertEquals(true,$bill);
    }

    /**
     * @test
     */
    public function deveDispararExcecaoAoDeletarContaAPagar(){
        $billId = 1;
        $accounts = $this->factoryAccount();

        $user = $this->factoryUser(2);
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
        $accountBusiness = $this->getMockAccountBusiness();
        $billBusiness = new BillBusiness($billRepository,$accountBusiness);
        $this->expectException(ItemNotFoundException::class);
        $bill = $billBusiness->deleteBill($billId);

        $this->assertEquals(true,$bill);
    }

}
