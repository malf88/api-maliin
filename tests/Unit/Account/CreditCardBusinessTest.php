<?php

namespace Tests\Unit\Account;

use App\Models\CreditCard;
use App\Modules\Account\Business\AccountBusiness;
use App\Modules\Account\Business\CreditCardBusiness;
use App\Modules\Account\Business\InvoiceBusiness;
use App\Modules\Account\Business\UserBusiness;
use App\Modules\Account\DTO\CreditCardDTO;
use App\Modules\Account\Jobs\CreateInvoice;
use App\Modules\Account\Repository\AccountRepository;
use App\Modules\Account\Repository\BillRepository;
use App\Modules\Account\Repository\CreditCardRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;
use Tests\Unit\Account\Factory\DataFactory;

class CreditCardBusinessTest extends TestCase
{
    public Collection $creditCardsList;
    private DataFactory $factory;
    private CreditCardRepository $creditCardRepository;
    private AccountRepository $accountRepository;
    private AccountBusiness $accountBusiness;
    private CreditCardBusiness $creditCardBusiness;
    private InvoiceBusiness $invoiceBusiness;
    private UserBusiness $userBusiness;
    private BillRepository $billRepository;
    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new DataFactory();

        $this->creditCardRepository = $this->createMock(CreditCardRepository::class);
        $this->accountRepository = $this->createMock(AccountRepository::class);
        $this->invoiceBusiness = $this->createMock(InvoiceBusiness::class);
        $this->userBusiness = $this->createMock(UserBusiness::class);
        $this->billRepository = $this->createMock(BillRepository::class);
    }
    public function prepareCreditCardRepository($creditCardId)
    {
        $creditCards = $this->factory->factoryCreditCards();
        $this->creditCardRepository
            ->method('getCreditCardById')
            ->with($creditCardId)
            ->willReturn($creditCards->get(0));
    }
    public function prepareAccountBusiness():AccountBusiness
    {
        $this->accountBusiness = new AccountBusiness($this->accountRepository, $this->userBusiness);
        return $this->accountBusiness;
    }
    public function prepareCreditCardBusiness():CreditCardBusiness
    {
        $this->creditCardBusiness = new CreditCardBusiness(
            $this->creditCardRepository,
            $this->invoiceBusiness,
            $this->billRepository
        );
        return $this->creditCardBusiness;
    }
    /**
     * @test
     */
    public function deveListarCartoesDeCreditoDeUmaConta(){
        $this->factory->configureUserSession();
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
        $this->factory->configureUserSession(true);
        $accountId = 2;
        $this->prepareAccountBusiness();

        $this->creditCardRepository
            ->method('getCreditCardsByAccountId')
            ->with($accountId)
            ->willReturn($this->factory->factoryCreditCards());
        $creditCardBusiness = $this->prepareCreditCardBusiness();
        $this->expectException(NotFoundHttpException::class);
        $creditCards = $creditCardBusiness->getListCreditCardByAccount($accountId);

    }
    /**
     * @test
     */
    public function deveRetornarUmCartaoDeCreditoComIdInformado(){
        $this->factory->configureUserSession();
        $accountId = 1;
        $creditCardId = 1;
        $this->prepareAccountBusiness();

        $creditCards = $this->factory->factoryCreditCards();
        $this->creditCardRepository
            ->method('getCreditCardById')
            ->with($creditCardId)
            ->willReturn($creditCards->get(0));
        $creditCardBusiness = $this->prepareCreditCardBusiness();
        $creditCard = $creditCardBusiness->getCreditCardById($creditCardId);

        $this->assertEquals($creditCardId,$creditCard->id);
    }
    /**
     * @test
     */
    public function deveRetornarUmaExcecaoAoRetornarUmCartaoDeCreditoComIdInformado(){
        $this->factory->configureUserSession(true);
       $creditCardId = 5;
        $this->prepareAccountBusiness();

        $creditCardBusiness = $this->prepareCreditCardBusiness();
        $this->expectException(NotFoundHttpException::class);
        $creditCard = $creditCardBusiness->getCreditCardById($creditCardId);

    }

    /**
     * @test
     */
    public function deveInserirUmCartaoDeCredito(){
        $this->factory->configureUserSession();
        $accountId = 1;
        $creditCardId = 1;

        $creditCardData = new CreditCardDTO([
            'name'      => 'Bradesco',
            'due_day'  => 01,
            'close_day' => 26
        ]);
        $this->prepareAccountBusiness();

        $creditCard = new CreditCardDTO([
            'name'      => 'Bradesco',
            'due_day'  => 01,
            'close_day' => 26
        ]);
        $creditCard->id = $creditCardId;

        $this->creditCardRepository
            ->method('saveCreditCard')
            ->willReturn($creditCard);

        $creditCardBusiness = $this->prepareCreditCardBusiness();
        $creditCard = $creditCardBusiness->insertCreditCard($accountId,$creditCardData);

        $this->assertEquals($creditCardId,$creditCard->id);

    }

    /**
     * @test
     */
    public function deveDispararExcecaoAoInserirUmCartaoDeCredito(){
        $this->factory->configureUserSession(true);
        $accountId = 2;
        $creditCardData = new CreditCardDTO([
            'name'      => 'Bradesco',
            'due_day'  => 01,
            'close_day' =>26
        ]);
        $this->prepareAccountBusiness();
        $creditCardBusiness = $this->prepareCreditCardBusiness();
        $this->expectException(NotFoundHttpException::class);
        $creditCard = $creditCardBusiness->insertCreditCard($accountId,$creditCardData);
    }

    /**
     * @test
     */
    public function deveAlterarUmCartaoDeCredito(){
        Queue::fake();
        DB::shouldReceive('beginTransaction')
            ->once();

        DB::shouldReceive('commit')
            ->once();

        $this->factory->configureUserSession();
        $creditCardId = 1;
        $accountId = 1;
        $creditCardData = new CreditCardDTO([
            'name'      => 'Bradesco',
            'due_day'  => 01,
            'close_day' => 26
        ]);

        $this->prepareAccountBusiness();
        $creditCard = new CreditCardDTO([
            'name'      => 'Bradesco',
            'due_day'  => 01,
            'close_day' => 26
        ]);
        $creditCard->id = $creditCardId;

        $creditCards = $this->factory->factoryCreditCards();
        $this->creditCardRepository
            ->method('getCreditCardById')
            ->with($creditCardId)
            ->willReturn($creditCards->get(0));

        $this->creditCardRepository
            ->method('updateCreditCard')
            ->willReturn($creditCard);

        $this->creditCardRepository
            ->expects($this->once())
            ->method('deleteInvoiceFromCreditCardId');

        $creditCardBusiness = $this->prepareCreditCardBusiness();
        $creditCard = $creditCardBusiness->updateCreditCard($creditCardId,$creditCardData);

        $this->assertEquals('Bradesco',$creditCard->name);
        $this->assertEquals(26,$creditCard->close_day);
        $this->assertEquals(01,$creditCard->due_day);
        Queue::assertPushed(CreateInvoice::class);
    }

    /**
     * @test
     */
    public function deveDispararExcecaoAoAlterarUmCartaoDeCredito(){
        Queue::fake();
        DB::shouldReceive('beginTransaction')
            ->once();

        DB::shouldReceive('rollBack')
            ->once();

        $this->factory->configureUserSession();
        $creditCardId = 1;
        $accountId = 1;
        $creditCardData = new CreditCardDTO([
            'name'      => 'Bradesco',
            'due_day'  => 01,
            'close_day' => 26
        ]);

        $this->prepareAccountBusiness();
        $creditCard = new CreditCardDTO([
            'name'      => 'Bradesco',
            'due_day'  => 01,
            'close_day' => 26
        ]);
        $creditCard->id = $creditCardId;

        $creditCards = $this->factory->factoryCreditCards();
        $this->creditCardRepository
            ->method('getCreditCardById')
            ->with($creditCardId)
            ->willReturn($creditCards->get(0));

        $this->creditCardRepository
            ->method('updateCreditCard')
            ->willReturn($creditCard);

        $this->creditCardRepository
            ->expects($this->once())
            ->method('deleteInvoiceFromCreditCardId')
            ->willThrowException(new \Exception());
        $this->expectException(\Exception::class);
        $creditCardBusiness = $this->prepareCreditCardBusiness();
        $creditCard = $creditCardBusiness->updateCreditCard($creditCardId,$creditCardData);
        Queue::assertNothingPushed();
    }
    /**
     * @test
     */
    public function deveDispararUmaExecaoAoAlterarUmCartaoDeCredito(){
        Queue::fake();
        DB::shouldReceive('beginTransaction')
            ->once();

        DB::shouldReceive('rollBack')
            ->once();
        $this->factory->configureUserSession(true);
        $creditCardId = 5;
        $creditCardData = new CreditCardDTO([
            'name'      => 'Bradesco',
            'due_day'  => 01,
            'close_day' => 26
        ]);

        $this->prepareAccountBusiness();

        $creditCardBusiness = $this->prepareCreditCardBusiness();
        $this->expectException(NotFoundHttpException::class);
        $creditCard = $creditCardBusiness->updateCreditCard($creditCardId,$creditCardData);
        Queue::assertNothingPushed();

    }

    /**
     * @test
     */
    public function deveExcluirUmCartaoDeCredito(){
        $this->factory->configureUserSession();
        $creditCardId = 1;
        $accountId = 1;
        $creditCardData = new CreditCardDTO([
            'name'      => 'Bradesco',
            'due_day'  => 01,
            'close_day' => 26
        ]);
        $this->prepareAccountBusiness();

        $creditCard = new CreditCardDTO([
            'name'      => 'Bradesco',
            'due_day'  => 01,
            'close_day' => 26
        ]);
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
        $this->factory->configureUserSession(true);
        $creditCardId = 5;
        $creditCardData = new CreditCardDTO([
            'name'      => 'Bradesco',
            'due_day'  => 01,
            'close_day' => 26
        ]);

        $this->prepareAccountBusiness();

        $creditCardBusiness = $this->prepareCreditCardBusiness();
        $this->expectException(NotFoundHttpException::class);
        $creditCard = $creditCardBusiness->removeCreditCard($creditCardId);

    }

    /**
     * @test
     */
    public function deveRetornarListaDeFaturasDoCartao(){
        $this->factory->configureUserSession();
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
            ->willReturn($creditCards->get(0)->invoices);
        $creditCardBusiness = $this->prepareCreditCardBusiness();
        $invoices = $creditCardBusiness->getInvoicesByCreditCard($creditCardId);
        $this->assertCount(2,$invoices);

    }
    /**
     * @test
     */
    public function deveRetornarUmaExcecaoAoBuscarListaDeFaturasDoCartao(){
        $this->factory->configureUserSession(true);
        $creditCardId = 5;
        $this->prepareAccountBusiness();
        $creditCardBusiness = $this->prepareCreditCardBusiness();
        $this->expectException(NotFoundHttpException::class);
        $invoices = $creditCardBusiness->getInvoicesByCreditCard($creditCardId);


    }

    /**
     * @test
     */
    public function deveRetornarListaFaturasComDeContasAPagarOuReceberPorCartaoDeCredito()
    {
        $this->factory->configureUserSession();
        $creditCardId = 1;
        $this->invoiceBusiness
            ->method('getInvoicesWithBill')
            ->willReturn($this->factory->factoryInvoiceList());
        $this->prepareCreditCardRepository($creditCardId);
        $this->creditCardRepository
            ->expects($this->once())
            ->method('getCreditCardById');

        $this->prepareAccountBusiness();
        $this->prepareCreditCardBusiness();
        $invoices = $this->creditCardBusiness->getInvoicesWithBillByCreditCard($creditCardId);

        $this->assertCount(3,$invoices->get(0)->bills);
    }
}
