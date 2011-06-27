{extends file="layout.tpl"}

{block name="content"}
<h1>New Labspace Proposal Form</h1>
<div class="main">
	<form method="post" >
		<p>
		<label for="name">Proposer Name</label>
		<input class="long" type="text" name="name" value="{$request->user->name}" disabled>
		</p>
		<p>
		<label for="title">Proposal Title</label>
		<input class="long" type="text" name="title" />
		</p>
		<label for="Department">Department</label>
		<select name='dept_id'>
			<option value=''>select one:</option>
			{foreach item=dept from=$depts}
			<option {if $dept->id == $request->user->dept_id}selected="selected"{/if} value='{$dept->id}'>{$dept->name}</option>
			{/foreach}
		</select>
		</p>
		<p>
		<input type="submit" value="start new proposal"/>
		</p>
	</form>
</div>
{/block}

