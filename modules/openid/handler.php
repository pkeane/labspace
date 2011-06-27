<?php
class Dase_ModuleHandler_Openid extends Dase_Handler
{
	public $resource_map = array(
		'/' => 'login',
		'form' => 'login_form',
		'confirmation' => 'confirmation',
		'registration' => 'registration',
		'{eid}' => 'login',
	);

	protected function setup($r)
	{
		$this->save_dir = $r->retrieve('config')->getCacheDir();
		session_save_path($this->save_dir);
		require_once "Auth/OpenID/FileStore.php";
		require_once "Auth/OpenID/Consumer.php";
		require_once "Auth/Yadis/Manager.php";
	}

	//rewrite/replace for alternate authentication
	public function getLogin($r)
	{
		$t = new Dase_Template($r,true);
		//'target' is the page to redirect to after login is complete
		$t->assign('target',$r->get('target'));
		$r->renderResponse($t->fetch('login_form.tpl'));
	}

	public function getRegistration($r)
	{
		$t = new Dase_Template($r,true);
		$r->renderResponse($t->fetch('login_form.tpl'));
	}

	public function getConfirmation($r)
	{
		session_start();
		$store_path = $this->save_dir."/openid_consumer";
		if (!file_exists($store_path) && !mkdir($store_path)) {
			print "Could not create the FileStore directory '$store_path'. ".
				" Please check the effective permissions.";
			exit(0);
		}
		$store = new Auth_OpenID_FileStore($store_path);
		$consumer = new Auth_OpenID_Consumer($store);
		$response = $consumer->complete($r->app_root.'/'.$r->getUrl()); 
		if ('success' == $response->status) {
			$eid = trim(str_replace('http://','',$response->getDisplayIdentifier()),'/');
			//life is simpler w/o dots in eid:
			$eid = str_replace('.','_',$eid);
			$eid = str_replace('/','_',$eid);
			$r->setCookie('eid',$eid);
			$db_user = $r->retrieve('user');
			if (!$db_user->retrieveByEid($eid)) {
				$db_user->eid = strtolower($eid); 
				$db_user->name = $eid; 
				$db_user->insert();
				$params = array(
					'msg' => "Welcome, ".$eid,
				);
				$r->renderRedirect('/',$params);
			}
			//do this so cookie is passed along
			$r->renderRedirect(urldecode($r->get('target')));
		} else {
			//I could probably just display here instead of redirect
			$params['msg'] = 'incorrect username/password';
			$r->renderRedirect("login/form",$params);
		}
	}

	public function getLoginForm($r)
	{
		$t = new Dase_Template($r,true);
		$t->assign('target',$r->get('target'));
		$r->renderResponse($t->fetch('login_form.tpl'));
	}

	public function postToLogin($r)
	{
		session_start();
		$store_path = $this->save_dir."/openid_consumer";
		if (!file_exists($store_path) && !mkdir($store_path)) {
			print "Could not create the FileStore directory '$store_path'. ".
				" Please check the effective permissions.";
			exit(0);
		}
		$store = new Auth_OpenID_FileStore($store_path);
		$consumer = new Auth_OpenID_Consumer($store);
		$auth_request = $consumer->begin($r->get('openid_identifier'));
		if ($auth_request) {
			$redirect_url = $auth_request->redirectURL($r->app_root,$r->app_root.'/login/confirmation');
			if (Auth_OpenID::isFailure($redirect_url)) {
				$r->renderError(500,"Could not redirect to server: " . $redirect_url->message);
			} else {
				$r->renderRedirect($redirect_url);
			}
		} else {
			$params['msg'] = 'sorry, try again';
			$r->renderRedirect('login',$params);
		}
	}

	/**
	 * this method will be called
	 * w/ an http delete to '/login' *or* '/login/{eid}'
	 *
	 */
	public function deleteLogin($r)
	{
		$r->retrieve('cookie')->clear();
		$r->renderRedirect('login/form');
	}

}

