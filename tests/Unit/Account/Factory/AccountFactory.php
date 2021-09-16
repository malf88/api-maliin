<?php

namespace Tests\Unit\Account\Factory;

use App\Models\Account;
use App\Models\CreditCard;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class AccountFactory extends TestCase
{
    public function factoryCreditCards(){
        $user = $this->factoryUser(1);
        $account = $this->createPartialMock(Account::class,['user']);
        $account
            ->method('user')
            ->willReturn($user);
        $account->user = $user;
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
}
