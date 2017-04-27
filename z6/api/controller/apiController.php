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
      case 'weather':
        $data = $this->getWeather($request);
        break;
      case'geoip':
        $data = $this->getGeoip($request);
        break;
      case'stats':
        $data = $this->getStats($request);
        break;
      case'statsCountry':
        $data = $this->getStatsCountry($request);
        break;

      case'geojson':
        $data = $this->geoJson($request);
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


  private function request($sRequest){
    global $objDb;


    $cache = $objDb->getRows('cache',['url'=>$sRequest],1);
    $cache = isset($cache[0])?$cache[0]:[];

    if(empty($cache) || !empty($cache) && strtotime($cache['date']) < ( time() - 3600)){



      $pSession = curl_init();
      curl_setopt($pSession, CURLOPT_URL, $sRequest);

      //POST
      if (!empty($aPost)) {
        curl_setopt($pSession, CURLOPT_POST, true);
        curl_setopt($pSession, CURLOPT_POSTFIELDS, $aPost);
      }
      curl_setopt($pSession, CURLINFO_HEADER_OUT, false);
      curl_setopt($pSession, CURLOPT_FOLLOWLOCATION, true);

      //DATA
      curl_setopt($pSession, CURLOPT_RETURNTRANSFER, true);

      $sResponse = curl_exec($pSession);
      //var_dump(curl_getinfo($pSession));

      curl_close($pSession);

      $objDb->updateInsert('cache',['url'=>$sRequest],[ 'data'=>$sResponse, 'date'=>date('Y-m-d h:i:s')]);

      $this->message[] = 'req';
    }else{
      $sResponse = $cache['data'];
      $this->message[] = 'from cache';
    }

    return $sResponse;
  }


  /**
   * @param $request
   * @return array
   */
  private function getWeather($request){

    $geoip = $this->geoIP($_SERVER['REMOTE_ADDR']);
    $this->track('weather',$geoip);

    $data = json_decode($this->request('http://api.openweathermap.org/data/2.5/weather?lat=' . $geoip->lat . '&lon=' . $geoip->lon . '&units=metric&appid=48f3037c7168ff932d45b0c21e1f1eb8'));

    //var_dump($data);

    $result[] = (object)[
      'place'=>$data->name,
      'temp'=>$data->main->temp,
      'weather'=>$data->weather[0]->main
    ];

    return $result;
  }


  private function geoIP($ip){

    $data = json_decode($this->request('http://freegeoip.net/json/' . $ip));

    $cities = json_decode('{"BD": "Dhaka", "BE": "Brussels", "BF": "Ouagadougou", "BG": "Sofia", "BA": "Sarajevo", "BB": "Bridgetown", "WF": "Mata Utu", "BL": "Gustavia", "BM": "Hamilton", "BN": "Bandar Seri Begawan", "BO": "Sucre", "BH": "Manama", "BI": "Bujumbura", "BJ": "Porto-Novo", "BT": "Thimphu", "JM": "Kingston", "BV": "", "BW": "Gaborone", "WS": "Apia", "BQ": "", "BR": "Brasilia", "BS": "Nassau", "JE": "Saint Helier", "BY": "Minsk", "BZ": "Belmopan", "RU": "Moscow", "RW": "Kigali", "RS": "Belgrade", "TL": "Dili", "RE": "Saint-Denis", "TM": "Ashgabat", "TJ": "Dushanbe", "RO": "Bucharest", "TK": "", "GW": "Bissau", "GU": "Hagatna", "GT": "Guatemala City", "GS": "Grytviken", "GR": "Athens", "GQ": "Malabo", "GP": "Basse-Terre", "JP": "Tokyo", "GY": "Georgetown", "GG": "St Peter Port", "GF": "Cayenne", "GE": "Tbilisi", "GD": "St. George\'s", "GB": "London", "GA": "Libreville", "SV": "San Salvador", "GN": "Conakry", "GM": "Banjul", "GL": "Nuuk", "GI": "Gibraltar", "GH": "Accra", "OM": "Muscat", "TN": "Tunis", "JO": "Amman", "HR": "Zagreb", "HT": "Port-au-Prince", "HU": "Budapest", "HK": "Hong Kong", "HN": "Tegucigalpa", "HM": "", "VE": "Caracas", "PR": "San Juan", "PS": "East Jerusalem", "PW": "Melekeok", "PT": "Lisbon", "SJ": "Longyearbyen", "PY": "Asuncion", "IQ": "Baghdad", "PA": "Panama City", "PF": "Papeete", "PG": "Port Moresby", "PE": "Lima", "PK": "Islamabad", "PH": "Manila", "PN": "Adamstown", "PL": "Warsaw", "PM": "Saint-Pierre", "ZM": "Lusaka", "EH": "El-Aaiun", "EE": "Tallinn", "EG": "Cairo", "ZA": "Pretoria", "EC": "Quito", "IT": "Rome", "VN": "Hanoi", "SB": "Honiara", "ET": "Addis Ababa", "SO": "Mogadishu", "ZW": "Harare", "SA": "Riyadh", "ES": "Madrid", "ER": "Asmara", "ME": "Podgorica", "MD": "Chisinau", "MG": "Antananarivo", "MF": "Marigot", "MA": "Rabat", "MC": "Monaco", "UZ": "Tashkent", "MM": "Nay Pyi Taw", "ML": "Bamako", "MO": "Macao", "MN": "Ulan Bator", "MH": "Majuro", "MK": "Skopje", "MU": "Port Louis", "MT": "Valletta", "MW": "Lilongwe", "MV": "Male", "MQ": "Fort-de-France", "MP": "Saipan", "MS": "Plymouth", "MR": "Nouakchott", "IM": "Douglas, Isle of Man", "UG": "Kampala", "TZ": "Dodoma", "MY": "Kuala Lumpur", "MX": "Mexico City", "IL": "Jerusalem", "FR": "Paris", "IO": "Diego Garcia", "SH": "Jamestown", "FI": "Helsinki", "FJ": "Suva", "FK": "Stanley", "FM": "Palikir", "FO": "Torshavn", "NI": "Managua", "NL": "Amsterdam", "NO": "Oslo", "NA": "Windhoek", "VU": "Port Vila", "NC": "Noumea", "NE": "Niamey", "NF": "Kingston", "NG": "Abuja", "NZ": "Wellington", "NP": "Kathmandu", "NR": "Yaren", "NU": "Alofi", "CK": "Avarua", "XK": "Pristina", "CI": "Yamoussoukro", "CH": "Berne", "CO": "Bogota", "CN": "Beijing", "CM": "Yaounde", "CL": "Santiago", "CC": "West Island", "CA": "Ottawa", "CG": "Brazzaville", "CF": "Bangui", "CD": "Kinshasa", "CZ": "Prague", "CY": "Nicosia", "CX": "Flying Fish Cove", "CR": "San Jose", "CW": " Willemstad", "CV": "Praia", "CU": "Havana", "SZ": "Mbabane", "SY": "Damascus", "SX": "Philipsburg", "KG": "Bishkek", "KE": "Nairobi", "SS": "Juba", "SR": "Paramaribo", "KI": "Tarawa", "KH": "Phnom Penh", "KN": "Basseterre", "KM": "Moroni", "ST": "Sao Tome", "SK": "Bratislava", "KR": "Seoul", "SI": "Ljubljana", "KP": "Pyongyang", "KW": "Kuwait City", "SN": "Dakar", "SM": "San Marino", "SL": "Freetown", "SC": "Victoria", "KZ": "Astana", "KY": "George Town", "SG": "Singapur", "SE": "Stockholm", "SD": "Khartoum", "DO": "Santo Domingo", "DM": "Roseau", "DJ": "Djibouti", "DK": "Copenhagen", "VG": "Road Town", "DE": "Berlin", "YE": "Sanaa", "DZ": "Algiers", "US": "Washington", "UY": "Montevideo", "YT": "Mamoudzou", "UM": "", "LB": "Beirut", "LC": "Castries", "LA": "Vientiane", "TV": "Funafuti", "TW": "Taipei", "TT": "Port of Spain", "TR": "Ankara", "LK": "Colombo", "LI": "Vaduz", "LV": "Riga", "TO": "Nuku\'alofa", "LT": "Vilnius", "LU": "Luxembourg", "LR": "Monrovia", "LS": "Maseru", "TH": "Bangkok", "TF": "Port-aux-Francais", "TG": "Lome", "TD": "N\'Djamena", "TC": "Cockburn Town", "LY": "Tripolis", "VA": "Vatican City", "VC": "Kingstown", "AE": "Abu Dhabi", "AD": "Andorra la Vella", "AG": "St. John\'s", "AF": "Kabul", "AI": "The Valley", "VI": "Charlotte Amalie", "IS": "Reykjavik", "IR": "Tehran", "AM": "Yerevan", "AL": "Tirana", "AO": "Luanda", "AQ": "", "AS": "Pago Pago", "AR": "Buenos Aires", "AU": "Canberra", "AT": "Vienna", "AW": "Oranjestad", "IN": "New Delhi", "AX": "Mariehamn", "AZ": "Baku", "IE": "Dublin", "ID": "Jakarta", "UA": "Kiev", "QA": "Doha", "MZ": "Maputo"}');

    //
    $result = (object)[
      'ip' => $data->ip,
      'lat' => $data->latitude,
      'lon' => $data->longitude,
      'city' => empty($data->city)?'mesto sa nedá lokalizovať alebo sa nachádzate na vidieku':$data->city, //
      'country' => $data->country_name,
      'time_zone' => $data->time_zone,
      'capital' => $cities->{$data->country_code},
    ];

    return $result;
  }


  private function getGeoip($request){
    $geoip = $this->geoIP($_SERVER['REMOTE_ADDR']);
    $this->track('geoip',$geoip);

    return [$this->geoIP($_SERVER['REMOTE_ADDR'])];

  }

  private function track($request, $geoip){
    global $objDb;


    if($geoip->time_zone){
      $date = new \DateTime("now", new \DateTimeZone($geoip->time_zone) );
      $time = $date->format('H');
    }else{
      $time = date('H');
    }





    //6:00-14:00, 14:00-20:00, 20:00-24:00, 24:00-6:00
    if($time >= 6 && $time< 14 ){
      $time = '6:00-14:00';
    }elseif($time >= 14 && $time < 20 ){
      $time = '14:00-20:00';
    }elseif($time >= 20 && $time < 24 ){
      $time = '20:00-24:00';
    }elseif($time >= 0 && $time < 6 ){
      $time = '24:00-6:00';
    }


    $data = [
      'country' => $geoip->country,
      'city' => $geoip->city,
      'ip' => $geoip->ip,
      'lat' => $geoip->lat,
      'lon' => $geoip->lon,
      'time' => $time,
      'request' => $request,
      'date' => date('Y-m-d'),

    ];


    $objDb->insert('history',$data);
  }

  private function getStats($request){

    $geoip = $this->geoIP($_SERVER['REMOTE_ADDR']);

    $this->track('stats',$geoip);


    global $objDb;
    $perCountry = $objDb->getRowsByQuery('SELECT `country`, count(*) as "count" FROM (SELECT `country`, count(*)  FROM `history` WHERE 1 GROUP by `ip` , `date`) s1 group by  `country` ');
    $result = [];
    foreach($perCountry as $row){
      $result[] = (object)[
        "key" => $row['country'],
        "val" => $row['count'],
        "filter" => 'country',
      ];
    }

    $result[] = (object)[
      "key" => '========',
      "val" => '========',
      "filter" => '',
    ];


    $perTime = $objDb->getRowsByQuery('SELECT `time`, count(*) count  FROM `history` WHERE 1 GROUP by `time`');

    foreach($perTime as $row){
      $result[] = (object)[
        "key" => $row['time'],
        "val" => $row['count'],
        "filter" => '',
      ];
    }

    $result[] = (object)[
      "key" => '========',
      "val" => '========',
      "filter" => '',
    ];


    $perRequest = $objDb->getRowsByQuery('SELECT `request`, count(*) count  FROM `history` WHERE 1 GROUP by `request`');

    foreach($perRequest as $row){
      $result[] = (object)[
        "key" => $row['request'],
        "val" => $row['count'],
        "filter" => '',
      ];
    }


    return $result;

  }


  private function getStatsCountry($request){
    $geoip = $this->geoIP($_SERVER['REMOTE_ADDR']);

    $this->track('stats',$geoip);


    global $objDb;
    $perCountry = $objDb->getRowsByQuery('SELECT `city`, count(*) as "count" FROM (SELECT `city`, count(*)  FROM `history` WHERE `country` = "'.addslashes($request['country']).'" GROUP by `ip` , `date`) s1 group by  `city` ');
    $result = [];
    foreach($perCountry as $row){
      $result[] = (object)[
        "key" => $row['city'],
        "val" => $row['count'],
      ];
    }

    return $result;
  }

  public function geoJson($request){
    global $objDb;
    $data = $objDb->getRowsByQuery('SELECT `lat`,`lon`  FROM `history` WHERE 1 GROUP by `lat` , `lon` ');

    $geojson = (object) [
      'type' => 'FeatureCollection',
      'features' => []
    ];
    foreach ($data as $item) {
      //dd($item);
      $geojson->features[] = (object) [
        "type" => "Feature",
        "geometry" => (object) [
          "type" => "Point",
          "coordinates" => [$item['lon'], $item['lat']]
        ],
        "properties" => (object) [
//          "title" => false,
//          "description" => false,
          'marker-color' => '#f86767',
          'marker-size' => 'large',
        ]
      ];
    }
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST');
    header("Access-Control-Allow-Headers: X-Requested-With");
    die ((json_encode($geojson)));
  }


  /**
   * @param string $msg
   */
  protected function error($msg = '') {
    error_log("\n" . date(time()) . "API error:$msg");
    header('Content-Type: application/json');
    die(json_encode(['status' => 'error', 'msg' => $msg]));
  }

  /**
   * @param array $data
   * @param string $msg
   */
  protected function success($data = [], $msg = '') {
    header('Content-Type: application/json');
    die(json_encode(['status' => 'ok', 'data' => $data, 'msg' => $msg]));
  }


}
