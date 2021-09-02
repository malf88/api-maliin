<?php

namespace App\Modules\Account\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Account\Services\AccountService;
use App\Modules\Account\Services\AccountServiceLocal;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    private AccountServiceLocal $accountServices;
    public function __construct(AccountServiceLocal $accountServices)
    {
        $this->accountServices = $accountServices;
    }
    public function index(Request $request)
    {
        return $this->accountServices->getListAllAccountFromLoggedUser();
    }
}
