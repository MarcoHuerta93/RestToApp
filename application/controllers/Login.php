<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once(APPPATH.'/libraries/REST_Controller.php');
use Restserver\libraries\Rest_Controller;
class Login extends REST_Controller {


	public function __construct(){
		header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
		header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
		header("Access-Control-Allow-Origin: *");
		parent::__construct();
		$this->load->database();
	}



	public function index_post(){

		$data = $this->post();


		if (!isset( $data['correo']) OR !isset( $data['contrasena'])) {

			$respuesta = array(
				'error' =>  TRUE,
				'mensaje' => 'La información enviada no es valida'
			);

			$this->response($respuesta, REST_Controller::HTTP_BAD_REQUEST);
			return;
		}
		//tenemos correo y contraseña en un post
		$condiciones = array(
			'correo'=> $data['correo'],
			'contrasena'=> $data['contrasena']
		);

			$query = $this->db->get_where('login',$condiciones);
			$usuario = $query->row();
		if (!isset($usuario)) {
			$respuesta = array(
				'error' =>  TRUE,
				'mensaje' => 'Usuario/contraseña incorrectos'
			);

			$this->response($respuesta);
			return;  
			
		}
		//aqui tenemos un usuario y contrasena correctos

		//TOKEN
		$token = bin2hex(openssl_random_pseudo_bytes(20));
		$token = hash('ripemd160', $data['correo']);


		//guradar en base de datos el token 
		$this->db->reset_query();
		$actualizar_token = array('token'=> $token);
		$this->db->where('id',$usuario->id);  
		$hecho = $this->db->update('login', $actualizar_token);

		$respuesta = array(
			'error' => FALSE,
			'token' => $token,
			'id_usuario' => $usuario->id
        
		);
		$this->response($respuesta);



	}



}
