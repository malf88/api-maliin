<?php

namespace App\Modules\Account\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Account\Services\AccountService;
use App\Modules\Account\Services\AccountServiceLocal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function show(Request $request, int $id)
    {
        return $this->accountServices->getAccountById($id);
    }
    public function insert(Request $request)
    {
        return $this->accountServices->insertAccount(Auth::user(),$request->all());
    }

    public function update(Request $request,int $id)
    {
        return $this->accountServices->updateAccount($id,$request->all());
    }

    public function delete(Request $request,int $id)
    {
        return $this->accountServices->deleteAccount($id);
    }
}
