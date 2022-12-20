<?php
include('function_helper.php');
$token=generate_token();
$ApiUrl='https://teamlease.my.salesforce.com';
if(!empty($_GET['Attachment_id'])){  
  downloadEmailAttachment($ApiUrl,$token,$_GET['Attachment_id']);
}
function downloadEmailAttachment($ApiUrl,$token,$attachment_id){
  $url=$ApiUrl."/services/data/v56.0/sobjects/Attachment/{$attachment_id}";
  $attachment_response=processCurl($url,$token);  
  if(!empty($attachment_response)){
    $url=$ApiUrl.$attachment_response['Body'];
    $response=processCurl($url,$token);
    $file=$attachment_response['Name'];
    $contentType=$attachment_response['ContentType'];

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "authorization: Bearer $token",
        "cache-control: no-cache",
        "postman-token: 767e5723-6cd7-5b78-d455-84ea008c2bf4"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
        header("Cache-Control: no-cache private");
        header("Content-Description: File Transfer");
        header("Content-disposition: attachment; filename=$file");
        header("Content-Type: $contentType");
        header("Content-Transfer-Encoding: binary");
        header('Content-Length: '. strlen($response));
        header("Pragma: no-cache");
        header("Expires: 0");

        ob_clean();
        flush();
        echo $response;
        // exit;
    }
  }
}
// $curl = curl_init();

// curl_setopt_array($curl, array(
//   CURLOPT_URL => "https://teamlease.my.salesforce.com/services/data/v56.0/sobjects/Attachment/00P2x00000O8YpgEAF/Body",
//   CURLOPT_RETURNTRANSFER => true,
//   CURLOPT_ENCODING => "",
//   CURLOPT_MAXREDIRS => 10,
//   CURLOPT_TIMEOUT => 30,
//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//   CURLOPT_CUSTOMREQUEST => "GET",
//   CURLOPT_HTTPHEADER => array(
//     "authorization: Bearer 00D280000018oCD!ARUAQOVHNoeuBJEOY.eReAV_rK0_C.LHPGFbK_j0EeAKZVBrXcUbn.C9TFafYEDtpkpK205cOHVQonnCBt4IDitjdHTY9LIh",
//     "cache-control: no-cache",
//     "postman-token: 767e5723-6cd7-5b78-d455-84ea008c2bf4"
//   ),
// ));

// $response = curl_exec($curl);
// $err = curl_error($curl);

// curl_close($curl);

// if ($err) {
//   echo "cURL Error #:" . $err;
// } else {
//     header("Cache-Control: no-cache private");
//     header("Content-Description: File Transfer");
//     header('Content-disposition: attachment; filename=test.png');
//     header("Content-Type: image/png");
//     header("Content-Transfer-Encoding: binary");
//     header('Content-Length: '. strlen($response));
//     header("Pragma: no-cache");
//     header("Expires: 0");

//     ob_clean();
//     flush();
//     echo $response;
//     // exit;
// }
