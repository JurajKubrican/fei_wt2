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
  public function run(){

    $URI = explode('/',explode('?',str_replace('/z5/api/','',$_SERVER['REQUEST_URI']))[0]);
    $filter = [];
    foreach ($URI as $key => $item){
      if($key % 2 === 0 ){
        $filter[$item] = isset($URI[$key + 1]) ? $URI[$key + 1] : '';
      }
    }
    $filter += $_GET;

    $METHOD = $_SERVER['REQUEST_METHOD'];

    switch ($METHOD){
      case 'GET':
        $data = $this->get($filter);
        break;
      case 'PUT':
        $putfp = fopen('php://input', 'r');
        $putdata = '';
        while($data = fread($putfp, 1024))
          $putdata .= $data;
        fclose($putfp);

        $data = $this->put($filter,$putdata);
        break;
    }

    $this->formulateResponse($data);

  }


  private function get($filter){

    $stat = isset($filter['stat']) ? trim($filter['stat']) : 'SK' ;
    $fields = ['den'];

    $conds = [];
    if( isset($filter['sviatky']) ){
      $fields[] = $stat . 'sviatky';
      if(!empty($filter['sviatky'])){
        $conds['den'] = $filter['sviatky'];
      }
    }elseif( isset($filter['meniny']) ){
      $fields[] = $stat . '';
      if(!empty($filter['meniny'])){
        $conds['den'] = $filter['meniny'];
      }
    }elseif( isset($filter['dni']) ){
      $fields[] = $stat . 'dni';
      if(!empty($filter['dni'])){
        $conds['den'] = $filter['dni'];
      }
    }elseif( isset($filter['meno']) ){
      $fields[] = $stat . '';
      if(!empty($filter['meno'])){
        $conds['meno'] = $filter['meno'];
      }
    }



    $xml = new \SimpleXMLElement(file_get_contents('../meniny.xml'));

    $result = [];

    foreach($xml as $item){
      $continue = false;
      foreach($conds as $key => $cond){
        if($key === 'meno' ){


          if( strpos(strtolower($item->{$stat}), strtolower($cond)) !== false
            ||  isset($item->{$stat.'d'})
            && strpos(strtolower($item->{$stat.'d'}), strtolower($cond)) !== false ){
            continue;
          }else{
            $continue = true;
          }

        }
        if(!isset($item->$key) || (string)$item->$key !== $cond){
          $continue = true;
          break;
        }
      }
      if($continue)
        continue;


      $partial = (object)[];
      foreach($fields as $field){
        if(isset($item->$field)) {
//          if ($field === 'den') {
            $partial->$field = (string)$item->$field;
//          } else {
//            $partial->$field = explode(', ', $item->$field);
//          }

          if ($field === 'SK' && isset($item->SKd)) {
//            $partial->SKd = explode(', ', (string)$item->SKd);
            $partial->SKd = (string)$item->SKd;
          }
        }
      }

      if(count((array)$partial)){
        $result[] = $partial;
      }


    }


    return $result;
  }


  private function put($filter,$data){
    $data = \json_decode($data);
    $stat = isset($filter['stat']) ? trim($filter['stat']) : 'SK' ;

    $xml = new \SimpleXMLElement(file_get_contents('../meniny.xml'));

    $item=false;
    foreach($xml as $item) {

      if ($filter['meniny'] !== (string)$item->den)
        continue;

      foreach ($data as $key => $val) {
        $item->$key = $val;
      }
      break;

    }

    $xml->asXML('../meniny.xml');

    return [];
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
