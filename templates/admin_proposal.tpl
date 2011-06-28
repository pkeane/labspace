{extends file="preview_layout.tpl"}

{block name="content"}
<div class="main">
	<div class="controls">
		<a href="proposal/{$proposal->id}">proposal form</a> |
		<a href="admin/proposals">return to admin proposals list</a>
	</div>
	<h1 class="title">Proposal: {$proposal->title}</h1>
	<dl id="proposal">
		<dt>Title</dt>
		<dd>{$proposal->title}</dd>
		<dt>Department</dt>
		<dd>{$proposal->dept->name}</dd>
		<dt>Created By</dt>
		<dd>{$proposal->creator->name}</dd>
		<dt>Status</dt>
		<dd>
		{$proposal->workflow_status}
		<form method="post" action="admin/proposal/{$proposal->id}/status">
			<label for="workflow_status">change to:</label>
			<select name="workflow_status">
				<option>created</option>
				<option>proposed</option>
				<option>awarded</option>
				<option>declined</option>
				<option>test</option>
			</select>
			<input type="submit" value="update">
		</form>
		</dd>
		<dt>Budget Items</dt>
		<dd>
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
		<a href="proposal/{$proposal->id}/budget_items.csv">get budget items as CSV</a>
		</dd>
		<dt>Admin Attachments</dt>
		<dd>
		<ul id="attachments" class="attachments">
			{foreach item=attachment from=$proposal->admin_attachments}
			<li><a href="proposal/{$proposal->id}/attachment/{$attachment->unique_id}">{$attachment->name}</a> 
			({$attachment->short_desc})
			<a href="proposal/{$proposal->id}/attachment/{$attachment->unique_id}" class="delete">[delete]</a> 
			</li>
			{/foreach}
		</ul>
		<form action="admin/proposal/{$proposal->id}/attachments" method="post" enctype="multipart/form-data">
			<h3>attach a PDF document:</h3>
			<p>
			<label for="short_desc">short description</label>
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
		</dd>
	</dl>

</div>
{/block}

