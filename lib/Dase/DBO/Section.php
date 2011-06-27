<?php

require_once 'Dase/DBO/Autogen/Section.php';

class Dase_DBO_Section extends Dase_DBO_Autogen_Section 
{
	public $proposal_data = '';

	public static function sortAll($db)
	{
		$sections = new Dase_DBO_Section($db);
		$sections->orderBy('sort_order');
		$sections->addWhere('ascii_id','home','!=');
		$i = 0;
		foreach ($sections->findAll(1) as $s) {
			$i++;
			$s->sort_order = $i;
			$s->update();
		}
		return $i;
	}

	public function getProposalData($proposal_id)
	{
		$ps = new Dase_DBO_ProposalSection($this->db);
		$ps->section_id = $this->id;
		$ps->proposal_id = $proposal_id;
		if ($ps->findOne()) {
			$this->proposal_data = $ps->text;
		}
		return $this->proposal_data;
	}
}
