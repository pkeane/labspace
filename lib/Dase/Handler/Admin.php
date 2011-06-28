<?php

class Dase_Handler_Admin extends Dase_Handler
{
	public $resource_map = array(
		'/' => 'admin',
		'section_order' => 'section_order_form',
		'sections' => 'sections_form',
		'sections/{section}' => 'sections_form',
		'users' => 'users',
		'user_form/{eid}' => 'user_form',
		'proposals' => 'proposals',
		'proposal/{id}' => 'proposal',
		'proposal/{id}/status' => 'proposal_status',
		'proposal/{id}/attachments' => 'proposal_attachments',
		'depts' => 'depts',
		'dept/{id}' => 'dept',
		'dept/{id}/status' => 'dept_status',
		'budget_line/{id}' => 'budget_line',
		'budget_lines' => 'budget_lines_form',
		'attachment_type/{id}' => 'attachment_type',
		'attachment_types' => 'attachment_types_form',
	);

	protected function setup($r)
	{
		$this->user = $r->getUser();
		if ($this->user->is_admin) {
			//ok
		} else {
			$r->renderError(401);
		}
	}

	public function postToProposalAttachments($r) 
	{
		$p = new Dase_DBO_Proposal($this->db);
		if (!$p->load($r->get('id'))) {
			$r->renderResponse(404);
		}
		$data_dir = MEDIA_DIR."/attachments";

		$att = new Dase_DBO_Attachment($this->db);
		$att->short_desc = $r->get('short_desc');
		$att->uploaded_by = $this->user->eid;
		$att->uploaded = date(DATE_ATOM);
		$att->attachment_type_id = 0;
		$att->proposal_id = $p->id;
		$att->is_admin = 1;
		$att->name = $_FILES['uploaded_file']['name'];
		$tmp_path = $_FILES['uploaded_file']['tmp_name'];
		$target_path = $data_dir.'/'."proposal_".$p->id.'_'.Dase_Util::dirify($att->name);

		$att->unique_id = md5($target_path);
		$att->mime_type = $_FILES['uploaded_file']['type'];
		$att->path = $target_path;
		if (move_uploaded_file($tmp_path,$target_path)) {
			$att->insert();
		} else{
			$r->renderError(400,'no go '.$target_path.' '.$tmp_path);
		}

		$r->renderRedirect('admin/proposal/'.$p->id);
	}

	public function initTemplate($t)
	{
		//useful for menu stuff
		//$t->assign('exercise_sets',Dase_DBO_ExerciseSet::getAll($this->db));
	}

	public function getAttachmentTypesForm($r)
	{
		$t = new Dase_Template($r);
		$bl = new Dase_DBO_AttachmentType($this->db);
		$bl->orderBy('name');
		$t->assign('attachment_types',$bl->findAll());
		$r->renderResponse($t->fetch('admin_attachment_types_form.tpl'));
	}

	public function postToAttachmentTypesForm($r)
	{
		$t = new Dase_Template($r);
		$bl = new Dase_DBO_AttachmentType($this->db);
		$bl->name = $r->get('name');
		$bl->insert();
		$r->renderRedirect('admin/attachment_types');
	}

	public function deleteAttachmentType($r)
	{
		$type = new Dase_DBO_AttachmentType($this->db);
		if ($type->load($r->get('id'))) {
			$att = new Dase_DBO_Attachment($this->db);
			$att->attachment_type_id = $type->id;
			if ($att->findOne()) {
				$r->renderError(412,'there are attachments w/ that attachment type');
			}
			$type->delete();
			$r->renderResponse('deleted attachement type');
		}
		$r->renderError(404);
	}

	public function getBudgetLinesForm($r)
	{
		$t = new Dase_Template($r);
		$bl = new Dase_DBO_BudgetLine($this->db);
		$bl->orderBy('name');
		$t->assign('budget_lines',$bl->findAll());
		$r->renderResponse($t->fetch('admin_budget_lines_form.tpl'));
	}

	public function postToBudgetLinesForm($r)
	{
		$t = new Dase_Template($r);
		$bl = new Dase_DBO_BudgetLine($this->db);
		$bl->name = $r->get('name');
		$bl->insert();
		$r->renderRedirect('admin/budget_lines');
	}

