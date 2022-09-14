<?php

namespace App\Modules\Account\Jobs;

use App\Models\CreditCard;
use App\Models\User;
use App\Modules\Account\DTO\CreditCardDTO;
use App\Modules\Account\Impl\Business\CreditCardBusinessInterface;
use App\Modules\Account\Mail\ShareEmail;
use App\Traits\RepositoryTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CreateInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RepositoryTrait;
    public $timeout = (60*60)*24;
    public $tries = 10;
    const QUEUE_NAME = 'invoice';
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        private readonly CreditCardDTO $creditCard,
        private readonly CreditCardBusinessInterface $creditCardBusiness
    )
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            Auth::setUser($this->creditCard->account->user);
            $this->creditCardBusiness->regenerateInvoicesByCreditCard($this->creditCard->id);
            Log::debug('Invoices created');
        }catch (\Exception $e){
            $this->rollbackTransaction();
            Log::error($e);
            throw $e;
        }
    }
}
