<?php

namespace App\Modules\Account\Mail;

use App\Models\Account;
use App\Models\User;
use App\Modules\Account\Business\UserBusiness;
use App\Modules\Account\Impl\Business\AccountBusinessInterface;
use App\Modules\Account\Impl\Business\UserBusinessInterface;
use App\Modules\Account\Repository\UserRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use phpDocumentor\Reflection\Project;

class ShareEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $accountId = 0;
    public $userId = 0;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        int $account,
        int $user
    )
    {
        $this->userId = $user;
        $this->accountId = $account;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $user = User::find($this->userId);
        $account = Account::find($this->accountId);
        return $this->subject('Compartilhamento de projeto financeiro')->view('emails.share', ['account' => $account,'user' => $user]);
    }
}
