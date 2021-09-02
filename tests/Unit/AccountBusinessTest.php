<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\User;
use App\Modules\Account\Bussines\AccountBusiness;
use App\Modules\Account\Respository\AccountRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\LazyLoadingViolationException;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use LogicException;

class AccountBusinessTest extends TestCase
{
    /**
     * @test
     */
    public function deveListarContasDoUsuario(){

        $user = new User();

        $accountRepositoryMock = $this->createMock(AccountRepository::class);

        $accountRepositoryMock->method('getAccountFromUser')
            ->with($user)
            ->willReturn(new Collection());
        $account = new AccountBusiness($accountRepositoryMock);
        $listAccount = $account->getListAllAccounts($user);

        $this->assertIsIterable($listAccount);
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
            ->willReturn(new Collection());
        $account = new AccountBusiness($accountRepositoryMock);
        $listAccount = $account->getListAllAccountFromLoggedUser();

        $this->assertIsIterable($listAccount);
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
