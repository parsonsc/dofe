<?php
function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }
    array_multisort($sort_col, $dir, $arr);
}
function unstrip_array($array){
	foreach($array as &$val){
		if(is_array($val)){
			$val = unstrip_array($val);
		}else{
			$val = stripslashes($val);
		}
	}
    return $array;
}

function validateJGInputs($inputs,$rules,$default){
    $errors = array();
    foreach ($inputs as $key => $value){
        if (!is_array($rules[$key])){
            $rule = $rules[$key];
            $rules[$key] = array();
            $rules[$key][] = $rule;
        }
        foreach ($rules[$key] as $rule){
            switch($rule){
                case 'notEmpty':
                    if (trim($value) == "" && !is_array($value)){
                        if (isset($rules[$key.'_alt'])){
                           if (trim($inputs[$key.'_alt']) =='')  $errors[$key]['message'] = $default[$key];
                        }
                        else $errors[$key]['message'] = $default[$key];
                    }
                    break;
                case 'length2':
                    if (trim($value) == "" && !is_array($value))	{
                        $errors[$key]['message'] = $default[$key];
                    }
                    if (strlen(trim($value)) < 2) {
                        $errors[$key]['message'] = $default[$key];
                    }
                    break;
                case 'numbers':
                    if(!preg_match("/[0-9]+$/", $value)) {
                        echo 'failed '.$key;
                        $errors[$key]['message'] = $default[$key];
                    }
                    break;                    
                case 'invalidChars':
                    if(preg_match("/[&,;\\\"#\(\)\'*+:<=>?{}~£\$@!%\[\]]+$/", $value)) {
                        //echo 'failed '.$key;
                        $errors[$key]['message'] = $default[$key];
                    }
                    break;
                case 'invalidCharsOrg':
                    if(preg_match("/[&,;\\\"#\'*<=>?{}\$%\[\]]+$/", $value)) {
                        ///echo 'failed '.$key;
                        $errors[$key]['message'] = $default[$key];
                    }
                    break;     
                case 'letters2f':
                    if (trim($value) == "" && !is_array($value))	{
                        $errors[$key]['message'] = $default[$key];
                    }
                    if(preg_match("/[&,;\\\"#\(\)\'*+:<=>?{}~£\$@!%\[\]0-9]+$/", $value)) {
                        echo 'failed '.$key;
                        $errors[$key]['message'] = $default[$key];
                    }
                    if (strlen(trim($value)) < 2 && trim($value) != '.') {
                        echo 'failed short '.$key;
                        $errors[$key]['message'] = $default[$key];
                    }
                    break;                     
                case 'letters2':
                    if (trim($value) == "" && !is_array($value))	{
                        $errors[$key]['message'] = $default[$key];
                    }
                    if(preg_match("/[0-9]+$/", $value)) {
                        //echo 'failed '.$key;
                        $errors[$key]['message'] = $default[$key];
                    }
                    if (strlen(trim($value)) < 2) {
                        $errors[$key]['message'] = $default[$key];
                    }
                    break;  
                case 'letters2amp':
                    if (trim($value) == "" && !is_array($value))	{
                        $errors[$key]['message'] = $default[$key];
                    }
                    if(!preg_match("/^[a-zA-Z\-'&]+$/", $value)) {
                        $errors[$key]['message'] = $default[$key];
                    }
                    if (strlen(trim($value)) < 2) {
                        $errors[$key]['message'] = $default[$key];
                    }
                    break;                  
                case 'notDefault':
                    if ($value == $default[$key] || trim($value) == "" ){
                        $errors[$key]['message'] = $default[$key];
                    }
                    break;
                case 'postCode':
                    $value = strtoupper(str_replace(' ','',$value));
                    if (!(preg_match("/^[A-Z]{1,2}[0-9]{2,3}[A-Z]{2}$/",$value) || preg_match("/^[A-Z]{1,2}[0-9]{1}[A-Z]{1}[0-9]{1}[A-Z]{2}$/",$value) || preg_match("/^GIR0[A-Z]{2}$/",$value))){
                        $errors[$key]['message'] = $default[$key];					
                    }
                    //$errors[$key] = 1;
                    break;			
                case 'email':
                    if (!(preg_match("/^[^@]*@[^@]*\.[^@]*$/", $value))){
                        $errors[$key]['message'] = $default[$key];					
                    }
                    break;
                case 'url':
                    if (!(preg_match("/^[0-9a-z\-]+$/", $value))){
                        $errors[$key]['message'] = $default[$key];					
                    }
                    break;                    
                case 'ukonly':
                    if ($value != 'United Kingdom') {         
                        $errors[$key]['message'] = $default[$key] ;					
                    }
                    break;   
                case 'length6':
                    if (trim($value) == "" && !is_array($value))	{
                        $errors[$key]['message'] = $default[$key];
                    }
                    if (strlen(trim($value)) < 6) {
                        $errors[$key]['message'] = $default[$key];
                    }
                    break;                    
                case 'date':
                    if (date('d-m-Y', strtotime(str_replace('/', '-', $value))) != str_replace('/', '-', $value)) {         
                        $errors[$key]['message'] = $default[$key] ;					
                    }
                    break;
            }
        }
    }
    return $errors;
}
?>