<?php
/**
 * Description of Chili
 *
 * @author DezzmonD
 */
class Chili {
    private $apikey;
    private $salt;
    private $url;
    
    public function __construct(){
        $chili = elgg_get_plugin_from_id('chili');
        $this->url    = $chili->getSetting('server_url');
        $this->salt   = $chili->getSetting('security_salt');
        $this->apikey = $chili->getSetting('apikey');
    }
    
    /**
     * generate random string 
     * $length is desired string length 
     */
    protected function randString($length){
        $chars = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numChars = strlen($chars);
        $string = "";
        for ($i = 0; $i < $length; $i++) {
            $string .= $chars[mt_rand(0, $numChars - 1)];
        }
    return $string;
    }

    //get secutiry salt
    public function getSalt(){
        $salt = $this->salt;
        
        if($salt == ''){
        	$salt = $this->randString(15);
        	//$vars['entity']->security_salt = $salt;
        }
        return $salt;
    }

    //create ChiliProject password for current user
    public function passCreate(){
        $current_email = get_loggedin_user()->email;
        $pass = md5($current_email . $this->salt);
        
        return $pass;
    }
    
    private function request($path, $method = 'GET', $data = '') {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $path); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                   'Content-Type: application/json',
                    'X-ChiliProject-API-Key: ' . $this->apikey
                ));
        
        switch ($method) {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, true);
                if (isset($data)) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
            case 'PUT':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                if (isset($data)) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
            default: // GET
                break;
        }
        
        $result = curl_exec($curl);
        
        if(curl_errno($curl)){
            register_error(elgg_echo(curl_errno($curl) . ": " . curl_error($curl)));
        }
        else{
            return $result;
        }
        curl_close($curl);
    }
    
    public function getUserData(){
        $userdata['user'] = get_loggedin_user()->username;
        $userdata['name'] = get_loggedin_user()->name;
        $userdata['email'] = get_loggedin_user()->email;
        return $userdata;
    }
    
    public function userCheck(){
        $userdata = $this->getUserData();
        $json = $this->request($this->url . 'users.json?name=' . $userdata['user']);
        $json_array = json_decode($json, true);
        if(($json_array['total_count'] == 0)){
            return 0;
        }
        else{
            for($i = 0; $i < $json_array['total_count']; $i++){
                $login_array[$json_array['users'][$i]['id']] = $json_array['users'][$i]['login'];
            }
			if(($num = array_search($userdata['user'], $login_array)) !== false){
                return $num;
            }
			else{
				return 0;
			}
        }    
    }
    
    /**
    * check if the currently logged in user exists in ChiliProject
    * if not, creates new ChiliProject user with the login matched the login of currently logged in user
    */
    public function userCreate(){
        $userdata = $this->getUserData();
        $check = $this->userCheck();
        if($check == 0){
            if(strstr($userdata['name'], " ") !== false){
                $firstname = strstr($userdata['name'], " ", true);
                $lastname = ltrim(strstr($userdata['name'], " "));
            }
            else{
                $firstname = $lastname = $userdata['name'];
            }
            $user_data = array('user' => array('login' => $userdata['user'], 
                                            'firstname' => $firstname, 
                                            'lastname' => $lastname, 
                                            'mail' => $userdata['email'], 
                                            'password' => chili_passCreate()
            ));
            $this->request($this->url . 'users.json', 'POST', json_encode($user_data));
        }
    }
    
    /**
    * check if the currently logged in user exists in ChiliProject
    * if does, updates ChiliProject user to set new password
    */
    public function userUpdate(){
        $userdata = $this->getUserData();
        $check = $this->userCheck();
        if($check && $check != 1){
            if(strstr($userdata['name'], " ") !== false){
                $firstname = strstr($userdata['name'], " ", true);
                $lastname = ltrim(strstr($userdata['name'], " "));
            }
            else{
                $firstname = $lastname = $userdata['name'];
            }
            $user_data = array('user' => array('login' => $userdata['user'], 
                                            'firstname' => $firstname, 
                                            'lastname' => $lastname, 
                                            'mail' => $userdata['email'], 
                                            'password' => chili_passCreate()
            ));
            $upd = $this->request($this->url . 'users/' . $check . '.json', 'PUT', json_encode($user_data));
        }
    }
}
