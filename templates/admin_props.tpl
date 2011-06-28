{extends file="layout.tpl"}

{block name="content"}
<div class="admin_props">
	<h1>Proposals</h1>
	{foreach item=ps key=status from=$props}
	<h3>{$status}</h3>
	<table class="proposals">
		<tr>
			<th class="title">title</th>
			<th>created</th>
			<th>created by</th>
			<th>submitted</th>
		</tr>

		{foreach item=prop from=$ps}
		<tr>
			<td>
				<a href="admin/proposal/{$prop->id}">{$prop->title}</a>
			</td>
			<td>
				{$prop->created|date_format:'%Y-%m-%d'}
			</td>
			<td>
				{$prop->creator->name}
			</td>
			<td>
				{$prop->submitted|date_format:'%Y-%m-%d'}
			</td>
		</tr>
		{/foreach}
	</table>
	{/foreach}
</div>
{/block}
