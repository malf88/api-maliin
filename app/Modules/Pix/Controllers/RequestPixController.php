<?php

namespace App\Modules\Pix\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Pix\Impl\Business\RequestPixBusinessInterface;
use Illuminate\Http\Request;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Group;

#[Group(prefix: '/api/pix')]
class RequestPixController extends Controller
{
    public function __construct(
        private readonly RequestPixBusinessInterface $requestPixBusiness
    )
    {
    }
    #[Get('teste')]
    public function teste(Request $request){
        return $this->requestPixBusiness->generateKeyPix([]);
    }
}
