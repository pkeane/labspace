<?php

class Dase_Handler_Proposal extends Dase_Handler
{
	public $resource_map = array(
		'form' => 'form',
		'{id}' => 'proposal',
		'{id}/share' => 'share_form',
		'{id}/chair' => 'proposal_chair',
		'{id}/preview' => 'proposal_preview',
		'{id}/submit' => 'proposal_submit',
		'{id}/plain' => 'proposal_plain',
		'{id}/attachments' => 'attachments',
		'{id}/attachment/{unique_id}' => 'attachment',
		'{id}/user/{eid}' => 'user',
		'{id}/budget_items' => 'budget_items',
		'{id}/budget_item/{budget_item_id}' => 'budget_item',
		'{id}/{section}' => 'section',
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
		if ($this->user->isSuperuser($r->superusers)) {
			$this->is_superuser = true;
		} else {
		//	$r->renderError(401);
		}
	}

	public function getShareForm($r) 
	{
		$t = new Dase_Template($r);
		$p = new Dase_DBO_Proposal($this->db);
		if (!$p->load($r->get('id'))) {
			$r->renderResponse(404);
		}
		if (!$this->user->can('write',$p)) {
			$r->renderError(401);
		}
		$p->getCreator();
		$p->getUsers();
		$t->assign('proposal',$p);
		$r->renderResponse($t->fetch('proposal_share_form.tpl'));
	}

	public function postToShareForm($r) 
	{
		$p = new Dase_DBO_Proposal($this->db);
		if (!$p->load($r->get('id'))) {
			$r->renderResponse(404);
		}
		if (!$this->user->can('write',$p)) {
			$r->renderError(401);
		}
		$user = Dase_DBO_User::findOrCreate($this->db,$r->get('eid'));
		if (!$user) {
			$params['msg'] = 'no such eid in directory';
			$r->renderRedirect('proposal/'.$p->id.'/share',$params);
		}
		$pu = new Dase_DBO_ProposalUser($this->db);
		$pu->eid = $user->eid;
		$pu->proposal_id = $p->id;
		if ($pu->findOne()) {
			$params['msg'] = 'proposal already shared to '.$pu->eid;
			$r->renderRedirect('proposal/'.$p->id.'/share',$params);
		}
		if ($r->get('auth_level')) {
			$pu->auth_level = $r->get('auth_level');
		} else {
			$pu->auth_level = 'read';
		}
		$pu->insert();
		$r->renderRedirect('proposal/'.$p->id.'/share');
	}

	public function getForm($r) 
	{
		$t = new Dase_Template($r);
		$depts = new Dase_DBO_Dept($this->db);
		$depts->is_active = 1;
		$depts->orderBy('name');
		$t->assign('depts',$depts->findAll());
		$r->renderResponse($t->fetch('proposal_form.tpl'));
	}

	public function postToForm($r) 
	{
		$p = new Dase_DBO_Proposal($this->db);
		$p->title = $r->get('title');
		$p->created_by = $this->user->eid;
		$p->created = date(DATE_ATOM);
		$p->dept_id = $r->get('dept_id');
		if (!$p->title || !$p->dept_id) {
			$params['msg'] = "Please enter title and select a department";
			$r->renderRedirect('proposal/form',$params);
		}
		$id = $p->insert();
		$r->renderRedirect('proposal/'.$id);
	}

	public function postToSection($r) 
	{
		$p = new Dase_DBO_Proposal($this->db);
		if (!$p->load($r->get('id'))) {
			$r->renderResponse(404);
		}
		if (!$this->user->can('write',$p)) {
			$r->renderError(401);
		}
		$sec = new Dase_DBO_Section($this->db);
		$sec->ascii_id = $r->get('section');
		if (!$sec->findOne()) {
			$r->renderResponse(404);
		}
		$propsec = new Dase_DBO_ProposalSection($this->db);
		$propsec->section_id = $sec->id;
		$propsec->proposal_id = $p->id;
		if ($propsec->findOne()) {
			$propsec->text = $r->get('text');
			$propsec->update();
		} else {
			$propsec->text = $r->get('text');
			$propsec->insert();
		}
		$r->renderRedirect('proposal/'.$p->id.'#'.$sec->ascii_id);
	}

	public function deleteBudgetItem($r) 
	{
		$p = new Dase_DBO_Proposal($this->db);
		if (!$p->load($r->get('id'))) {
			$r->renderResponse(404);
		}
		if (!$this->user->can('write',$p)) {
			$r->renderError(401);
		}

		$bi = new Dase_DBO_BudgetItem($this->db);
		if ($bi->load($r->get('budget_item_id'))) {
			$bi->delete();
			$r->renderResponse('deleted budget item');
		} else {
			$r->renderError(404);
		}
	}

