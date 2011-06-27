{extends file="layout.tpl"}
{block name="title"}DASe: Collections List{/block} 

{block name="content"}
<div class="list" id="browse">
	{if $msg}<h3 class="alert">{$msg}</h3>{/if}
	<h1>Please Login to Dase using an OpenID:</h1>
	<form id="loginForm" action="modules/openid/login" method="post">
		<p>
		<label for="username-input">openid</label>
		<input type="text" id="openid_identifier" name="openid_identifier"/>
		</p>
		<p>
		<input type="submit" value="login"/>
		</p>
	</form>
</div>


{/block}
