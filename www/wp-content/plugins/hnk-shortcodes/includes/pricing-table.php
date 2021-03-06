<?php
if ( ! defined( 'PTP_PLUGIN_PATH' ) ) {
    return;
}

global $features_metabox;

/**
 * Helper function to generate pricing table from the database
 * 
 * @param   int  $id  Table ID
 * @return  string
 */
function hnk_shortcode_pricing_table_generator( $id, $options = array() ) {
	global $features_metabox;
    global $meta;

    $meta = get_post_meta( $id, $features_metabox->get_the_id(), TRUE );

    $loop_index = 0;
    $pricing_table_html = '';
    
    foreach ( $meta['column'] as $column ) {
        // Column details
		$plan_name     = isset( $column['planname'] ) ? $column['planname'] : '';
		$plan_price    = isset( $column['planprice'] ) ? $column['planprice'] : '';
		$plan_features = isset( $column['planfeatures'] ) ? $column['planfeatures'] : '';
		$button_text   = isset( $column['buttontext'] ) ? $column['buttontext'] : '';
		$button_url    = isset( $column['buttonurl'] ) ? $column['buttonurl'] : '';
		$button_url    = trim( $button_url );
        
        // Get custom shortcode if any
		$custom_button     = false;
		$shortcode_pattern = '|^\[shortcode\](?P<custom_button>.*)\[/shortcode\]$|';

        if ( preg_match( $shortcode_pattern, $button_text, $matches) ||
        	 preg_match( $shortcode_pattern, $button_url, $matches) ) {
            $custom_button = $matches['custom_button'];
        }

        // Featured column
		$feature       = '';
		$feature_label = '';
        
        if( isset( $column['feature'] ) && $column['feature'] == "featured" ) {
			$feature           = "highlight";
			$most_popular_text = isset( $meta['most-popular-label-text'] ) ? $meta['most-popular-label-text'] : __( 'Most Popular', PTP_LOC );
			$feature_label     = '<div class="popular">' . $most_popular_text . '</div>';
        }

        $description = '';

        if ( ( $position = strpos( $plan_features, '--' ) ) !== false ) {
        	$description = sprintf( '<div class="description">%s</div>', substr( $plan_features, 0, $position ) );
        	$plan_features = substr( $plan_features, $position + 2 );
        }

        // create the html code
        $pricing_table_html .= '
		<div class="price-column '. $feature . '">' .
            $feature_label .
            '<div class="column-container">' . 
				'<div class="plan">' . $plan_name . '</div> ' .
		  		'<div class="price">' . $plan_price . '</div>' . $description .
		  		'<ul class="features">' .
                    hnk_shortcode_pricing_table_features( $plan_features, dh_ptp_get_max_number_of_features() ) .
                '</ul>' .
	  			'<div class="cta">'.
                    ( ( $custom_button ) ? $custom_button : '<a class="button" href="' . $button_url . '">' . $button_text . '</a>' ) .
	  			'</div>' .
			'</div>' .
		'</div>';

        $loop_index++;
    }

    return $pricing_table_html;
}

function hnk_shortcode_pricing_table_features( $plan_features, $max_number_of_features, $fill_blank_rows = false ) {
	// the string to be returned
    $html = '';

    // explode string into a useable array
    $features = explode( "\n", $plan_features );
    $features = array_map( 'trim', $features );

    //how many features does this column have?
    $this_columns_number_of_features = count( $features );

    foreach ( $features as $feature ) {
    	// Add divider line
    	if ( $feature == '-' ) {
    		$html.= '<li class="divider"></li>';
    		continue;
    	}

    	if ( empty( $feature ) ) {
    		$html.= '<li class="spacer"></li>';
    		continue;
    	}

    	$html.= '<li>' . $feature . '</li>';
    }

    if ( $fill_blank_rows )
    	$html.= str_repeat( '<li class="blank"></li>', $max_number_of_features - count( $features ) );

    return $html;
}

$atts = shortcode_atts( array(
	'id'    => '',
	'class' => '',
	'css'   => ''
), $atts );

// check if id exists
if ( $atts['id'] != '' ) {
	$translate = array(
		1 => 'one-column',
		2 => 'two-columns',
		3 => 'three-columns',
		4 => 'four-columns',
		5 => 'five-columns',
		6 => 'six-columns',
		7 => 'seven-columns',
		8 => 'eight-columns',
		9 => 'nine-columns',
		10 => 'ten-columns',
		11 => 'eleven-columns',
		12 => 'twelve-columns'
	);

	$meta  = get_post_meta( $atts['id'], $features_metabox->get_the_id(), TRUE );
	$class = array( 'pricing-table', $translate[count( $meta['column'] )], $atts['class'] );

	// check if our pricing table contains any content
	if ( ! empty( $meta ) ) {
        // if the table contains content, call the function that generates the table
		echo sprintf( '<div class="%s">%s</div>',
            esc_attr( implode( ' ', $class ) ),
            do_shortcode( hnk_shortcode_pricing_table_generator( $atts['id'] ) ) );
	}
}
else {
	echo __( 'Pricing table does not exist. Please check your shortcode.', 'hnk' );
}