<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Products extends REST_Controller{

	function __construct($config = 'rest'){
		parent::__construct($config);
	}

	//Menampilkan data
	public function index_get(){

		$id = $this->get('id');
		$products=[];
		if ($id == '') {
			$data = $this->db->get('products')->result();
			foreach ($data as $row => $key): 
				$products[]=[
						"productCode"=>$key->productCode,
						"productName"=>$key->productName,
						"_links"=>[(object)[
							"href"=>"orders/($key->orderNumber)",
							"rel"=>"orders",
							"type"=>"GET"],
							(object)[
								"href"=>"orderdetails/($key->orderNumber)",
								"rel"=>"orderdetails",
								"type"=>"GET"]],
						"productLines"=>$key->productLines,
						"productVendor"=>$key->productVendor,
						"quantityInStock"=>$key->UnitPrice,
						"buyPrice"=>$key->buyPrice,
					];		
			endforeach;
		}else{
			$this->db->where('customerNumber, $id');
			$data = $this->db->get('products')->result();
		}
		$result = [
			"took"=>$_SERVER["REQUEST_TIME_FLOAT"],
			"code"=>200,
			"message"=>"Response successfully",
			"data"=>$products
		];
		$this->response($result, 200);
	}
}
?>