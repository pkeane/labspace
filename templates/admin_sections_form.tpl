{extends file="layout.tpl"}

{block name="content"}
<div id="instruction_form">
	<h1>Manage Sections</h1>
	<form method="post" action="admin/sections">
		<h2>Create a Section:</h2>
		<p>
		<label for="name">Section Name</label>
		<input type="text" name="name">
		</p>
		<p>
		<label for="type">Section Type</label>
		<p>
		<input type="radio" checked name="type" value="user_input"> user input
		</p>
		<p>
		<input type="radio" name="type" value="content"> content
		</p>
		<p>
		<input type="submit" value="create section">
		</p>
	</form>
	<form method="get" action="admin/sections">
		<h2>Select section to edit:</h2>
		<p>
		<select name="section">
			<option value="">select section to edit:</option>
			{foreach item=sec from=$sections}
			<option {if $sec->ascii_id == $section->ascii_id}selected{/if} value="{$sec->ascii_id}">{$sec->name}</option>
			{/foreach}
		</select>
		<p>
		<input type="submit" value="get form">
		</p>
	</form>
	{if $section}
	<form id="section_form" method="post" action="admin/sections/{$section->ascii_id}">
		<dl>
			<dt>section</dt>
			<dd>{$section->ascii_id}</dd>
			<dt>Name</dt>
			<dd><input type="text" name="name" value="{$section->name}"></dd>
			<dt>Instruction Text</dt>
			<dd>
			<textarea
				{if "content" == $section->type}class="large"{/if}
				{if "user_input" == $section->type}class="small"{/if}
			   	name="instruction_text">{$section->instruction_text}</textarea>
			<a href="#" class="toggle" id="toggle_markdown">show/hide markdown cheatsheet</a>
			<div class="hide" id="target_markdown">
			{ include file="markdown.tpl" }
		</div>
			</dd>
			{if 'content' != $section->type}
			<dt>textbox size</dt>
			<dd>
			<select name="textbox_size">
				<option {if 'small' == $section->textbox_size}selected{/if}>small</option>
				<option {if 'medium' == $section->textbox_size}selected{/if}>medium</option>
				<option {if 'large' == $section->textbox_size}selected{/if}>large</option>
			</select>
			</dd>
			{/if}
			<dt>Section Type</dt>
			<dd>
			<p>
			<input type="radio" {if 'user_input' == $section->type}checked{/if}  name="type" value="user_input"> user input
			</p>
			<p>
			<input type="radio" {if 'content' == $section->type}checked{/if} name="type" value="content"> content
			</p>
			</dd>
			<dt>Is Active?</dt>
			<dd>
			<p>
			<input type="radio" {if $section->is_active}checked{/if}  name="is_active" value="1"> yes
			</p>
			<p>
			<input type="radio" {if !$section->is_active}checked{/if} name="is_active" value="0"> no 
			</p>
			</dd>
			<dd>
			<input type="submit" value="update section">
			</dd>
		</dl>
	</form>
	<form method="delete" action="admin/sections/{$section->ascii_id}">
		<input type="submit" value="delete section {$setion->name}">
	</form>
	<div id="section_preview">
		<h1>Section Preview ({$section->ascii_id}):</h1>
		<dl>
		<dt>{$section->name}</dt>
		<dd>
		<div class="instruction_text">{$section->instruction_text|markdown}</div>
		{if 'content' != $section->type}
		<form method="get" action="admin/instruction/{$section->section}">
			<textarea class="{$section->textbox_size}"></textarea>
			<p>
			<input type="submit" value="update {$section->label}">
			</p>
		</form>
		</dd>
		{/if}
	</dl>
	</div>
	{/if}
</div>
{/block}
