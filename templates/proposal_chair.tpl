{extends file="preview_layout.tpl"}

{block name="content"}
<div class="main">
	<div class="controls">
		<a href="user/proposals">return to proposals list</a>
	</div>
	<h1 class="title">Proposal: {$proposal->title} ({$proposal->id})</h1>
	{if $proposal->submitted}
	<h3 class="msg">Submitted by {$proposal->submitted_by} {$proposal->submitted|date_format:"%a, %b %e at %l:%M%p"}</h3>
	{else}
	<h3 class="msg">Not yet submitted</h3>
	{/if}
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
			{if $proposal->chair_comments}
			<a href="#" class="toggle" id="toggle_chair">[edit]</a>
			{/if}
		</div>
		<div id="target_chair" {if $proposal->chair_comments}class="hide"{/if}>
			<form method="post">
				<p>
				<textarea class="large" name="chair_comments">{$proposal->chair_comments}</textarea>
				</p>
				<p>
				<input type="submit" value="submit comments">
				</p>
			</form>
		</div>
		</dd>
	</dl>
</div>
{/block}

