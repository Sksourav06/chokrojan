/**
 * Emazesol
 *
 * An application development framework for PHP 4.3.2 or newer
 *
 * @package		Emazesol
 * @author		Md Khalid Musa Sagar
 * @copyright	Copyright (c) 2010, Emazesol.
 * @link		http://emazesol.com
 * @since		Version 1.0
 */

function textValidation(fldlabel,fldid,validation)
{
	var vld=validation.split("|");
	var txtfld=document.getElementById(fldid);
	for(var i=0; i<vld.length; i++)
	{
		//field required check ######################################################################################
		if(vld[i]=='required')
		{
			if(txtfld.value=="")
			{
				alert(fldlabel+" is a required field");
				txtfld.focus();
				return false;
			}
		}
		//field value alphabate check ####################################################################################
		else if(vld[i]=='alpha')
		{
			var myRegxp = /^[a-zA-Z _-]+$/i;
			if(txtfld.value!="") {
				if (myRegxp.test(txtfld.value) == false) {
					alert(fldlabel + " must be Alphabetic");
					txtfld.focus();
					return false;
				}
			}
		}
		//field value numeric check #######################################################################################
		else if(vld[i]=='numeric')
		{
			var myRegxp = /^[\-+]?[0-9]*\.?[0-9]+$/;
			if(txtfld.value!="") {
				if (myRegxp.test(txtfld.value) == false) {
					alert(fldlabel + " must be Numeric");
					txtfld.focus();
					return false;
				}
			}
		}
		//field value min and max length check ########################################################################
		else if(vld[i].substr(0,6)=='length')
		{
			var minl=vld[i].substring(vld[i].lastIndexOf('(')+1,vld[i].lastIndexOf(','));
			var maxl=vld[i].substring(vld[i].lastIndexOf(',')+1,vld[i].lastIndexOf(')'));
			if(minl==maxl)
			{
				if(txtfld.value.length<minl || txtfld.value.length>minl)
				{
					alert(fldlabel+" length must be exact "+minl+" characters");
					txtfld.focus();
					return false;
				}
			}
			if(txtfld.value.length<minl && minl!=0)
			{
				alert(fldlabel+" length must be greater than "+minl+" characters");
				txtfld.focus();
				return false;
			}
			else if(txtfld.value.length>maxl && maxl!=0)
			{
				alert(fldlabel+" length must be less than "+maxl+" characters");
				txtfld.focus();
				return false;
			}
		}
		
		//field value valid email check ###################################################################################
		else if(vld[i]=='email')
		{
			var myRegxp = /^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/i;
			if(txtfld.value!="") {
				if (myRegxp.test(txtfld.value) == false) {
					alert(fldlabel + " must be valid Email [#@#.#]");
					txtfld.focus();
					return false;
				}
			}
		}

		//field value valid URL check ###################################################################################
		else if(vld[i]=='url')
		{
			//var myRegxp = /^[A-Za-z]+\:\/\/[A-Za-z0-9-_]+\.[A-Za-z0-9-_%&?\/\.=]+$/i;
			var myRegxp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
			if(txtfld.value!="") {
				if (myRegxp.test(txtfld.value) == false) {
					alert(fldlabel + " must be valid URL [http://#.#]");
					txtfld.focus();
					return false;
				}
			}
		}

		//field value Deny URL check ###################################################################################
		else if(vld[i]=='deny_url')
		{
			var myRegxp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
			if(myRegxp.test(txtfld.value)==true)
			{
				alert(fldlabel+" not accept URL");
				txtfld.focus();
				return false;
			}
		}
		//field value Deny HTML TAG check ###################################################################################
		else if(vld[i]=='deny_tag')
		{
			var myRegxp = /<(?:"[^"]*"['"]*|'[^']*'['"]*|[^'">])+>/;
			if(myRegxp.test(txtfld.value)==true)
			{
				alert(fldlabel+" not accept HTML code");
				txtfld.focus();
				return false;
			}
		}
		//Match two fields value ########################################################################
		else if(vld[i].substr(0,5)=='match')
		{
			var fld1=vld[i].substring(vld[i].lastIndexOf('(')+1,vld[i].lastIndexOf(')'));

			if(document.getElementById(fld1).value !="")
			{
				if(txtfld.value != document.getElementById(fld1).value)
				{
					alert(fldlabel+" Mismatch");
					txtfld.focus();
					return false;
				}
			}
		}

	}
}