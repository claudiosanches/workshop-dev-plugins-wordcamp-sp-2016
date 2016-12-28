<?php
/**
 * Deleta opções do plugin quando ele desinstalado.
 */

// Verifica se o arquivo esta sendo realmente chamado pelo WordPress.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Deleta as opções do plugin.
delete_option( 'mylb_settings' );
