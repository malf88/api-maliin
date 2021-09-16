<?php

namespace Tests\Unit\Account;

use App\Models\CreditCard;
use App\Modules\Account\Business\AccountBusiness;
use App\Modules\Account\Business\CreditCardBusiness;
use App\Modules\Account\Repository\AccountRepository;
use App\Modules\Account\Repository\CreditCardRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ItemNotFoundException;
use Tests\TestCase;
use Tests\Unit\Account\Factory\AccountFactory;

class CreditCardBusinessTest extends TestCase
{
    public Collection $creditCardsList;
    private AccountFactory $factory;
    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new AccountFactory();
        $user = $this->factory->factoryUser(1);
        Auth::shouldReceive('user')->andReturn($user);
    }



    /**
     * @test
     */
    public function deveListarCartoesDeCreditoDeUmaConta(){
        $accountId = 1;
        $creditCardRepository = $this->createMock(CreditCardRepository::class);
        $accountRepository = $this->createMock(AccountRepository::class);
        $accountBusiness = new AccountBusiness($accountRepository);

        $creditCardRepository
            ->method('getCreditCardsByAccountId')
            ->with($accountId)
            ->willReturn($this->factory->factoryCreditCards());
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
        $creditCardRepository = $this->createMock(CreditCardRepository::class);
        $accountRepository = $this->createMock(AccountRepository::class);
        $accountBusiness = new AccountBusiness($accountRepository);

        $creditCardRepository
            ->method('getCreditCardsByAccountId')
            ->with($accountId)
            ->willReturn($this->factory->factoryCreditCards());
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

        $creditCardRepository = $this->createMock(CreditCardRepository::class);
        $accountRepository = $this->createMock(AccountRepository::class);
        $accountBusiness = new AccountBusiness($accountRepository);

        $creditCards = $this->factory->factoryCreditCards();
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
       $creditCardId = 5;
        $creditCardRepository = $this->createMock(CreditCardRepository::class);
        $accountRepository = $this->createMock(AccountRepository::class);
        $accountBusiness = new AccountBusiness($accountRepository);

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
        $creditCardData = [
            'name'      => 'Bradesco',
            'due_day'  => 01,
            'close_day' =>26
        ];
        $creditCardRepository = $this->createMock(CreditCardRepository::class);
        $accountRepository = $this->createMock(AccountRepository::class);
        $accountBusiness = new AccountBusiness($accountRepository);
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

        $creditCardRepository = $this->createMock(CreditCardRepository::class);
        $accountRepository = $this->createMock(AccountRepository::class);
        $accountBusiness = new AccountBusiness($accountRepository);

        $creditCard = new CreditCard();
        $creditCard->fill($creditCardData);
        $creditCard->id = $creditCardId;

        $creditCards = $this->factory->factoryCreditCards();
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
        $creditCardId = 5;
        $creditCardData = [
            'name'      => 'Bradesco',
            'due_day'  => 01,
            'close_day' => 26
        ];

        $creditCardRepository = $this->createMock(CreditCardRepository::class);
        $accountRepository = $this->createMock(AccountRepository::class);
        $accountBusiness = new AccountBusiness($accountRepository);

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
        $creditCardRepository = $this->createMock(CreditCardRepository::class);
        $accountRepository = $this->createMock(AccountRepository::class);
        $accountBusiness = new AccountBusiness($accountRepository);

        $creditCard = new CreditCard();
        $creditCard->fill($creditCardData);
        $creditCard->id = $creditCardId;

        $creditCards = $this->factory->factoryCreditCards();
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
        $creditCardId = 5;
        $creditCardData = [
            'name'      => 'Bradesco',
            'due_day'  => 01,
            'close_day' => 26
        ];

        $creditCardRepository = $this->createMock(CreditCardRepository::class);
        $accountRepository = $this->createMock(AccountRepository::class);
        $accountBusiness = new AccountBusiness($accountRepository);

        $creditCard = new CreditCard();
        $creditCard->fill($creditCardData);
        $creditCard->id = $creditCardId;

        $creditCardBusiness = new CreditCardBusiness($creditCardRepository,$accountBusiness);
        $this->expectException(ItemNotFoundException::class);
        $creditCard = $creditCardBusiness->removeCreditCard($creditCardId);

    }

    /**
     * @test
     */
    public function deveRetornarListaDeFaturasDoCartao(){
        $creditCardId = 1;
        $creditCardRepository = $this->createMock(CreditCardRepository::class);
        $accountRepository = $this->createMock(AccountRepository::class);
        $accountBusiness = new AccountBusiness($accountRepository);
        $creditCards = $this->factory->factoryCreditCards();
        $creditCardRepository
            ->method('getCreditCardById')
            ->with($creditCardId)
            ->willReturn($creditCards->get(0));

        $creditCardRepository
            ->method('getInvoicesByCreditCard')
            ->with($creditCardId)
            ->willReturn($creditCards->get(0)->invoices());
        $creditCardBusiness = new CreditCardBusiness($creditCardRepository,$accountBusiness);
        $invoices = $creditCardBusiness->getInvoicesByCreditCard($creditCardId);
        $this->assertCount(2,$invoices);

    }
    /**
     * @test
     */
    public function deveRetornarUmaExcecaoAoBuscarListaDeFaturasDoCartao(){
        $creditCardId = 5;
        $creditCardRepository = $this->createMock(CreditCardRepository::class);
        $accountRepository = $this->createMock(AccountRepository::class);
        $accountBusiness = new AccountBusiness($accountRepository);
        $creditCardBusiness = new CreditCardBusiness($creditCardRepository,$accountBusiness);
        $this->expectException(ItemNotFoundException::class);
        $invoices = $creditCardBusiness->getInvoicesByCreditCard($creditCardId);


    }
}
