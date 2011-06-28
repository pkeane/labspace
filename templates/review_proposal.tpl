{extends file="preview_layout.tpl"}

{block name="content"}
<div class="main">
	<div class="controls">
		<a href="user/proposals">return to proposals list</a>
	</div>
	<h1 class="title">Proposal: {$proposal->title}</h1>
	<dl id="proposal">
		<dt>Title</dt>
		<dd>{$proposal->title}</dd>
		<dt>Department</dt>
		<dd>{$proposal->dept->name}</dd>
		<dt>Created By</dt>
		<dd>{$proposal->creator->name}</dd>


		{foreach item=section from=$sections}
		<a name="{$section->ascii_id}"></a>
		<dt>{$section->name}</dt>
		<dd>
		<div>
			{$section->proposal_data|markdown|nl2br}
			<dl class="section">
				{if $section->show_date_input}
				<dt>Date Start</dt>
				<dd>{$section->date_start}</dd>
				<dt>Date End</dt>
				<dd>{$section->date_end}</dd>
				{/if}
				{if $section->show_dollar_input}
				<dt>Dollar Amount</dt>
				<dd>$ {$section->dollar_amount}</dd>
				{/if}
			</dl>
		</div>
		{if 'attachments' == $section->ascii_id}
		<ul class="attachments">
			{foreach item=attachment from=$proposal->attachments}
			<li><a href="proposal/{$proposal->id}/attachment/{$attachment->unique_id}">{$attachment->name}</a> 
			({$attachment->desc})
			</li>
			{/foreach}
		</ul>
		{/if}

		{if 'budget' == $section->ascii_id}
		<table id="budget_items" class="budget_items">
			<tr>
				<th>description</th>
				<th>vendor/product note</th>
				<th>price per unit</th>
				<th>quantity</th>
				<th>total</th>
			</tr>
			{foreach item=budget_item from=$proposal->budget_items}
			{assign var=total value=$total+$budget_item->cost} 
			<tr>
				<td>
					{$budget_item->description}
				</td>
				<td>
					{$budget_item->note}
				</td>
				<td>
					$ {$budget_item->price|string_format:"%.2f"}
				</td>
				<td>
					{$budget_item->quantity}
				</td>
				<td>
					$ {$budget_item->cost|string_format:"%.2f"}
				</td>
			</tr>
			{/foreach}
			<tr>
				<th colspan="4">total budget:</th>
				<td>$ {$total|string_format:"%.2f"}</td>
			</tr>
		</table>
		{/if}

		</dd>
		{/foreach}

		<dt>Department Chair Comments</dt>
		<dd>
		<div>
			{$proposal->chair_comments|markdown|nl2br}
		</div>
		</dd>
		<a name="reviewer_comments"></a>
		<dt>Reviewer Comments</dt>
		<dd>
		<ul id="reviewer_comments">
			{foreach item=comment from=$reviewer_comments}
			{if $comment->timestamp}
			<li>
			{$comment->text|markdown} 
			<span class="info">(by {$comment->reviewer_eid} {$comment->timestamp|date_format:"%a, %b %e at %l:%M%p"})</span>
			{if $request->user->eid == $comment->reviewer_eid}
			<a href="review/proposal/{$proposal->id}/comment/{$comment->id}" class="delete">[delete]</a>
			{/if}
			</li>
			{/if}
			{/foreach}
		</ul>
		<div>
			<form method="post" action="review/proposal/{$proposal->id}/comments">
				<h2>Add Comment</h2>
				<p>
				<textarea class="small" name="text"></textarea>
				</p>
				<p>
				<input type="submit" value="submit comment">
				</p>
			</form>
		</div>
		</dd>
	</dl>

</div>
{/block}

