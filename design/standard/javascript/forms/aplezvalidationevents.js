/*
  -------------------------------------------------------------------------
Apl eZ Forms Event Hanlder
Author : David Sanchez Escobar
  -------------------------------------------------------------------------  
*/



var passwordObj = null;

function starteZForm()
{
	$("input[name^='ContentObjectAttribute_data_user_password_confirm']").attr("disabled", true); 
}

function fieldEvents(obj)
{
	var attrName = $(obj).attr('name');
	$(obj).select();
	if(attrName.indexOf("data_user_password") != -1)
	{
		if(attrName.indexOf("data_user_password_confirm") == -1)
		{
			$("input[name^='ContentObjectAttribute_data_user_password_confirm']").attr("disabled", false); 
		}
	}
}

function validate(obj)
{

	var attrName = $(obj).attr('name');
	var value =  $(obj).attr('value');
	
	if(attrName.indexOf("data_user_email") != -1)
	{
		var required = isRequired(obj);
		if ( !  validateRequired(required, value) ) 
		{
			setRequiredErrorMessage(obj, required, value);
		}
		else
		{
			var check = validateEmail(value);
			updateValidationResponse('email', obj, check);		
		}							
	}
	else if(attrName.indexOf("data_integer") != -1)
	{
		var required = isRequired(obj);
		if ( !  validateRequired(required, value) ) 
		{
			setRequiredErrorMessage(obj, required, value);
		}
		else
		{
			var check = validateInteger(value);
			updateValidationResponse('integer', obj, check);
		}					
	}	
	else if(attrName.indexOf("data_float") != -1)
	{
		var required = isRequired(obj);
		if ( !  validateRequired(required, value) ) 
		{
			setRequiredErrorMessage(obj, required, value);
		}
		else
		{
			var check = validateFloat(value);			
			updateValidationResponse('float', obj, check);
		}				
	}		
	else if(attrName.indexOf("data_user_password") != -1)
	{
		if(attrName.indexOf("data_user_password_confirm") == -1)
		{		
			passwordObj = obj;
			if( $(obj).attr('value') == '')
			{			
				$("input[name^='ContentObjectAttribute_data_user_password_confirm']").attr("disabled", true); 
			}
		}
		else
		{
			var check = validatePasswordConfirm(obj);			
			updateValidationResponse('passwordconfirm', obj, check);
		}
							
	}	
	else if(attrName.indexOf("data_user_login") != -1)
	{		
		setRequiredErrorMessage(obj, 1, value);					
	}		
	else if(attrName.indexOf("ezstring_data_text") != -1)
	{
		var required = isRequired(obj);
		setRequiredErrorMessage(obj, required, value);					
	}			
	else if(attrName.indexOf("data_text") != -1)
	{
		var required = isRequired(obj);
		setRequiredErrorMessage(obj, required, value);					
	}			
	else{}
}

function validatePasswordConfirm(obj)
{
	if(passwordObj != null)
	{
		if( $(passwordObj).attr('value') == $(obj).attr('value') )
			return 1;
		else
			return 0;		
	}
	else
		return 0;	
}


function validateRequired(required, value)
{
	if(required)
	{
		if(value == "")
			return 0;
		else
			return 1;
	}
	else
		return 1;
}

function isRequired(obj)
{
	var parent = $(obj).parent();
	var flagChildren = 0;
	var requiredSpam = $(parent).find("span[class='required_field']");	
	requiredSpam.each(function(index) {		
		flagChildren = 1;
	});	
	return flagChildren;	
}

function setRequiredErrorMessage(obj, required, value)
{
	var parent = $(obj).parent();
	if ( !  validateRequired(required, value) ) 
	{	
		var errorMsg = buildErrorMessage('required');
		setError(parent, errorMsg);	
	}		
	else
	{
		cleanErrorMessages(parent);
	}
}


function updateValidationResponse(type, obj, check)
{
	var parent = $(obj).parent();
	if(check == 0)
	{			
		var errorMsg = buildErrorMessage(type);
		setError(parent, errorMsg);
	}		
	else
	{
		cleanErrorMessages(parent);
	}	
}


function setError(parent, errorMsg)
{
	var spanElements = parent.children('span');			
	spanElements.each(function(index) {			
		if( $(this).attr('class') == 'error_strings' )
		{
			$(this).remove();			
		}			
	});
	$(parent).append(errorMsg);	
}

function cleanErrorMessages(parent)
{
	var spanElements = parent.children('span');			
	spanElements.each(function(index) {			
		if( $(this).attr('class') == 'error_strings' )
		{
			$(this).remove();			
		}			
	});
}

function validateEmail(email)
{
    var splitted = email.match("^(.+)@(.+)$");
    if(splitted == null) return false;
    if(splitted[1] != null )
    {
      var regexp_user=/^\"?[\w-_\.]*\"?$/;
      if(splitted[1].match(regexp_user) == null) return false;
    }
    if(splitted[2] != null)
    {
      var regexp_domain=/^[\w-\.]*\.[A-Za-z]{2,4}$/;
      if(splitted[2].match(regexp_domain) == null) 
      {
	    var regexp_ip =/^\[\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\]$/;
	    if(splitted[2].match(regexp_ip) == null) return false;
      }// if
      return 1;
    }
return 0;
}

function validateInteger(val) 
{
	var IsFound = /^-?\d+$/.test(val);
	if(IsFound)
	{
		if(!(parseInt(val) <= 0))
			return 1;
		else
			return 0;
	}
	else 
		return 0;
}


function validateFloat(val)
{
	if( isNaN(val) )
		return 0;
	else
		return 1;
}

function TestRegExp(objValue,strRegExp,strError)
{
var ret = true;
    if( objValue.value.length > 0 && 
        !objValue.value.match(strRegExp) ) 
    { 
      if(!strError || strError.length ==0) 
      { 
        strError = objValue.name+": Invalid characters found "; 
      }//if                                                               
      sfm_show_error_msg(strError,objValue); 
      ret = 0;                   
    }//if 
return ret;
}


function buildErrorMessage(type)
{
	var htmlmessage = "<span class='error_strings'>";
	if(type == 'email')
	{
		htmlmessage+= "enter a valid email address";
	}
	else if(type == 'integer')
	{
		htmlmessage+= "enter a valid integer number and greater than 0";
	}
	else if(type == 'float')
	{
		htmlmessage+= "enter a valid float";
	}
	else if(type == 'password')
	{
		htmlmessage+= "Your pasword confirmation doesn't match";
	}	
	else if(type == 'required')
	{
		htmlmessage+= "This field must be filled";
	}
	else if(type == 'passwordconfirm')
	{
		htmlmessage+= "Password confirm didn't match";
	}			
	else{}
	htmlmessage+="</span>";	
	return htmlmessage;
}
