{extends file="layout.tpl"}

{block name="content"}
<div>
	<h1>Add Budget Lines</h1>
	<ul id="budget_lines">
		{foreach item=line from=$budget_lines}
		<li>
		{$line->name}
		<a href="admin/budget_line/{$line->id}" class="delete">[delete]</a>
		</li>
		{/foreach}
	</ul>
	<form method="post">
		<p>
		<input type="text" name="name">
		<input type="submit" value="add">
		</p>
	</form>
</div>
{/block}
