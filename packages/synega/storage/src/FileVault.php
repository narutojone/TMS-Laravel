<?php

namespace Synega\Storage;

use Ixudra\Curl\Facades\Curl;

class FileVault {
	private $headers;
	private $baseUrl;
    private $apiRoutes = [
        'store'    => 'files',
        'download' => 'files/download/{id}/removeThis',
        'delete'   => 'files/{id}',
        'update'   => 'files/{id}/update',
        'move'     => 'files/{id}/move',
    ];

	public function __construct()
	{
		$this->baseUrl = env('FILEVAULT_BASE_URL', '');
		$this->headers = [
			'Authorization: Basic '. base64_encode(env('FILEVAULT_USER', '').':'.env('FILEVAULT_PASSWORD'))
		];
	}

	public function store($clientId, $file, $filePath, $fileName='')
	{
        $url = $this->buildUrl('store');
        $postFields = [
            'file'		=> curl_file_create($file),
            'file_path'	=> $filePath,
            'file_name'	=> $fileName,
            'client_id'	=> $clientId,
        ];

        $response = Curl::to($url)
            ->withHeaders($this->headers)
            ->withData($postFields)
            ->containsFile()
            ->returnResponseObject()
            ->post();

        if($response->status == 200) {
            $returnValue = json_decode($response->content);
            return $returnValue->id;
        }

        return false;
	}

	public function publicUrl($id) {
		return route('filevault.url', $id);
	}

	public function buildUrl($routeName, $params = []) {
		$url = $this->baseUrl . $this->apiRoutes[$routeName];
		foreach($params as $paramKey => $paramValue) {
			$url = str_replace('{'.$paramKey.'}', $paramValue, $url);
		}
		return $url;
	}

	public function download($id)
	{
		$url = $this->buildUrl('download', ['id'=>$id]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
		$result=curl_exec ($ch);

		return [
		    'data'         => $result,
            'Content-Type' => curl_getinfo($ch, CURLINFO_CONTENT_TYPE),
        ];
	}

	public function delete($id)
	{
		$url = $this->buildUrl('delete', ['id'=>$id]);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

		$result = curl_exec ($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if($httpCode == 200) {
			return true;
		}

		return false;
	}

	public function update($fileId, $clientId, $file, $filePath, $fileName='')
    {
        $url = $this->buildUrl('update', ['id'=>$fileId]);
        $postFields = [
            'file'		=> curl_file_create($file),
            'file_path'	=> $filePath,
            'file_name'	=> $fileName,
            'client_id'	=> $clientId,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $result=curl_exec ($ch);
        $returnValue = json_decode($result);

        if(!isset($returnValue->id)) {
            return false;
        }

        return true;
    }

    public function move($fileId, $newPath)
    {
        $url = $this->buildUrl('move', ['id'=>$fileId]);
        $postFields = [
            'file_path'	=> $newPath,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_exec ($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($httpCode == 200) {
            return true;
        }

        return false;

    }
} 