<?php
/*
 *	Facebook BetterSDK Handle Class
 *	By: Avihay Menahem
 *	Email: Avihay@hazvuv.com
 *	
 *	To start using the class, first you need to config the class file
 *	according to your Facebook application details:
 *	
 *	$this->appID = 'YOUR_APP_ID';
 *	$this->appSecret = 'YOUR_APP_SECRET';
 *	$this->perArray = array('user_about_me', 'publish_stream');
 *	$this->appPage = 'YOUR_APP_FAN_PAGE';
 *	
 *	Include this file wherever you want to perform the action
 *	and start a new session for the class with the user signed request.
 *	Now, you have an active session for the current user, and to access
 *	his parameters all you need to do is just get theme that way:
 *	
 *	include('class.facebook.php');
 *	$facebook = new Facebook($_REQUEST['signed_request']);
 *	
 *	$facebook->userID;				 // User ID returns int num, Example: 731580237
 *	$facebook->pageID;				 // Page ID returns int num, Example: 140238812752434
 *	$facebook->pageLiked; 		 // Check if user has liked the page returns BOOLEAN, Example: 1
 *	$facebook->pageAdmin; 		 // Check if user is admin of the page returns BOOLEAN, Example: 1
 *	$facebook->accessToken; 	 // Get the user's oauth_token Returns String, Example: AAAEfL9792ukBAJgrqjoivyKl3J6ZARzPuW2i9cbI6wpCA9fZB0QPCLc6ZBdRzrjT7rZC8ZCju98lbXlILBu8UonyZBPjnNZA0sZD
 *
 *	To perform an like or comment on a object you need to do it this way:
 *
 *	Comment: 									$facebook->graphAction(POST_ID,"comments", ACCESS_TOKEN, THE_MESSAGE);
 *	Like: 										$facebook->graphAction(POST_ID,"likes", ACCESS_TOKEN);
 *	Post To Wall (user/page): $facebook->graphPost(USER_ID/PAGE_ID, ACCESS_TOKEN, THE_MESSAGE);
 *
 *	FREE TO USE UNDER THE MIT LICENSE
 *	http://www.opensource.org/licenses/mit-license.php
 *
 *
 *
 *	FOR MORE Q&A EMAIL ME TO: Avihay@hazvuv.com
 */
class Facebook {
	
	protected $graphUrl;
	protected $appID;
	protected $appSecret;
	protected $appPage;
	protected $permArray;

	public $pageID;
	public $pageLiked;
	public $pageAdmin;
	public $userID;
	public $accessToken;
	
	function __construct($signed)
	{
		$this->graphUrl = 'https://graph.facebook.com/';
		$this->appID = '315765441813225';
		$this->appSecret = 'ba8f63f87c50204c0e0c7a7d8e320c48';
		$this->appPage = 'https://www.facebook.com/pages/Test-Fan-Page/140238812752434?sk=app_315765441813225';
		$this->permArray = array('user_about_me', 'offline_access', 'publish_checkins', 'publish_stream');
		
		$this->parseSR($signed);
	}
	
	public function getLoginUrl()
	{
		$scope = implode(",", $this->permArray);
		$loginUrl = 'https://www.facebook.com/dialog/oauth/?scope='.$scope.'&client_id='.$this->appID.'&redirect_uri='.$this->appPage;
		header('Location: '.$loginUrl);
	}
	
	// Get Details On A Certain Objects Of Current User, For Example: User Photos, User Posts, Etc...
	public function userGraphDetails($obj)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->graphUrl.$this->userID."/".$obj."?access_token=".$this->accessToken);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 10000);
		$out = curl_exec($ch);
		$data = json_decode($out, true);
		curl_close ($ch);
		return $data;	
	}
	
	// Perform A Graph Like Or Comment Action On Object
	public function graphAction($obj,$action,$access = NULL, $message = NULL)
	{
		//if($access == NULL) { $access = $this->accessToken; }
		$attachment =  array('access_token'  => $access, 'message' => $message);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$this->graphUrl.$obj."/".$action."?access_token=".$access);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POST, true);
		if($message != NULL) { curl_setopt($ch, CURLOPT_POSTFIELDS, $attachment); }
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$out = curl_exec($ch);
		curl_close ($ch);
	}
	
	public function graphPost($to, $access, $message)
	{
		$attachment =  array('access_token'  => $access, 'message' => $message);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$this->graphUrl.$to."/feed");
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $attachment);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$out= curl_exec($ch);
		curl_close ($ch);
	}
	
	// Assign Current Vars To Class From Signed Request Decoding
	protected function parseSR($signed)
	{
		$sigarray = $this->parseSignedRequest($signed);
		$this->userID = $sigarray['user_id'];
		$this->pageID = $sigarray['page']['id'];
		$this->pageLiked = $sigarray['page']['liked'];
		$this->pageAdmin = $sigarray['page']['admin'];
		$this->accessToken = $sigarray['oauth_token'];
	}
	
	
	// Facebook Signed Request Decoding
	protected function parseSignedRequest($signed)
	{
		list($encoded_sig, $payload) = explode('.', $signed, 2);
		$sig = $this->base64_url_decode($encoded_sig);
		$data = json_decode($this->base64_url_decode($payload), true);
		
		if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
			error_log('Unknown algorithm. Expected HMAC-SHA256');
			return null;
		}
		
		$expected_sig = hash_hmac('sha256', $payload, $this->appSecret, $raw = true);
		if ($sig !== $expected_sig) {
			error_log('Bad Signed JSON signature!');
			return null;
		}
		
		return $data;
	}
	
	protected function base64_url_decode($input) {
		return base64_decode(strtr($input, '-_', '+/'));
	}
}
?>