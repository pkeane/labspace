<?php

require_once 'Dase/DBO/Autogen/User.php';

class Dase_DBO_User extends Dase_DBO_Autogen_User 
{
	public $is_superuser;
	public $is_chair;
	public $created_proposals = array();
	public $readable_proposals = array();
	public $writeable_proposals = array();
	public $chair_proposals = array();
	public $chaired_depts = array();

	public static function get($db,$id)
	{
		$user = new Dase_DBO_User($db);
		if ($user->load($id)) {
			return $user;
		} else {
			return false;
		}
	}

	public static function findOrCreate($db,$eid) 
	{
		$user = new Dase_DBO_User($db);
		$user->eid = $eid;
		if ($user->findOne()) {
			return $user;
		}
		$person = Utlookup::getRecord($eid);
		if ($person) {
			$user = new Dase_DBO_User($db);
			$user->name = $person['name'];
			$user->eid = $person['eid'];
			$user->email = $person['email'];
			$user->insert();
			return $user;
		} else {
			return false;
		}
	}

	public function can($auth_level,$proposal)
	{
		if ('chair' != $auth_level && $this->eid == $proposal->created_by) {
			return true;
		}
		if ('read' == $auth_level && $this->is_admin) {
			return true;
		}
		if ('read' == $auth_level && $this->is_reviewer) {
			return true;
		}
		$this->getProposals();
		if ('chair' == $auth_level) {
			foreach ($this->chair_proposals as $prop) {
				if ($prop->id == $proposal->id) {
					return true;
				}
			}
			return false;
		}
		if ('read' == $auth_level && array_key_exists($proposal->id,$this->readable_proposals)) {
			return true;
		}
		if ('write' == $auth_level && $proposal->submitted) {
			return false;
		}
		if ('write' == $auth_level && array_key_exists($proposal->id,$this->writeable_proposals)) {
			return true;
		}
		return false;
	}

	public function getChairedDepts()
	{
		$dept = new Dase_DBO_Dept($this->db);
		$dept->chair_eid = $this->eid;
		$this->chaired_depts = $dept->findAll(1);
		if (count($this->chaired_depts)) {
			$this->is_chair = true;
		}
		return $this->chaired_depts;
	}

	public function getProposals()
	{
		//should cache this lookup!
		$created = new Dase_DBO_Proposal($this->db);
		$created->created_by = $this->eid;
		$this->created_proposals = $created->findAll(1);
		$propuser = new Dase_DBO_ProposalUser($this->db);
		$propuser->eid = $this->eid;
		foreach ($propuser->findAll(1) as $pu) {
			if ('read' == $pu->auth_level) {
				$re = $pu->getProposal();
				$this->readable_proposals[$re->id] = $re;
			}
			if ('write' == $pu->auth_level) {
				$wr = $pu->getProposal();
				$this->writeable_proposals[$wr->id] = $wr;
			}
		}
		foreach ($this->getChairedDepts() as $dept) {
			foreach ($dept->getProposals() as $prop) {
				$this->chair_proposals[] = $prop;
			}
		}
	}

	public function retrieveByEid($eid)
	{
		$prefix = $this->db->table_prefix;
		$dbh = $this->db->getDbh(); 
		$sql = "
			SELECT * FROM {$prefix}user 
			WHERE lower(eid) = ?
			";	
		$sth = $dbh->prepare($sql);
		$sth->execute(array(strtolower($eid)));
		$row = $sth->fetch();
		if ($row) {
			foreach ($row as $key => $val) {
				$this->$key = $val;
			}
			Dase_Log::debug(LOG_FILE,'DEBUG: retrieved user '.$eid);
			return $this;
		} else {
			Dase_Log::debug(LOG_FILE,'DEBUG: could NOT retrieve user '.$eid);
			return false;
		}
	}

	public function setHttpPassword($token)
	{
		$this->http_password = substr(md5($token.$this->eid.'httpbasic'),0,12);
		return $this->http_password;
	}

	public function getHttpPassword($token=null)
	{
		if (!$token) {
			if ($this->http_password) {
				//would have been set by request
				return $this->http_password;
			}
			throw new Dase_Exception('user auth is not set');
		}
		if (!$this->http_password) {
			$this->http_password = $this->setHttpPassword($token);
		}
		return $this->http_password;
	}

	public function isSuperuser($superusers)
	{
		if (in_array($this->eid,array_keys($superusers))) {
			$this->is_superuser = true;
			return true;
		}
		return false;
	}
}
