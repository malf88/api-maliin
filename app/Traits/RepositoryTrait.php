<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait RepositoryTrait
{
    public function startTransaction()
    {
        DB::beginTransaction();
    }

    public function rollbackTransaction()
    {
        DB::rollBack();
    }

    public function commitTransaction()
    {
        DB::commit();
    }
}
