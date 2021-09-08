<?php

namespace Tests\Unit\Account;

use App\Models\Account;
use App\Models\CreditCard;
use App\Models\User;
use App\Modules\Account\Business\AccountBusiness;
use App\Modules\Account\Business\CreditCardBusiness;
use App\Modules\Account\Repository\AccountRepository;
use App\Modules\Account\Repository\CreditCardRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ItemNotFoundException;
use Tests\TestCase;

class CreditCardBusinessTest extends TestCase
{
    public Collection $creditCardsList;

    public function factoryCreditCards(){
        $user = new User();
        $user->id = 1;
        $account = $this->createPartialMock(Account::class,['user']);
        $account
            ->method('user')
            ->willReturn($user);
        $account->user = $user;
        $creditCard1 = $this->createPartialMock(CreditCard::class,['account']);
        $creditCard1
            ->method('account')
            ->willReturn($account);
        $creditCard1->id = 1;
        $creditCard1->name = 'Nubank';
        $creditCard1->due_day = 31;
        $creditCard1->close_day = 01;
        $creditCard1->account = $account;

        $creditCard2 = new CreditCard();
        $creditCard2->id = 2;
        $creditCard2->name = 'Itaú';
        $creditCard2->due_day = 27;
        $creditCard2->close_day = 15;

        $creditCard3 = new CreditCard();
        $creditCard3->id = 3;
        $creditCard3->name = 'Caixa';
        $creditCard3->due_day = 13;
        $creditCard3->close_day = 06;

        $creditCard4 = new CreditCard();
        $creditCard4->id = 4;
        $creditCard4->name = 'Santander';
        $creditCard4->due_day = 13;
        $creditCard4->close_day = 06;

        return Collection::make([$creditCard1,$creditCard2,$creditCard3,$creditCard4]);
    }
    public function factoryUser(int $id):User
    {
        $account = new Account();
        $account->id = 1;
        $user = $this->createPartialMock(User::class,['accounts']);
        $user
            ->method('accounts')
            ->willReturn($account);
        $user->id = $id;

        return $user;
    }
    /**
     * @test
     */
    public function deveListarCartoesDeCreditoDeUmaConta(){
        $accountId = 1;

        $user = $this->factoryUser(1);

        Auth::shouldReceive('user')->andReturn($user);

        $creditCardRepository = $this->createMock(CreditCardRepository::class);
        $accountRepository = $this->createMock(AccountRepository::class);
        $accountBusiness = new AccountBusiness($accountRepository);

        $creditCardRepository
            ->method('getCreditCardsByAccountId')
            ->with($accountId)
            ->willReturn($this->factoryCreditCards());
        $creditCardBusiness = new CreditCardBusiness($creditCardRepository,$accountBusiness);
        $creditCards = $creditCardBusiness->getListCreditCardByAccount($accountId);

        $this->assertIsIterable($creditCards);
        $this->assertCount(4,$creditCards);
    }
    /**
     * @test
     */
    public function deveDispararExcecaoAoListarCartoesDeCreditoDeUmaConta(){
        $accountId = 2;
        $user = $this->factoryUser(1);

        Auth::shouldReceive('user')->andReturn($user);

        $creditCardRepository = $this->createMock(CreditCardRepository::class);
        $accountRepository = $this->createMock(AccountRepository::class);
        $accountBusiness = new AccountBusiness($accountRepository);

        $creditCardRepository
            ->method('getCreditCardsByAccountId')
            ->with($accountId)
            ->willReturn($this->factoryCreditCards());
        $creditCardBusiness = new CreditCardBusiness($creditCardRepository,$accountBusiness);
        $this->expectException(ItemNotFoundException::class);
        $creditCards = $creditCardBusiness->getListCreditCardByAccount($accountId);

    }
    /**
     * @test
     */
    public function deveRetornarUmCartaoDeCreditoComIdInformado(){
        $accountId = 1;
        $creditCardId = 1;


        $user = $this->factoryUser(1);
        Auth::shouldReceive('user')->andReturn($user);

        $creditCardRepository = $this->createMock(CreditCardRepository::class);
        $accountRepository = $this->createMock(AccountRepository::class);
        $accountBusiness = new AccountBusiness($accountRepository);

        $creditCards = $this->factoryCreditCards();
        $creditCardRepository
            ->method('getCreditCardById')
            ->with($creditCardId)
            ->willReturn($creditCards->find($creditCardId));
        $creditCardBusiness = new CreditCardBusiness($creditCardRepository,$accountBusiness);
        $creditCard = $creditCardBusiness->getCreditCardById($creditCardId);

        $this->assertEquals($creditCardId,$creditCard->id);
    }
    /**
     * @test
     */
    public function deveRetornarUmaExcecaoAoRetornarUmCartaoDeCreditoComIdInformado(){
        $accountId = 1;
        $creditCardId = 1;


        $user = $this->factoryUser(2);
        Auth::shouldReceive('user')->andReturn($user);

        $creditCardRepository = $this->createMock(CreditCardRepository::class);
        $accountRepository = $this->createMock(AccountRepository::class);
        $accountBusiness = new AccountBusiness($accountRepository);

        $creditCards = $this->factoryCreditCards();
        $creditCardRepository
            ->method('getCreditCardById')
            ->with($creditCardId)
            ->willReturn($creditCards->find($creditCardId));
        $creditCardBusiness = new CreditCardBusiness($creditCardRepository,$accountBusiness);
        $this->expectException(ItemNotFoundException::class);
        $creditCard = $creditCardBusiness->getCreditCardById($creditCardId);

    }

