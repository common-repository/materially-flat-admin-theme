// JavaScript Document
jQuery(document).ready(function($) {
	/*
	* On Load
	*/
	
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
});