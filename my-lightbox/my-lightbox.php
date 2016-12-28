<?php
/**
 * Plugin Name: My Lightbox
 */

/**
 * Adiciona suporte para tradução.
 */
function mylb_load_plugin_textdomain() {
    load_plugin_textdomain( 'my-lightbox', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'mylb_load_plugin_textdomain' );

/**
 * Funções do back-end.
 */

/**
 * Adiciona menu no admin do WordPress.
 */
function mylb_add_options_page() {
	add_options_page(
		__( 'My Lightbox Options', 'my-lightbox' ), // Título da página.
		__( 'My Lightbox', 'my-lightbox' ), // Título no menu.
		'manage_options', // Permissões, no caso manage_options é apenas para admins.
		'my-lightbox', // Indentificação do menu.
		'mylb_options_page' // Função callback que irá exibir a página do menu.
	);
}
add_action( 'admin_menu', 'mylb_add_options_page' );

/**
 * Função utilizada como callback para exibir a página de opções.
 */
function mylb_options_page() {
	echo '<div class="wrap">';

	// Exibe o título da página já configurado em mylb_add_options_page().
	echo '<h1>' . esc_html( get_admin_page_title() ) . '</h1>';

	echo '<form action="options.php" method="post">';

	// Adiciona campos ocultos de nonce, ações e outros para segurança e validação.
	settings_fields( 'mylb_display_settings' );

	// Adiciona as suas sessões de opções.
	do_settings_sections( 'mylb_display_settings' );

	// Inclui o botão de salvar.
	submit_button();

	echo '</form>';

	echo '</div>';
}

/**
 * Registra as opções deste plugin.
 */
function mylb_register_settings() {
	// Indetificação do grupo de configurações.
	$group_id = 'mylb_display_settings';

	// Registra as configurações.
	register_setting(
		$group_id, // Indetificação do grupo de configurações.
		'mylb_settings', // Indentificação usada da opção no banco de dados.
		'mylb_settings_sanitize' // Função callback que irá higienizar as opções antes de salvar no banco de dados.
	);

	// Adiciona uma sessão de configurações.
	add_settings_section(
		'mylb_settings_section', // Identificação da sessão.
		__( 'Display settings', 'my-lightbox' ), // Título da sessão.
		null, // Funcão callback para exibir HTML para uma descrição.
		$group_id // Identificação do grupo que esta sessão pertence.
	);

	add_settings_field(
		'transaction_type', // Identificação da opção.
		__( 'Transaction Type', 'my-lightbox' ), // Rótulo (label) da opção.
		'mylb_setting_transaction_type_output', // Função callback para exibir o campo desta opção.
		$group_id, // Identificação do grupo que esta opção pertence.
		'mylb_settings_section', // Indentificação da sessão desta opção.
		array( 'label_for' => 'transaction_type' ) // Argumentos adicionais do campo, neste exemplo define o "for" para o <label>
	);

	add_settings_field(
		'max_width',
		__( 'Max Width', 'my-lightbox' ),
		'mylb_setting_max_width_output',
		$group_id,
		'mylb_settings_section',
		array( 'label_for' => 'max_width' )
	);

	add_settings_field(
		'mylb_text_field_2',
		__( 'Max Height', 'my-lightbox' ),
		'mylb_setting_max_height_output',
		$group_id,
		'mylb_settings_section',
		array( 'label_for' => 'max_width' )
	);
}

add_action( 'admin_init', 'mylb_register_settings' );

/**
 * Exibe o campo HTML para a opção "Transaction Type".
 */
function mylb_setting_transaction_type_output() {
	$options = get_option( 'mylb_settings' );
	$current = ! empty( $options['transaction_type'] ) ? $options['transaction_type'] : 'elastic';
	?>
	<select id="transaction_type" name="mylb_settings[transaction_type]" required="required">
		<option value="elastic" <?php selected( $current, 'elastic' ); ?>><?php esc_html_e( 'Elastic', 'my-lightbox' ); ?></option>
		<option value="fade" <?php selected( $current, 'fade' ); ?>><?php esc_html_e( 'Fade', 'my-lightbox' ); ?></option>
		<option value="none" <?php selected( $current, 'none' ); ?>><?php esc_html_e( 'None', 'my-lightbox' ); ?></option>
	</select>
	<p class="description"><?php esc_html_e( 'Transaction type for slideshows, when available.', 'my-lightbox' ) ?></p>
	<?php
}

/**
 * Exibe o campo HTML para a opção "Max Width".
 */
function mylb_setting_max_width_output() {
	$options = get_option( 'mylb_settings' );
	$current = ! empty( $options['max_width'] ) ? $options['max_width'] : 90;
	?>
	<input type="number" name="mylb_settings[max_width]" value="<?php echo esc_attr( $current ); ?>" id="max_width" min="10" max="100" step="1" required="required" />
	<?php
}

/**
 * Exibe o campo HTML para a opção "Max Height".
 */
function mylb_setting_max_height_output() {
	$options = get_option( 'mylb_settings' );
	$current = ! empty( $options['max_height'] ) ? $options['max_height'] : 90;
	?>
	<input type="number" name="mylb_settings[max_height]" value="<?php echo esc_attr( $current ); ?>" id="max_height" min="10" max="100" step="1" required="required" />
	<?php
}

/**
 * Higieniza os campos antes de salvar no banco de dados.
 */
function mylb_settings_sanitize( $input ) {
	// Valida o campo "transaction_type".
	if ( ! in_array( $input['transaction_type'], array( 'elastic', 'fade', 'none' ), true ) ) {
		$input['transaction_type'] = 'elastic';
	}

	// Higieniza e valida campo "max_width".
	if ( 10 <= $input['max_width'] && 100 >= $input['max_width'] ) {
		$input['max_width'] = absint( $input['max_width'] );
	} else {
		$input['max_width'] = 90;
	}

	// Higieniza e valida campo "max_height".
	if ( 10 <= $input['max_height'] && 100 >= $input['max_height'] ) {
		$input['max_height'] = absint( $input['max_height'] );
	} else {
		$input['max_height'] = 90;
	}

	return $input;
}

/**
 * Salva opções padrões no banco de dados quando o plugin é ativado.
 */
function mylb_initial_settings() {
	$settings = array(
		'transaction_type' => 'elastic',
		'max_width'        => '90',
		'max_height'       => '90',
	);

	update_option( 'mylb_settings', $settings );
}
register_activation_hook( __FILE__, 'mylb_initial_settings' );

/**
 * Adiciona o link da página de configuração do plugin na lista de plugins.
 *
 * @param  array $links Links do plugin.
 * @return array
 */
function mylb_action_links( $links ) {
	$new_links = array();

	$new_links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=my-lightbox' ) ) . '">' . __( 'Settings', 'my-lightbox' ) . '</a>';

	return array_merge( $new_links, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'mylb_action_links' );

/**
 * Funções para o front-end.
 */

/**
 * Inclui os scripts para abrir o lightbox.
 */
function mylb_include_scripts() {
	// Verifica se SCRIPT_DEBUG para usar versões não comprimidas de arquivos.
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	// Carrega os estilos para o Colorbox versão 1.6.4.
	wp_enqueue_style(
		'my-lightbox-colorbox', // ID do arquivo.
		plugins_url( 'assets/css/colorbox' . $suffix . '.css', __FILE__ ), // URL do arquivo.
		array(), // Lista de dependencias.
		'1.6.4' // Versão.
	);

	// Carrea o jQuery do WordPress.
	// NUNCA adicione uma copia do jQuery que não seja do próprio WordPress.
	wp_enqueue_script( 'jquery' );

	// Carrega o script do colorbox.
	wp_enqueue_script(
		'my-lightbox-colorbox', // ID do arquivo.
		plugins_url( 'assets/js/jquery.colorbox' . $suffix . '.js', __FILE__ ), // URL do arquivo.
		array( 'jquery' ), // Lista de dependencias, note que ao declarar "jquery" como dependencia, não seria necessário carregar ele antes.
		'1.6.4', // Versão.
		true // Quando `true` faz carregar no rodapé do site.
	);

	// Carrega o script deste plugin.
	wp_enqueue_script(
		'my-lightbox', // ID do arquivo.
		plugins_url( 'assets/js/my-lightbox' . $suffix . '.js', __FILE__ ), // URL do arquivo.
		array( 'my-lightbox-colorbox' ), // Lista de dependencias.
		'0.0.1', // Versão.
		true // Quando `true` faz carregar no rodapé do site.
	);

	// Carrega opções do banco de dados.
	$settings = get_option( 'mylb_settings' );

	// Adiciona algumas variáveis que serão utilizadas no arquivo JS do plugin.
	wp_localize_script(
		'my-lightbox',
		'myLightboxParams',
		array(
			// Faz com que as strings do Colobox sejam traduziveis.
			'i18n' => array(
				'current'        => esc_js( __( 'Image {current} of {total}', 'my-lightbox' ) ),
				'previous'       => esc_js( __( 'Previous', 'my-lightbox' ) ),
				'next'           => esc_js( __( 'Next', 'my-lightbox' ) ),
				'close'          => esc_js( __( 'Close', 'my-lightbox' ) ),
				'slideshowStart' => esc_js( __( 'Start slideshow', 'my-lightbox' ) ),
				'slideshowStop'  => esc_js( __( 'Stop slideshow', 'my-lightbox' ) ),
				'xhrError'       => esc_js( __( 'This content failed to load.', 'my-lightbox' ) ),
				'imgError'       => esc_js( __( 'This image failed to load.', 'my-lightbox' ) ),
			),
			// Opções do Colobox baseadas nas configurações do plugin.
			'options' => array(
				'transition' => esc_js( $settings['transaction_type'] ),
				'maxWidth'   => esc_js( $settings['max_width'] ) . '%',
				'maxHeight'  => esc_js( $settings['max_height'] ) . '%',
			)
		)
	);
}

add_action( 'wp_enqueue_scripts', 'mylb_include_scripts' );
