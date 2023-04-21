<?php

// Where to create login access : https://console.cloud.google.com/apis/credentials/
// tutorial : https://www.webslesson.info/2019/09/how-to-make-login-with-google-account-using-php.html
// official doc : https://developers.google.com/identity/protocols/oauth2/web-server#prerequisites

class Google_oauth
{
	var $client_id;
	var $secret;
	var $active;
	var $redirectUri;

	public function __construct() {
		$this->client_id = CONFIG_GOOGLE_OAUTH_CLIENT_ID;
		$this->secret = CONFIG_GOOGLE_OAUTH_CLIENT_SECRET;
		$this->active = CONFIG_GOOGLE_OAUTH_ACTIVE;
		$this->redirectUri = CONFIG_SOPLANNING_URL;
	}

	public function getLink() {
		if($this->active == 1){
			$google_client = new Google_Client();
			$google_client->setClientId($this->client_id);
			$google_client->setClientSecret($this->secret);
			$google_client->setRedirectUri($this->redirectUri);
			$google_client->addScope('email');
			//$google_client->addScope('profile');
			return $google_client->createAuthUrl();
		} else{
			return "google OAuth not activated";
		}
	}

	public function getAccess($code){
		//It will Attempt to exchange a code for an valid authentication token.
		$google_client = new Google_Client();
		$google_client->setClientId($this->client_id);
		$google_client->setClientSecret($this->secret);
		$google_client->setRedirectUri($this->redirectUri);
		$google_client->addScope('email');
		$token = $google_client->fetchAccessTokenWithAuthCode($code);

		//This condition will check there is any error occur during geting authentication token. If there is no any error occur then it will execute if block of code/
		if(isset($token['error'])) {
			throw new Exception('google_sso_error_connection');
		} else{
			//Set the access token used for requests
			$google_client->setAccessToken($token['access_token']);

			//Create Object of Google Service OAuth 2 class
			$google_service = new Google_Service_Oauth2($google_client);

			//Get user profile data from google
			$data = $google_service->userinfo->get();

			if(empty($data['email'])) {
				throw new Exception('google_sso_error_empty_email');
			}
			return $data['email'];
		}
	}
}
?>
