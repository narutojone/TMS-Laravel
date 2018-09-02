<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Repositories\Contract\ContractInterface;
use App\Repositories\Contract\Contract;

class AlterContractsAddExtraFieds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('bank_reconciliation_frequency', 20)->nullable()->after('bank_reconciliation_date');
            $table->string('bookkeeping_frequency_1', 20)->nullable()->after('bookkeeping_date');
            $table->string('bookkeeping_frequency_2', 20)->nullable()->after('bookkeeping_frequency_1');
        });


        $contractsRepository = app()->make(ContractInterface::class);
        $contracts = $contractsRepository->all();

        foreach($contracts as $contract) {

            if($contract->mva_type ==  Contract::MVA_TYPE_TERM) {
                $brf = '2 months 10';
                $bf2 = '2 months 10';
            }
            else {
                $brf = '4 months 10';
                $bf2 = '4 months 10';
            }

            $contract->update([
                'bank_reconciliation_frequency' => $brf,
                'bookkeeping_frequency_1'       => '1 months 15',
                'bookkeeping_frequency_2'       => $bf2,
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
