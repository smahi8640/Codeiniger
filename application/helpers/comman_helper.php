<?php 


function GetFormError(){
	$CI = & get_instance();
	$errors = $CI->form_validation->error_array();
	if(count($errors) === 0){
		return false;		
	}else{
		foreach ($errors as $key => $error) {
			return $error;
		}
	}
}

?>