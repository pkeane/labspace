{extends file="layout.tpl"}

{block name="content"}
<div>
	<h1>Add Attachment Types</h1>
	<ul id="attachment_types">
		{foreach item=type from=$attachment_types}
		<li>
		{$type->name}
		<a href="admin/attachment_type/{$type->id}" class="delete">[delete]</a>
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
