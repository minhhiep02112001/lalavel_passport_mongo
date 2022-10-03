<?php

namespace App\Models;

use Illuminate\Support\Facades\Http;

class Contact
{
    protected $domain;
    protected $token;
    function __construct()
    {
        $this->domain = env('API_MICROSERVICE_URL');
        $this->token = env('API_MICROSERVICE_TOKEN');
    }
    public function create($data){
        $url = $this->domain."/crm/contacts/ebombid";
        try {
            $data = array_filter($data);
            $response = Http::withToken($this->token)->post($url, $data);

            if ($response->successful()) {
                return $response->json();
            }
            \Log::error($response->body());
            return [];
        } catch (\Exception $ex) {
            return false;
        }
    }
}
