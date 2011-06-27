{extends file="preview_layout.tpl"}

{block name="content"}
<div class="main">
	<h1 class="title">Proposal: {$proposal->title}</h1>
	<dl id="proposal">
		<hr>
		<dt>Title</dt>
		<dd>{$proposal->title}</dd>
		<hr>
		<dt>Department</dt>
		<dd>{$proposal->dept->name}</dd>
		<dt>Created By</dt>
		<dd>{$proposal->creator->name}</dd>

		{foreach item=section from=$sections}
		<hr>
		<a name="{$section->ascii_id}"></a>
		<dt>{$section->name}</dt>
		<dd>
		<div>
			{$section->proposal_data|markdown|nl2br}
		</div>
		{if 'attachments' == $section->ascii_id}
		<ul class="attachments">
			{foreach item=attachment from=$proposal->attachments}
			<li>{$attachment->name}
			({$attachment->desc})
			</li>
			{/foreach}
		</ul>
		{/if}

		{if 'budget' == $section->ascii_id}
		<table id="budget_items" class="budget_items">
			<tr>
				<th>description</th>
				<th>price per unit</th>
				<th>quantity</th>
				<th>total</th>
				<td class="link"></td>
			</tr>
			{foreach item=budget_item from=$proposal->budget_items}
			{assign var=total value=$total+$budget_item->cost} 
			<tr>
				<td>
					{$budget_item->description}
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
				<th colspan="3">total budget:</th>
				<td>$ {$total|string_format:"%.2f"}</td>
				<td class="link"></td>
			</tr>
		</table>
		{/if}

		</dd>
		{/foreach}
	</dl>

</div>
{/block}

