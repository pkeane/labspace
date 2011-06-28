<?php

require_once 'Dase/DBO/Autogen/Proposal.php';

class Dase_DBO_Proposal extends Dase_DBO_Autogen_Proposal 
{
	public $dept;
	public $creator;
	public $attachments = array();
	public $admin_attachments = array();
	public $budget_items = array();
	public $read_users = array();
	public $write_users = array();


	public function delete()
	{
		$target = MEDIA_DIR.'/deleted_proposals/proposal_'.$this->id.'_'.time().'.json';
		file_put_contents($target,$this->asJson());
		$ps = new Dase_DBO_ProposalSection($this->db);
		$ps->proposal_id = $this->id;
		foreach ($ps->findAll(1) as $doomed) {
			$doomed->delete();
		}
		$pu = new Dase_DBO_ProposalUser($this->db);
		$pu->proposal_id = $this->id;
		foreach ($pu->findAll(1) as $doomed1) {
			$doomed1->delete();
		}
		$att = new Dase_DBO_Attachment($this->db);
		$att->proposal_id = $this->id;
		foreach ($att->findAll(1) as $doomed2) {
			$doomed2->delete();
		}
		$bi = new Dase_DBO_BudgetItem($this->db);
		$bi->proposal_id = $this->id;
		foreach ($bi->findAll(1) as $doomed3) {
			$doomed3->delete();
		}
		parent::delete();
	}

	public function asJson()
	{
		$prop_data = array();
		$prop_data['title'] = $this->title;
		$prop_data['creator'] = array();
		$prop_data['sections'] = array();
		$prop_data['attachments'] = array();
		$prop_data['budget_items'] = array();
		$prop_data['users'] = array();

		$creator = $this->getCreator();
		$prop_data['creator']['eid'] = $creator->eid;
		$prop_data['creator']['name'] = $creator->name;

		foreach ($this->getSections() as $section) {
			$prop_data['sections'][] = array('name' => $section->name, 'text' => $section->proposal_data);
		}

		foreach ($this->getAttachments() as $att) {
			$prop_data['attachments'][] = array('desc' => $att->desc, 'name' => 'proposal_'.$this->id.'_'.$att->name);
		}

		foreach ($this->getBudgetItems() as $item) {
			$prop_data['budget_items'][] = array('desc' => $item->description, 'price' => $item->price, 'quantity' => $item->quantity);
		}

		$this->getUsers();
		foreach ($this->read_users as $user) {
			$prop_data['users'][] = array('auth' => 'read', 'eid' => $user->eid);
		}
		foreach ($this->write_users as $user) {
			$prop_data['users'][] = array('auth' => 'write', 'eid' => $user->eid);
		}
		return json_encode($prop_data);
	}

	public function getSections()
	{
		$sections = new Dase_DBO_Section($this->db);
		$sections->addWhere('ascii_id','home','!=');
		$sections->is_active = true;
		$sections->orderBy('sort_order');
		$set = array();
		foreach ($sections->findAll(1) as $section) {
			$section->getProposalData($this->id);
			$set[] = $section;
		}
		return $set;
	}

	public function getDept()
	{
		$d = new Dase_DBO_Dept($this->db);
		if ($d->load($this->dept_id)) {
			$this->dept = $d;
		}
		return $this->dept;
	}

	public function getCreator()
	{
		$user = new Dase_DBO_User($this->db);
		$user->eid = $this->created_by;
		$this->creator = $user->findOne();
		return $this->creator;
	}

	public function getUsers() 
	{
		$pu = new Dase_DBO_ProposalUser($this->db);
		$pu->proposal_id = $this->id;
		foreach ($pu->findAll(1) as $propuser) {
			if ('read' == $propuser->auth_level) {
				$this->read_users[] = $propuser->getUser();
			}
			if ('write' == $propuser->auth_level) {
				$this->write_users[] = $propuser->getUser();
			}
		}
	}

	public function getAdminAttachments()
	{
		$att = new Dase_DBO_Attachment($this->db);
		$att->proposal_id = $this->id;
		$att->is_admin = 1;
		$att->orderBy('uploaded');
		$this->admin_attachments = $att->findAll(1);
		return $this->attachments;
	}

	public function getAttachments()
	{
		$types = new Dase_DBO_AttachmentType($this->db);
		$lookup = array();
		foreach($types->findAll(1) as $type) {
			$lookup[$type->id] = $type->name;
		}

		$att = new Dase_DBO_Attachment($this->db);
		$att->proposal_id = $this->id;
		$att->orderBy('uploaded');
		foreach ($att->findAll(1) as $attachment) {
			if ($attachment->attachment_type_id) {
				$attachment->desc = $lookup[$attachment->attachment_type_id];
			} else {
				$attachment->desc = $attachment->short_desc;
			}
			//do NOT include admin attachments
			if (!$attachment->is_admin) {
					$this->attachments[] = $attachment;
			}
		}
		return $this->attachments;
	}

	public function getBudgetItems()
	{
		$lines = new Dase_DBO_BudgetLine($this->db);
		$lookup = array();
		foreach($lines->findAll(1) as $line) {
			$lookup[$line->id] = $line->name;
		}

		$bi = new Dase_DBO_BudgetItem($this->db);
		$bi->proposal_id = $this->id;
		$bi->orderBy('created');
		foreach ($bi->findAll(1) as $budget_item) {
			if ($budget_item->budget_line_id) {
				$budget_item->description = $lookup[$budget_item->budget_line_id];
			} 
			$budget_item->cost = $budget_item->price * $budget_item->quantity;
			$this->budget_items[] = $budget_item;
		}
		return $this->budget_items;
	}

	public function getBudgetItemsCsv()
	{
			$csv = '"description","vendor/product note","price per unit","quantity","cost"'."\n";
			foreach ($this->getBudgetItems() as $bi) {
					$csv .= '"'.$bi->description.'","'.$bi->note.'","'.$bi->price.'","'.$bi->quantity.'","'.$bi->cost.'"'."\n";
			}
			return $csv;
	}
}
