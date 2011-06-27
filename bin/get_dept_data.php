<?php

include 'config.php';


$client = new webservice_client();

$client->uid = "pkeane";
$client->pwd = "4R153v1";
$client->url = "http://wwwtest.utexas.edu/cola/_webservices/colaweb/offices";
$client->http_verb = 'GET';

echo $client->make_request();


class webservice_client {

	public $uid;
	public $pwd;
	public $url;
	public $http_verb;
	public $arr_vals = array();

	function make_request() {

		$ch = curl_init();

		if (substr($this->url, -1)=="/") $this->url=substr($this->url,0,-1); // TRIM ANY TRAILING SLASH FROM URL

		switch($this->http_verb) {

		case "GET":

			foreach ($this->arr_vals as $key=>$val) {
				$this->url.="/$key/$val";
			}

			break;

		case "POST":

			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->arr_vals);

			break;

		default:

			foreach ($this->arr_vals as $key=>$val) {
				$this->url.="/$key/$val";
			}

			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->http_verb);

		}


		// CONSTRUCT AUTH TOKEN
		$request_time = gmdate("D, d M Y H:i:s T");
		$auth_token = md5($this->pwd.$this->http_verb.$request_time);
		$arr_headers = array("x-colaweb-date: $request_time","Authorization: $this->uid $auth_token");
		curl_setopt($ch, CURLOPT_HTTPHEADER, $arr_headers);

		// GET A RESPONSE BACK
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		curl_setopt($ch, CURLOPT_URL, $this->url);
		$response = curl_exec($ch);
		return $response;


	}

}






