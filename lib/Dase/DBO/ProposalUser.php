<?php

require_once 'Dase/DBO/Autogen/ProposalUser.php';

class Dase_DBO_ProposalUser extends Dase_DBO_Autogen_ProposalUser 
{
	public $proposal;
	public $user;

	public function getUser()
	{
		$user = new Dase_DBO_User($this->db);
		$user->eid = $this->eid;
		//it is possible that a user is not yet registered
		if ($user->findOne()) {
			$this->user = $user;
			return $this->user;
		} else {
			//just has eid
			//$user = new Dase_DBO_User($this->db);
			//$user->eid = $this->eid;
			return $user;
		}
	}

	public function getProposal()
	{
		$proposal = new Dase_DBO_Proposal($this->db);
		$proposal->load($this->proposal_id);
		$this->proposal = $proposal;
		return $this->proposal;
	}

}