	public function deleteBudgetLine($r)
	{
		$bl = new Dase_DBO_BudgetLine($this->db);
		if ($bl->load($r->get('id'))) {
			$b = new Dase_DBO_BudgetItem($this->db);
			$b->budget_line_id = $bl->id;
			if ($b->findOne()) {
				$r->renderError(412,'cannot delete: budget items for this line exists');
			}
			$bl->delete();
			$r->renderResponse('deleted budget line');
		}
		$r->renderError(404);
	}

	public function getSectionOrderForm($r)
	{
		$t = new Dase_Template($r);
		$sections = new Dase_DBO_Section($this->db);
		$sections->orderBy('sort_order');
		$sections->addWhere('ascii_id','home','!=');
		$t->assign('sections',$sections->findAll());
		$r->renderResponse($t->fetch('admin_section_order_form.tpl'));
	}

	public function postToSectionOrderForm($r)
	{
		$sections = new Dase_DBO_Section($this->db);
		foreach ($sections->findAll() as $s) {
			$s->sort_order = $r->get($s->ascii_id.'_sort_order');
			$s->update();
		}
		Dase_DBO_Section::sortAll($this->db);
		$r->renderRedirect('admin/section_order');
	}

	public function getAdmin($r) 
	{
		$t = new Dase_Template($r);
		$r->renderResponse($t->fetch('admin.tpl'));
	}

	public function getUsers($r) 
	{
		$t = new Dase_Template($r);
		$t->init($this);
		$users = new Dase_DBO_User($this->db);
		$users->orderBy('name');
		$t->assign('users', $users->findAll(1));
		$r->renderResponse($t->fetch('admin_users.tpl'));
	}

	public function postToProposalStatus($r)
	{
		$t = new Dase_Template($r);
		$prop = new Dase_DBO_Proposal($this->db);
		$prop->load($r->get('id'));
		$prop->workflow_status = $r->get('workflow_status');
		$prop->update();
		$r->renderRedirect('admin/proposal/'.$prop->id);
	}

	public function getProposal($r)
	{
		$t = new Dase_Template($r);
		$prop = new Dase_DBO_Proposal($this->db);
		$prop->load($r->get('id'));
		$prop->getCreator();
		$prop->getDept();
		$prop->getAdminAttachments();
		$prop->getBudgetItems();
		$t->assign('proposal', $prop);
		$r->renderResponse($t->fetch('admin_proposal.tpl'));
	}

	public function getProposals($r) 
	{
		$t = new Dase_Template($r);
		$props = new Dase_DBO_Proposal($this->db);
		$props->orderBy('submitted');
		$set = array();
		foreach ($props->findAll(1) as $p) {
				if (!$p->workflow_status) {
						$p->workflow_status = 'proposed';
				}
				if (!isset($set[$p->workflow_status])) {
						$set[$p->workflow_status] = array();
				}
				$p->getCreator();
				$set[$p->workflow_status][] = $p;
		}
		$t->assign('props', $set);
		$r->renderResponse($t->fetch('admin_props.tpl'));
	}

	public function getUserForm($r) 
	{
		$t = new Dase_Template($r);
		$t->init($this);
		$record = Utlookup::getRecord($r->get('eid'));
		$u = new Dase_DBO_User($this->db);
		$u->eid = $r->get('eid');
		if ($u->findOne()) {
			$t->assign('user',$u);
			$t->assign('email',$u->email);
		} else {
			$t->assign('email',$record['email']);
		}
		$t->assign('record',$record);
		$r->renderResponse($t->fetch('admin_user_form.tpl'));
	}

	public function postToUserForm($r)
	{
		$record = Utlookup::getRecord($r->get('eid'));
		$user = new Dase_DBO_User($this->db);
		$user->eid = $record['eid'];
		if (!$user->findOne()) {
			$user->name = $record['name'];
			$user->insert();
		}
		if ($r->get('email')) {
			$user->email = $r->get('email');
		} else {
			$user->email = $record['email'];
		}
		$user->is_admin = $r->get('is_admin');
		$user->is_reviewer = $r->get('is_reviewer');
		$user->update();
		$r->renderRedirect('admin/user_form/'.$user->eid);

	}

