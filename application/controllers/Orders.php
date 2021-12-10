	<?php 
	defined('BASEPATH') OR exit('No direct script access allowed');

	require APPPATH . '/libraries/REST_Controller.php';
	use Restserver\Libraries\REST_Controller;

	class Orders extends REST_Controller{

		function __construct($config = 'rest'){
			parent::__construct($config);
			$this->load->driver('cache', array('adapter' => 'apc','backup' => 'file'));
		}

		//Menampilkan data
		public function index_get(){

			$id = $this->get('id');
			$orders=[];
			if ($id == '') {
				$data = $this->db->get('orderdetails')->result();
				foreach ($data as $row => $key): 
					$orders[]=[
							"orderNumber"=>$key->orderNumber,
							"_links"=>[(object)[
								"href"=>"orders/($key->orderNumber)",
								"rel"=>"orders",
								"type"=>"GET"],
								(object)[
									"href"=>"products/($key->productCode)",
									"rel"=>"products",
									"type"=>"GET"]],
							"quantityOrdered"=>$key->quantityOrdered,
							"priceEach"=>$key->priceEach,
							"orderLineNumber"=>$key->orderLineNumber
						];
				endforeach;

				$etag = hash('sha256', time());
				$this->cache->save($etag, $orders, 300);
				$this->output->set_header('ETag:'.$etag);
				$this->output->set_header('Cache-Control: must-revalidate');
				if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag){
					$this->output->set_header('HTTP/1.1 304 Not Modified');
				}else{
					$result = [
						"took"=>$_SERVER["REQUEST_TIME_FLOAT"],
						"code"=>200,
						"message"=>"Response successfully",
						"data"=>$orders
					];
					$this->response($result, 200);
				}

			}else{
				$this->db->where('orderNumber', $id);
				$data = $this->db->get('orderdetails')->result();
				foreach ($data as $row => $data[0]) {
						$orders[]=[
						"orderNumber"=>$data[0]->orderNumber,
						"_links"=>[(object)["href"=>"orders/{$data[0]->orderNumber}",
											"rel"=>"orders",
											"type"=>"GET"],
									(object)["href"=>"products/{$data[0]->productCode}",
											"rel"=>"products",
											"type"=>"GET"]],
						"quantityOrdered"=>$data[0]->quantityOrdered,
						"priceEach"=>$data[0]->priceEach,
						"orderLineNumber"=>$data[0]->orderLineNumber
					];		
				}

				$etag = hash('sha256', $data[0]->orderNumber);
				$this->cache->save($etag, $orders, 300);
				$this->output->set_header('ETag:'.$etag);
				$this->output->set_header('Cache-Control: must-revalidate');
				if(isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag){
					$this->output->set_header('HTTP/1.1 304 Not Modified');
				}else{
					$result = [
						"took"=>$_SERVER["REQUEST_TIME_FLOAT"],
						"code"=>200,
						"message"=>"Response successfully",
						"data"=>$orders];
					$this->response($result, 200);
				}
		}

	}

		//Menambah data
		public function index_post(){
			$data = array(
						'orderNumber' => $this->post('orderNumber'), 
						'productCode'=> $this->post('productCode'),
						'quantityOrdered'=> $this->post('quantityOrdered'),
						'priceEach'=> $this->post('priceEach'),
						'orderLineNumber'=> $this->post('orderLineNumber')
					);
			$this->db->where("productCode", $this->post('productCode'));
			$this->db->where("quantityOrdered", $this->post('quantityOrdered'));
			$check = $this->db->get('orderdetails')->num_rows();
			if ($check==0):
				$insert = $this->db->insert('orderdetails', $data);
				if ($insert) {
					$result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
						"code"=>201,
						"message"=>"Data has successfully added",
						"data"=>$data];
					$this->response($result, 201);
				}else{
					$result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
						"code"=>502,
						"message"=>"Failed adding data",
						"data"=>null];
					$this->response($result, 502);
				}
			else:
				$result = ["took"=>$_SERVER["REQUEST_TIME_FLOAT"],
						"code"=>304,
						"message"=>"Data already added",
						"data"=>$data];
				$this->response($result, 304);
			endif;
		}

		//Memperbarui data
		public function index_put(){
			$id = $this->put('productCode');
			$data = array(
						'orderNumber' => $this->put('orderNumber'), 
						'productCode'=> $this->put('productCode'),
						'quantityOrdered'=> $this->put('quantityOrdered'),
						'priceEach'=> $this->put('priceEach'),
						'orderLineNumber'=> $this->put('orderLineNumber')
					);
			$this->db->where('productCode', $id);
			$update = $this->db->update('orderdetails', $data);
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

		//Mnghapus data
		public function index_delete(){
			$id = $this->delete('productCode');
			//check data
			$this->db->where('productCode', $id);
			$check = $this->db->get('orderdetails')->num_rows();
			if($check==0):
				$this->output->set_header('HTTP/1.1 304 Not Modified');
			else:
				$this->db->where('productCode', $id);
				$delete = $this->db->delete('orderdetails');
				$this->db->where('productCode', $id);
				if ($delete) {
					$this->response(array('status' => 'success'), 201);
				}else{
					$this->response(array('status' => 'fail', 502));
				}
			endif;
		}
	}
	?>	