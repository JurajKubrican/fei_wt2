<?php

/**
 * Created by PhpStorm.
 * User: kubri
 * Date: 25.2.2017
 * Time: 20:37
 */



namespace wt;

use Google;


class ApiController{




  protected $error = [];
  protected $warning = [];
  protected $message = [];

  /**
   * ApiController constructor.
   */
  function __construct(){
//    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');


  }


  /**
   * @param $request
   */
  public function run($request){
    if(empty($request['request'])){
      $this->error('missing_request');
    }

    $requestType = $request['request'];
    unset($request['request']);
    $data =[];

    switch ($requestType){
      case'login-local':
        $data = $this->loginAttempt($request);
        break;
      case'login-ldap':
        $data = $this->loginLdap($request);
        break;
      case'login-google':
        $data = $this->loginGoogle($request);
        break;
      case'logout':
        $data = $this->logout($request);
        break;
      case'register':
        $data = $this->register($request);
        break;
      default:
        $this->error('wrong_request_type');
        break;
    }

    $this->formulateResponse($data);

  }

  /**
   * @param $data
   */
  private function formulateResponse($data){
    if(count($this->error)){
      $this->error(join(' ', $this->error));
    }else{
      $this->success($data,join(' ', $this->warning).join(' ', $this->message));
    }
  }

  /**
   * @return array
   */
  private function getLegend(){
    global $objDb;
    $data = $objDb->getRows('absence_type',[]);
    return $data;
  }

  /**
   * @return array
   */
  private function getUsers(){
    global $objDb;
    $data = $objDb->getRows('employees',[]);
    return $data;
  }


  /**
 * @param $request
 * @return array
 */
  private function loginAttempt($request){
    global $objDb;
    $result = [];
    $data = $objDb->getRows('users',['login'=>$request['login']]);
    foreach($data as $val){
      if(password_verify($_REQUEST['pass'],$val['pass'])){
        $_SESSION['user'] = $val['id'];
        $this->message = $_SESSION;
        $objDb->insert('users_history',['id' => $val['id']]);
        return [];
      }else{
        $this->error[] = 'auth fail';
        return [];
      }
    }
    $this->error[] = 'auth fail';

    return $result;
  }

  /**
   * @param $request
   * @return array
   */
  private function register($request){
    global $objDb;
    $result = [];
    $data = $objDb->getRows('users',['login'=>$request['login']]);
    if(count($data)){
      $this->error[]='user already exists';
      return;
    }

    $pass = password_hash($_REQUEST['pass'],PASSWORD_DEFAULT);
    $objDb->insert('users',['login'=>$request['login'],'name1'=>$request['name1'],'name2'=>$request['name2'],'email'=>$request['email'],'pass' => $pass]);
    $this->message[] = 'OK';


    return $result;
  }

  /**
   * @param $request
   * @return array|void
   */
  private function loginLdap($request){
    $ds=ldap_connect("ldap.stuba.sk");
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    if(!$ds){
      $this->error[] = 'LDAP conn failed';
      return;
    }

    $ldap_dn = 'uid = ' . $request['login'] . ', ou=People, DC=stuba, DC=sk';
    $ldap = ldap_bind($ds, $ldap_dn,  $request['pass']);

    if (!$ldap) {
      $this->error[] = 'Auth Fail';
      return;
    }

    $rs = ldap_search($ds, $ldap_dn, "uid=" . $request['login'], array("givenname", "employeetype", "surname", "mail", "faculty", "cn", "uisid", "uid"));
    $user  = ldap_get_entries($ds, $rs);
    $this->message[] = json_encode($user);
    $_SESSION['user'] = $user[0]['uid'][0];
    global  $objDb;
    $objDb->insert('users_history',['id' => $_SESSION['user']]);
    return $user;

  }

  private function loginGoogle($request){
    $client = new \Google_Client();
    $ticket = $client->verifyIdToken($request['token']);
    if (!empty($ticket['email'])) {
      $_SESSION['user'] = $ticket['email'];
      global  $objDb;
      $objDb->insert('users_history',['id' => $_SESSION['user']]);
      return true;
    }
    $this->error[] = 'auth fail';
    return false;
  }

  private function logout(){
    unset ( $_SESSION['user']) ;
    return true;
  }


  private function update($data,$date){

    global $objDb;
    $from = date("Y-m-01 00:00:00",strtotime($date));
    $to = date("Y-m-01 00:00:00",strtotime($date.' + 1 month'));
    $stmt = "DELETE  FROM `absence` WHERE 1";
    $objDb->getResult($stmt);

    foreach($data as $user => $dates){
      foreach($dates as $date => $type){
        if($type != 0)
        $objDb->updateInsert('absence',['id_employee'=>$user,'date'=>$date],['id_type'=>$type]);
      }
    }
  }

  /**
   * @param $request
   * @return object
   */
  private function getState($request){
    if($request['json']!=='undefined' ){
      $this->update(json_decode($request['json']),$request['date']);
    }

    return (object)[
      'date'=> null,
      'legend'=>$this->getLegend(),
      'users'=>$this->getUsers(),
      'data'=>$this->getData()
    ];
  }




  /**
   * @param string $msg
   */
  protected function error($msg = '') {
    error_log("\n" . date(time()) . "API error:$msg");
    die(json_encode(['status' => 'error', 'msg' => $msg]));
  }

  /**
   * @param array $data
   * @param string $msg
   */
  protected function success($data = [], $msg = '') {
    die(json_encode(['status' => 'ok', 'data' => $data, 'msg' => $msg]));
  }


}
