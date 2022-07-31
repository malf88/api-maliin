<?php

namespace App\Modules\Pix\Business;

use App\Modules\Pix\BankAPI\NubankApi;
use App\Modules\Pix\Impl\Business\RequestPixBusinessInterface;
use Piggly\Pix\Parser;
use Piggly\Pix\Reader;
use Piggly\Pix\StaticPayload;

class RequestPixBusiness implements RequestPixBusinessInterface
{
    public function generateKeyPix(array $payload = []):mixed
    {

        $payload =
            (new StaticPayload())
                ->applyValidCharacters()
                //->applyUppercase()
                ->setPixKey(Parser::KEY_TYPE_DOCUMENT, 'CPF')
                ->setMerchantName('Nome do cidadao')
                ->setMerchantCity('Cidade do cidadao')
                ->setAmount(30.00)
                ->setTid(null)
                ->setDescription('');

//        $nubankApi = NubankApi::use(
//
//        )
        dd($payload->getPixCode());


    }
}