	public function postToBudgetItems($r) 
	{
		$p = new Dase_DBO_Proposal($this->db);
		if (!$p->load($r->get('id'))) {
			$r->renderResponse(404);
		}
		if (!$this->user->can('write',$p)) {
			$r->renderError(401);
		}

		$bi = new Dase_DBO_BudgetItem($this->db);
		$bi->description = $r->get('description');
		$bi->proposal_id = $p->id;
		$bi->budget_line_id = $r->get('budget_line_id');
		$bi->quantity = $r->get('quantity');
		$bi->price = $r->get('price');
		$bi->created = date(DATE_ATOM);
		$bi->created_by = $this->user->eid;
		$bi->insert();
		$r->renderRedirect('proposal/'.$p->id.'#budget');
	}

	public function postToAttachments($r) 
	{
		$p = new Dase_DBO_Proposal($this->db);
		if (!$p->load($r->get('id'))) {
			$r->renderResponse(404);
		}
		if (!$this->user->can('write',$p)) {
			$r->renderError(401);
		}

		$data_dir = MEDIA_DIR."/attachments";

		$att = new Dase_DBO_Attachment($this->db);
		$att->attachment_type_id = $r->get('attachment_type_id');
		$att->short_desc = $r->get('short_desc');
		$att->uploaded_by = $this->user->eid;
		$att->uploaded = date(DATE_ATOM);
		$att->proposal_id = $p->id;
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

		$r->renderRedirect('proposal/'.$p->id.'#attachments');
	}

	public function getProposalJson($r) 
	{
		$p = new Dase_DBO_Proposal($this->db);
		if (!$p->load($r->get('id'))) {
			$r->renderResponse(404);
		}
		if (!$this->user->can('read',$p)) {
			$r->renderError(401);
		}
		$r->renderResponse($p->asJson());
	}

	public function getProposal($r) 
	{
		$t = new Dase_Template($r);

		$p = new Dase_DBO_Proposal($this->db);
		if (!$p->load($r->get('id'))) {
			$r->renderRedirect('home');
		}
		if (!$this->user->can('read',$p)) {
			$r->renderError(401);
		}

		if ($p->submitted) {
			$r->renderRedirect('proposal/'.$p->id.'/preview');
		}

		$t->assign('sections',$p->getSections());
		$p->getCreator();
		$p->getDept();
		$p->getAttachments();
		$p->getBudgetItems();
		$budget_lines = new Dase_DBO_BudgetLine($this->db);
		$budget_lines->orderBy('name');
		$t->assign('budget_lines',$budget_lines->findAll(1));
		$attachment_types = new Dase_DBO_AttachmentType($this->db);
		$attachment_types->orderBy('name');
		$t->assign('attachment_types',$attachment_types->findAll(1));
		$t->assign('proposal',$p);
		$r->renderResponse($t->fetch('proposal.tpl'));
		
	}

	public function postToProposalSubmit($r)
	{
		$p = new Dase_DBO_Proposal($this->db);
		if (!$p->load($r->get('id'))) {
			$r->renderResponse(404);
		}
		if (!$this->user->can('write',$p)) {
			$r->renderError(401);
		}

		$dept = $p->getDept();
		//EMAIL
		$title = 'Liberal Arts ITS Grant Proposal: '.$p->title;
		$email = $dept->chair_email;
		//$email = 'mikehegedus@mail.utexas.edu';
		//$email = 'pkeane@mail.utexas.edu';
		$chair_name = $dept->chair_name;

		$t = new Dase_Template($r);
		$t->assign('sections',$p->getSections());
		$p->getCreator();
		$p->getDept();
		$p->getAttachments();
		$p->getBudgetItems();
		$t->assign('proposal',$p);
		$h2t = new html2text($t->fetch('plain.tpl'));
		$text = $h2t->get_text();
		$text = 
			"Please find below a LabSpace Proposal submission for your department. Visit the the Labspace Proposal Site ($r->app_root) to comment on and rank the proposals for your department.\n\ndirect link to proposal: $r->app_root/proposal/$p->id/chair\n\n".$text;
	
		$header = 'From: LabSpace Proposal Application'."\r\n";
		$header .= 'Cc: pkeane@mail.utexas.edu, emilyc@mail.utexas.edu' . "\r\n";
		//use $email when its for real
		Dase_Log::debug('sending email to '.$email);
		mail($email,$title,$text,$header);
		mail('pkeane@mail.utexas.edu','[DEBUG] '.$title,$text,$header);
		$submitter_email = $this->user->email;
		$text = 
			"[The following message was sent to your department chair]\n\n".$text;
		mail($submitter_email,$title,$text,$header);
		
		$p->submitted = date(DATE_ATOM);
		$p->submitted_by = $this->user->eid;
		$p->update();
		$r->renderRedirect('proposal/'.$p->id);
	}

