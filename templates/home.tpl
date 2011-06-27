{extends file="layout.tpl"}

{block name="content"}
{$content|markdown}
<form method="get" action="proposal/form">
	<input type="submit" value="Create a Proposal >>">
</form>
{/block}
