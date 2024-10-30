jQuery(document).ready(function($) {
    /**
	* On load
	*/
	var includeImg = $('#include-background-image').attr('checked');
	if(includeImg == 'checked'){
		$('.background-image-url').css('display', 'table-row');
	}
	
	var fileFrame;
	
	//Color picker
	//font color
	var fontColor = $('#font-color');
	fontColor.wpColorPicker();
	
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
	
	/**
	* Background Image Section:
	*/
	
	//show or hide background image url field depending on the state of the checkbox
	$('#include-background-image').bind('change', function(){
		var checked = $(this).attr('checked');
		if(checked == 'checked')
			$('.background-image-url').show();
		
		else
			$('.background-image-url').hide();
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
	
});