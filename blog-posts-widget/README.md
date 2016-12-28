# Nome do plugin: Blog posts widget

Exemplo de plugin usando widget, requisição a uma API REST e salvando os resultados com a API de Transients do WordPress.

Para o exemplo é utilizada a API REST do WordPress.com: <https://developer.wordpress.com/docs/api/1.1/get/sites/%24site/posts/>

Documentação de funções, ganchos e APIs:

- [`load_plugin_textdomain()`](https://developer.wordpress.org/reference/functions/load_plugin_textdomain/)
- [`plugins_loaded`](https://developer.wordpress.org/reference/hooks/plugins_loaded/)
- [`esc_html__()`](https://developer.wordpress.org/reference/functions/esc_html__/)
- [`esc_attr()`](https://developer.wordpress.org/reference/functions/esc_attr/)
- [`esc_attr_e()`](https://developer.wordpress.org/reference/functions/esc_attr_e/)
- [`sanitize_text_field()`](https://developer.wordpress.org/reference/functions/sanitize_text_field/)
- [`esc_html()`](https://developer.wordpress.org/reference/functions/esc_html/)
- [`absint()`](https://developer.wordpress.org/reference/functions/absint/)
- [`apply_filters()`](https://developer.wordpress.org/reference/functions/apply_filters/)
- [`wp_remote_get()`](https://developer.wordpress.org/reference/functions/wp_remote_get/)
- [`is_wp_error()`](https://developer.wordpress.org/reference/functions/is_wp_error/)
- [`get_transient()`](https://developer.wordpress.org/reference/functions/get_transient/)
- [`set_transient()`](https://developer.wordpress.org/reference/functions/set_transient/)
- [`delete_transient()`](https://developer.wordpress.org/reference/functions/delete_transient/)
- [`date_i18n()`](https://developer.wordpress.org/reference/functions/date_i18n/)
- [`get_option()`](https://developer.wordpress.org/reference/functions/get_option/)
- [`register_widget()`](https://developer.wordpress.org/reference/functions/register_widget/)
- [`widgets_init`](https://developer.wordpress.org/reference/hooks/widgets_init/)
