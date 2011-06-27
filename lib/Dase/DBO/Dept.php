<?php

require_once 'Dase/DBO/Autogen/Dept.php';

class Dase_DBO_Dept extends Dase_DBO_Autogen_Dept 
{
	public $proposals = array();

	public function getProposals()
	{
		$props = new Dase_DBO_Proposal($this->db);
		$props->dept_id = $this->id;
		$this->proposals = $props->findAll(1);
		return $this->proposals;
	}

}
