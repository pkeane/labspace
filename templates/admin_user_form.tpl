{extends file="layout.tpl"}

{block name="content"}
<div>
	<div class="controls"><a href="admin/users">Go To User List</a></div>
	<h1>Add/Update User</h1>
	<form method="post">
		<dl class="user">
			<dt>name</dt>
			<dd>{$record.name}</dd>
			<dt>eid</dt>
			<dd>{$record.eid}</dd>
			<dt>email</dt>
			<dd id="email">
			{$email} <a href="" id="toggleEmail" class="toggle">[update]</a>
			<p>
			<input class="hide" id="targetEmail" type="text" value="{$email}" name="email">
			</p>
			</dd>
			<dt>title</dt>
			<dd>{$record.title}</dd>
			<dt>unit</dt>
			<dd>{$record.unit}</dd>
			<dt>phone</dt>
			<dd>{$record.phone}</dd>
			<dt>current status</dt>
			<dd>
			{if $user}
			{$user->name} is already registered.
			{else}
			{$user->name} is NOT YET registered.
			{/if}
			</dd>
			<dt>Is Admin?</dt>
			<dd>
			<p>
			<input type="radio" {if $user->is_admin}checked{/if}  name="is_admin" value="1"> yes
			</p>
			<p>
			<input type="radio" {if !$user->is_admin}checked{/if} name="is_admin" value="0"> no 
			</p>
			</dd>
			<dt>Is Reviewer?</dt>
			<dd>
			<p>
			<input type="radio" {if $user->is_reviewer}checked{/if}  name="is_reviewer" value="1"> yes
			</p>
			<p>
			<input type="radio" {if !$user->is_reviewer}checked{/if} name="is_reviewer" value="0"> no 
			</p>
			</dd>
		</dl>
		{if $user}
		<input type="submit" value="update {$record.name}">
		{else}
		<input type="submit" value="add {$record.name}">
		{/if}
	</form>
</div>
{/block}
