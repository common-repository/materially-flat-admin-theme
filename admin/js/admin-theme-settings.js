// JavaScript Document
jQuery(document).ready(function($) {
	
	/**
	* On Load
	*/
	
	var cssFileQuery = $('#mfatStylesheet-css');
    //get location of stylesheet
	var cssFile = cssFileQuery.attr('href');
	//get a reference to the theme link tag
	var themeFile;
	//check to see if link tag for theme stylesheet is available
	if($('link').is('#mfat-css')){
		themeFile = $('#mfat-css');
	}
	//if not create one
	else{
		themeFile = document.createElement('link');
        themeFile = $(themeFile);
		themeFile.attr('rel', 'stylesheet');
		themeFile.attr('id', 'mfat-css');
		themeFile.attr('media', 'all');
        themeFile.insertAfter(cssFileQuery);
	}
	var themeImg;
	//check to see if link tag for background image stylesheet is available
	if($('link').is('#mfat-img-css')){
		themeImg = $('#mfat-img-css');
	}
	//if not create one
	else{
		themeImg = document.createElement('link');
        themeImg = $(themeImg);
		themeImg.attr('rel', 'stylesheet');
		themeImg.attr('id', 'mfat-img-css');
		themeImg.attr('media', 'all');
		themeImg.insertAfter(themeFile);
	}
	//get folder of stylesheet 
	var cssFileHrefCharCount = cssFile.lastIndexOf('/') + 1;
	var cssFolderLocation = cssFile.substr(0, cssFileHrefCharCount);
	var includeImage = $('#include-background-image').attr('checked');
	var backgroundImage = $('.background-image-url');
	//show background image url text box if a background image is included
	if(includeImage == 'checked')
		backgroundImage.css('display', 'table-row');
	//media upload view frame
	var fileFrame;
	//loaded font style
	var fontStyle = $('#font-style').val();
	/*
	* Help tip section
	*/
	$( '#tiptip_holder' ).removeAttr( 'style' );
	$( '#tiptip_arrow' ).removeAttr( 'style' );
	$( '.helptip' ).tipTip({
		'attribute': 'data-tip',
		'fadeIn': 50,
		'fadeOut': 50,
		'delay': 200
	});
	$('.helptip').css('display', 'inline-block');
	
    /** Update Theme Interactively as option is chosen */
	$('select[name="mfat_theme_options[theme]"]').bind('change', function(){
		var theme = $(this).val();
		switch(theme){
			case "Blue":
				themeFile.attr('href', cssFolderLocation + 'blue.css');
				if(includeImage == 'checked')
					themeImg.attr('href', cssFolderLocation + 'blue-img.css');
			break;
            
			case "Red":
				themeFile.attr('href', cssFolderLocation + 'red.css');
				if(includeImage == 'checked')
					themeImg.attr('href', cssFolderLocation + 'red-img.css');
		}
		if($('body').css('color') !== $('#general-font-color').val()){
			$("body,h1,h2,h3,h4,h5,h6,.wrap h1,.wrap h2,.form-table th,.form-wrap label,"+
			  ".form-wrap p,.description,p.description,#wpfooter,.about-wrap h1,"+
			  ".about-wrap .about-text,.about-wrap h4,.drag-drop-inside p,#upload-form label,"+
			  "#available-widgets .widget-description,#widgets-right a.widget-control-edit,"+
			  ".no-plugin-results,.in-widget-title,.widgets-holder-wrap .widget-holder .description,"+
	          "#available-widgets .widget-description,#available-widgets h3,#wp_inactive_widgets h3,"+
			  ".subsubsub a .count,.subsubsub a.current .count,#wpbody,.nonessential,.tablenav .displaying-num,"+
			  ".media-frame.mode-grid .attachments-browser .no-media, input, textarea, select, .nav-tab").css('color', $('#general-font-color').val());
			$("div.updated,  .contextual-help-tabs-wrap, .contextual-help-sidebar,"+
			  ".contextual-help-sidebar h5, #screen-options-wrap, #screen-options-wrap h5"+
			  ".notice-success, div.error,.media-modal-content h2,.media-modal-content h3").css('color', '#1f1f1f');
		}
	});
	
	/**
	* Background Image Section:
	*/
	
	//show or hide background image url field depending on the state of the checkbox
	$('#include-background-image').bind('change', function(){
		var checked = $(this).attr('checked');
		if(checked == 'checked'){
			backgroundImage.show();
		}
		else{
			includeImage = '';
			backgroundImage.hide();
			themeImg.attr('media', 'tty');
			$('html, #wpwrap').css('background-image', 'none');
			$('#adminmenu').css('margin-top', '12px');
		}
	});
	
	//show the wordpress media view when the select image icon is clicked on
	$('.select-image').bind('click', function(e){
		 e.preventDefault();

			// Let's start over to make sure everything works
		    if ( fileFrame )
		        fileFrame.remove();

		    fileFrame = wp.media.frames.file_frame = wp.media( {
		        title: $(this).data( 'uploader_title' ),
		        button: {
		            text: $(this).data( 'uploader_button_text' )
		        },
		        multiple: false
		    } );

		    fileFrame.on( 'select', function() {
		        var attachment = fileFrame.state().get( 'selection' ).first().toJSON();
				$('#background-image-url').val( attachment.url );
		    } );

		    fileFrame.open();
	});
	
	//General font color
	var fontColor = $('#general-font-color');
	fontColor.wpColorPicker({
		change: function(event, ui){
			var val = fontColor.wpColorPicker('color');
			$("body,h1,h2,h3,h4,h5,h6,.wrap h1,.wrap h2,.form-table th,.form-wrap label,"+
			  ".form-wrap p,.description,p.description,#wpfooter,.about-wrap h1,option[selected=selected]"+
			  ".about-wrap .about-text,.about-wrap h4,.drag-drop-inside p,#upload-form label,"+
			  "#available-widgets .widget-description,#widgets-right a.widget-control-edit,"+
			  ".no-plugin-results,.in-widget-title,.widgets-holder-wrap .widget-holder .description,"+
	          "#available-widgets .widget-description,#available-widgets h3,#wp_inactive_widgets h3,"+
			  ".subsubsub a .count,.subsubsub a.current .count,#wpbody,.nonessential,.tablenav .displaying-num,"+
			  ".media-frame.mode-grid .attachments-browser .no-media, input, textarea, select, .nav-tab").css('color', val);
		}
	});
	
	//link color
	var linkColor = $('#link-color');
	linkColor.wpColorPicker({
		change: function(event, ui){
			var val = linkColor.wpColorPicker('color');
			$('#preview-link-color').css('color', val)
		}
	});
	
	//hovered link color
	var hoverLinkColor = $('#hover-link-color');
	hoverLinkColor.wpColorPicker({
		change: function(event, ui){
			var val = hoverLinkColor.wpColorPicker('color')
			$('#preview-hover-color').css('color', val);
		}
	});
    
	/*
	* Preview Section
	*/
	
	$('#preview-change').bind('click', function(e){
		e.preventDefault();
		preview();
	});
	
	//function to preview user selected option with javascript
	var preview = function(){
		includeImage = $('#include-background-image').attr('checked');
		var backgroundImgURL = $('#background-image-url').val();
		var theme = $('select[name="mfat_theme_options[theme]"]').val();
		var fs = $('#font-style').val();
		if((includeImage == 'checked') && (backgroundImgURL != '')){
			themeFile.attr('media', 'all');
			$('html, #wpwrap').css('background-image', 'url(' + backgroundImgURL + ')');
			$('html, #wpwrap').css('background-size', 'cover');
			$('#adminmenu').css('margin-top', '36px');
			switch(theme){
				case "Blue":
					themeImg.attr('href', cssFolderLocation + 'blue-img.css');
				break;
				case "Red":
					themeImg.attr('href', cssFolderLocation + 'red-img.css');
			}
		}//end if
		
		//font style
		
		if(fs != "Default"){
			var output = '';
			var fontStyleTag;
			//configure internal stylesheet depending on whether the font style contains the space character
			if(fs.indexOf(" ") == -1){
				output += "body, #wpadminbar *{ font-family: "+fs+", Helvetica; }";
			}
			else{
				output += "body, #wpadminbar *{ font-family: '"+fs+"', Helvetica; }";
			}
			//check to see if font style tag exists and create one if it doesn't exists
			if($('style').is('#mfat-custom-font')){
				fontStyleTag = $('#mfat-custom-font');
			}
			else{
				fontStyleTag = document.createElement('style');
				fontStyleTag = $(fontStyleTag);
				fontStyleTag.attr('id', 'mfat-custom-font');
				$('head').append(fontStyleTag);
			}
			//make sure font style is not like loaded font styles
			if(fs != fontStyle){
				fontStyle = fs;
				if($('link').is('#mfat-google-font')){
					$('#mfat-google-font').attr('href', "http://fonts.googleapis.com/css?family=" + encodeURI(fs));
				}
				else{
					var googleFontLinkTag = document.createElement('link');
					googleFontLinkTag = $(googleFontLinkTag);
					googleFontLinkTag.attr('id', 'mfat-google-font');
					googleFontLinkTag.attr('href', 'http://fonts.googleapis.com/css?family=' + encodeURI(fs));
					googleFontLinkTag.insertAfter(themeFile);
				}
			}
			fontStyleTag.text(output);
		}
		else{
			$('#mfat-custom-font').text('');
		}//end if
	
	}//end function
	
});