<?php

namespace App\Modules\Pix\Impl\Business;

interface RequestPixBusinessInterface
{
    public function generateKeyPix(array $payload = []):mixed;
}
