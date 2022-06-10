<?php

namespace App\Modules\Account\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Account\Impl\Business\AccountBusinessInterface;
use App\Modules\Account\ServicesLocal\AccountServiceLocal;
use App\VersionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UtilController extends Controller
{

    /**
     * @OA\Get(
     *     tags={"Util"},
     *     summary="Retorna versão da api",
     *     description="Retorna a versão da api",
     *     path="/version",
     *     @OA\Response(
     *          response="200",
     *          description="Uma string contendo a versão da api"
     *     ),
     * )
     *
     */
    public function index(Request $request)
    {
        return VersionHelper::version();
    }

}