    /**
     * @test
     */
    public function deveInserirUmCartaoDeCredito(){
        $accountId = 1;
        $creditCardId = 1;

        $creditCardData = [
            'name'      => 'Bradesco',
            'due_day'  => 01,
            'close_day' =>26
        ];

        $user = $this->factoryUser(1);
        Auth::shouldReceive('user')->andReturn($user);

        $creditCardRepository = $this->createMock(CreditCardRepository::class);
        $accountRepository = $this->createMock(AccountRepository::class);
        $accountBusiness = new AccountBusiness($accountRepository);

        $creditCard = new CreditCard();
        $creditCard->fill($creditCardData);
        $creditCard->id = $creditCardId;

        $creditCardRepository
            ->method('saveCreditCard')
            ->with($accountId,$creditCardData)
            ->willReturn($creditCard);
        $creditCardBusiness = new CreditCardBusiness($creditCardRepository,$accountBusiness);
        $creditCard = $creditCardBusiness->insertCreditCard($accountId,$creditCardData);

        $this->assertEquals($creditCardId,$creditCard->id);

    }

    /**
     * @test
     */
    public function deveDispararExcecaoAoInserirUmCartaoDeCredito(){
        $accountId = 2;
        $creditCardId = 1;

        $creditCardData = [
            'name'      => 'Bradesco',
            'due_day'  => 01,
            'close_day' =>26
        ];

        $user = $this->factoryUser(1);
        Auth::shouldReceive('user')->andReturn($user);

        $creditCardRepository = $this->createMock(CreditCardRepository::class);
        $accountRepository = $this->createMock(AccountRepository::class);
        $accountBusiness = new AccountBusiness($accountRepository);

        $creditCard = new CreditCard();
        $creditCard->fill($creditCardData);
        $creditCard->id = $creditCardId;

        $creditCardRepository
            ->method('saveCreditCard')
            ->with($accountId,$creditCardData)
            ->willReturn($creditCard);
        $creditCardBusiness = new CreditCardBusiness($creditCardRepository,$accountBusiness);
        $this->expectException(ItemNotFoundException::class);
        $creditCard = $creditCardBusiness->insertCreditCard($accountId,$creditCardData);
    }

