<?php
/**
 * Library.
 *
 * @package   Library_Admin
 * @author    Patrick Daly <patrick@developdaly.com>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/library
 * @copyright 2014 Patrick Daly
 */

/**
 * Library_Admin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-library.php`
 *
 * @package Library_Admin
 * @author  Patrick Daly <patrick@developdaly.com>
 */
class Library_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		$plugin = Library::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		add_action( 'init', array( $this, 'register' ) );

		/* Fire our meta box setup function on the post editor screen. */
		add_action( 'load-post.php',		array( $this, 'library_meta_boxes_setup' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function register() {

		$labels = array(
			'name' => _x( 'Terms', 'shortcode terms general name', 'library' ),
			'singular_name' => _x( 'Term', 'shortcode term singular name', 'library' ),
			'add_new' => _x( 'Add New', 'shortcode term', 'library' ),
			'add_new_item' => __( 'Add New Term', 'library' ),
			'edit_item' => __( 'Edit Term', 'library' ),
			'new_item' => __( 'New Term', 'library' ),
			'view_item' => __( 'View Term', 'library' ),
			'search_items' => __( 'Search Terms', 'library' ),
			'not_found' => __( 'No terms found', 'library' ),
			'not_found_in_trash' => __( 'No terms found in Trash', 'library' ),
			'parent_item_colon' => __( 'Parent Term:', 'library' ),
			'menu_name' => _x( 'Library', 'shortcode term collection', 'library' ),
		);

		$args = array(
			'labels' => $labels,
			'supports' => array( 'title', 'editor', 'revisions' ),
			'public' => false, // hide the front end UI
			'show_ui' => true, // keep admin UI
			'show_in_nav_menus' => true,
			'menu_position' => 5,
		);

		register_post_type( 'library_term', $args );
	}

	/**
	 * Meta box setup.
	 *
	 * @since     1.0.0
	 */
	public function library_meta_boxes_setup() {

		/* Add meta boxes on the 'add_meta_boxes' hook. */
		add_action( 'add_meta_boxes', array( $this, 'library_add_post_meta_boxes' ) );
	}

	/**
	 * Creates the meta box.
	 *
	 * @since     1.0.0
	 */
	public function library_add_post_meta_boxes() {

		add_meta_box(
			'library',
			esc_html__( 'How to Use this Term', 'library' ),
			array( $this, 'library_class_meta_box' ),
			'library_term',
			'normal',
			'high'
		);
	}

	/**
	 * Display the meta box.
	 *
	 * @since     1.0.0
	 */
	public function library_class_meta_box( $object, $box ) {
		global $post;

		$shortcode_example = '<code>[library term="' . $post->post_name . '"]</code>';
		$php_example = '<code>&lt;?php echo do_shortcode( \'[library term="' . $post->post_name . '"]\' ) ?&gt;</code>';
		echo '<p>';
		printf( __( 'To display this inside your content use %s OR to use this inside of a template file use %s', 'library' ), $shortcode_example, $php_example );
		echo '</p><p>';
		_e( 'You can change the term slug by editing the permalink slug underneath the title.', 'library' );
		echo '</p>';

	}

}
