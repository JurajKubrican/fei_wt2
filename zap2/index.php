<link href="css/bootstrap.min.css" rel="stylesheet">

<form class="form" action="">
  <input class="form-control" type="text" name="url" value="<?= empty($_GET['url']) ? 'https://wt.knet.sk/zap2/lorem' : $_GET['url'] ?>">
  <input class="form-control" type="text" name="search" value="<?= empty($_GET['search']) ? 'ipsum' : $_GET['search'] ?>">
  <input class="form-control" type="submit">
</form>

<?php
ini_set('display_errors',true);
error_reporting (E_ALL);


if(empty($_GET['url']) || empty($_GET['search']))
  die;

$ch = curl_init($_GET['url']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
$result = strip_tags($result);
$pattern = preg_quote($_GET['search']);
$new = preg_replace("/($pattern)/", "<span>$0</span>", $result);

?>
<div class="result">
  <?=$new?>
</div>
<style>
  .result {margin:5px; border:1px solid mediumspringgreen;}
  .result span { background-color:mediumvioletred;}
</style>



