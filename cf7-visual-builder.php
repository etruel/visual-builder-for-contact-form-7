<?php
/*
Plugin Name: Visual Builder for Contact Form 7
Plugin URI: http://etruel.com/
Description: Adds a Visual Builder for contact form 7 forms.  ADD-on.  Requires Contact Form 7 Plugin.
Author: Esteban Truelsegaard
Author URI: http://www.netmdp.com
License: GPLv2
Text Domain: wpecf7vb
Domain Path: /lang/
Version: 1.0
*/

/* 
Copyright (C) 2015 esteban

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

define( 'wpecf7vb_PLUGIN', __FILE__ );



//aplicando script en el fotter



add_action( 'admin_enqueue_scripts', 'wpecf7vb_admin_enqueue_scripts', 999 );
function wpecf7vb_admin_enqueue_scripts( $hook_suffix ) {
	if ( false === strpos( $hook_suffix, 'wpcf7' ) ) {
		return;
	}

	wp_enqueue_style( 'wpecf7vb-admin',
		wpecf7vb_plugin_url( 'css/styles.css' ));

	wp_enqueue_script( 'wpecf7vb-admin-vSort',
	wpecf7vb_plugin_url( 'js/jquery.vSort.min.js' ),
	array( 'jquery', 'thickbox', 'wpcf7-admin' ) );
	



//	wp_enqueue_style( 'wpecf7vb-syntax',
//		wpecf7vb_plugin_url( 'css/codemirror.css' ));
//
//
//	// *********	 http://codemirror.net/
//	wp_enqueue_script( 'wpecf7vb-syntax',
//		wpecf7vb_plugin_url( 'js/codemirror-compressed.js' ),
//		array( 'jquery', 'wpcf7-admin' ) );

	add_action('admin_head', 'wpecf7vb_admin_head_scripts');
	
}

function wpecf7vb_admin_head_scripts() {
?><script type="text/javascript" language="javascript">
	jQuery(document).ready(function($){
		var $_wpcf7_taggen_insert = _wpcf7.taggen.insert;
		_wpcf7.taggen.insert = function( content ) {
			var content = "<p>"+content+"</p>";
			$_wpcf7_taggen_insert.apply( this, arguments );
//			$('#wpecf7visualeditor').html('<?php _e( 'Save to change order', 'wpecf7vb' ); ?>').fadeIn();
		};
		
		changeorder = function($form){
			$textarea = $("textarea#wpcf7-form").clone();
			$textform = '<div>' + $textarea.text() + '</div>';
			var $fields = [];
			$($textform).find('p').each(function() {
				$fields[$fields.length]=$(this).prop('outerHTML');

			});
			var $i= 0;
			var $newfields = [];
			var $newtextarea = "";
			$form.find('.sortitem').each(function() {
				$newfields[ $newfields.length ] = $fields[ $(this).attr('data-order') ];
				$newtextarea += $fields[ $(this).attr('data-order') ] + "\n\n";
				$(this).attr('data-order',$i);
				$i++;
			});
			//sincronized textarea and codemirror
			$("textarea#wpcf7-form").text($newtextarea);
			sincronized_textarea();
		};
		
		$form = $("#wpecf7visualeditor .wpcf7[role='form']");
		$form.find(".screen-reader-response").remove();
		$form.find(".wpcf7-response-output").remove();
		$form.find(".wpcf7-display-none").remove();
		$form.find("div[style='display: none;']").remove();
		$form.find(".wpcf7-textarea").attr('rows','3');
		$form.find(".wpcf7-submit").attr('type','button');
		$form.prop('outerHTML',	$form.html());
		var $i= 0;
		$('#wpecf7visualeditor p').each(function() {
			$(this).prop('outerHTML', '<div class="sortitem" data-order="'+$i+'"><span class="sorthandle"> </span><span unselectable="on" class="itemactions"><span class="itemdelete"> </span></span>' +	$(this).prop('outerHTML') + '</div>' );
			$i++;
		});
		$('#wpecf7visualeditor').vSort();

		$(document).on("click", '.itemdelete', function(){
			$nItem = $(this).parent().parent().attr('data-order');
			$('.sortitem[data-order="'+$nItem+'"]').remove();
			changeorder( $('#wpecf7visualeditor') );
			var $i= 0;
			$('.sortitem').each(function() {
				$(this).attr('data-order', $i );
				$i++;
			});
		});

		$('.seeornot').click(function(){
			$('.seeornot').toggleClass('icon-eye-open').toggleClass('icon-eye-close');
			$('#wpecf7visualeditor').toggle();
		});

//		var editor = CodeMirror.fromTextArea(document.getElementById("wpcf7-form"), {
//			lineNumbers: true,
//			mode: "text/html",
//			extraKeys: {"Ctrl-Space": "autocomplete"}
//		});
		
		//$("#wpcf7-form").css({'background-color':'black'});
		$(".insert-tag").click(function(){
			mitag = "<p>"+$(this).parent().parent().find("input.tag").val()+"</p>";
			insertTextAtCursor(mitag);

		});
	});
</script>
<?php
}

//creating functions for footer ALBERTO
add_action('admin_footer','wp_visual_script_footer');
function wp_visual_script_footer(){

	wp_enqueue_style( 'wpecf7vb-syntax',wpecf7vb_plugin_url( 'codemirror/css/monokai.css' ));
	
?>	
<link rel="stylesheet" type="text/css" href="https://codemirror.net/lib/codemirror.css">
<style type="text/css">
	.CodeMirror{width: 440px !important; height: 500px; word-wrap: break-word;}
	#wpcf7-form{display: none !important;}
</style>
<script type="text/javascript" src="https://codemirror.net/lib/codemirror.js"></script>
<script type="text/javascript" src="https://codemirror.net/mode/javascript/javascript.js"></script>
<script type="text/javascript" src="https://codemirror.net/mode/xml/xml.js"></script>
<script type="text/javascript">
    var config, editor;
    var mytextarea = document.getElementById("wpcf7-form");
    config = {
        lineNumbers: true,
        mode: "xml",
        theme: "monokai",
        indentWithTabs: false,
        htmlMode: true,
        readOnly: false,
    };
    editor = CodeMirror.fromTextArea(document.getElementById("wpcf7-form"), config)
   	//FUNCTIONS
    function selectTheme() {
        editor.setOption("theme", "monokai");
    }
    function sincronized_codemirror(){
    	text = editor.getValue();
    	document.getElementById("wpcf7-form").value = text;
    }
    function sincronized_textarea(){
    	text = document.getElementById("wpcf7-form").value;
    	editor.setValue(text);
    }
    //CLOSED FUNCTIONS---

    //sincronized codemirror
	editor.on('keyup', function(){
   		sincronized_codemirror();
	});
	//sincronized textarea
	mytextarea.addEventListener('keyup', function(e) {
		sincronized_textarea();
	});	

	//replace cursor in text
	function insertTextAtCursor(text) {
   		cursor = editor.getCursor();
    	editor.replaceRange(text, cursor);
		sincronized_codemirror();
	}

    setTimeout(selectTheme, 5000);
</script>
<?php	
}

add_filter('wpcf7_editor_panels', 'WPe_Visual_CF7');
function WPe_Visual_CF7($panels
	) {
	
	//$visualform['visualform-panel'] = array(
	$panels['form-panel'] = array(
			'title' => __( 'Visual Form', 'contact-form-7' ),
			'callback' => 'wpecf7vb_editor_panel_form' );
	
	//$panels = array_merge($visualform, $panels);
	
	return $panels;
}

function wpecf7vb_editor_panel_form($post) {
//	global $pagenow, $screen, $current_screen, $current_page;
?>
<i class="seeornot icon-eye-open"></i>
<h3><?php echo __( 'Visual Form', 'wpecf7vb' ); ?></h3>
	<?php // if($current_screen->id="toplevel_page_wpcf7" ) {} ?>
	<div class="wpecf7editors">
	<div class="wpecf7vb_col" id="wpecf7visualeditor" data-callback="changeorder( jQuery('#wpecf7visualeditor') );"><?php
//		echo '<br><br>'.wpcf7_do_shortcode( '[email* your-email]' ).'<br><hr><hr>';
		echo  do_shortcode( $post->shortcode() );
	?></div>
	<div class="wpecf7vb_col" id="wpecf7textareaeditor">
		<?php
		$tag_generator = WPCF7_TagGenerator::get_instance();
		$tag_generator->print_buttons();
		?>
		<textarea id="wpcf7-form" name="wpcf7-form" cols="100" rows="24" class="large-text code">
		<?php echo esc_textarea( $post->prop( 'form' ) ); ?></textarea>
	</div>
	</div>
	<div class="clear">	</div>
	<?php
}

function wpecf7vb_plugin_url( $path = '' ) {
	$url = plugins_url( $path, wpecf7vb_PLUGIN );

	if ( is_ssl() && 'http:' == substr( $url, 0, 5 ) ) {
		$url = 'https:' . substr( $url, 5 );
	}

	return $url;
}