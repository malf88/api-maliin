<?php

namespace Tests\Unit\Account\Factory;

use App\Models\Account;
use App\Models\Bill;
use App\Models\CreditCard;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class DataFactory extends TestCase
{
    public function factoryCreditCards(){
        $user = $this->factoryUser(1);
        $account = $this->createPartialMock(Account::class,['user']);
        $account
            ->method('user')
            ->willReturn($user);
        $account->user = $user;
        $account->id = 1;
        $invoice1 = new Invoice();
        $invoice1->end_date = Carbon::createFromFormat('d/m/Y','30/08/2021');
        $invoice1->start_date = Carbon::createFromFormat('d/m/Y','01/08/2021');
        $invoice1->due_date = Carbon::createFromFormat('d/m/Y','15/09/2021');
        $invoice1->month_reference = 8;

        $invoice2 = new Invoice();
        $invoice2->end_date = Carbon::createFromFormat('d/m/Y','30/07/2021');
        $invoice2->start_date = Carbon::createFromFormat('d/m/Y','01/07/2021');
        $invoice2->due_date = Carbon::createFromFormat('d/m/Y','06/08/2021');
        $invoice2->month_reference = 7;


        $creditCard1 = $this->createPartialMock(CreditCard::class,['account','invoices']);
        $creditCard1
            ->method('account')
            ->willReturn($account);
        $creditCard1
            ->method('invoices')
            ->willReturn(Collection::make([$invoice1,$invoice2]));

        $creditCard1->id = 1;
        $creditCard1->name = 'Nubank';
        $creditCard1->due_day = 07;
        $creditCard1->close_day = 30;
        $creditCard1->account = $account;
        $creditCard1->account_id = $account->id;

        $creditCard2 = new CreditCard();
        $creditCard2->id = 2;
        $creditCard2->name = 'Itaú';
        $creditCard2->due_day = 27;
        $creditCard2->close_day = 15;
        $creditCard2->account_id = $account->id;

        $creditCard3 = new CreditCard();
        $creditCard3->id = 3;
        $creditCard3->name = 'Caixa';
        $creditCard3->due_day = 13;
        $creditCard3->close_day = 06;
        $creditCard3->account_id = $account->id;

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

        $creditCard = new CreditCard();
        $creditCard->id = 1;
        $creditCard->name = 'Santander';
        $creditCard->due_day = 13;
        $creditCard->close_day = 06;

        $user = $this->createPartialMock(User::class,['accounts','creditCards']);
        $user
            ->method('accounts')
            ->willReturn(Collection::make([$account]));
        $user
            ->method('creditCards')
            ->willReturn(Collection::make([$creditCard]));
        $user->id = $id;

        return $user;
    }
    public function factoryAccount():Collection{
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
    public function factoryBill(int $id, float $amount, int $parentId = null,string $pay_day = null,int $portion=1):Bill
    {
        $bill = $this->createPartialMock(Bill::class,['load']);
        $bill
            ->method('load');
        $bill->description = "Mercado";
        $bill->id = $id;
        $bill->bill_parent_id = $parentId;
        $bill->pay_day = $pay_day;
        $bill->amount = $amount;
        $bill->portion = $portion;

        return $bill;

    }
    public function factoryInvoiceList()
    {
        $invoice1 = new Invoice();
        $invoice1->bills = Collection::make([
            $this->factoryBill(1,3.50),
            $this->factoryBill(2,3.30),
            $this->factoryBill(3,103.50)
        ]);

        $invoice2 = new Invoice();
        $invoice2->bills = Collection::make([
            $this->factoryBill(4,3.50),
            $this->factoryBill(5,300.30),
            $this->factoryBill(6,100.50)
        ]);
        return Collection::make([$invoice1,$invoice2]);
    }
}
