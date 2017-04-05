<?php


namespace wt;
class AISConnect {

  protected $sAuthCookie;
  protected $sName;
  protected $objDB;

  public function request($sRequest, $aPost = []) {


    $pSession = curl_init();
    curl_setopt($pSession, CURLOPT_URL, $sRequest);

    //POST
    if (!empty($aPost)) {
      curl_setopt($pSession, CURLOPT_POST, true);
      curl_setopt($pSession, CURLOPT_POSTFIELDS, $aPost);
    }
    curl_setopt($pSession, CURLINFO_HEADER_OUT, false);

    //DATA
    curl_setopt($pSession, CURLOPT_RETURNTRANSFER, true);

    $sResponse = curl_exec($pSession);

    curl_close($pSession);


    return $sResponse;
  }

}
