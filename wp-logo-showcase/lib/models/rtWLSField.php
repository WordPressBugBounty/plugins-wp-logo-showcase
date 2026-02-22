<?php
/**
 * Field Generator Class
 *
 * @package RT_WSL
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'This script cannot be accessed directly.' );
}

if ( ! class_exists( 'rtWLSField' ) ) :
	/**
	 * Field Generator Class
	 */
	class rtWLSField {
		private $type;
		private $name;
		private $value;
		private $default;
		private $label;
		private $id;
		private $class;
		private $holderClass;
		private $holderID;
		private $description;
		private $options;
		private $option;
		private $attr;
		private $multiple;
		private $alignment;
		private $placeholder;
		private $blank;

		function __construct() {
		}

		/**
		 *
		 * Initiate the predefined property for the field object
		 *
		 * @param $attr
		 */
		private function setArgument( $attr ) {
			$this->type     = isset( $attr['type'] ) ? ( $attr['type'] ? $attr['type'] : 'text' ) : 'text';
			$this->multiple = isset( $attr['multiple'] ) ? ( $attr['multiple'] ? $attr['multiple'] : false ) : false;
			$this->name     = isset( $attr['name'] ) ? ( $attr['name'] ? $attr['name'] : null ) : null;
			$this->name     = isset( $attr['name'] ) ? ( $attr['name'] ? $attr['name'] : null ) : null;
			$this->default  = isset( $attr['default'] ) ? ( $attr['default'] ? $attr['default'] : null ) : null;
			$this->value    = isset( $attr['value'] ) ? ( $attr['value'] ? $attr['value'] : null ) : null;

			if ( ! $this->value ) {
				if ( $this->multiple ) {
					$v = get_post_meta( get_the_ID(), $this->name );
				} else {
					$v = get_post_meta( get_the_ID(), $this->name, true );
				}
				$this->value = ( $v ? $v : $this->default );
			}

			$this->label       = isset( $attr['label'] ) ? ( $attr['label'] ? $attr['label'] : null ) : null;
			$this->id          = isset( $attr['id'] ) ? ( $attr['id'] ? $attr['id'] : null ) : null;
			$this->class       = isset( $attr['class'] ) ? ( $attr['class'] ? $attr['class'] : null ) : null;
			$this->holderClass = isset( $attr['holderClass'] ) ? ( $attr['holderClass'] ? $attr['holderClass'] : null ) : null;
			$this->holderID    = isset( $attr['holderID'] ) ? ( $attr['holderID'] ? $attr['holderID'] : null ) : null;
			$this->placeholder = isset( $attr['placeholder'] ) ? ( $attr['placeholder'] ? $attr['placeholder'] : null ) : null;
			$this->description = isset( $attr['description'] ) ? ( $attr['description'] ? $attr['description'] : null ) : null;
			$this->options     = isset( $attr['options'] ) ? ( $attr['options'] ? $attr['options'] : [] ) : [];
			$this->option      = isset( $attr['option'] ) ? ( $attr['option'] ? $attr['option'] : null ) : null;
			$this->attr        = isset( $attr['attr'] ) ? ( $attr['attr'] ? $attr['attr'] : null ) : null;
			$this->alignment   = isset( $attr['alignment'] ) ? ( $attr['alignment'] ? $attr['alignment'] : null ) : null;
			$this->blank       = ! empty( $attr['blank'] ) ? $attr['blank'] : null;
			$this->class       = $this->class ? $this->class . ' rt-form-control' : 'rt-form-control';
		}

		/**
		 * Create field
		 *
		 * @param $attr
		 *
		 * @return null|string
		 */
		public function Field( $attr ) {
			$this->setArgument( $attr );
			$html  = null;
			$html  = null;
			$html .= "<div class='rt-field-wrapper " . esc_attr( $this->holderClass ) . "' id='" . esc_attr( $this->holderID ) . "'>";
			$html .= sprintf(
				'<div class="rt-label">%s</div>',
				$this->label ? sprintf( '<label for="">%s</label>', $this->label ) : ''
			);
			$html .= "<div class='rt-field'>";
			switch ( $this->type ) {
				case 'text':
					$html .= $this->text();
					break;

				case 'url':
					$html .= $this->url();
					break;

				case 'number':
					$html .= $this->number();
					break;

				case 'select':
					$html .= $this->select();
					break;

				case 'textarea':
					$html .= $this->textArea();
					break;

				case 'checkbox':
					$html .= $this->checkbox();
					break;

				case 'radio':
					$html .= $this->radioField();
					break;

				case 'colorpicker':
					$html .= $this->colorPicker();
					break;

				case 'custom_css':
					$html .= $this->customCss();
					break;
				case 'image_size':
					$html .= $this->imageSize();
					break;
			}

			if ( $this->description ) {
				$html .= "<p class='description'>" . wp_kses_post( $this->description ) . '</p>';
			}

			$html .= '</div>'; // field
			$html .= '</div>'; // field holder

			return $html;
		}

		/**
		 * Generate text field
		 *
		 * @return null|string
		 */
		private function text() {
			$h  = null;
			$h .= "<input
                    type='text'
                    class='" . esc_attr( $this->class ) . "'
                    id='" . esc_attr( $this->id ) . "'
                    value='" . esc_attr( $this->value ) . "'
                    name='" . esc_attr( $this->name ) . "'
                    placeholder='" . esc_attr( $this->placeholder ) . "'
                    />";

			return $h;
		}

		/**
		 * Generate color picker
		 *
		 * @return null|string
		 */
		private function colorPicker() {
			$h  = null;
			$h .= "<input
                    type='text'
                    class='" . esc_attr( $this->class ) . " rt-color'
                    id='" . esc_attr( $this->id ) . "'
                    value='" . esc_attr( $this->value ) . "'
                    name='" . esc_attr( $this->name ) . "'
                    placeholder='" . esc_attr( $this->placeholder ) . "'
                    />";

			return $h;
		}

		/**
		 * Custom css field
		 *
		 * @return null|string
		 */
		private function customCss() {
			$h  = null;
			$h .= '<div class="rt-custom-css">';
			$h .= '<p class="description" style="color: red">Please use default customizer to add your css. This option is deprecated.</p>';

			$h .= '<div class="custom_css_container">';
			$h .= "<div name='" . esc_attr( $this->name ) . "' id='ret-" . wp_rand() . "' class='custom-css'>";
			$h .= '</div>';
			$h .= '</div>';
			$h .= "<textarea
                        style='display: none;'
                        class='custom_css_textarea'
                        id='" . esc_attr( $this->id ) . "'
                        name='" . esc_attr( $this->name ) . "'
                        >" . esc_textarea( $this->value ) . "</textarea>";
			$h .= '</div>';

			return $h;
		}

		/**
		 * Generate URL field
		 *
		 * @return null|string
		 */
		private function url() {
			$h  = null;
			$h .= "<input
                    type='url'
                    class='" . esc_attr( $this->class ) . "'
                    id='" . esc_attr( $this->id ) . "'
                    value='" . esc_attr( $this->value ) . "'
                    name='" . esc_attr( $this->name ) . "'
                    placeholder='" . esc_attr( $this->placeholder ) . "'
                    />";

			return $h;
		}

		/**
		 * Generate number field
		 *
		 * @return null|string
		 */
		private function number() {
			$h  = null;
			$h .= "<input
                    type='number'
                    class='" . esc_attr( $this->class ) . "'
                    id='" . esc_attr( $this->id ) . "'
                    value='" . esc_attr( $this->value ) . "'
                    name='" . esc_attr( $this->name ) . "'
                    placeholder='" . esc_attr( $this->placeholder ) . "'
                    />";

			return $h;
		}

		/**
		 * Generate Drop-down field
		 *
		 * @return null|string
		 */
		private function select() {
			$h = null;
			if ( $this->multiple ) {
				$this->attr  = " style='min-width:160px;'";
				$this->name  = $this->name . '[]';
				$this->attr  = $this->attr . " multiple='multiple'";
				$this->value = ( is_array( $this->value ) && ! empty( $this->value ) ? $this->value : [] );
			} else {
				$this->value = [ $this->value ];
			}

			$h .= "<select name='" . esc_attr( $this->name ) . "' id='" . esc_attr( $this->id ) . "' class='" . esc_attr( $this->class ) . "'>";
			if ( $this->blank ) {
				$h .= "<option value=''>" . esc_html( $this->blank ) . '</option>';
			}
			if ( is_array( $this->options ) && ! empty( $this->options ) ) {
				foreach ( $this->options as $key => $value ) {
					$slt = ( in_array( $key, $this->value ) ? 'selected' : null );
					$h  .= "<option {$slt} value='" . esc_attr( $key ) . "'>" . esc_html( $value ) . "</option>";
				}
			}
			$h .= '</select>';

			return $h;
		}

		/**
		 * Generate textArea field
		 *
		 * @return null|string
		 */
		private function textArea() {
			$h  = null;
			$h .= "<textarea
					rows='8'
					cols='40'
                    class='" . esc_attr( $this->class ) . " rt-textarea'
                    id='" . esc_attr( $this->id ) . "'
                    name='" . esc_attr( $this->name ) . "'
                    placeholder='" . esc_attr( $this->placeholder ) . "'
                    >" . esc_textarea( $this->value ) . "</textarea>";

			return $h;
		}

		/**
		 * Generate check box
		 *
		 * @return null|string
		 */
		private function checkbox() {
			$h = null;
			if ( $this->multiple ) {
				$this->name  = $this->name . '[]';
				$this->value = ( is_array( $this->value ) && ! empty( $this->value ) ? $this->value : [] );
			}
			if ( $this->multiple ) {
				$h .= "<div class='checkbox-group " . esc_attr( $this->alignment ) . "' id='" . esc_attr( $this->id ) . "'>";
				if ( is_array( $this->options ) && ! empty( $this->options ) ) {
					foreach ( $this->options as $key => $value ) {
						$checked = ( in_array( $key, $this->value ) ? 'checked' : null );
						$h      .= "<label for='" . esc_attr( $this->id ) . '-' . esc_attr( $key ) . "'>
                                <input type='checkbox' id='" . esc_attr( $this->id ) . '-' . esc_attr( $key ) . "' {$checked} name='" . esc_attr( $this->name ) . "' value='" . esc_attr( $key ) ."'>" . esc_html( $value ) ."
                                </label>";
					}
				}
				$h .= '</div>';
			} else {
				$checked = ( $this->value ? 'checked' : null );
				$h      .= "<label><input type='checkbox' {$checked} id='" . esc_attr( $this->id ) . "' name='" . esc_attr( $this->name ) . "' value='1' />" . esc_html( $this->option ) . "</label>";
			}

			return $h;
		}

		/**
		 * Generate Radio field
		 *
		 * @return null|string
		 */
		private function radioField() {
			$h  = null;
			$h .= "<div class='radio-group " . esc_attr( $this->alignment ) . "' id='" . esc_attr( $this->id ) . "'>";
			if ( is_array( $this->options ) && ! empty( $this->options ) ) {
				foreach ( $this->options as $key => $value ) {
					$checked = ( $key == $this->value ? 'checked' : null );
					$h      .= "<label for='" . esc_attr( $this->id ) . '-' . esc_attr( $key ) . "'>
                            <input type='radio' id='" . esc_attr( $this->id ) . '-' . esc_attr( $key ) . "' {$checked} name='" . esc_attr( $this->name ) . "' value='" . esc_attr( $key ) ."'>" . esc_html( $value ) ."
                            </label>";
				}
			}
			$h .= '</div>';

			return $h;
		}

		/**
		 * Image Size
		 *
		 * @return void
		 */
		private function imageSize() {
			global $rtWLS;
			$width    = ( ! empty( $this->value['width'] ) ? absint( $this->value['width'] ) : null );
			$height   = ( ! empty( $this->value['height'] ) ? absint( $this->value['height'] ) : null );
			$cropV    = ( ! empty( $this->value['crop'] ) ? $this->value['crop'] : false );
			$h        = null;
			$h       .= "<div class='rt-image-size-holder d-flex'>";
			$h       .= "<div class='rt-image-size-width rt-image-size d-flex'>";
			$h       .= '<label>Width</label>';
			$h       .= "<input type='number' name='" . esc_attr( $this->name ) . "[width]' value='" . esc_attr( $width ) . "' />";
			$h       .= '</div>';
			$h       .= "<div class='rt-image-size-height rt-image-size d-flex'>";
			$h       .= '<label>Height</label>';
			$h       .= "<input type='number' name='" . esc_attr( $this->name ) . "[height]' value='" . esc_attr( $height ) . "' />";
			$h       .= '</div>';
			$h       .= "<div class='rt-image-size-crop rt-image-size d-flex'>";
			$h       .= '<label>Crop</label>';
			$h       .= "<select name='" . esc_attr( $this->name ) . "[crop]' class='rt-select2'>";
			$cropList = $rtWLS->imageCropType();
			foreach ( $cropList as $crop => $cropLabel ) {
				$cSl = ( $crop == $cropV ? 'selected' : null );
				$h  .= "<option value='".esc_attr( $crop )."' {$cSl}>{$cropLabel}</option>";
			}
			$h .= '</select>';
			$h .= '</div>';
			$h .= '</div>';

			return $h;
		}



	}
endif;
