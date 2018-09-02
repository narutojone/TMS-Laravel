<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Repositories\Contract\ContractInterface;

class AltetTableContractsAddControlClient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->unsignedTinyInteger('control_client')->default(0)->after('shareholder_registry');
        });

        $contractRepository = app()->make(ContractInterface::class);
        $contracts = $contractRepository->all();

        foreach($contracts as $contract) {
            $controlClient = 0;

            if($contract->bank_reconciliation == 0 && $contract->bookkeeping == 0) {
                $controlClient = 1;
            }

            $contract->update([
                'control_client' => $controlClient,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
