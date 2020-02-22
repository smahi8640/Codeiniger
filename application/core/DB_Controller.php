<?php 
require_once APPPATH . '/libraries/REST_Controller.php';
require_once APPPATH . '/libraries/JWT.php';
require_once APPPATH . '/libraries/BeforeValidException.php';
require_once APPPATH . '/libraries/ExpiredException.php';
require_once APPPATH . '/libraries/SignatureInvalidException.php';
use \Firebase\JWT\JWT;
class DB_Controller extends REST_Controller
{
	
	public function auth()
    {
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        //$this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
        //$this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        //$this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
        //JWT Auth middleware
        $headers = $this->input->get_request_header('Authorization');       
        if (empty($headers)) {
            $headersObj = $this->input->request_headers();
            if (array_key_exists('Authorization', $headersObj) && !empty($headersObj['Authorization'])) {
                $headers = $headersObj['Authorization'];
            }
        }

        if (empty($headers)) {
            $headers = $this->getAuthorizationHeader();
        }        
        $kunci = $this->config->item('kunci'); //secret key for encode and decode
        $token = "token";        
       	if (!empty($headers)) {
        	if (preg_match('/Bearer\s(\S+)/', $headers , $matches)) {
                $token = $matches[1];
        	}
    	}               
        try {
            $decoded = JWT::decode($token, $kunci, array('HS256'));                  
            $this->user_data = $decoded;
        } catch (Exception $e) {           
            $invalid = ['error' => true, 'status' => $e->getMessage()]; //Response if credential invalid
            $this->response($invalid, 401);//401
        }
    }

    public function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
	
}
?>