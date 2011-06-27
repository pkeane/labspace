{extends file="layout.tpl"}

{block name="content"}
<div>
	<h1>User Settings for {$request->user->name}</h1>
	<dl class="user">
		<dt>name</dt>
		<dd>{$request->user->name}</dd>
		<dt>eid</dt>
		<dd>{$request->user->eid}</dd>
		<dt>email</dt>
		<dd id="email">
		{$request->user->email} <a href="" id="toggleEmail" class="toggle">[update]</a>
		<form id="targetEmail" class="hide" method="post" action="user/email">
			<p>
			<input type="text" value="{$request->user->email}" name="email">
			<input type="submit" value="update">
			</p>
		</form>
		</dd>
		{if $request->user->is_admin}
		<dt>Is Admin</dt>
		<dd>yes</dd>
		{/if}
		{if $request->user->is_reviewer}
		<dt>Is Reviewer</dt>
		<dd>yes</dd>
		{/if}
		{if $request->user->is_chair}
		<dt>Department Chaired</dt>
		<dd>
		<ul>
			{foreach item=dept from=$request->user->chaired_depts}
			<li>{$dept->name}</li>
			{/foreach}
		</ul>
		</dd>
		{/if}
	</dl>
</div>
{/block}
