{extends file="layout.tpl"}

{block name="content"}
<div class="sets_instructor">
	<h1>Users</h1>
	<ul class="users">
		{foreach item=u from=$users}
		<li><a href="admin/user_form/{$u->eid}">{$u->name}</a>
		{if $u->is_admin}<span class="admin">[is admin]</span>{/if}
		{if $u->is_reviewer}<span class="reviewer">[is reviewer]</span>{/if}
		</li>
		{/foreach}
	</ul>
</div>
{/block}
