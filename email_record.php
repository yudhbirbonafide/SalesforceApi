<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);
$ApiUrl='https://teamlease.my.salesforce.com';
$token="";
include('function_helper.php');
$time = time()+(60*60*6);
if(!empty($_COOKIE['saleforce_token'])) {
	$token=$_COOKIE['saleforce_token'];
}else{
	$token=generate_token();
	setcookie("saleforce_token", $token, 0);
	// setcookie("saleforce_token", $token, 2147483647);
}
$case_id=(!empty($_GET['case_id']))?$_GET['case_id']:'5002x00000RYfem';//'5002x00000W6dAZ';//'5002x00000W5M9T';
$resultant=getCaseEmails($ApiUrl,$case_id,$token);

?>

<style>
	body{background-color: #fff !important;}
	#content_body img{display: none;}
</style>
<table border="0" width="80%">
	<tr><th>Email Content</th></tr>
	<?php 
		if(!empty($resultant)){
		foreach ($resultant as $key => $emailContent) { ?>			
			<tr>
				<td id="content_body"><?php echo (!empty($emailContent['HtmlBody']))?$emailContent['HtmlBody']:"";?>
				<?php if(!empty($emailContent['HasAttachment'])){ ?>
					<?php 
						$attachments=getEmailAttachments($ApiUrl,@$emailContent['Id'],$token);
					?>
					<ul>	
					<?php if(!empty($attachments)){
						echo "<p>Download Attachment</p>";
						foreach ($attachments as $key => $attach_val) {?>
							<li><a href="info.php?Attachment_id=<?php echo $attach_val['Id']; ?>" target="_blank"><?php echo $attach_val['name']; ?></a></li>
						<?php }}
					?>	
					</ul>	
					
				<?php }?>
				</td>
			</tr>			
	<?php }}?>
</table>
