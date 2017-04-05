<HTML>
<head>
  <title>WT</title>
</head>
<?php
foreach(glob(__DIR__.'/*', GLOB_ONLYDIR) as $val){
  ?><a href="<?=basename($val)?>"><?=basename($val)?></a><br><?php
}
?>
</body>
</HTML>