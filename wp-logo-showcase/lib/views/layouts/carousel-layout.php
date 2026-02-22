<?php
/**
 * Template: Carousel Layout
 *
 * @package RT_WSL
 */

global $rtWLS;

$desc   = null;
$itemsA = [];

if ( $linkType == 'no_link' || ! $url ) {
	$itemsA['logo']  = $img_src;
	$itemsA['title'] = '<h3>' . esc_html( $title ) . '</h3>';
} else {
	$target          = ( $linkType == 'new_window' ? 'target="_blank"' : null );
	$target         .= $nofollow ? ' rel="nofollow"' : null;
	$itemsA['logo']  = "<a href='" . esc_url( $url ) . "' {$target} >$img_src</a>";
	$itemsA['title'] = "<h3><a href='" . esc_url( $url ) . "' {$target}>" . esc_html( $title ) . '</a></h3>';
}

$desc                 .= "<div class='logo-description'>";
$desc                 .= apply_filters( 'the_content', $description );
$desc                 .= '</div>';
$itemsA['description'] = $desc;

$html  = null;
$html .= "<div class='rt-col-md-" . esc_attr( $grid ) . " rt-col-sm-6 rt-col-xs-12'>";
$html .= "<div class='single-logo rt-equal-height' data-title='" . esc_attr( $title ) . "'>";
$html .= "<div class='single-logo-container'>";

foreach ( $items as $item ) {
	$html .= ! empty( $itemsA[ $item ] ) ? $itemsA[ $item ] : null;
}

$html .= '</div>';
$html .= '</div>';
$html .= '</div>';

$rtWLS->print_html( $html );
