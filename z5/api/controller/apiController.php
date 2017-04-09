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
      case 'getFocusedProject':
        $data = $this->getFocusedProject($request);
        break;
      case'getFreeProjects':
        $data = $this->getFreeProjects($request);
        break;
      case'getProjectsForPerson':
        $data = $this->getProjectsForPerson($request);
        break;
      default:
        $this->error('wrong_request_type:' . $requestType);
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
