<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH.'core/DB_Controller.php';
use \Firebase\JWT\JWT;
class Login extends DB_Controller{


    /**
	 * Log the user in
	 */
	public function signin_post()
	{
		$_POST = json_decode(file_get_contents("php://input"),TRUE);
		$this->form_validation->set_rules('email','Email', 'trim|required');
		$this->form_validation->set_rules('password','Password', 'required');
		if($this->form_validation->run() === TRUE)
		{
			$remember = (bool)$this->input->post('remember');
			if ($this->ion_auth->login($this->input->post('email'), $this->input->post('password'), $remember))
			{
				$userData = $this->ion_auth->select('first_name,last_name,id,email')->user()->row();
				$userData->isAdmin = $this->ion_auth->is_admin();
				$kunci = $this->config->item('kunci');
				$token['id'] = $userData->id;
				$token['email'] = $userData->email;
				$date = new DateTime();				
				$token['iat'] = $date->getTimestamp();
				$token['exp'] = $date->getTimestamp()+60*60*24;
				$data['token'] = JWT::encode($token,$kunci);
				$data['status'] = TRUE;
				$data['data'] = $kunci;
				$data['message'] = "Login successfully.";
				$this->set_response($data,REST_Controller::HTTP_OK);
				
			}
			else
			{
				$data['status'] = false;
				$data['data'] = new ArrayObject();
				$data['message'] = $this->ion_auth->errors();
				$this->set_response($data,REST_Controller::HTTP_OK);
			}
			
		}else{
			$data['status'] = false;
			$data['data']  = new ArrayObject();
			$data['message'] = GetFormError();
			$this->set_response($data,REST_Controller::HTTP_OK);
		}
	}

	public function updateUser_post(){		
		$this->auth();
		$_POST = json_decode(file_get_contents("php://input"),TRUE);

		print_r($_POST);
	}

}
