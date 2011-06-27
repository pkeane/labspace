{extends file="base.tpl"}

{block name="title"}{$main_title}{/block}

{block name="wordmark"}{/block}

{block name="header"}{/block}

{block name="sidebar"}{/block}

{block name="main"}
{if $msg}<h3 class="msg">{$msg}</h3>{/if}
{block name="content"}default content{/block}
{/block}

{block name="footer"}
{/block}
