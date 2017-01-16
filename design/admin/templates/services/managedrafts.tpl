
<form name="draftaction" action={concat( 'services/managedrafts/' )|ezurl} method="post">

<div class="context-block content-draft">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h1 class="context-title">Registered User Drafts</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">


{* Items per page and view mode selector. *}

{foreach $draft_data as $user_draft_data}

<h2>{$user_draft_data.name}</h2>
	<table class="list" cellspacing="0">
	<tr>
	    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} width="16" height="16" alt="{'Invert selection.'|i18n( 'design/admin/content/draft' )}" onclick="ezjs_toggleCheckboxes( document.draftaction, 'DeleteIDArray[]' ); return false;" title="{'Invert selection.'|i18n( 'design/admin/content/draft' )}" /></th>
	    <th>{'Name'|i18n( 'design/admin/content/draft' )}</th>
	    <th>{'Type'|i18n( 'design/admin/content/draft' )}</th>
	    <th>{'Section'|i18n( 'design/admin/content/draft' )}</th>
	    <th>{'Language'|i18n( 'design/admin/content/draft' )}</th>
	    <th>{'Modified'|i18n( 'design/admin/content/draft' )}</th>
	</tr>
	
	{section var=Drafts loop=$user_draft_data.drafts sequence=array( bglight, bgdark )}
	<tr class="{$Drafts.sequence}">
	    <td><input type="checkbox" name="DeleteIDArray[]" value="{$Drafts.item.id}" title="{'Select draft for removal.'|i18n( 'design/admin/content/draft' )}" /></td>
	    <td>{$Drafts.item.contentobject.content_class.identifier|class_icon( small, $Drafts.item.contentobject.content_class.name|wash )}&nbsp;<a href={concat( '/content/versionview/', $Drafts.item.contentobject.id, '/', $Drafts.item.version, '/', $Drafts.item.initial_language.locale, '/' )|ezurl}>{$Drafts.item.version_name|wash}</a></td>
	    <td>{$Drafts.item.contentobject.content_class.name|wash}</td>
	    <td>{let section_object=fetch( section, object, hash( section_id, $Drafts.item.contentobject.section_id ) )}{section show=$section_object}{$section_object.name|wash}{section-else}<i>{'Unknown'|i18n( 'design/admin/content/draft' )}</i>{/section}{/let}</td>
	    <td><img src="{$Drafts.item.initial_language.locale|flag_icon}" width="18" height="12" alt="{$Drafts.item.initial_language.locale|wash}" style="vertical-align: middle;" />&nbsp;{$Drafts.item.initial_language.name|wash}</td>
	    <td>{$Drafts.item.modified|l10n( shortdatetime )}</td>	
	</tr>
	{/section}
	</table>


{/foreach}


<div class="context-toolbar">
{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri='/content/draft'
         item_count=$list_count
         view_parameters=$view_parameters
         item_limit=$number_of_items}
</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
<input class="button" type="submit" name="RemoveButton" value="{'Remove selected'|i18n( 'design/admin/content/draft')}"  />
    <input class="button" type="submit" name="EmptyButton"  value="{'Remove all'|i18n( 'design/admin/content/draft')}"  />
</div>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>

</form>


