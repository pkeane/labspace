{extends file="layout.tpl"}

{block name="content"}
<div>
	<h1>Administration</h1>
	<ul class="operations">
		<li><a href="user/settings">my user settings</a></li>
		{if $request->user->is_admin}
		<li><a href="admin/sections">configure sections</a></li>
		<li><a href="admin/section_order">configure section order</a></li>
		<li><a href="admin/budget_lines">configure budget lines</a></li>
		<li><a href="admin/attachment_types">configure attachment types</a></li>
		<li><a href="directory">add a user</a></li>
		<li><a href="admin/users">list users</a></li>
		<li><a href="admin/depts">Manage Departments</a></li>
		{/if}
	</ul>
</div>
{/block}
