{extends file="layout.tpl"}

{block name="content"}
<h1>Information for Department: {$dept->name}</h1>
<p>(<a href="admin/depts">view all departments</a>)</p>
<div class="main">
	<form action="admin/dept/{$dept->id}" id="deptForm" class="shortForm" method="post" >
		<p>
		<label for="name">Name</label>
		<input class="long" type="text" name="name" value="{$dept->name}"/>
		</p>
		<p>
		<label for="ascii_id">Code</label>
		<input class="long" type="text" name="ascii_id" value="{$dept->ascii_id}"/>
		</p>
		<p>
		<label for="phone">Phone</label>
		<input class="long" type="text" name="phone" value="{$dept->phone}"/>
		</p>
		<p>
		<label for="address">Address</label>
		<input class="long" type="text" name="address" value="{$dept->address}"/>
		</p>
		<p>
		<label for="chair_name">Chair</label>
		<input class="long" type="text" name="chair_name" value="{$dept->chair_name}"/>
		</p>
		<p>
		<label for="chair_title">Chair Title</label>
		<input class="long" type="text" name="chair_title" value="{$dept->chair_title}"/>
		</p>
		<p>
		<label for="chair_eid">Chair EID</label>
		<input class="long" type="text" name="chair_eid" value="{$dept->chair_eid}"/>
		</p>
		<p>
		<label for="chair_email">Chair Email</label>
		<input class="long" type="text" name="chair_email" value="{$dept->chair_email}"/>
		</p>
		<p>
		<input type="submit" value="update"/>
		<!--
		<input type="submit" name="refresh" value="refresh from directory"/>
		-->
		</p>
	</form>
	<dl class="current">
		<dt>name</dt> <dd> {$dept->name|default:'--'}</dd>
		<dt>code</dt> <dd> {$dept->ascii_id|default:'--'}</dd>
		<dt>phone</dt> <dd> {$dept->phone|default:'--'}</dd>
		<dt>address</dt> <dd> {$dept->address|default:'--'}</dd>
		<dt>chair</dt> <dd> {$dept->chair_name|default:'--'}</dd>
		<dt>chair title</dt> <dd> {$dept->chair_title|default:'--'}</dd>
		<dt>chair eid</dt> <dd> {$dept->chair_eid|default:'--'}</dd>
		<dt>chair email</dt> <dd> {$dept->chair_email|default:'--'}</dd>
	</dl>
</div>
{/block}