	public function getDepts($r) 
	{
		$t = new Dase_Template($r);
		$depts = new Dase_DBO_Dept($this->db);
		$depts->orderBy('name');
		$t->assign('depts',$depts->findAll());
		$r->renderResponse($t->fetch('admin_depts.tpl'));
	}

	public function getDept($r) 
	{
		$t = new Dase_Template($r);
		$dept = new Dase_DBO_Dept($this->db);
		$dept->load($r->get('id'));
		$t->assign('dept',$dept);
		$r->renderResponse($t->fetch('admin_dept.tpl'));
	}

	public function postToDept($r) 
	{
		$t = new Dase_Template($r);
		$dept = new Dase_DBO_Dept($this->db);
		$dept->load($r->get('id'));
		$dept->name = $r->get('name');
		$dept->ascii_id = $r->get('ascii_id');
		$dept->address = $r->get('address');
		$dept->phone = $r->get('phone');
		$dept->chair_name = $r->get('chair_name');
		$dept->chair_title = $r->get('chair_title');
		$dept->chair_eid = $r->get('chair_eid');
		$dept->chair_email = $r->get('chair_email');
		$dept->update();
		$r->renderRedirect('admin/dept/'.$dept->id);
	}

	public function putDeptStatus($r)
	{
		$new_status = $r->getBody();
		$dept = new Dase_DBO_Dept($this->db);
		$dept->load($r->get('id'));
		$dept->is_active = $new_status;
		$dept->update();
		$r->renderResponse('updated dept');
	}

	public function getSectionsForm($r) 
	{
		$t = new Dase_Template($r);
		if ($r->get('section')) {
			$section = new Dase_DBO_Section($this->db);
			$section->ascii_id = $r->get('section');
			if (!$section->findOne()) {
				$r->renderRedirect('admin/sections');
			}
			$t->assign('section',$section);
		}
		$sections = new Dase_DBO_Section($this->db);
		//$sections->orderBy('sort_order');
		$sections->orderBy('name');
		$t->assign('sections',$sections->findAll());
		$r->renderResponse($t->fetch('admin_sections_form.tpl'));
	}

	public function deleteSectionsForm($r) 
	{
		$section = new Dase_DBO_Section($this->db);
		$section->ascii_id = $r->get('section');
		if ($section->findOne()) {
			$ps = new Dase_DBO_ProposalSection($this->db);
			$ps->section_id = $section->id;
			if ($ps->findOne()) {
				$r->renderError(412,'cannot delete: user data exists');
			}
			$section->delete();
			$r->renderResponse('deleted!');
		} else {
			$r->renderError(404);
		}	
	}

	public function postToSectionsForm($r) 
	{
		//used for updating AND creating a section
		//depending on whether {section} is in url
		if ($r->get('section')) {
			$section = new Dase_DBO_Section($this->db);
			$section->ascii_id = $r->get('section');
			if ($section->findOne()) {
				$section->instruction_text = $r->get('instruction_text');
				$section->name = $r->get('name');
				if (!$section->name) {
					$r->renderError(409,'error: no section name (or you clicked the wrong button)');
				}
				$section->type = $r->get('type');
				$section->is_active = $r->get('is_active');
				$section->show_date_input = $r->get('show_date_input');
				$section->show_dollar_input = $r->get('show_dollar_input');
				$section->textbox_size = $r->get('textbox_size');
				$section->update();
				$r->renderRedirect('admin/sections/'.$r->get('section'));
			} else {
				$r->renderError(404);
			}	
		} else {
			$section = new Dase_DBO_Section($this->db);
			$section->ascii_id = Dase_Util::dirify($r->get('name'));
			if ($section->findOne()) {
				$r->renderError(409,'please choose another name');
			}
			$section->name = $r->get('name');
			if (!$section->name) {
				$r->renderError(409,'error: no section name (or you clicked the wrong button)');
			}
			$section->type = $r->get('type');
			$section->is_active = 1;
			$section->show_date_input = $r->get('show_date_input');
			$section->show_dollar_input = $r->get('show_dollar_input');
			$section->insert();
		}
		$r->renderRedirect('admin/sections');
	}

}

