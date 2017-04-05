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
      case'getProjectsforPerson':
        $data = $this->getProjectsforPerson($request);
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
 * @param $request
 * @return array
 */
  private function getFreeProjects($request){

    global $bDebug;
    $bDebug = false;

    $connector = new AISConnect();
    $html = $connector->request('http://is.stuba.sk/pracoviste/prehled_temat.pl?lang=sk;pracoviste=' . $request['project']);


    $html = trim(preg_replace('/\s\s+/', ' ', $html));
    //die($html);

    preg_match('/<form method="post" action="\/pracoviste\/prehled_temat.pl.*<\/form>/',$html,$matches);
    $xml = new \SimpleXMLElement(html_entity_decode($matches[0]));

    $data = [];
    foreach($xml->table[2]->tbody->children() as $item){
      if((string)$item->td[9]->small === '--' && (string)$item->td[1]->small === 'BP'){

        preg_match('/(:?detail=)([0-9]*)/',(string)$item->td[7]->small->a->attributes()['href'],$link);

        $data[] = (object)[
          'name' => (string)$item->td[2]->small,
          'lead' => (string)$item->td[3]->small->a,
          'dept' => (string)$item->td[4]->small,
          'link' => $link[2],
        ];
        //var_dump($item->td[3]);die;
      }
    }

    return $data;
  }

  private function getProjectsforPerson($request){

    $user =  4948;

    $connector = new AISConnect();
    $html = $connector->request('http://is.stuba.sk/lide/clovek.pl?lang=sk;zalozka=5;rok=1;id='.$user );
    $html = trim(preg_replace('/\s\s+/', ' ', $html));


    $doc = new \DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML($html);
    $xPath = new \DOMXPath($doc);

    $formTable = $xPath->query("(//div[@class='mainpage'])/table");

    var_dump($formTable->item(0));



  }

  private function getFocusedProject($request){

    $connector = new AISConnect();
    $html = $connector->request('http://is.stuba.sk/pracoviste/prehled_temat.pl?detail=' . $request['project'] . ';pracoviste=' . $request['dept'] . ';lang=sk' );
    $html = trim(preg_replace('/\s\s+/', ' ', $html));


    $doc = new \DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML($html);
    $xPath = new \DOMXPath($doc);

    $formTable = $xPath->query("(//div[@class='mainpage'])/table");


    return $doc->saveHTML($formTable[0]);

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
