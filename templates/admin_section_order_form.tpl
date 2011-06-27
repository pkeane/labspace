{extends file="layout.tpl"}

{block name="content"}
<div>
	<form method="post">
		<ul>
			{foreach item=sec from=$sections}
			<li>
			<input class="sort_order" type="text" name="{$sec->ascii_id}_sort_order" value="{$sec->sort_order}">
			<a href="admin/sections/{$sec->ascii_id}">{$sec->name} ({$sec->type})</a>
			{if !$sec->is_active} [inactive]{/if}
			</li>
			{/foreach}
		</ul>
		<p>
		<input type="submit" value="update sort order">
		</p>
	</form>
</div>
{/block}
