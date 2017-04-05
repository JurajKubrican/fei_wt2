<form action="">
  <input type="number" name="rok" value="2017">
  <input type="submit">
</form>

<?php
ini_set('display_errors',true);
error_reporting (E_ALL);

setlocale(LC_TIME,'sk_SK.utf8');


if(empty($_REQUEST['rok']))
  die;

$year = filter_var( $_REQUEST['rok'],FILTER_SANITIZE_NUMBER_INT );

$holidays = [
  '1 January' => 'Deň vzniku Slovenskej republiky',
  '6 January' => 'Zjavenie Pána (Traja králi)',
  '1 May' => 'Sviatok práce',
  '8 May' => 'Deň víťazstva nad fašizmom',
  '5 July' => 'Sviatok Svätého cyrila a metoda',
  '29 August' => 'Výročie SNP',
  '1 September' => 'Deň ústavy Slovenskej Replubliky',
  '15 September' => 'Sedembolestná panna mária',
  '1 November' => 'Sviatok všetkých svätých',
  '17 November' => 'Deň boja za slobodu a demokraciu',
  '24 December' => 'Štedrý deň',
  '25 December' => 'Prvý sviatok vianočný',
  '26 December' => 'Druhý sviatok vianočný',
];

$days = [
  'Pondelok'=>[],
  'Utorok'=>[],
  'Streda'=>[],
  'Štvrtok'=>[],
  'Piatok'=>[],
];

foreach($holidays as $sHDate => $holiday){

  $timestamp = strtotime($sHDate . $year);
  $date = date("Y-m-d H:i:s",$timestamp);
  $doW = strftime('%A',$timestamp);

  if( !isset($days[$doW]) ){
    continue;
  }
  $days[$doW][] = (object)[
    'date'=>$sHDate,
    'name'=>$holiday,
  ];
}

echo <<<HTML
<h1>$year</h1>
HTML;

foreach($days as $day => $data){
  if(empty($data))
    continue;

  echo <<<HTML
<h2>$day</h2><br/>
HTML;

  foreach($data as $holiday){
    echo <<<HTML
&nbsp;-&nbsp;<b>$holiday->name</b>&nbsp;-&nbsp;$holiday->date<br/>
HTML;
  }
}