	public function postToProposalChair($r)
	{
		$p = new Dase_DBO_Proposal($this->db);
		if (!$p->load($r->get('id'))) {
			$r->renderResponse(404);
		}
		if (!$this->user->can('chair',$p)) {
			$r->renderError(401);
		}
		$p->chair_comments = $r->get('chair_comments');
		$p->update();
		$r->renderRedirect('proposal/'.$p->id.'/chair');
	}

	public function getProposalChair($r) 
	{
		$t = new Dase_Template($r);

		$p = new Dase_DBO_Proposal($this->db);
		if (!$p->load($r->get('id'))) {
			$r->renderResponse(404);
		}
		if (!$this->user->can('chair',$p)) {
			$r->renderError(401,'must be a department chair');
		}

		$t->assign('sections',$p->getSections());
		$p->getDept();
		$p->getCreator();
		$p->getAttachments();
		$p->getBudgetItems();
		$t->assign('proposal',$p);
		$r->renderResponse($t->fetch('proposal_chair.tpl'));
		
	}

	public function getProposalPreview($r) 
	{
		$t = new Dase_Template($r);

		$p = new Dase_DBO_Proposal($this->db);
		if (!$p->load($r->get('id'))) {
			$r->renderResponse(404);
		}
		if (!$this->user->can('read',$p)) {
			$r->renderError(401);
		}

		if ($this->user->can('write',$p) && !$p->submitted) {
			$t->assign('can_edit',true);
		}


		$t->assign('sections',$p->getSections());
		$p->getDept();
		$p->getCreator();
		$p->getAttachments();
		$p->getBudgetItems();
		$t->assign('proposal',$p);
		$r->renderResponse($t->fetch('proposal_preview.tpl'));
		
	}

	//from old app
	//probably won't need as we'll just 
	//send links in email
	public function getProposalPlainTxt($r) 
	{
		$t = new Dase_Template($r);
		$p = new Dase_DBO_Proposal($this->db);
		if (!$p->load($r->get('id'))) {
			$r->renderResponse(404);
		}
		if (!$this->user->can('read',$p)) {
			$r->renderError(401);
		}

		$t->assign('sections',$p->getSections());
		$p->getDept();
		$p->getAttachments();
		$p->getBudgetItems();
		$t->assign('proposal',$p);
		$h2t = new html2text($t->fetch('plain.tpl'));
		$text = $h2t->get_text();
		$r->renderResponse($text);
	}

	public function deleteProposal($r) 
	{
		$p = new Dase_DBO_Proposal($this->db);
		if (!$p->load($r->get('id'))) {
			$r->renderResponse(404);
		}
		if (!$this->user->can('write',$p)) {
			$r->renderError(401);
		}

		$title = $p->title;
		$p->delete();
		$r->renderResponse('deleted proposal '.$title);
	}

	public function getAttachment($r) 
	{
		$p = new Dase_DBO_Proposal($this->db);
		if (!$p->load($r->get('id'))) {
			$r->renderResponse(404);
		}
		if (!$this->user->can('read',$p)) {
			$r->renderError(401);
		}

		$att = new Dase_DBO_Attachment($this->db);
		$att->unique_id = $r->get('unique_id');
		if (!$att->findOne()) {
			$r->renderResponse(404);
		}
		$r->serveFile($att->path,$att->mime_type,false);
	}

	public function deleteAttachment($r) 
	{
		$p = new Dase_DBO_Proposal($this->db);
		if (!$p->load($r->get('id'))) {
			$r->renderResponse(404);
		}
		if (!$this->user->can('write',$p)) {
			$r->renderError(401);
		}

		$att = new Dase_DBO_Attachment($this->db);
		$att->unique_id = $r->get('unique_id');
		if (!$att->findOne()) {
			$r->renderResponse(404);
		}
		//we don't bother deleting file itself
		$att->delete();
		$r->renderResponse('deleted attachment');
	}

	public function deleteUser($r) 
	{
		$p = new Dase_DBO_Proposal($this->db);
		if (!$p->load($r->get('id'))) {
			$r->renderResponse(404);
		}
		if (!$this->user->can('write',$p)) {
			$r->renderError(401);
		}

		$pu = new Dase_DBO_ProposalUser($this->db);
		$pu->eid = $r->get('eid');
		$pu->proposal_id = $p->id;
		if (!$pu->findOne()) {
			$r->renderResponse(404);
		}
		$pu->delete();
		$r->renderResponse('deleted proposal user');
	}
}

