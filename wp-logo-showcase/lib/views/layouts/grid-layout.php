<?php
/**
 * Template: Grid Layout
 *
 * @package RT_WSL
 */

$desc   = null;
$itemsA = [];

if ( $linkType == 'no_link' || ! $url ) {
	$itemsA['logo']  = $img_src;
	$itemsA['title'] = "<h3>{$title}</h3>";
} else {
	$target          = ( $linkType == 'new_window' ? 'target="_blank"' : null );
	$target         .= $nofollow ? ' rel="nofollow"' : null;
	$itemsA['logo']  = "<a href='{$url}' {$target} >$img_src</a>";
	$itemsA['title'] = "<h3><a href='{$url}' {$target}>{$title}</a></h3>";
}

$desc                 .= "<div class='logo-description'>";
$desc                 .= apply_filters( 'the_content', $description );
$desc                 .= '</div>';
$itemsA['description'] = $desc;

$html  = null;
$html .= "<div class='rt-col-md-{$grid} rt-col-sm-{$Tgrid} rt-col-xs-{$Mgrid}'>";
$html .= "<div class='single-logo rt-equal-height data-title='{$title}'>";
$html .= "<div class='single-logo-container'>";

foreach ( $items as $item ) {
	$html .= ! empty( $itemsA[ $item ] ) ? $itemsA[ $item ] : null;
}

$html .= '</div>';
$html .= '</div>';
$html .= '</div>';

$rtWLS->print_html( $html );
