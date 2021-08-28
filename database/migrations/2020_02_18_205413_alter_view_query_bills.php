<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterViewQueryBills extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::connection()->getPdo()->exec("DROP VIEW maliin.query_bills");
        DB::connection()->getPdo()->exec("CREATE
                                                VIEW maliin.query_bills
                                                AS

                                                SELECT
                                                    id::bigint,
                                                    date::date,
                                                    bill_parent_id,
                                                    description::varchar(255),
                                                    amount::numeric(9,2),
                                                    due_date::date,
                                                    pay_day::date,
                                                    account_id::bigint,
                                                    false::bool as is_credit_card,
                                                    null as month_reference,
                                                    null::bigint as credit_card_id,
                                                    category_id,
                                                    created_at,
                                                    updated_at,
                                                    portion
                                                FROM
                                                    maliin.bills
                                                WHERE
                                                    credit_card_id IS null AND
                                                    deleted_at IS NULL
                                                UNION
                                                SELECT
                                                    invoices.id::bigint,
                                                    start_date::date,
                                                    null,
                                                    concat('Cartão de crédito ',name)::varchar(255),
                                                    COALESCE((SELECT
                                                        SUM(amount)
                                                     FROM maliin.bills
                                                     WHERE
                                                        (bills.date BETWEEN invoices.start_date AND invoices.end_date)
                                                        AND bills.credit_card_id = invoices.credit_card_id
                                                        AND deleted_at IS NULL
                                                    ),0.00)::numeric(9,2),
                                                    due_date::date,
                                                    pay_day::date,
                                                    credit_cards.account_id::bigint,
                                                    true::bool,
                                                    month_reference,
                                                    credit_card_id::bigint,
                                                    null,
                                                    invoices.created_at,
                                                    invoices.updated_at,
                                                    null
                                                FROM
                                                    maliin.invoices
                                                    JOIN maliin.credit_cards ON credit_cards.id = invoices.credit_card_id
                                                WHERE
                                                    invoices.deleted_at IS NULL
                                                ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->getPdo()->exec("DROP VIEW maliin.query_bills");
    }
}
