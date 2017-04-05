<?php

/**
 * Created by PhpStorm.
 * User: kubri
 * Date: 25.2.2017
 * Time: 20:37
 */
namespace wt;

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
      case'getState':
        $data = $this->getState($request);
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
   * @return array
   */
  private function getData(){
    global $objDb;
    $result = [];
    $data = $objDb->getRows('absence',[]);
    foreach($data as $val){
      if(!isset($result[$val['id_employee']])){
        $result[$val['id_employee']] = [];
      };

      if(!isset($result[$val['id_employee']][$val['date']])){
        $result[$val['id_employee']][$val['date']] = (object)[];
      };

      $result[$val['id_employee']][$val['date']] = $val['id_type'];

    }

    return $result;
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
    error_log("\n" . date(time()) . "API error:$msg", 3, __DIR__ . "/my-errors.log");
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
