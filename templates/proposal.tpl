{extends file="layout.tpl"}

{block name="content"}
<div class="main">
	<div class="controls">
		<a href="proposal/{$proposal->id}/preview">preview</a> |
		<a href="proposal/{$proposal->id}/share">share</a>
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
		<div class="instruction_text">{$section->instruction_text|markdown}</div>
		<div>
			{$section->proposal_data|markdown|nl2br}
			{if $section->proposal_data}
			<a href="#" class="toggle" id="toggle_{$section->ascii_id}">[edit]</a>
			{/if}
		</div>
		{if 'user_input' == $section->type}
		<div id="target_{$section->ascii_id}" {if $section->proposal_data}class="hide"{/if}>
			<form method="post" action="proposal/{$proposal->id}/{$section->ascii_id}">
				<p>
				<textarea id="textarea_{$section->ascii_id}" class="{$section->textbox_size}" name="text">{$section->proposal_data}</textarea>
				</p>
				<p>
				<input id="submit_{$section->ascii_id}" type="submit" value="update {$section->name}">
				<span id="updated_{$section->ascii_id}" class="hide pending"> &lt;- Please save your changes! <a href="#">[dismiss]</a></span>
				</p>
			</form>
		</div>
		{/if}

		{if 'attachments' == $section->ascii_id}
		<ul id="attachments" class="attachments">
			{foreach item=attachment from=$proposal->attachments}
			<li><a href="proposal/{$proposal->id}/attachment/{$attachment->unique_id}">{$attachment->name}</a> 
			({$attachment->desc})
			<a href="proposal/{$proposal->id}/attachment/{$attachment->unique_id}" class="delete">[delete]</a> 
			</li>
			{/foreach}
		</ul>
		<form action="proposal/{$proposal->id}/attachments" method="post" enctype="multipart/form-data">
			<h3>attach a PDF document:</h3>
			<p>
			<select name="attachment_type_id">
				<option value="">select an attachment type</option>
				{foreach item=type from=$attachment_types}
				<option value="{$type->id}">{$type->name}</option>
				{/foreach}
			</select>
			OR enter short description:
			<input class="short_desc" name="short_desc" type="text">
			</p>
			<p>
			<label for="uploaded_file">attach a file</label>
			<input name="uploaded_file" size="50" type="file">
			</p>
			<p>
			<input value="upload" type="submit">
			</p>
		</form>
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
				<td class="link">
					<a href="proposal/{$proposal->id}/budget_item/{$budget_item->id}" class="delete">[delete]</a>
				</td>
			</tr>
			{/foreach}
			<tr>
				<th colspan="3">total budget:</th>
				<td>$ {$total|string_format:"%.2f"}</td>
				<td class="link"></td>
			</tr>
		</table>
		<form class="budget_item" method="post" action="proposal/{$proposal->id}/budget_items">
			<h3>Add Budget Item</h3>
			<dl class="budget_item_form">
				<dt>budget line / description<dt>
				<dd>
				<select name="budget_line_id">
					<option value="">select a budget item type</option>
					{foreach item=line from=$budget_lines}
					<option value="{$line->id}">{$line->name}</option>
					{/foreach}
				</select>
				OR enter short description:
				<input class="short_desc" type="text" name="description">
				</dd>
				<dt>price</dt>
				<dd>
				$<input type="text" class="int" name="price">
				</dd>
				<dt>quantity</dt>
				<dd>
				<input type="text" class="int" name="quantity">
				</dd>
				<dd>
			<input type="submit" value="add budget item">
			</dd>
		</dl>
		</form>
		{/if}

		</dd>
		{/foreach}
		<dt>Submit Proposal</dt>
		<dd>
		<div class="instruction_text">Please <a href="proposal/{$proposal->id}/preview">review the proposal</a> carefully before submitting it.  You will not be able to make further edits to the proposal after it has been submitted.</div>
		<p>
		<form method="post" class="prompt" action="proposal/{$proposal->id}/submit">
			<input type="hidden" name="message" value="are you sure you wish to submit this proposal at this time?">
			<input type="submit" value="submit this proposal">
		</form>
		</p>
		</dd>
		<dt>Delete Proposal</dt>
		<dd>
		<div class="controls">
			<p>
			<form method="delete" action="proposal/{$proposal->id}">
				<p>
				<input type="submit" value="delete this proposal">
				</p>
			</form>
			</p>
		</div>
		</dd>
	</dl>

</div>
{/block}

