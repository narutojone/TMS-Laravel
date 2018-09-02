<?php

namespace App\Lib\Reports;

use Illuminate\Http\Request;
use App\Repositories\Report\Report;

class Reports
{
    private $report = null;
    private $reportInstance = null;
    private $requestData = null;

    public function __construct(Report $reportModel, Request $request)
    {
        $this->report = $reportModel;
        $this->reportInstance = ReportTypes::get($this->report->id);
        $this->requestData = $request->all();
    }

    public function render()
    {
        $data = $this->reportInstance->getReportData($this->requestData);
        $filters = $this->reportInstance->getFilterData($this->requestData);

        return view('reports.'.$this->report->key.'.index')->with([
            'data'    => $data,
            'report'  => $this->report,
            'filters' => $filters,
        ]);
    }

}