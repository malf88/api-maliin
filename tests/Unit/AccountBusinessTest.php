<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Bill;
use App\Models\User;
use App\Modules\Account\Bussines\AccountBusiness;
use App\Modules\Account\Respository\AccountRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use LogicException;

class AccountBusinessTest extends TestCase
{
    private function factoryAccount(){
        $accountInfo = [
            'name'      => 'João',
            'bank'      => '102 - Nu pagamentos SA',
            'account'   =>  '23423'
        ];

        $billAcount1 = new Bill();
        $billAcount1->pay_day = Carbon::today();
        $billAcount1->amount = 100.00;

        $billAcount2 = new Bill();
        $billAcount2->pay_day = Carbon::today();
        $billAcount2->amount = 100.00;

        $billAcount3 = new Bill();
        $billAcount3->amount = -100.00;

        $account = $this->createPartialMock(Account::class,['bills','load']);

        $billCollection = Collection::make([$billAcount1,$billAcount2,$billAcount3]);

        $account
            ->method('bills')
            ->willReturn($billCollection);
        $account
            ->method('load')
            ->with(['bills'])
            ->willReturn($account);


        $account->bills()->make([$billAcount1,$billAcount2,$billAcount3]);
        $account->fill($accountInfo);

        return Collection::make([$account]);

    }
    /**
     * @test
     */
    public function deveListarContasDoUsuario(){
        $user = new User();
        $accountRepositoryMock = $this->createMock(AccountRepository::class);

        $accountFactory = $this->factoryAccount();

        $accountRepositoryMock->method('getAccountFromUser')
            ->with($user)
            ->willReturn($accountFactory);

        $account = new AccountBusiness($accountRepositoryMock);
        $listAccount = $account->getListAllAccounts($user);

        $this->assertIsIterable($listAccount);

        $this->assertEquals(200.00,$listAccount->get(0)->total_balance);
        $this->assertEquals(100.00,$listAccount->get(0)->total_estimated);
        $this->assertCount(3,$listAccount->get(0)->bills());
    }

    /**
     * @test
     */
    public function deveListarContasDoUsuarioLogado(){

        $user = new User();
        Auth::shouldReceive('user')->once()->andReturn($user);
        $accountRepositoryMock = $this->createMock(AccountRepository::class);

        $accountRepositoryMock->method('getAccountFromUser')
            ->with($user)
            ->willReturn($this->factoryAccount());
        $account = new AccountBusiness($accountRepositoryMock);
        $listAccount = $account->getListAllAccountFromLoggedUser();
        $this->assertIsIterable($listAccount);
        $this->assertEquals(200.00,$listAccount->get(0)->total_balance);
        $this->assertEquals(100.00,$listAccount->get(0)->total_estimated);
        $this->assertCount(3,$listAccount->get(0)->bills());
    }

    /**
     * @test
     */
    public function deveInserirContaParaUsuario(){

        $user = new User();
        $accountInfo = [
            'name'      => 'João',
            'bank'      => '102 - Nu pagamentos SA',
            'account'   =>  '23423'
        ];
        $account = new Account();
        $account->fill($accountInfo);

        $accountRepositoryMock = $this->createMock(AccountRepository::class);

        $accountRepositoryMock->method('saveAccount')
            ->with($user,$accountInfo)
            ->willReturn($account);

        $accountBusiness = new AccountBusiness($accountRepositoryMock);
        $newAccount = $accountBusiness->insertAccount($user,$accountInfo);

        $this->assertEquals($accountInfo['name'],$newAccount->name);
        $this->assertEquals($accountInfo['bank'],$newAccount->bank);
        $this->assertEquals($accountInfo['account'],$newAccount->account);
    }

    /**
     * @test
     */
    public function deveAlterarConta(){

        $user = new User();

        $accountInfo = [
            'name'      => 'João',
            'bank'      => '102 - Nu pagamentos SA',
            'account'   =>  '23423'
        ];
        $id = 1;
        $account = new Account();
        $account->id = $id;
        $account->fill($accountInfo);

        $accountRepositoryMock = $this->createMock(AccountRepository::class);

        $accountRepositoryMock->method('updateAccount')
            ->with($id,$accountInfo)
            ->willReturn($account);
        $accountBusiness = new AccountBusiness($accountRepositoryMock);
        $newAccount = $accountBusiness->updateAccount($id,$accountInfo);

        $this->assertEquals($id,$newAccount->id);
        $this->assertEquals($accountInfo['name'],$newAccount->name);
        $this->assertEquals($accountInfo['bank'],$newAccount->bank);
        $this->assertEquals($accountInfo['account'],$newAccount->account);
    }

    /**
     * @test
     */
    public function deveRemoverConta(){
        $accountInfo = [
            'name'      => 'João',
            'bank'      => '102 - Nu pagamentos SA',
            'account'   =>  '23423'
        ];
        $id = 1;
        $accountRepositoryMock = $this->createMock(AccountRepository::class);
        $accountRepositoryMock->method('deleteAccount')
            ->with($id)
            ->willReturn(true);
        $accountBusiness = new AccountBusiness($accountRepositoryMock);
        $result = $accountBusiness->deleteAccount($id);

        $this->assertEquals(true,$result);
    }
    /**
     * @test
     */
    public function deveRetornarExcecaoAoRemoverConta()
    {
        $accountInfo = [
            'name' => 'João',
            'bank' => '102 - Nu pagamentos SA',
            'account' => '23423'
        ];
        $id = 1;
        $accountRepositoryMock = $this->createMock(AccountRepository::class);
        $accountRepositoryMock->method('deleteAccount')
            ->with($id)
            ->willThrowException(new LogicException());
        $accountBusiness = new AccountBusiness($accountRepositoryMock);
        $this->expectException(LogicException::class);
        $accountBusiness->deleteAccount($id);
    }
}
