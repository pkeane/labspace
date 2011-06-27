<?php

class Dase_Handler_Review extends Dase_Handler
{
	public $resource_map = array(
		'/' => 'proposals',
		'proposal/{id}' => 'proposal_review',
		'proposal/{id}/comments' => 'comments',
		'proposal/{id}/comment/{comment_id}' => 'comment',
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
		if (!$this->user->is_reviewer) {
			$r->renderError(401);
		}
	}

	public function postToComments($r)
	{
		$prc = new Dase_DBO_ProposalReviewerComment($this->db);
		$prc->proposal_id = $r->get('id');
		$prc->text = $r->get('text');
		$prc->timestamp = date(DATE_ATOM);
		$prc->reviewer_eid = $this->user->eid;
		$prc->insert();
		$r->renderRedirect('review/proposal/'.$r->get('id').'#reviewer_comments');

	}

	public function deleteComment($r)
	{
		$p = new Dase_DBO_Proposal($this->db);
		if (!$p->load($r->get('id'))) {
			$r->renderResponse(404);
		}
		$prc = new Dase_DBO_ProposalReviewerComment($this->db);
		if ($prc->load($r->get('comment_id'))) {
			if ($prc->reviewer_eid = $this->user->eid) {
				$prc->delete();
			}
		}
		$r->renderResponse('comment deleted');
	}

	public function getProposalReview($r) 
	{
		$t = new Dase_Template($r);

		$p = new Dase_DBO_Proposal($this->db);
		if (!$p->load($r->get('id'))) {
			$r->renderResponse(404);
		}

		$sections = new Dase_DBO_Section($this->db);
		$sections->addWhere('ascii_id','home','!=');
		$sections->is_active = true;
		$sections->orderBy('sort_order');
		$set = array();
		foreach ($sections->findAll(1) as $section) {
			$section->getProposalData($p->id);
			$set[] = $section;
		}

		$prc = new Dase_DBO_ProposalReviewerComment($this->db);
		$prc->proposal_id = $p->id;
		$prc->orderBy('timestamp DESC');
		$t->assign('reviewer_comments',$prc->findAll());

		$t->assign('sections',$set);
		$p->getDept();
		$p->getCreator();
		$p->getAttachments();
		$p->getBudgetItems();
		$t->assign('proposal',$p);
		$r->renderResponse($t->fetch('review_proposal.tpl'));

	}
}

