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

  private function usersCmp($a, $b)
  {
      if ($a->sort == $b->sort) {
          return 0;
      }
  return ($a->sort > $b->sort) ? -1 : 1;
  }


private function getProjectsForPerson($request){

    $this->getUserLdap($request['person']);

    $user =   $this->getUserLdap($request['person']);

    if(!$user){
      $this->error[] = 'not found';
      return false;
    }



    $connector = new AISConnect();
    $html = $connector->request('http://is.stuba.sk/lide/clovek.pl?lang=sk;zalozka=5;rok=1;id=' . $user );
    //var_dump('http://is.stuba.sk/lide/clovek.pl?lang=sk;zalozka=5;rok=1;id=' . $user);
    $html = trim(preg_replace('/\s\s+/', ' ', $html));


    $doc = new \DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML($html);
    $xPath = new \DOMXPath($doc);

    $formTable = $xPath->query("(//div[@class='mainpage'])/table[3]/tbody/tr");


    $data = [];
    foreach($formTable as $row){
      $row->childNodes[1]->nodeValue;//name

      if(strpos($row->childNodes[2]->nodeValue,'asopisoch')!== false ){
        $type = 1;
      }elseif(strpos($row->childNodes[2]->nodeValue,'onografie')!== false){
        $type = 2;
      }elseif(strpos($row->childNodes[2]->nodeValue,'kapitoly')!== false){
        $type = 0;
      }else{
        continue;
      }


      $data[]=(object)[
        'sort' => $type . $row->childNodes[3]->nodeValue,
        'year' => $row->childNodes[3]->nodeValue,
        'type' => $row->childNodes[2]->nodeValue,
        'name' => $row->childNodes[1]->childNodes[0]->childNodes[0]->nodeValue
      ];

    }


    usort($data,[$this,'usersCmp']);
      foreach ($data as $key => $item){
        unset ($data[$key]->sort);
      }

    return $data;

  }

  private function getFocusedProject($request){

    $connector = new AISConnect();
    $html = $connector->request('http://is.stuba.sk/pracoviste/prehled_temat.pl?detail=' . $request['project'] . ';pracoviste=' . $request['dept'] . ';lang=sk' );
    $html = trim(preg_replace('/\s\s+/', ' ', $html));


    $doc = new \DOMDocument();
    libxml_use_internal_errors(true);
    $doc->loadHTML($html);
    $xPath = new \DOMXPath($doc);

    $formTable = $xPath->query("(//div[@class='mainpage'])/table/tbody/tr");


    $data = [];
    foreach($formTable as $row){
      $data[] = (object)[
        'name' => $row->childNodes[0]->nodeValue,
        'val' => isset($row->childNodes[1]->nodeValue)?$row->childNodes[1]->nodeValue:'',
      ];
    }


    return $data;

  }


  private function getUserLdap($user){
    $ds=ldap_connect("ldap.stuba.sk");
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    if(!$ds){
      $this->error[] = 'LDAP conn failed';
      return;
    }

    $ldap_dn = 'ou=People, DC=stuba, DC=sk';
    $ldap = ldap_bind($ds);

    if (!$ldap) {
      $this->error[] = 'Auth Fail';
      return;
    }

    $rs = ldap_search($ds, $ldap_dn, "uid=" . $user, array( 'uid' ,"uisid"));
    $user  = ldap_get_entries($ds, $rs);
//    $rs = ldap_search($ds, $ldap_dn, "uisid=" . $user, array( 'uid' ,"uisid"));
//    $user  = ldap_get_entries($ds, $rs);
//    var_dump($user);
    @$user = $user[0]['uisid'][0];
    return $user;

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
