<?php
/*
  Plugin Name: Visual Builder for Contact Form 7
  Plugin URI: https://etruel.com/downloads/visual-builder-contact-form-7/
  Description: Adds a Visual Builder for contact form 7 forms.  ADD-on.  Requires Contact Form 7 Plugin.
  Author: Etruel Developments LLC
  Author URI: https://etruel.com
  License: GPLv2
  Text Domain: visual-builder-for-contact-form-7
  Domain Path: /languages/
  Version: 2.5
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

function wpecf7vb_load() {
	load_plugin_textdomain('visual-builder-for-contact-form-7', false, 'visual-builder-for-contact-form-7/languages');

	if (!class_exists('WPCF7')) {
		//deactivate_plugins(plugin_basename( __FILE__ ));
		require_once 'class.wpcf7vb-extension-activation.php';
		$activation = new wpcf7_Extension_Activation(plugin_dir_path(__FILE__), basename(__FILE__));
		$activation = $activation->run();
		deactivate_plugins(plugin_basename(__FILE__));
	} else {
		return wpecf7vb_init();
	}
}

add_action('plugins_loaded', 'wpecf7vb_load', 999);

function wpecf7vb_init() {

	define('wpecf7vb_PLUGIN', __FILE__);
	add_action('admin_enqueue_scripts', 'wpecf7vb_admin_enqueue_scripts', 999);

	function wpecf7vb_admin_enqueue_scripts($hook_suffix) {
		if (false === strpos($hook_suffix, 'wpcf7')) {
			return;
		}
// wp_enqueue_script( $handle, $src = false, $deps = array(), $ver f= false, $in_footer = false ) 
		wp_enqueue_style('wpecf7vb-admin', wpecf7vb_plugin_url('css/styles.css'));
		wp_enqueue_script('wpecf7vb-admin-vSort', wpecf7vb_plugin_url('js/jquery.vSort.min.js'), array('jquery', 'thickbox', 'wpcf7-admin'));

		wp_enqueue_style('wpecf7vb-codemirror', wpecf7vb_plugin_url('codemirror/css/codemirror.css'));
		/* 	https://codemirror.net/lib/codemirror.css */

		wp_enqueue_style('wpecf7vb-monokai', wpecf7vb_plugin_url('codemirror/css/monokai.css'));
		wp_enqueue_style('wpecf7vb-colbat', wpecf7vb_plugin_url('codemirror/css/colbat.css'));
		wp_enqueue_style('wpecf7vb-blackboard', wpecf7vb_plugin_url('codemirror/css/blackboard.css'));

		wp_enqueue_script('wpecf7vb-mirrorcode', wpecf7vb_plugin_url('codemirror/js/codemirror.js'), array('jquery', 'wpcf7-admin'));
		wp_enqueue_script('wpecf7vb-javascript', wpecf7vb_plugin_url('codemirror/js/javascript.js'), array('wpecf7vb-mirrorcode'));
		wp_enqueue_script('wpecf7vb-xml', wpecf7vb_plugin_url('codemirror/js/xml.js'), array('wpecf7vb-mirrorcode'));
		wp_enqueue_script('wpecf7vb-css', wpecf7vb_plugin_url('codemirror/js/css.js'), array('wpecf7vb-mirrorcode'));
		wp_enqueue_script('wpecf7vb-htmlmixed', wpecf7vb_plugin_url('codemirror/js/htmlmixed.js'), array('wpecf7vb-mirrorcode', 'wpecf7vb-xml'));

		//Added here to call it just on cf7 settings page
		if ( (isset($_GET['action']) && $_GET['action']=='edit')
                        || (isset($_GET['page']) && $_GET['page'] =='wpcf7-new' )
                        || (isset($_GET['page']) && $_GET['page'] =='wpcf7' )
                        ) {
			add_action('admin_head', 'wpecf7vb_admin_head_scripts');
			add_action('admin_footer', 'wp_visual_script_footer');
		}
	}

	function wpecf7vb_admin_head_scripts() {

		$nonce = wp_create_nonce('wpc_visual_nonce');
		?>

		<script type="text/javascript" language="javascript">
			jQuery(document).ready(function ($) {
				//button selector novalidate html
				jQuery("[name='wpcf7-save']").attr('formnovalidate', 'formnovalidate');


				var $wpcf7_taggen_insert = wpcf7.taggen.insert;
				wpcf7.taggen.insert = function (content) {
					var content = "<p>" + content + "</p>";
					insertTextAtCursor(content);
					$("#wpcf7-form").text(get_codemirror());
					$wpcf7_taggen_insert.apply(this, arguments);

		//			$('#wpecf7visualeditor').html('<?php _e('Save to change order', 'visual-builder-for-contact-form-7'); ?>').fadeIn();
				};
				//alert("jaqjaja ");


				//funcion para remover etiquetas creadas temporalmente a los shortcodes
				function removetags($textarea_temp) {
					$textform = '<div>' + $textarea_temp + '</div>';
					var textcontent = '';
					$($textform).find('>p').each(function () {
						textcontent = $(this).text();
						if ($(this).hasClass('temp_paragraph')) {
							$textarea_temp = $textarea_temp.replace($(this).prop('outerHTML'), textcontent);
						}
					});
					return $textarea_temp;
				}

				String.prototype.replaceAll = function (search, replacement) {
					var target = this;
					return target.split(search).join(replacement);
				};


				//Function grouping in text to move tags while CF7
				function changeEtiquete(textareavalue) {
					//function to change the data
					var separators = ['\n\n', '\\\+', '/>\n', '\n\n\n'];
					var new_textarea = textareavalue;
					var elements_repeat = Array();
					var cont_verdadero_elements = 0;
					//before pass by here
					var piezes_submit = textareavalue.split(new RegExp(separators.join('|'), 'g'));
					for (var i = 0; i < piezes_submit.length; i++) {
						cont_verdadero_elements = 0;
						if (piezes_submit[i].indexOf('[submit') > -1) {
							//here to see if you have some HTML to be tag behind so not happened
							if (piezes_submit[i].indexOf('>') > -1) {
								//no pasa nada
							} else {
								//delete the blank spaces that exist
								//piezes_submit[i] = piezes_submit[i].replace('\n','');
								//now save the positions and field on the array
								//$("textarea#wpcf7-form").text($("textarea#wpcf7-form").text().replace(piezes_submit[i],'<p>'+piezes_submit[i]+'</p>'));
								//sincronized_textarea();
								//alert(piezes_submit[i]);
								//hacemos un for para guardar los datos en el arreglo
								for (var j = 0; j < elements_repeat.length; j++) {
									if (elements_repeat[j].indexOf(piezes_submit[i]) > -1) {
										cont_verdadero_elements++;
									}
								}//cierre del for
								//guardamos el valor
								if (cont_verdadero_elements == 0)
									elements_repeat.push(piezes_submit[i]);
							}//cierre del else
						}//cierre if
					}//

					//hacemos el for de los elementos obtenidos a reemplaza
					for (k = 0; k < elements_repeat.length; k++) {
						//alert("entro en el for "+elements_repeat[k]);
						new_textarea = new_textarea.replaceAll(elements_repeat[k], "<p class='temp_paragraph'>" + elements_repeat[k] + "</p>");
					}//cierre del form

					//alert("este es el nuievo textarea "+new_textarea);
					return new_textarea;
				}


				changeorder = function ($form) {
					$textarea = $("textarea#wpcf7-form").clone();
					//changeEtiquete($textarea.text());
					//$textform = '<div>' + $textarea.text() + '</div>';
					$textform = '<div>' + changeEtiquete($textarea.text()) + '</div>';
					//alert("Esto es textform"+$textform);
					var $fields = [];
					var $styles_fields = [];

					$($textform).find('>p,>label,>style,>script').each(function () {
						myetiquete = $(this)[0].tagName;

						if (myetiquete == 'LABEL') {
							//p_etiquete = $(this).parent()[0].tagName;
							//if(p_etiquete=='DIV'){
							$fields[$fields.length] = $(this).prop('outerHTML');
							//}	
						} else if (myetiquete == 'P' || myetiquete == 'SCRIPT') {
							$fields[$fields.length] = $(this).prop('outerHTML');
						} else {
							$styles_fields[$styles_fields.length] = $(this).prop('outerHTML');
							//$fields[$fields.length]=$(this).prop('outerHTML');

						}
					});

					var $i = 0;
					var $j = 0;
					var $newfields = [];
					var $newstyles = [];
					var $newtextarea = "";

					//function to sort all the element
					function function_add_field() {
						for ($j = 0; $j < $styles_fields.length; $j++) {
							$newtextarea += $styles_fields[$j] + "\n\n";
						}
						if ($j >= $styles_fields.length) {
							$form.find('.sortitem').each(function () {
								$newfields[$newfields.length] = $fields[$(this).attr('data-order')];
								$newtextarea += $fields[$(this).attr('data-order')] + "\n\n";
								$(this).attr('data-order', $i);
								$i++;
							});
						}
					}
					function_add_field();
					//remover tags 
					$newtextarea = removetags($newtextarea);
					//sincronized textarea and codemirror
					$("textarea#wpcf7-form").text($newtextarea);
					sincronized_textarea();
					sincronized_codemirror_to_textarea($("#wpcf7-form").text());

				};

				$form = $("#wpecf7visualeditor .wpcf7[role='form']");
				$form.find(".screen-reader-response").remove();
				$form.find(".wpcf7-response-output").remove();
				$form.find(".wpcf7-display-none").remove();
				$form.find("div[style='display: none;']").remove();
				$form.find(".wpcf7-textarea").attr('rows', '3');
				$form.find(".wpcf7-submit").attr('type', 'button');
				$form.prop('outerHTML', $form.html());
				var $i = 0;
				$('#wpecf7visualeditor p').each(function () {

					$(this).prop('outerHTML', '<div class="sortitem" data-order="' + $i + '"><span class="sorthandle"> </span><span unselectable="on" class="itemactions"><span class="itemdelete"> </span></span>' + $(this).prop('outerHTML') + '</div>');
					$i++;
				});
				$('#wpecf7visualeditor').vSort();

				$(document).on("click", '.itemdelete', function () {
					$nItem = parseInt($(this).parent().parent().attr('data-order'));
					$html_temp = "";
					$('.sortitem[data-order="' + $nItem + '"]').remove();
					changeorder($('#wpecf7visualeditor'));
					var $i = 0;
					$('.sortitem').each(function () {
						$(this).attr('data-order', $i);
						$i++;
					});

					if ($(this).hasClass('refresh-delete')) {
						sincronized_textarea_to_codemirror($("#wpcf7-form").text());
						sincronized_codemirror_to_textarea($("#wpcf7-form").text());
					}

				});


				//creating ajax function iconeyes
				function save_icon_eyes(iconeyes) {
					var data = {
						'action': 'save_iconeyes',
						_ajax_nonce: "<?php echo $nonce; ?>",
						'iconeyes': iconeyes
					};
					// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
					jQuery.post(ajaxurl, data, function (response) {
						//response
					});
				}
				//creating ajax function selection_theme
				function save_selection_theme(mytheme) {
					var data = {
						'action': 'save_selection_theme',
						_ajax_nonce: "<?php echo $nonce; ?>",
						'selection_theme': mytheme
					};
					// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
					jQuery.post(ajaxurl, data, function (response) {
						//response
					});
				}
				//creating ajax function refresh visual form
				function fnrefresh_visual_form(myshortcode) {
					var data = {
						'action': 'refresh_visual',
						_ajax_nonce: "<?php echo $nonce; ?>",
						'refresh_visual_form': myshortcode
					};

					jQuery.post(ajaxurl, data, function (response) {
						//response
						$("#wpecf7visualeditor").html(response);
						//igualamos el textarea con el codemirror
						$("#wpcf7-form").text(get_codemirror());

						//refrescanis
						$('#wpecf7visualeditor').vSort();
					});
				}


				$('.seeornot').click(function () {
					$('.seeornot').toggleClass('seeornot dashicons dashicons-visibility').toggleClass('seeornot dashicons dashicons-hidden');

					if ($(this).hasClass("dashicons-hidden")) {
						$(".refresh-visual").hide(0);
					} else {
						$(".refresh-visual").show(0);
					}

					$('#wpecf7visualeditor').toggle();
					iconeyes = $(this).attr("class");
					if (iconeyes == 'seeornot dashicons dashicons-hidden') {
						$('#wpecf7textareaeditor').attr('style', 'width: 99% !important;');
					} else {
						$('#wpecf7textareaeditor').attr('style', 'width: 52% !important;');
					}

					save_icon_eyes(iconeyes);
				});
				//select theme editor
				$("#themes-selection-editor").change(function () {
					mytheme = $(this).val();
					selectTheme(mytheme)
					save_selection_theme(mytheme)
				});

				/*$('p.submit').on('click',function(){
				 //function to change the data
				 var separators = ['\n\n', '\\\+', '>\n','\n\n\n'];
				 //before pass by here
				 var piezes_submit = $("textarea#wpcf7-form").text().split(new RegExp(separators.join('|'), 'g'));
				 for (var i = 0; i < piezes_submit.length; i++) {
				 
				 if(piezes_submit[i].indexOf('[submit') >-1){
				 //here to see if you have some HTML to be tag behind so not happened
				 if(piezes_submit[i].indexOf('>')>-1){
				 //no pasa nada
				 }else{
				 //delete the blank spaces that exist
				 piezes_submit[i] = piezes_submit[i].replace('\n','');
				 //now save the positions and field on the array
				 $("textarea#wpcf7-form").text($("textarea#wpcf7-form").text().replace(piezes_submit[i],'<p>'+piezes_submit[i]+'</p>'));
				 sincronized_textarea();
				 }
				 }
				 }
				 //ADD TO THE SHORTCODES A PARAGRAPH TO THIS WORK
				 });*/


				//refresh visual form
				$(".refresh-visual").click(function () {
					var refesh_icon_url = "<?php echo get_bloginfo('wpurl') . '/wp-admin/images/wpspin_light.gif'; ?>";
					//We will first remove the data
					$("#wpecf7visualeditor").css({'width': '400px !important', 'border': '2px solid #ccc'});
					$("#wpecf7visualeditor").find("*").remove();

					$("#wpecf7visualeditor").html('<div style="width:100% !important; height:150px !important; border:2px solid #ccc;"><center><p><img style="width:20px; height:20px; margin-top:30px;" src="' + refesh_icon_url + '"></i></p><p>Refresh....</p></center></div>');


					$textform_temp = '<div>' + changeEtiquete($("#wpcf7-form").val()) + '</div>';
					//alert($textform_temp);
					$myhtml = '';
					var initial = 0;
					//take the form to refresh the visual
					$($textform_temp).find('>p,>label,>style,>script').each(function (i) {

						myetiquete = $(this)[0].tagName;
						if (myetiquete == 'LABEL') {
							$myhtml += '<div class="sortitem" data-order="' + initial + '"><span class="sorthandle" unselectable="on"></span><span unselectable="on" class="itemactions"><span class="itemdelete refresh-delete" style="position:absolute; top:0; margin-top-20px; right:0; padding-right:10px;">  </span></span><p>' + $(this).prop("outerHTML") + '</p></div>';
							initial += 1;
						} else if (myetiquete == 'P') {
							$myhtml += '<div class="sortitem" data-order="' + initial + '"><span class="sorthandle" unselectable="on"></span><span unselectable="on" class="itemactions"><span class="itemdelete refresh-delete" style="position:absolute; top:0; right:0; margin-top-20px; padding-right:10px;"> </span></span>' + $(this).prop("outerHTML") + '</div>';
							initial += 1;
						} else if (myetiquete == 'SCRIPT') {
							$myhtml += '<div class="sortitem" data-order="' + initial + '"><span class="sorthandle" unselectable="on"></span><span unselectable="on" class="itemactions"><span class="itemdelete refresh-delete" style="position:absolute; top:0; right:0; margin-top-20px; padding-right:10px;"> </span></span><p>' + $(this).prop("outerHTML") + '</p></div>';
							initial += 1;
						}


					});
					//	alert($myhtml);
					//here call the ajax that we return the cooled form
					fnrefresh_visual_form($myhtml);
				});


			});
		</script>
		<?php
	}

	function wp_visual_script_footer() {
		?>	
		<script type="text/javascript">
			var config, editor;
			var mytextarea = document.getElementById("wpcf7-form");
			var mytheme = "<?php print(get_option('wpecf7vb_selection_theme')); ?>"
			config = {
				lineNumbers: true,
				mode: "htmlmixed",
				theme: mytheme,
				indentWithTabs: false,
				htmlMode: true,
				readOnly: false,
			};
			editor = CodeMirror.fromTextArea(document.getElementById("wpcf7-form"), config);
			editor.setSize(100, 100);
			editor.refresh()


			//FUNCTIONS
			function selectTheme(mytheme) {
				editor.setOption("theme", mytheme);
			}
			function sincronized_codemirror() {
				text = editor.getValue();
				document.getElementById("wpcf7-form").value = text;
			}
			//polimorfismo
			function sincronized_textarea_to_codemirror(text) {

				document.getElementById("wpcf7-form").value = text;
			}
			function get_codemirror() {
				return editor.getValue();
			}
			function sincronized_textarea() {
				text = document.getElementById("wpcf7-form").value;
				editor.setValue(text);
			}
			function sincronized_codemirror_to_textarea(text) {
				editor.setValue(text);
			}

			//CLOSED FUNCTIONS---

			//sincronized codemirror
			editor.on('keyup', function () {
				sincronized_codemirror();
			});
			//sincronized textarea
			mytextarea.addEventListener('keyup', function (e) {
				sincronized_textarea();
			});

			//replace cursor in text
			function insertTextAtCursor(text) {
				cursor = editor.getCursor();
				editor.replaceRange(text, cursor);
			}
			//setTimeout(selectTheme, 5000);


		</script>


		<?php
	}

	add_filter('wpcf7_editor_panels', 'WPe_Visual_CF7');

	function WPe_Visual_CF7($panels) {
		//$visualform['visualform-panel'] = array(
		$panels['form-panel'] = array(
			'title' => __('Visual Form', 'visual-builder-for-contact-form-7'),
			'callback' => 'wpecf7vb_editor_panel_form');

		//$panels = array_merge($visualform, $panels);

		return $panels;
	}

	function wpecf7vb_editor_panel_form($post) {


//	global $pagenow, $screen, $current_screen, $current_page;
		$style_wpecf7vb_editor = '';
		$class_iconeyes = "seeornot dashicons dashicons-visibility";
//	if(get_option("icon_eyes_status")=="seeornot dashicons dashicons-visibility"){
		//$style_wpecf7vb_editor = "display:block";
//	}else if(get_option("icon_eyes_status")=="seeornot dashicons dashicons-hidden"){
		if (strpos(get_option("icon_eyes_status"), 'hidden') !== false) {
			$style_wpecf7vb_editor = "display:none";
			$class_iconeyes = "seeornot dashicons dashicons-hidden";
		}
		?>


		<!--element-->
		<i style="float: right;" class="<?php print($class_iconeyes); ?>" id="icon_eyes"></i>

		<i title="Refresh Visual Form" class="dashicons dashicons-image-rotate refresh-visual" style="float: right;margin-right: 275px; margin-top: 5px; cursor: pointer; <?php echo $style_wpecf7vb_editor; ?>"></i>
		<h3><?php echo __('Visual Form', 'visual-builder-for-contact-form-7'); ?></h3>
		<?php // if($current_screen->id="toplevel_pagewpcf7" ) {}  ?>
		<div class="wpecf7editors">
			<!--option to hide the element visual provided the post not be has saved yet-->
			<?php
                        if( isset($_GET['post']) )
                            $posst = get_post_status($_GET['post']);
			if (!empty($posst)) {
				?>	
				<div style="<?php print($style_wpecf7vb_editor); ?>" class="wpecf7vb_col"   id="wpecf7visualeditor" data-callback="changeorder( jQuery('#wpecf7visualeditor') );">
					<?php
					//echo print_r($post->shortcode());
					echo do_shortcode($post->shortcode());
					//echo wpcf7_replace_all_form_tags('<p>Mensaje[textarea your-message]</p><p>[submit "Enviar2"]</p>');
					?>
				</div>
				<?php } ?>
			<div class="wpecf7vb_col" id="wpecf7textareaeditor">
				<?php
				$tag_generator = WPCF7_TagGenerator::get_instance();
				$tag_generator->print_buttons();
				?>
				<textarea id="wpcf7-form" name="wpcf7-form" cols="100" rows="24" class="large-text code"><?php echo esc_textarea($post->prop('form')); ?></textarea>
				<!--select themes-->
				<?php /**
				 * Outputs the html selected attribute.
				 * Compares the first two arguments and if identical marks as selected
				 * @param mixed $selected One of the values to compare
				 * @param mixed $current  (true) The other value to compare if not just true
				 * @param bool  $echo     Whether to echo or just return the string
				 * @return string html attribute or empty string
				 *
				  function selected( $selected, $current = true, $echo = true ) {
				 */ ?>
		<?php $selected_theme = get_option('wpecf7vb_selection_theme'); ?>
				<select id="themes-selection-editor"> 
					<option value="" <?php selected('', $selected_theme, true) ?>><?php _e('Colors Scheme', 'visual-builder-for-contact-form-7'); ?></option>
					<option value="monokai" <?php selected('monokai', $selected_theme, true) ?>>Monokai</option>
					<option value="blackboard" <?php selected('blackboard', $selected_theme, true) ?>>Blackboard</option>
					<option value="cobalt" <?php selected('cobalt', $selected_theme, true) ?>>Cobalt</option>
				</select>
			</div>
		</div>
		<div class="clear">	</div>
		<?php
	}

	function wpecf7vb_plugin_url($path = '') {
		$url = plugins_url($path, wpecf7vb_PLUGIN);

		if (is_ssl() && 'http:' == substr($url, 0, 5)) {
			$url = 'https:' . substr($url, 5);
		}

		return $url;
	}

	/* AJAX FUNCTIONS EDITOR */
	add_action('wp_ajax_save_iconeyes', 'save_iconeyes_callback');
	add_action("wp_ajax_save_selection_theme", 'save_selection_theme_callback');
//actions refresh visual 
	add_action('wp_ajax_refresh_visual', 'refresh_visual_callback');

//ajax icons
	function save_iconeyes_callback() {
		//nonce referer
		check_ajax_referer('wpc_visual_nonce');
		$iconeyes = $_POST['iconeyes'];
		//save option
		update_option('icon_eyes_status', $iconeyes);
		wp_die(); // this is required to terminate immediately and return a proper response
	}

//ajax save theme sublime
	function save_selection_theme_callback() {
		check_ajax_referer('wpc_visual_nonce');
		$selection_theme = $_POST['selection_theme'];
		//save theme
		update_option('wpecf7vb_selection_theme', $selection_theme);
		wp_die();
	}

//ajax refresh visual
	function refresh_visual_callback() {
		check_ajax_referer('wpc_visual_nonce');
		$refresh_visual_form = wpcf7_replace_all_form_tags($_POST['refresh_visual_form']);
		$refresh_visual_form = str_replace('\\', '', $refresh_visual_form);
		$refresh_visual_form = str_replace('<span class', '<br><span class', $refresh_visual_form);
		echo $refresh_visual_form;
		wp_die();
	}

}

//closed init

