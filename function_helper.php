<?php 
function generate_token(){
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://login.salesforce.com/services/oauth2/token?grant_type=password&client_id=3MVG9ZL0ppGP5UrDXFilQ2bj1wxh4cWXx2.K0U3rNglPS3TmhRFHfm3hJZiLG9H1mv6xYsRAJ4lu5C9ST6PK1&client_secret=E259B718DFA2C61804F7CC1283579CCE299C46557CE4B77B4A731FE9A04459E7&username=bpi%40teamlease.com&password=LASTDAY%401122",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",	  
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);
	$response=json_decode($response,1);
	if (!empty($response)) {
		return $response['access_token'];
	} 
}

function getCaseEmails($ApiUrl,$case_id,$token){
	$query=rawurlencode("SELECT id,(select id from EmailMessages) from Case WHERE id='{$case_id}'");	
	$url=$ApiUrl."/services/data/v56.0/query?q={$query}";
	$response=processCurl($url,$token);
	$contentEmailRecords=[];

	if (!empty($response['records'][0]['EmailMessages'])) {
		$emailMessages=$response['records'][0]['EmailMessages'];
		$emailMessagesRecords=(!empty($emailMessages['records']))?$emailMessages['records']:[];
		if(!empty($emailMessagesRecords)){
			foreach ($emailMessagesRecords as $key => $email_msg_records) {
				 // echo "<pre>";print_r($email_msg_records);echo "</pre>";die;
				$request_url=(!empty($email_msg_records['attributes']['url']))?$email_msg_records['attributes']['url']:"";
				$contentEmailRecords[]=fetchEmailContentByID($ApiUrl.$request_url,$token);
			}
		}
	}
	return $contentEmailRecords;

}

function fetchEmailContentByID($url,$token){
	$response=processCurl($url,$token);
	if (!empty($response)) {
		return $response;
	}
}

function processCurl($url,$token,$method='GET'){
	$curl = curl_init();	
	curl_setopt_array($curl, array(
	  CURLOPT_URL => $url,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => $method,
	  CURLOPT_HTTPHEADER=>array("authorization: Bearer $token")
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);
	curl_close($curl);
	if(!empty($err)){
		echo "<pre>";print_r($err);echo "</pre>";die;
	}else{
		$response=json_decode($response,1);
		return $response;
	}
	
}
function getEmailAttachments($ApiUrl,$email_id,$token){
	$query=rawurlencode("SELECT id,(select id,Name,ContentType from Attachments) from EmailMessage WHERE id='{$email_id}'");	
	$url=$ApiUrl."/services/data/v56.0/query?q={$query}";
	$response=processCurl($url,$token);
	$contentEmailRecords=[];
	// echo "<pre>";print_r($response);echo "</pre>";die;

	if (!empty($response['records'][0]['Attachments'])) {
		$emailAttachments=$response['records'][0]['Attachments'];
		$emailAttachmentsRecords=(!empty($emailAttachments['records']))?$emailAttachments['records']:[];
		if(!empty($emailAttachmentsRecords)){
			foreach ($emailAttachmentsRecords as $key => $email_msg_records) {
				 // echo "<pre>";print_r($email_msg_records);echo "</pre>";//die;
				$request_url=(!empty($email_msg_records['attributes']['url']))?$email_msg_records['attributes']['url']:"";
				$main_url=$ApiUrl.$request_url.'/Body';
				$contentEmailRecords[]=['url'=>$main_url,'Id'=>$email_msg_records['Id'],'name'=>$email_msg_records['Name'],'ContentType'=>$email_msg_records['ContentType']];
				// $contentEmailRecords[]=$test=fetchEmailContentByID($main_url,$token);
				// echo "<pre>";print_r($test);echo "</pre>";
			}
		}
	}
	return $contentEmailRecords;

}
// function downloadEmailAttachment($ApiUrl,$token,$attachment_id){
// 	$url=$ApiUrl."/services/data/v56.0/sobjects/Attachment/{$attachment_id}";
// 	$attachment_response=processCurl($url,$token);	
// 	if(!empty($attachment_response)){
// 		$url=$ApiUrl.$attachment_response['Body'];
// 		$response=processCurl($url,$token);
// 		$file=$attachment_response['Name'];
// 		$contentType=$attachment_response['ContentType'];
// 		header("Cache-Control: no-cache private");
// 	    header("Content-Description: File Transfer");
// 	    header('Content-disposition: attachment; filename='.$file);
// 	    header("Content-Type: $contentType");
// 	    header("Content-Transfer-Encoding: binary");
// 	    header('Content-Length: '. strlen($response));
// 	    header("Pragma: no-cache");
// 	    header("Expires: 0");

// 	    ob_clean();
// 	    flush();
// 	    echo $response;
// 	}
// }






?>