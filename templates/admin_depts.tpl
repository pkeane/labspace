{extends file="layout.tpl"}

{block name="content"}
<h2>Manage Departments</h2>
<ul id="deptsList">
{foreach item=dept from=$depts}
<li>
<form action="admin/dept/{$dept->id}/status" method="put">
{if $dept->is_active}
<p>
<input type="hidden" name="is_active" value="0">
<input type="submit" value="deactivate">
<span class="active"><a href="admin/dept/{$dept->id}">{$dept->name}</a></span>
</p>
{else}
<p>
<input type="hidden" name="is_active" value="1">
<input type="submit" value="activate">
<span class="inactive">{$dept->name}</span>
<p>
{/if}
</form>
</li>
{/foreach}
</ul>
{/block}
