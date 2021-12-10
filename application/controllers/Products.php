<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Products extends REST_Controller{

	function __construct($config = 'rest'){
		parent::__construct($config);
			$this->load->driver('cache', array('adapter' => 'apc','backup' => 'file'));
	}

	//Menampilkan data
	public function index_get(){
		$id = $this->get('id');
		$products=[];
		if ($id == '') {
			$data = $this->db->get('products')->result();
			foreach($data as $row => $key): 
				$products[]=[
						"productCode"=>$key->productCode,
						"productName"=>$key->productName,
						"_links"=>[	(object)["href"=>"productlines/{$key->productLine}",
										"rel"=>"productlines",
										"type"=>"GET"],
									(object)["href"=>"orderdetails/{$key->productCode}",
										"rel"=>"orderdetails",
										"type"=>"GET"]],
						"productScale"=>$key->productScale,
						"productVendor"=>$key->productVendor,
						"productDescription"=>$key->productDescription,
						"quantityInStock"=>$key->quantityInStock,
						"buyPrice"=>$key->buyPrice,
						"MSRP"=>$key->MSRP
					];		
			endforeach;
			$result = [
					"took"=>$_SERVER["REQUEST_TIME_FLOAT"],
					"code"=>200,
					"message"=>"Response successfully",
					"data"=>$products
				];
			$this->response($result, 200);
		} else {
			$this->db->where('productCode', $id);
			$data = $this->db->get('products')->result();
			$products=[	"productCode" => $data[0]->productCode,
						"productName" => $data[0]->productName,
						"_links"=>[	(object)["href"=>"productlines/{$data[0]->productLine}",
										"rel"=>"productlines",
										"type"=>"GET"],
									(object)["href"=>"orderdetails/{$data[0]->productCode}",
										"rel"=>"orderdetails",
										"type"=>"GET"]],
						"productScale"=>$data[0]->productScale,
						"productVendor"=>$data[0]->productVendor,
						"productDescription"=>$data[0]->productDescription,
						"quantityInStock"=>$data[0]->quantityInStock,
						"buyPrice"=>$data[0]->buyPrice,
						"MSRP"=>$data[0]->MSRP
					];
			$etag = hash('sha256', $data[0]->LastUpdate);
			$this->cache->save($etag, $products, 300);
			$this->output->set_header('ETag:' .$etag);
			$this->output->set_header('Cache-Control: must-revalidate');
			if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag){
				$this->output->set_header('HTTP/1.1 304 Not Modified');
			}else{
				$result = [
					"took"=>$_SERVER["REQUEST_TIME_FLOAT"],
					"code"=>200,
					"message"=>"Response successfully",
					"data"=>$products
				];
				$this->response($result, 200);
			}
		}
	}
}	
?>