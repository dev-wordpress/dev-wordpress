<?php
/**
 * WARNING: This file is part of the theme. DO NOT edit
 * this file under any circumstances.
 */

/**
 * Prevent direct access to this file
 */
defined( 'ABSPATH' ) or die();

/**
 * @package  Hnk
 * @author   Binh Pham Thanh <binhpham@linethemes.com>
 */
class Hnk_ThemeCustomize extends Hnk_Base
{
	protected function __construct() {
		add_action( 'customize_save', 'flush_rewrite_rules' );
		
		/**
		 * Remove default sections & controls in customizer
		 */
		// add_action( 'customize_register', array( $this, 'remove_default' ), 99 );

		/**
		 * Register sections
		 */
		add_action( 'customize_register', array( $this, 'setup_sections' ) );

		/**
		 * Register panels
		 */
		add_action( 'customize_register', array( $this, 'setup_panels' ) );

		/**
		 * Register sections
		 */
		add_action( 'customize_register', array( $this, 'setup_controls' ) );

		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Remove the built-in sections and controls
	 * 
	 * @param   WP_Customize_Manager  $wp_customize  Theme customize instance
	 * @return  void
	 */
	public function remove_default( $wp_customize ) {
		foreach ( array( 'title_tagline', 'static_front_page', 'nav' ) as $id ) {
			if ( $section = $wp_customize->get_section( $id ) ) {
				foreach ( $section->controls as $control )
					$wp_customize->remove_control( $control->id );

				$wp_customize->remove_section( $id );
			}
		}
	}

	/**
	 * Helper method for add sections to the theme customize
	 * 
	 * @param   WP_Customize_Manager  $wp_customize  Theme customize instance
	 * @return  void
	 */
	public function setup_sections( $wp_customize ) {
		$sections = array();

		if ( $template = locate_template( 'includes/customize-sections.php', false, false ) ) {
			$_sections = include( $template );

			if ( is_array( $_sections ) )
				$sections = array_merge( $sections, $_sections );
		}

		$section_index = 0;
		$sections      = apply_filters( 'theme/customize-sections', $sections );
		$defaults      = array(
			'type'        => 'section',
			'title'       => esc_html__( 'Untitled Section', 'hnk' ),
			'description' => ''
		);

		foreach ( $sections as $id => $params ) {
			$params = array_merge( $defaults, $params );
			$params['priority'] = ( isset( $params['priority'] ) ) ? $params['priority'] : $section_index++;

			if ( $params['type'] == 'section' )
				$wp_customize->add_section( new WP_Customize_Section( $wp_customize, $id, $params ) );
			elseif ( $params['type'] == 'panel' )
				$wp_customize->add_panel( new WP_Customize_Panel( $wp_customize, $id, $params ) );
		}
	}

	/**
	 * Helper method for add panels to the theme customize
	 * 
	 * @param   WP_Customize_Manager  $wp_customize  Theme customize instance
	 * @return  void
	 */
	public function setup_panels( $wp_customize ) {
		$panels = array();

		if ( $template = locate_template( 'includes/customize-panels.php', false, false ) ) {
			$_panels = include( $template );

			if ( is_array( $_panels ) )
				$panels = array_merge( $panels, $_panels );
		}

		$section_index = 0;
		$panels      = apply_filters( 'theme/customize-panels', $panels );
		$defaults      = array(
			'title'       => esc_html__( 'Untitled Section', 'hnk' ),
			'description' => ''
		);

		foreach ( $panels as $id => $params ) {
			$params = array_merge( $defaults, $params );
			$params['priority'] = ( isset( $params['priority'] ) ) ? $params['priority'] : $section_index++;

			$wp_customize->add_panel( new WP_Customize_Panel( $wp_customize, $id, $params ) );
		}
	}

	/**
	 * Helper method for add controls to the theme customize
	 * 
	 * @param   WP_Customize_Manager  $wp_customize  Theme customize instance
	 * @return  void
	 */
	public function setup_controls( $wp_customize ) {
		// Config transport for existing controls
		$wp_customize->get_setting( 'blogname' )->transport        = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

		$controls = array();

		if ( $template = locate_template( 'includes/customize-controls.php', false, false ) ) {
			$_controls = include( $template );

			if ( is_array( $_controls ) ) {
				$controls = array_merge( $controls, $_controls );
			}
		}

		$control_index = 0;
		$controls      = apply_filters( 'theme/customize-controls', $controls );

		foreach ( $controls as $id => $params ) {
			$default = '';
			$params['priority'] = $control_index++;

			if ( isset( $params['default'] ) )
				$default = $params['default'];

			if ( !isset( $params['settings'] ) )
				$params['settings'] = $id;

			if ( !isset( $params['transport'] ) )
				$params['transport'] = 'refresh';

			$class = \OptionsPlus\Options\Helper::recognize_control_class( $params['type'] );

			if ( $wp_customize->get_setting( $params['settings'] ) == null ) {
				// Register setting for this control
				$wp_customize->add_setting( $params['settings'], array(
					'default'           => $default,
					'transport'         => $params['transport'],
					'sanitize_callback' => array( $class, 'sanitize' )
				) );
			}

			$control = new \OptionsPlus\Customize\Control( $wp_customize, $id, $params );

			if ( $control->is_valid() ) {
				$wp_customize->add_control( $control );
				$this->controls[ $id ] = $control;
			}
		}
	}

	/**
	 * Enqueue scripts for the theme customize
	 * 
	 * @return  void
	 */
	public function enqueue_scripts() {
		global $_options_plus_fonts, $wp_scripts;

		wp_enqueue_script( 'theme-customizer-controls' );
		wp_localize_script( 'op-options-controls', '_opFonts', $_options_plus_fonts );
	}

	/**
	 * Enqueue styles for the theme customize
	 * 
	 * @return  void
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'op-options-controls' );
		wp_enqueue_style( 'op-customizer' );
	}
}
