<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

use App\Repositories\Client\ClientInterface;
use App\Repositories\Contract\ContractInterface;
use App\Repositories\Contract\Contract;
use App\Repositories\ContractSalaryDay\ContractSalaryDayInterface;
use App\Repositories\ContractSalaryDay\ContractSalaryDay;

class ImportClientContracts extends Migration
{
    private $mvaTypesMapping = [
        'yearly' => Contract::MVA_TYPE_YEARLY,
        'term'   => Contract::MVA_TYPE_TERM,
    ];

    private $salaryDayMapping = [
        '29'   => ContractSalaryDay::DAY_END,
        '30'   => ContractSalaryDay::DAY_MAN,
    ];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $clientRepository = app()->make(ClientInterface::class);
        $contractRepository = app()->make(ContractInterface::class);
        $contractSalaryDayRepository = app()->make(ContractSalaryDayInterface::class);

        $errorLog = [];

        // Get all companies from api portal
        // We are only interested in fetching organization number because this is the unique identifier.
        $apiPortalCompanies = DB::table('API_companies')->get(['id', 'organization_number'])->pluck('organization_number', 'id');

        foreach ($apiPortalCompanies as $apiPortalCompanyId => $organizationNumber) {
            $tmsClient = $clientRepository->model()->where('organization_number', $organizationNumber)->first();
            if(!$tmsClient) {
                $errorLog['missing_org_number'][] = $organizationNumber;
                continue;
            }

            // Get all contracts for this client from api portal
            $apiPortalContracts = DB::table('API_company_contracts')->where('company_id', $apiPortalCompanyId)->orderBy('created_at', 'ASC')->get();
            foreach($apiPortalContracts as $apiPortalContract) {
                // Prepare new contract data
                $tmsContractData = [
                    'client_id'                 => $tmsClient->id,
                    'active'                    => $apiPortalContract->active,
                    'start_date'                => Carbon::parse($apiPortalContract->start_date),
                    'end_date'                  => $apiPortalContract->active ? null : Carbon::parse($apiPortalContract->end_date),
                    'one_time'                  => $apiPortalContract->one_time,
                    'under_50_bills'            => $apiPortalContract->under_50,
                    'shareholder_registry'      => $apiPortalContract->shareholder_registry,
                    'bank_reconciliation'       => $apiPortalContract->bank_reconciliation,
                    'bank_reconciliation_date'  => Carbon::parse($apiPortalContract->bank_reconciliation_date),
                    'bookkeeping'               => $apiPortalContract->bookkeeping,
                    'bookkeeping_date'          => Carbon::parse($apiPortalContract->bookkeeping_date),
                    'mva'                       => $apiPortalContract->mva,
                    'mva_type'                  => $this->castMvaType($apiPortalContract->mva_type),
                    'financial_statements'      => $apiPortalContract->financial_statements,
                    'financial_statements_year' => $apiPortalContract->financial_statements_year,
                    'salary_check'              => $apiPortalContract->salary_check,
                    'salary'                    => $apiPortalContract->salary,
                    'created_by'                => 30,
                ];



                try {
                    // Create the contract
                    $newContract = $contractRepository->model()->create($tmsContractData);

                    // Create salary days if needed
                    if((int)$apiPortalContract->salary == 1) {
                        $apiPortalSalaryDays = DB::table('API_contracts_salary_days')->where('contract_id', $apiPortalContract->id)->orderBy('day', 'ASC')->get(['day'])->pluck('day');
                        foreach($apiPortalSalaryDays as $apiPortalSalaryDay) {
                            $tmsSalaryDay = $this->castSalaryDay($apiPortalSalaryDay);
                            if($tmsSalaryDay !== false) {
                                $contractSalaryDayRepository->create([
                                    'contract_id' => $newContract->id,
                                    'day'         => $tmsSalaryDay,
                                ]);
                            }
                        }
                    }
                }
                catch (\Exception $e) {
                    $errorLog['contract-failed'][] = $tmsContractData;
                }
            }
        }

        // dd($errorLog);
    }

    /**
     * Cast API Portal MVA types to TMS mva type (cast from string to integer)
     *
     * @param string $apiPortalMvaType
     * @return int|null
     */
    protected function castMvaType(string $apiPortalMvaType)
    {
        $apiPortalMvaType = strtolower($apiPortalMvaType);

        if(isset($this->mvaTypesMapping[$apiPortalMvaType])) {
            return $this->mvaTypesMapping[$apiPortalMvaType];
        }

        return null;
    }

    /**
     * Cast API Portal salary days
     *
     * @param int $apiPortalSalaryDay
     * @return int|bool
     */
    protected function castSalaryDay(int $apiPortalSalaryDay)
    {
        if($apiPortalSalaryDay < 29) {
            return $apiPortalSalaryDay;
        }
        else {
            if(isset($this->salaryDayMapping[$apiPortalSalaryDay])) {
                return $this->salaryDayMapping[$apiPortalSalaryDay];
            }
        }

        return false;
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
