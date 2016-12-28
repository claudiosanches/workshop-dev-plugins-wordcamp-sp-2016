<?php
/**
 * Plugin Name: Blog Posts Widget
 */

/**
 * Adiciona suporte para tradução.
 */
function bpw_load_plugin_textdomain() {
    load_plugin_textdomain( 'blog-post-widgets', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'bpw_load_plugin_textdomain' );

/**
 * Adds Blog_Posts_Widget widget.
 */
class Blog_Posts_Widget extends WP_Widget {

	/**
	 * Inicia o widget.
	 */
	function __construct() {
		parent::__construct(
			'blog_posts_widget',
			esc_html__( 'Blog Posts', 'text_domain' ),
			array( 'description' => esc_html__( 'Display some posts from a blog on WordPress.com', 'blog-post-widgets' ), )
		);
	}

	/**
	 * Formulario do widget.
	 * Aparece apenas na página de administração de widgets.
	 *
	 * @param array $instance Instancia com os campos do formulário.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'blog-post-widgets' );
		$qty = ! empty( $instance['qty'] ) ? $instance['qty'] : 1;
		$blog_url = ! empty( $instance['blog_url'] ) ? $instance['blog_url'] : 'https://claudiosmweb.com';
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'blog-post-widgets' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'blog_url' ) ); ?>"><?php esc_attr_e( 'WordPress.com Blog URL:', 'blog-post-widgets' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'blog_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'blog_url' ) ); ?>" type="text" value="<?php echo esc_url( $blog_url ); ?>">
		</p>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'qty' ) ); ?>"><?php esc_attr_e( 'Qty:', 'blog-post-widgets' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'qty' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'qty' ) ); ?>" type="text" value="<?php echo esc_attr( $qty ); ?>">
		</p>
		<?php
	}

	/**
	 * Atualiza as opções do widget quando salvo.
	 *
	 * @param  array $new_instance Instancia com os novos valores.
	 * @param  array $old_instance Instancia com os valores antigos.
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		// Atualiza os valores dos campos, já higienizando cada entrada
		// de usuário e aplicando valores padrões quando os campos não
		// são preenchidos.
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['qty'] = ( ! empty( $new_instance['qty'] ) ) ? absint( $new_instance['qty'] ) : 1;
		$instance['blog_url'] = ( ! empty( $new_instance['blog_url'] ) ) ? esc_url_raw( $new_instance['blog_url'] ) : 'https://claudiosmweb.com';

		// Deleta o transient quando o widget tem suas opções atualizadas.
		delete_transient( 'blog_posts_widget_json' );

		return $instance;
	}

	/**
	 * Exibe o widget.
	 *
	 * @param array $args     Argumentos do widget.
	 * @param array $instance Instancia com os valores das opções do widget.
	 */
	public function widget( $args, $instance ) {
		// Abre HTML do widget.
		echo $args['before_widget'];

		// Exibe o título do widget se necessário:
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		// Verifica se os dados já estão salvos no transient.
		$json = get_transient( 'blog_posts_widget_json' );

		// Caso não esteja salvo, então faça a requisição para obter novos dados.
		if ( false === $json ) {
			// Limpa URL, deixando apenas o domínio ou subdomínio.
			$blog_url = str_replace( array( 'http:', 'https:', '/' ), '', $instance['blog_url'] );

			// Gera URL para a requisição.
			$url = sprintf( 'https://public-api.wordpress.com/rest/v1.1/sites/%s/posts/?number=%d&fields=title,date,URL', $blog_url, $instance['qty'] );

			// Faz a requisição GET.
			$response = wp_remote_get( $url, array( 'timeout' => 30 ) );

			$json = array();
			// Verifica se a requisição foi bem sucedida.
			if ( ! is_wp_error( $response ) || 200 === $response['response']['code'] ) {
				$json = $response['body'];

				// Salva os dados retornados em transients.
				set_transient( 'blog_posts_widget_json', $json, HOUR_IN_SECONDS );
			}
		}

		$content = json_decode( $json, true );

		// Exibe a lista de posts, se disponível.
		if ( ! empty( $content['posts'] ) ) {
			echo '<ul>';
			foreach ( $content['posts'] as $post ) {
				echo '<li><a href="' . esc_url( $post['URL'] ) . '" rel="nofollow" target="_blank"><strong>' . esc_html( $post['title'] ) . '</strong></a> - ' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $post['date'] ) ) ) . '</li>';
			}
			echo '</ul>';
		} else {
			echo '<p>' . esc_html__( 'No post found.', 'blog-post-widgets' ) . '</p>';
		}

		// Fecha o HTML do widget.
		echo $args['after_widget'];
	}
}

/**
 * Registra o widget no WordPress.
 */
function bpw_register_widget() {
    register_widget( 'Blog_Posts_Widget' );
}
add_action( 'widgets_init', 'bpw_register_widget' );
