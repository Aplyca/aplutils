<?php /* #?ini charset="utf8"?

[TooltipSettings]
#use TooltipMessage 'ini' if you want to set a tip message from ini file using attribute identifier or attribue id; 
#or use 'attribute' if you want use attribute description field of the content class (this funcionallity only works in ez >= 4.3.0 )
#or use 'both' if you need use both types (remember don't assing more than one message to each field)
TooltipMessage=ini
#TooltipMessage=attribute
#TooltipMessage=both

[TooltipAttributeMessages]
#TooltipAttributeMessageId[attribue_id]=ToolTip Message
#NOTE: ToolTip Message can contains HTML tags
TooltipAttributeMessageId[]
#TooltipAttributeMessageId[360]=Place your <h1>title</h1> here

#TooltipAttributeMessageIdentifier[attribue_identifier]=ToolTip Message
#NOTE: TooltipAttributeMessageId overrides TooltipAttributeMessageIdentifier
TooltipAttributeMessageIdentifier[]
#TooltipAttributeMessageIdentifier[price]=place price here

#TooltipCustomAttributeMessageIdentifier[Custom_id]=ToolTip Message
#Note:use this setting if you aren't using the default attribute id's (Eg: ezcoa-381_purpose) and uses a custom (Eg: ad_purpose)
TooltipCustomAttributeMessageIdentifier[]

#TooltipCustomAttributeMessageName[Custom_Name]=ToolTip Message
#Note:use this setting if you want to use a custom name.
TooltipCustomAttributeMessageName[]

*/ ?>
