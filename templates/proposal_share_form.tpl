{extends file="layout.tpl"}

{block name="content"}
<div class="main">
	<div class="controls">
		<a href="proposal/{$proposal->id}">return to editing</a>
	</div>
	<h1 class="title">Share Access to Proposal: {$proposal->title}</h1>
		<dl id="propusers" class="propusers">
			<dt>creator</dt>
			<dd>{$proposal->creator->name} ({$proposal->creator->eid})</dd>
			<dt>current read access users</dt>
			<dd>
			<ul>
				{foreach item=read_user from=$proposal->read_users}
				<li>{$read_user->name} ({$read_user->eid}) <a href="proposal/{$proposal->id}/user/{$read_user->eid}" class="delete">[delete]</a></li>
				{/foreach}
			</ul>
			</dd>
			<dt>current write access users</dt>
			<dd>
			<ul>
				{foreach item=write_user from=$proposal->write_users}
				<li>{$write_user->name} ({$write_user->eid}) <a href="proposal/{$proposal->id}/user/{$write_user->eid}" class="delete">[delete]</a></li>
				{/foreach}
			</ul>
			</dd>
		</dl>
		<form method="post">
			<h3>Add a User</h3>
			<p>
			<input type="radio" checked name="auth_level" value="read"> read access
			</p>
			<p>
			<input type="radio" name="auth_level" value="write"> write access
			</p>
			<label for="eid">EID</label>
			<input type="text" name="eid" class="short_desc">
			<p>
			<input type="submit" value="add user">
			</p>
		</form>
</div>
{/block}

