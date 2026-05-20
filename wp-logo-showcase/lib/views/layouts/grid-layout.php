<?php
/**
 * Template: Grid Layout
 *
 * @package RT_WSL
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

global $rtWLS;

$desc                 .= "<div class='logo-description'>";
$desc                 .= wpautop( wp_kses( (string) $description, $rtWLS->allowed_description_html() ) );
$desc                 .= '</div>';
$itemsA['description'] = $desc;

$html  = null;
$html .= "<div class='rt-col-md-" . esc_attr( $grid ) . " rt-col-sm-" . esc_attr( $Tgrid ) . " rt-col-xs-" . esc_attr( $Mgrid ) . "'>";
$html .= "<div class='single-logo rt-equal-height' data-title='" . esc_attr( $title ) . "'>";
$html .= "<div class='single-logo-container'>";

foreach ( $items as $item ) {
	$html .= ! empty( $itemsA[ $item ] ) ? $itemsA[ $item ] : null;
}

$html .= '</div>';
$html .= '</div>';
$html .= '</div>';

$rtWLS->print_html( $html );
