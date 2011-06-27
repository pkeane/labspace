{extends file="layout.tpl"}

{block name="content"}
<div class="main">
	<h1 class="title">{$request->user->name} Proposals</h1>
	<dl class="user_proposals">
		<dt>Created by me</dt>
		<dd>
		<ul>
			{foreach item=prop from=$request->user->created_proposals}
			<li>
			<a href="proposal/{$prop->id}">{$prop->title}</a>
			{if $prop->submitted}
			<span class="msg">[Submitted by {$prop->submitted_by} {$prop->submitted|date_format:"%a, %b %e at %l:%M%p"}]</span>
			{/if}
			</li>
			{/foreach}
		</ul>
		</dd>
		{if $request->user->is_chair}
		<dt>Department Proposals for Chair Comment (already submitted)</dt>
		<dd>
		{foreach item=prop from=$request->user->chair_proposals}
		{if $prop->submitted}
		<li><a href="proposal/{$prop->id}/chair">{$prop->title}</a></li>
		{/if}
		{/foreach}
		</dd>
		{/if}
		{if $request->user->readable_proposals|@count}
		<dt>Shared Read Access</dt>
		<dd>
			{foreach item=prop from=$request->user->readable_proposals}
			<li>
			<a href="proposal/{$prop->id}/preview">{$prop->title}</a>
			{if $prop->submitted}
			<span class="msg">[Submitted by {$prop->submitted_by} {$prop->submitted|date_format:"%a, %b %e at %l:%M%p"}]</span>
			{/if}
			</li>
			{/foreach}
		</dd>
		{/if}
		{if $request->user->writeable_proposals|@count}
		<dt>Shared Write Access</dt>
		<dd>
			{foreach item=prop from=$request->user->writeable_proposals}
			<li>
			<a href="proposal/{$prop->id}">{$prop->title}</a>
			{if $prop->submitted}
			<span class="msg">[Submitted by {$prop->submitted_by} {$prop->submitted|date_format:"%a, %b %e at %l:%M%p"}]</span>
			{/if}
			</li>
			{/foreach}
		</dd>
		{/if}
		{if $request->user->is_reviewer}
		<dt>For Review (already submitted)</dt>
		<dd>
			{foreach item=prop from=$all_proposals}
			{if $prop->submitted}
			<li><a href="review/proposal/{$prop->id}">{$prop->title}</a> ({$prop->creator->name})</li>
			{/if}
			{/foreach}
		</dd>
		{/if}
	</dl>

</div>
{/block}

