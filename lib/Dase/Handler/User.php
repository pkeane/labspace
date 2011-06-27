<?php

class Dase_Handler_User extends Dase_Handler
{
	public $resource_map = array(
		'/' => 'user',
		'settings' => 'settings',
		'proposals' => 'proposals',
		'email' => 'email'
	);

	protected function setup($r)
	{
		$this->user = $r->getUser();
	}

	public function getSettings($r) 
	{
		$t = new Dase_Template($r);
		$this->user->getChairedDepts();
		$r->renderResponse($t->fetch('user_settings.tpl'));
	}

	public function getUser($r) 
	{
		$t = new Dase_Template($r);
		$r->renderResponse($t->fetch('home.tpl'));
	}

	public function postToEmail($r)
	{
		$this->user->email = $r->get('email');
		$this->user->update();
		$r->renderRedirect('user/settings');
	}

	public function getProposals($r)
	{
		$t = new Dase_Template($r);
		$this->user->getProposals();
		if ($this->user->is_admin || $this->user->is_reviewer) {
			$props = new Dase_DBO_Proposal($this->db);
			$props->orderBy('title');
			$all_proposals = array();
			foreach ($props->findAll(1) as $prop) {
				$prop->getCreator();
				$all_proposals[] = $prop;
			}
			$t->assign('all_proposals',$all_proposals);
		}
		$r->renderResponse($t->fetch('proposals.tpl'));
	}
}

