
var plugins_path = ( navigator.userAgent.indexOf( 'MSIE' ) != -1 ) ? "../modules/tiny_mce/plugins/" : "../";
var browser_popup_win = null;
function fileBrowserCallBack(field_name, url, type, win){
	if( browser_popup_win == null || browser_popup_win.closed )
	{
		browser_popup_win = window.open(plugins_path+"ibrowser/ibrowser.php","browser","menubar=1,resizable=1,width=350,height=250");
		browser_popup_win.top.browser_parent = win;
		browser_popup_win.top.field_name = field_name;
	}
	else
	{
		browser_popup_win.focus();
	}
}
		
tinyMCE.init({
		// General options
		mode : "specific_textareas",
		editor_selector : "MCE_Editor",
		theme : "advanced",
		plugins : "ibrowser,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,zoom,flash,searchreplace,print,contextmenu,paste,directionality,fullscreen,safari,pagebreak,style,layer,inlinepopups,media,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
		
		// Theme options
		theme_advanced_buttons1 : "save,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull",
		theme_advanced_buttons2 : "styleselect,formatselect,fontselect,fontsizeselect,|,link,unlink,image,cleanup,code,|,ibrowser",
		theme_advanced_buttons3 : "outdent,indent,blockquote,|,undo,redo,|,preview,|,forecolor,backcolor,tablecontrols,|hr,removeformat,visualaid",
		theme_advanced_buttons4 : "sub,sup,|,charmap,emotions,iespell,advhr,|,ltr,rtl,|,insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak",	
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,

		// Example content CSS (should be your site CSS)
		content_css : "../css/style.css",

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",
		plugi2n_insertdate_dateFormat : "%Y-%m-%d",
		plugi2n_insertdate_timeFormat : "%H:%M:%S",
		flash_external_list_url : "example_flash_list.js",
		file_browser_callback : "fileBrowserCallBack",
		paste_use_dialog : false,
		theme_advanced_resizing : true,
		theme_advanced_resize_horizontal : false,
// 		theme_advanced_link_targets : "_something=My somthing;_something2=My somthing2;_something3=My somthing3;",
		paste_auto_cleanup_on_paste : true,
		paste_convert_headers_to_strong : false,
		paste_strip_class_attributes : "all",
		paste_remove_spans : false,
		paste_remove_styles : false,
		convert_urls : false,

		// Replace values for the template plugin
		template_replace_values : {
			username : "Some User",
			staffid : "991234"
		}
	});
