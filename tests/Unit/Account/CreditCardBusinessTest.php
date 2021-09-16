<?php

namespace Tests\Unit\Account;

use App\Models\CreditCard;
use App\Modules\Account\Business\AccountBusiness;
use App\Modules\Account\Business\CreditCardBusiness;
use App\Modules\Account\Business\InvoiceBusiness;
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
    private CreditCardRepository $creditCardRepository;
    private AccountRepository $accountRepository;
    private AccountBusiness $accountBusiness;
    private CreditCardBusiness $creditCardBusiness;
    private InvoiceBusiness $invoiceBusiness;
    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new AccountFactory();
        $user = $this->factory->factoryUser(1);
        Auth::shouldReceive('user')->andReturn($user);

        $this->creditCardRepository = $this->createMock(CreditCardRepository::class);
        $this->accountRepository = $this->createMock(AccountRepository::class);
        $this->invoiceBusiness = $this->createMock(InvoiceBusiness::class);
    }
    public function prepareAccountBusiness():AccountBusiness
    {
        $this->accountBusiness = new AccountBusiness($this->accountRepository);
        return $this->accountBusiness;
    }
    public function prepareCreditCardBusiness():CreditCardBusiness
    {
        $this->creditCardBusiness = new CreditCardBusiness(
            $this->creditCardRepository,
            $this->accountBusiness,
            $this->invoiceBusiness);
        return $this->creditCardBusiness;
    }
    /**
     * @test
     */
    public function deveListarCartoesDeCreditoDeUmaConta(){
        $accountId = 1;
        $this->prepareAccountBusiness();
        $this->creditCardRepository
            ->method('getCreditCardsByAccountId')
            ->with($accountId)
            ->willReturn($this->factory->factoryCreditCards());
        $this->prepareCreditCardBusiness();
        $creditCards = $this->creditCardBusiness->getListCreditCardByAccount($accountId);

        $this->assertIsIterable($creditCards);
        $this->assertCount(4,$creditCards);
    }
    /**
     * @test
     */
    public function deveDispararExcecaoAoListarCartoesDeCreditoDeUmaConta(){
        $accountId = 2;
        $this->prepareAccountBusiness();

        $this->creditCardRepository
            ->method('getCreditCardsByAccountId')
            ->with($accountId)
            ->willReturn($this->factory->factoryCreditCards());
        $creditCardBusiness = $this->prepareCreditCardBusiness();
        $this->expectException(ItemNotFoundException::class);
        $creditCards = $creditCardBusiness->getListCreditCardByAccount($accountId);

    }
    /**
     * @test
     */
    public function deveRetornarUmCartaoDeCreditoComIdInformado(){
        $accountId = 1;
        $creditCardId = 1;
        $this->prepareAccountBusiness();

        $creditCards = $this->factory->factoryCreditCards();
        $this->creditCardRepository
            ->method('getCreditCardById')
            ->with($creditCardId)
            ->willReturn($creditCards->find($creditCardId));
        $creditCardBusiness = $this->prepareCreditCardBusiness();
        $creditCard = $creditCardBusiness->getCreditCardById($creditCardId);

        $this->assertEquals($creditCardId,$creditCard->id);
    }
    /**
     * @test
     */
    public function deveRetornarUmaExcecaoAoRetornarUmCartaoDeCreditoComIdInformado(){
       $creditCardId = 5;
        $this->prepareAccountBusiness();

        $creditCardBusiness = $this->prepareCreditCardBusiness();
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
        $this->prepareAccountBusiness();

        $creditCard = new CreditCard();
        $creditCard->fill($creditCardData);
        $creditCard->id = $creditCardId;

        $this->creditCardRepository
            ->method('saveCreditCard')
            ->with($accountId,$creditCardData)
            ->willReturn($creditCard);
        $creditCardBusiness = $this->prepareCreditCardBusiness();
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
        $this->prepareAccountBusiness();
        $creditCardBusiness = $this->prepareCreditCardBusiness();
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

        $this->prepareAccountBusiness();
        $creditCard = new CreditCard();
        $creditCard->fill($creditCardData);
        $creditCard->id = $creditCardId;

        $creditCards = $this->factory->factoryCreditCards();
        $this->creditCardRepository
            ->method('getCreditCardById')
            ->with($creditCardId)
            ->willReturn($creditCards->get(0));

        $this->creditCardRepository
            ->method('updateCreditCard')
            ->with($creditCardId,$creditCardData)
            ->willReturn($creditCard);
        $creditCardBusiness = $this->prepareCreditCardBusiness();
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

        $this->prepareAccountBusiness();

        $creditCardBusiness = $this->prepareCreditCardBusiness();
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
        $this->prepareAccountBusiness();

        $creditCard = new CreditCard();
        $creditCard->fill($creditCardData);
        $creditCard->id = $creditCardId;

        $creditCards = $this->factory->factoryCreditCards();
        $this->creditCardRepository
            ->method('getCreditCardById')
            ->with($creditCardId)
            ->willReturn($creditCards->get(0));

        $this->creditCardRepository
            ->method('deleteCreditCard')
            ->with($creditCardId)
            ->willReturn(true);
        $creditCardBusiness = $this->prepareCreditCardBusiness();
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

        $this->prepareAccountBusiness();

        $creditCard = new CreditCard();
        $creditCard->fill($creditCardData);
        $creditCard->id = $creditCardId;

        $creditCardBusiness = $this->prepareCreditCardBusiness();
        $this->expectException(ItemNotFoundException::class);
        $creditCard = $creditCardBusiness->removeCreditCard($creditCardId);

    }

    /**
     * @test
     */
    public function deveRetornarListaDeFaturasDoCartao(){
        $creditCardId = 1;
        $this->prepareAccountBusiness();
        $creditCards = $this->factory->factoryCreditCards();
        $this->creditCardRepository
            ->method('getCreditCardById')
            ->with($creditCardId)
            ->willReturn($creditCards->get(0));

        $this->creditCardRepository
            ->method('getInvoicesByCreditCard')
            ->with($creditCardId)
            ->willReturn($creditCards->get(0)->invoices());
        $creditCardBusiness = $this->prepareCreditCardBusiness();
        $invoices = $creditCardBusiness->getInvoicesByCreditCard($creditCardId);
        $this->assertCount(2,$invoices);

    }
    /**
     * @test
     */
    public function deveRetornarUmaExcecaoAoBuscarListaDeFaturasDoCartao(){
        $creditCardId = 5;
        $this->prepareAccountBusiness();
        $creditCardBusiness = $this->prepareCreditCardBusiness();
        $this->expectException(ItemNotFoundException::class);
        $invoices = $creditCardBusiness->getInvoicesByCreditCard($creditCardId);


    }
}