    /**
     * @test
     */
    public function deveAlterarUmCartaoDeCredito(){
        $creditCardId = 1;
        $accountId = 1;
        $creditCardData = [
            'name'      => 'Bradesco',
            'due_day'  => 01,
            'close_day' => 26
        ];

        $user = $this->factoryUser(1);
        Auth::shouldReceive('user')->andReturn($user);

        $creditCardRepository = $this->createMock(CreditCardRepository::class);
        $accountRepository = $this->createMock(AccountRepository::class);
        $accountBusiness = new AccountBusiness($accountRepository);

        $creditCard = new CreditCard();
        $creditCard->fill($creditCardData);
        $creditCard->id = $creditCardId;

        $creditCards = $this->factoryCreditCards();
        $creditCardRepository
            ->method('getCreditCardById')
            ->with($creditCardId)
            ->willReturn($creditCards->get(0));

        $creditCardRepository
            ->method('updateCreditCard')
            ->with($creditCardId,$creditCardData)
            ->willReturn($creditCard);
        $creditCardBusiness = new CreditCardBusiness($creditCardRepository,$accountBusiness);
        $creditCard = $creditCardBusiness->updateCreditCard($creditCardId,$creditCardData);

        $this->assertEquals('Bradesco',$creditCard->name);
        $this->assertEquals(26,$creditCard->close_day);
        $this->assertEquals(01,$creditCard->due_day);
    }
    /**
     * @test
     */
    public function deveDispararUmaExecaoAoAlterarUmCartaoDeCredito(){
        $creditCardId = 1;
        $accountId = 1;
        $creditCardData = [
            'name'      => 'Bradesco',
            'due_day'  => 01,
            'close_day' => 26
        ];

        $user = $this->factoryUser(2);
        Auth::shouldReceive('user')->andReturn($user);

        $creditCardRepository = $this->createMock(CreditCardRepository::class);
        $accountRepository = $this->createMock(AccountRepository::class);
        $accountBusiness = new AccountBusiness($accountRepository);

        $creditCard = new CreditCard();
        $creditCard->fill($creditCardData);
        $creditCard->id = $creditCardId;

        $creditCards = $this->factoryCreditCards();
        $creditCardRepository
            ->method('getCreditCardById')
            ->with($creditCardId)
            ->willReturn($creditCards->get(0));

        $creditCardRepository
            ->method('updateCreditCard')
            ->with($creditCardId,$creditCardData)
            ->willReturn($creditCard);
        $creditCardBusiness = new CreditCardBusiness($creditCardRepository,$accountBusiness);
        $this->expectException(ItemNotFoundException::class);
        $creditCard = $creditCardBusiness->updateCreditCard($creditCardId,$creditCardData);

    }

    /**
     * @test
     */
    public function deveExcluirUmCartaoDeCredito(){
        $creditCardId = 1;
        $accountId = 1;
        $creditCardData = [
            'name'      => 'Bradesco',
            'due_day'  => 01,
            'close_day' => 26
        ];

        $user = $this->factoryUser(1);
        Auth::shouldReceive('user')->andReturn($user);

        $creditCardRepository = $this->createMock(CreditCardRepository::class);
        $accountRepository = $this->createMock(AccountRepository::class);
        $accountBusiness = new AccountBusiness($accountRepository);

        $creditCard = new CreditCard();
        $creditCard->fill($creditCardData);
        $creditCard->id = $creditCardId;

        $creditCards = $this->factoryCreditCards();
        $creditCardRepository
            ->method('getCreditCardById')
            ->with($creditCardId)
            ->willReturn($creditCards->get(0));

        $creditCardRepository
            ->method('deleteCreditCard')
            ->with($creditCardId)
            ->willReturn(true);
        $creditCardBusiness = new CreditCardBusiness($creditCardRepository,$accountBusiness);
        $creditCard = $creditCardBusiness->removeCreditCard($creditCardId);

        $this->assertTrue($creditCard);
    }
    /**
     * @test
     */
    public function deveDispararExcecaoAoExcluirUmCartaoDeCredito(){
        $creditCardId = 1;
        $accountId = 1;
        $creditCardData = [
            'name'      => 'Bradesco',
            'due_day'  => 01,
            'close_day' => 26
        ];

        $user = $this->factoryUser(2);
        Auth::shouldReceive('user')->andReturn($user);

        $creditCardRepository = $this->createMock(CreditCardRepository::class);
        $accountRepository = $this->createMock(AccountRepository::class);
        $accountBusiness = new AccountBusiness($accountRepository);

        $creditCard = new CreditCard();
        $creditCard->fill($creditCardData);
        $creditCard->id = $creditCardId;

        $creditCards = $this->factoryCreditCards();
        $creditCardRepository
            ->method('getCreditCardById')
            ->with($creditCardId)
            ->willReturn($creditCards->get(0));

        $creditCardRepository
            ->method('deleteCreditCard')
            ->with($creditCardId)
            ->willReturn(true);
        $creditCardBusiness = new CreditCardBusiness($creditCardRepository,$accountBusiness);
        $this->expectException(ItemNotFoundException::class);
        $creditCard = $creditCardBusiness->removeCreditCard($creditCardId);

    }
}
