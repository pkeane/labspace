<?php

class Dase_Handler_Home extends Dase_Handler
{
	public $resource_map = array(
		'/' => 'home',
		'{id}' => 'thing',
	);

	protected function setup($r)
	{
		$valid_users = Dase_Json::toPhp(file_get_contents(BASE_PATH.'/data/valid_users.json'));
		$this->user = $r->getUser();
		if (!in_array($this->user->eid,$valid_users)) {
			setcookie('DOC','',time()-86400,'/','.utexas.edu');
			setcookie('FC','',time()-86400,'/','.utexas.edu');
			setcookie('SC','',time()-86400,'/','.utexas.edu');
			setcookie('TF','',time()-86400,'/','.utexas.edu');
			$r->clearCookies();
			$r->renderError(401,'unauthorized: current UT Faculty & Staff Only');
		}
	}

	public function getHome($r) 
	{
		$t = new Dase_Template($r);
		$sec = new Dase_DBO_Section($this->db);
		$sec->ascii_id = 'home';
		$sec->findOne();
		$t->assign('content',$sec->instruction_text);
		$r->renderResponse($t->fetch('home.tpl'));
	}

	public function postToHome($r) 
	{
		$user = $r->getUser();
		//do stuff
		$r->renderRedirect('home');
	}

	public function getThing($r) 
	{
		$r->renderResponse($r->get('id'));
	}
}

