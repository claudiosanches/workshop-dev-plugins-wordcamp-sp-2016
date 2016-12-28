<?php
/**
 * Plugin Name: Trollface shortcode
 */

/**
 * Adiciona suporte para tradução.
 */
function tfs_load_plugin_textdomain() {
    load_plugin_textdomain( 'trollface-shortcode', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'tfs_load_plugin_textdomain' );

/**
 * Registra o shortcode [trollface].
 *
 * Aceita os parametros `href` e `title`
 * Exemplo de shortcode:
 *
 * [trollface href="https://2016.saopaulo.wordcamp.org/" title="Trollface"]
 *
 * @param array $atts Atributos do shortcode.
 */
function tfs_shortcode( $atts ) {
	// Extrai os atributos do shortcode,
	// atribuindo valores padrões caso não sejam preenchidos.
    $data = shortcode_atts( array(
        'href'  => '',
        'title' => __( 'Trollface', 'trollface-shortcode' ),
    ), $atts );

    // Gera o HTML que será exibido pelo shortcode.
    $html = '<img src="' . plugins_url( 'images/trollface.png', __FILE__ ) . '" alt="' . esc_attr( $data['title'] ) . '">';

    if ( ! empty( $data['href'] ) ) {
    	$html = '<a title="' . esc_attr( $data['title'] ) . '" href="' . esc_url( $data['href'] ) . '">' . $html . '</a>';
    }

    return $html;
}
add_shortcode( 'trollface', 'tfs_shortcode' );
