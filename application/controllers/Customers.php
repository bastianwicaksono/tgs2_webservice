<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
require "vendor\autoload.php";

use Restserver\Libraries\REST_Controller;
use \Firebase\JWT\JWT;

class Customers extends REST_Controller{

	function __construct($config = 'rest'){
		parent::__construct($config);
	}

	//Menampilkan data
	public function index_get(){
		$authHeader = $this->input->get_request_header('Authorization');
		$arr = explode(" ", $authHeader);
		$jwt = isset($arr[1])? $arr[1]: "";
		$secretkey = base64_encode("susah");

		if($jwt){
			try {
				$decode = JWT::decode($jwt, $secretkey, array('HS256'));
				$id = $this->get('id');
				if ($id == '') {
					$data = $this->db->get('customers')->result();
				}else{
					$this->db->where("customerNumber", $id);
					$data = $this->db->get('customers')->result();
				}
				$result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
						"code"=>200,
						"message"=>"Response successfully",
						"data"=>$data];	
				$this->response($result, 200);
			}catch (Exception $e){
					$result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
						"code"=>401,
						"message"=>"Access denied",
						"data"=>null];	
					$this->response($result, 401);
			}
		}else{
			$result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
				"code"=>402,
				"message"=>"Access denied",
				"data"=>null];	
			$this->response($result, 402);
		}
	}

	//Menambah data
	public function index_post(){
		$data = array(
					'customerNumber' => $this->post('customerNumber'), 
					'customerName' => $this->post('customerName'),
					'contactLastName' => $this->post('contactLastName'),
					'contactFirstName' => $this->post('contactFirstName'),
					'phone' => $this->post('phone'),
					'addressLine1' => $this->post('addressLine1'),
					'addressLine2' => $this->post('addressLine2'),
					'city' => $this->post('city'),
					'state' => $this->post('state'),
					'postalCode' => $this->post('postalCode'),
					'country' => $this->post('country'),
					'salesRepEmployeeNumber' => $this->post('salesRepEmployeeNumber'),
					'creditLimit' => $this->post('creditLimit')
				);
		$insert = $this->db->insert('customers', $data);
		if ($insert) {
			//this->response($data, 200);
			$result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
				"code"=>201,
				"message"=>"Data added",
				"data"=>$data];
			$this->response($result, 201);
		}else{
			$result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
				"code"=>502,
				"message"=>"Failed adding data",
				"data"=>null];
			$this->response($data, 502);
		}
	}

	//Merubah data
	public function index_put(){
		$id = $this->put('customerNumber');
		$data = array(
					'customerNumber' => $this->put('customerNumber'), 
					'customerName' => $this->put('customerName'),
					'contactLastName' => $this->put('contactLastName'),
					'contactFirstName' => $this->put('contactFirstName'),
					'phone' => $this->put('phone'),
					'addressLine1' => $this->put('addressLine1'),
					'addressLine2' => $this->put('addressLine2'),
					'city' => $this->put('city'),
					'state' => $this->put('state'),
					'postalCode' => $this->put('postalCode'),
					'country' => $this->put('country'),
					'salesRepEmployeeNumber' => $this->put('salesRepEmployeeNumber'),
					'creditLimit' => $this->put('creditLimit'));
		$this->db->where('customerNumber', $id);
		$update = $this->db->update('customers', $data);
		if ($update) {
			$result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
				"code"=>200,
				"message"=>"Data Updated",
				"data"=>$data];
			$this->response($result, 200);
		}else{
			$this->response(array('status' => 'fail', 502));
		}
	}

	//Menghapus data
	public function index_delete(){
		$id = $this->delete('customerNumber');
		$this->db->where('customerNumber', $id);
		$delete = $this->db->delete('customers');
		if ($delete) {
			$this->response(array('status' => 'success'), 201);
		}else{
			$this->response(array('status' => 'fail', 502));
		}
	}
}
?>