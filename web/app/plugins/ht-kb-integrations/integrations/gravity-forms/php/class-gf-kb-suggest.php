<?php
/**
 * Gravity Form KB Suggest text field class
 * Extends GF_Field_Text
 */

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class GF_Field_KB_Suggest extends GF_Field_Text {

	public $type = 'kbsuggest';

	/**
	* Name / title of field
	*/
	public function get_form_editor_field_title() {
		return esc_attr__( 'KB Suggest', 'ht-kb-integrations' );
	}

	/**
	* Conditional logic is supported for this field
	*/
	public function is_conditional_logic_supported() {
		return true;
	}

	/**
	* Override GF_Field_Text for GF_Field_KB_Suggest input field
	*/
	public function get_field_input( $form, $value = '', $entry = null ) {
		//check for hkb, if not present, disable the output and emit warning

		$form_id         = absint( $form['id'] );
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();

		$html_input_type = 'text';

		if ( $this->enablePasswordInput && ! $is_entry_detail ) {
			$html_input_type = 'password';
		}

		$logic_event = ! $is_form_editor && ! $is_entry_detail ? $this->get_conditional_logic_event( 'keyup' ) : '';
		$id          = (int) $this->id;
		$field_id    = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";

		$value        = esc_attr( $value );
		$size         = $this->size;
		$class_suffix = $is_entry_detail ? '_admin' : '';
		$theme_class  = 'theme-' . get_option('stylesheet');
		$class        = $size . $class_suffix . ' hkb_gfsuggest__field' . ' ' . $theme_class;

		$max_length = is_numeric( $this->maxLength ) ? "maxlength='{$this->maxLength}'" : '';

		$tabindex              = $this->get_tabindex();
		$disabled_text         = $is_form_editor ? 'disabled="disabled"' : '';
		$placeholder_attribute = $this->get_field_placeholder_attribute();
		$required_attribute    = $this->isRequired ? 'aria-required="true"' : '';
		$invalid_attribute     = $this->failed_validation ? 'aria-invalid="true"' : 'aria-invalid="false"';

		$max_results = $this->maxResults;
		$icl_lang_code = defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : '';

		//filterable ht_kb_suggest_rest_endpoint
		$kb_search_endpoint = apply_filters('ht_kb_suggest_rest_endpoint', site_url('/wp-json/wp/v2/ht-kb?search='));

		$input = "";

		//output the underscores template
		ob_start();
        include_once( 'ht-kb-suggest-results-underscore-templates.php' );
        $includes = ob_get_clean();
        $input .= $includes;
                
    $input .= "<div class='hkb_gfsuggest'>";

		$input .= "<input name='input_{$id}' id='{$field_id}' type='{$html_input_type}' value='{$value}' class='{$class}' {$max_length} {$tabindex} {$logic_event} {$placeholder_attribute} {$required_attribute} {$invalid_attribute} {$disabled_text}/>";

		$input .= "<input type='hidden' class='kb-suggest-max-results'  name='kb-suggest-max-results' value='{$max_results}' />
				   <input type='hidden' name='kb-suggest-endpoint-url' id='kb-suggest-endpoint-url' value='{$kb_search_endpoint}'/>
                   <input type='hidden' name='lang' value='{$icl_lang_code}'/>";

    $input .= "<div class='hkb-gfsuggest__resultsinfo'>
                        <!-- results info will appear here-->
                    </div>";
        
		$input .= "<div class='hkb-gfsuggest__results'>
                  <ul>
                    <!-- results will appear here -->
                  </ul>
                </div>";
    $input .= "<div class='hkb-gfsuggest__messages'>
                        <!-- loading and no results messages will appear here-->
                    </div>";
    $input .= "<div class='hkb-gfsuggest__allresults'>
                        <!-- see all results messages will appear here-->
                    </div>";

    $input .= "</div>";

		return sprintf( "<div class='ginput_container ginput_container_kbsuggest'>%s</div>", $input );
	}

	/**
	* HTML is disabled for kb suggest
	*/
	public function allow_html() {
		return false;
	}

}

GF_Fields::register( new GF_Field_KB_Suggest() );