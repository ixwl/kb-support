<?php
/**
 * Weclome Page Class
 *
 * @package     KBS
 * @subpackage  Admin/Welcome
 * @copyright   Copyright (c) 2016, Mike Howard
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * KBS_Welcome Class
 *
 * A general class for About and Credits page.
 *
 * @since	1.0
 */
class KBS_Welcome {

	/**
	 * @var	str		The capability users should have to view the page
	 */
	public $minimum_capability = 'manage_ticket_settings';

	/**
	 * @var	str		Singular label for tickets
	 */
	public $ticket_singular = 'Ticket';

	/**
	 * @var	str		Plural label for tickets
	 */
	public $ticket_plural = 'Tickets';

	/**
	 * @var	str		Singular label for KB Articles
	 */
	public $article_singular = 'KB Article';

	/**
	 * @var	str		Plural label for KB Articles
	 */
	public $article_plural = 'KB Articles';

	/**
	 * Get things started
	 *
	 * @since	1.0
	 */
	public function __construct()	{
		$this->ticket_singular  = kbs_get_ticket_label_singular();
		$this->ticket_plural    = kbs_get_ticket_label_plural();
		$this->article_singular = kbs_get_article_label_singular();
		$this->article_plural   = kbs_get_article_label_plural();

		add_action( 'admin_menu', array( $this, 'admin_menus') );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'welcome'    ) );
	} // __construct

	/**
	 * Register the Dashboard Pages which are later hidden but these pages
	 * are used to render the Welcome and Credits pages.
	 *
	 * @access	public
	 * @since	1.0
	 * @return	void
	 */
	public function admin_menus() {
		// About Page
		add_dashboard_page(
			__( 'Welcome to KB Support', 'kb-support' ),
			__( 'Welcome to KB Support', 'kb-support' ),
			$this->minimum_capability,
			'kbs-about',
			array( $this, 'about_screen' )
		);

		// Changelog Page
		add_dashboard_page(
			__( 'KB Support Changelog', 'kb-support' ),
			__( 'KB Support Changelog', 'kb-support' ),
			$this->minimum_capability,
			'kbs-changelog',
			array( $this, 'changelog_screen' )
		);

		// Getting Started Page
		add_dashboard_page(
			__( 'Getting started with KB Support', 'kb-support' ),
			__( 'Getting started with KB Support', 'kb-support' ),
			$this->minimum_capability,
			'kbs-getting-started',
			array( $this, 'getting_started_screen' )
		);

		// Now remove them from the menus so plugins that allow customizing the admin menu don't show them
		remove_submenu_page( 'index.php', 'kbs-about' );
		remove_submenu_page( 'index.php', 'kbs-changelog' );
		remove_submenu_page( 'index.php', 'kbs-getting-started' );

	} // admin_menus

	/**
	 * Hide Individual Dashboard Pages
	 *
	 * @access	public
	 * @since	1.0
	 * @return	void
	 */
	public function admin_head() {
		?>
		<style type="text/css" media="screen">
			/*<![CDATA[*/
			.kbs-about-wrap .kbs-badge { float: right; border-radius: 4px; margin: 0 0 15px 15px; max-width: 200px; }
			.kbs-about-wrap #kbs-header { margin-bottom: 15px; }
			.kbs-about-wrap #kbs-header h1 { margin-bottom: 15px !important; }
			.kbs-about-wrap .about-text { margin: 0 0 15px; max-width: 670px; }
			.kbs-about-wrap .feature-section { margin-top: 5px; }
			.kbs-about-wrap .feature-section-content,
			.kbs-about-wrap .feature-section-media { width: 50%; box-sizing: border-box; }
			.kbs-about-wrap .feature-section-content { float: left; padding-right: 50px; }
			.kbs-about-wrap .feature-section-content h4 { margin: 0 0 1em; }
			.kbs-about-wrap .feature-section-media { float: right; text-align: right; margin-bottom: 5px; }
			.kbs-about-wrap .feature-section-media img { border: 1px solid #ddd; }
			.kbs-about-wrap .feature-section:not(.under-the-hood) .col { margin-top: 0; }
			.kbs-about-wrap ul { list-style-type: disc; padding-left: 20px; }
			/* responsive */
			@media all and ( max-width: 782px ) {
				.kbs-about-wrap .feature-section-content,
				.kbs-about-wrap .feature-section-media { float: none; padding-right: 0; width: 100%; text-align: left; }
				.kbs-about-wrap .feature-section-media img { float: none; margin: 0 0 20px; }
			}
			/*]]>*/
		</style>
		<?php
	} // admin_head

	/**
	 * Welcome message
	 *
	 * @access	public
	 * @since	1.0
	 * @return	void
	 */
	public function welcome_message() {
		list( $display_version ) = explode( '-', KBS_VERSION );

		$page = isset( $_GET['page'] ) ? $_GET['page'] : 'kbs-about';

		?>
		<div id="kbs-header">
			<img class="kbs-badge" src="<?php echo KBS_PLUGIN_URL . 'assets/images/mdjm_web_header.png'; ?>" alt="<?php _e( 'MDJM Event Management', 'kb-support' ); ?>" / >
			<h1><?php printf( __( 'Welcome to KB Support %s', 'kb-support' ), $display_version ); ?></h1>
			<p class="about-text">
				<?php
				switch ( $page )	{
					case 'kbs-getting-started':
						_e( "Let's get Started!", 'kb-support' );
						break;

					default:
						_e( 'Thank you for updating to the latest version!', 'kb-support' );
						echo '<br />';
						printf(
							__( 'KB Support %s is ready to make improve your support business efficiency!', 'kb-support' ),
							$display_version
						);
				}
				?>
			</p>
		</div>
		<?php
	} // welcome_message

	/**
	 * Navigation tabs
	 *
	 * @access	public
	 * @since	1.0
	 * @return	void
	 */
	public function tabs() {
		$selected = isset( $_GET['page'] ) ? $_GET['page'] : 'kbs-about';
		?>
		<h1 class="nav-tab-wrapper">
			<a class="nav-tab <?php echo $selected == 'kbs-about' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'kbs-about' ), 'index.php' ) ) ); ?>">
				<?php _e( "What's New", 'kb-support' ); ?>
			</a>
			<a class="nav-tab <?php echo $selected == 'kbs-getting-started' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'kbs-getting-started' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Getting Started', 'kb-support' ); ?>
			</a>
		</h1>
		<?php
	} // tabs

	/**
	 * Render About Screen
	 *
	 * @access	public
	 * @since	1.0
	 * @return	void
	 */
	public function about_screen() {
		?>
		<div class="wrap about-wrap kbs-about-wrap">
			<?php
				// Load welcome message and content tabs
				$this->welcome_message();
				$this->tabs();
			?>

			<div class="changelog">
				<h3></h3>
				<div class="feature-section">
					<div class="feature-section-media">
						
					</div>
					<div class="feature-section-content">
						     
					</div>
				</div>
			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'kbs-settings' ), 'admin.php' ) ) ); ?>"><?php _e( 'Go to KB Support Settings', 'kb-support' ); ?></a> &middot;
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'kbs-changelog' ), 'index.php' ) ) ); ?>"><?php _e( 'View the Full Changelog', 'kb-support' ); ?></a>
			</div>
		</div>
		<?php
	} // about_screen

	/**
	 * Render Changelog Screen
	 *
	 * @access	public
	 * @since	1.0
	 * @return	void
	 */
	public function changelog_screen() {
		?>
		<div class="wrap about-wrap kbs-about-wrap">
			<?php
				// load welcome message and content tabs
				$this->welcome_message();
				$this->tabs();
			?>
			<div class="changelog">
				<h3><?php _e( 'Full Changelog', 'kb-support' );?></h3>

				<div class="feature-section">
					<?php echo $this->parse_readme(); ?>
				</div>
			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'post_type' => 'kbs_ticket', 'page' => 'kbs-settings' ), 'edit.php' ) ) ); ?>"><?php _e( 'Go to KB Support Settings', 'kb-support' ); ?></a>
			</div>
		</div>
		<?php
	} // changelog_screen

	/**
	 * Render Getting Started Screen
	 *
	 * @access	public
	 * @since	1.0
	 * @return	void
	 */
	public function getting_started_screen()	{
		?>
		<div class="wrap about-wrap kbs-about-wrap">
			<?php
				// Load welcome message and content tabs
				$this->welcome_message();
				$this->tabs();
			?>
			<p class="about-description"><?php _e( "Now that KB Support is installed, you're ready to get started. It works out of the box, but its also fully customisable.", 'kb-support' ); ?></p>

			<div class="changelog">
				<h3><?php _e( 'Configure KB Support Settings', 'kb-support' ); ?></h3>
				<div class="feature-section">
					<div class="feature-section-media">
						<img src="<?php echo KBS_PLUGIN_URL . 'assets/images/screenshots/getting-started-email.png'; ?>" class="kbs-welcome-screenshots" />
					</div>
					<div class="feature-section-content">
						<h4><a href="<?php echo admin_url( 'edit.php?post_type=kbs_ticket&page-kbs-settings' ); ?>"><?php printf( __( '%s &rarr; Settings', 'kb-support' ), $this->ticket_plural ); ?></a></h4>
						<p><?php _e( "We've set the default options for you, however slight adjustments may ensure that KB Support is fully optimal for you and your customers.", 'kb-support' ); ?></p>

						<h4><?php _e( 'Review Email Templates', 'kb-support' ); ?></h4>
						<p><?php printf( __( 'Take a few moments to review the email templates as this content will be sent to your customers during the life cycle of their support %s.', 'kb-support' ), strtolower( $this->ticket_plural ) ); ?></p>

					</div>
				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Customise your Submission Form(s)', 'kb-support' ); ?></h3>
				<div class="feature-section">
					<div class="feature-section-media">
						<img src="<?php echo KBS_PLUGIN_URL . 'assets/images/screenshots/getting-started-form.png'; ?>" class="kbs-welcome-screenshots" />
					</div>
					<div class="feature-section-content">
						<h4><a href="<?php echo admin_url( 'edit.php?post_type=kbs_form' ); ?>"><?php printf( __( '%s &rarr; Submission Forms', 'kb-support' ), $this->ticket_plural ); ?></a></h4>
						<p><?php printf( __( 'The %s submission forms are the first point at which your customers can provide you with details regarding the issues they are experiencing.', 'kb-support' ), strtolower( $this->ticket_singular ) ); ?></p>
						<p><?php _e( 'We created a default form for you during install, but it is fully customisable and you should ensure it has all the fields you need.', 'kb-support' ); ?></p>
					</div>
				</div>
			</div>

			<div class="changelog">
				<h3><?php printf( __( 'Start Creating %s', 'kb-support' ), $this->article_plural ); ?></h3>
				<div class="feature-section">
					<div class="feature-section-media">
						<img src="<?php echo KBS_PLUGIN_URL . 'assets/images/screenshots/getting-started-articles.png'; ?>" class="kbs-welcome-screenshots"/>
					</div>
					<div class="feature-section-content">
						<h4><a href="<?php echo admin_url( 'post-new.php?post_type=article' ); ?>"><?php printf( __( '%1$s &rarr; New %1$s', 'kb-support' ), $this->article_plural ); ?></a></h4>
						<p><?php printf( __( '%s provide not only a document repository for your products and/or services, but also a way for you to offer solutions to your customers issues during %s creation. ', 'kb-support' ), $this->article_plural, strtolower( $this->ticket_singular ) ); ?></p>
						<p><?php printf( __( 'If you offer premium products and/or services, you can restrict access to some %s as needed.', 'kb-support' ), $this->article_plural ); ?></p>
					</div>
				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Need Help?', 'kb-support' );?></h3>
				<div class="feature-section two-col">
                	<div class="col">
						<h4><?php _e( 'Documentation', 'kb-support' );?></h4>
						<p><?php _e( 'We have a searchable library of <a href="https://kb-support.com/support/" target="_blank">Support Documents</a> to help new and advanced users with features and customisations.', 'kb-support' ); ?></p>
					</div>
					<div class="col">
						<h4><?php _e( 'Excellent Support','kb-support' );?></h4>
						<p><?php printf( __( 'We pride ourselves on our level of support and excellent response times. If you are experiencing an issue, <a href="%s" target="_blank">submit a support ticket</a> and we will respond quickly.', 'kb-support' ), 'https://kb-support.com/support-request/' );?></p>
					</div>
				</div>
			</div>

			<div class="changelog">
				<div class="feature-section two-col">
					<div class="col">
						<h3><?php _e( 'Get the Latest News','kb-support' );?></h4>
						<p><?php printf( __( '<a href="%s" target="_blank">Subscribe to our Newsletter</a> for all the latest news from KB Support.', 'kb-support' ), 'http://eepurl.com/cnxWcz' ); ?></p>
					</div>
					<div class="col">
						<h4><?php _e( 'Socialise with us', 'kb-support' );?></h4>
						<p><?php printf( __( 'The <a href="%s" target="_blank">KB Support Facebook Page</a> and our <a href="%s" target="_blank">Twitter Account</a> are also great places for the latest news.', 'kb-support' ), 'https://www.facebook.com/kbsupport/', 'https://twitter.com/kbsupport_wp' ); ?></p>
					</div>
				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Extensions', 'kb-support' );?></h3>
                    <p><?php printf( __( 'We have an ever growing catalogue of extensions available at our <a href="%s" target="_blank">plugin store</a> that will extend the functionality of KB Support and further enhance your customers support experience.', 'kb-support' ), 'https://kb-support.com/downloads/' ); ?></p>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Contribute to KB Support', 'kb-support' );?></h3>
                <p><?php _e( 'Anyone is welcome to contribute to KB Support. Please read the <a href="" target="_blank">guidelines for contributing</a> to our <a href="" target="_blank">GitHub repository</a>.', 'kb-support' ); ?></p>
                <p><?php _e( 'There are various ways you can contribute', 'kb-support' ); ?>&hellip;</p>
                <ul>
                    <li><?php printf( __( '<a href="%s" target="_blank">Raise an Issue on GitHub</a>', 'kb-support' ), 'https://github.com/KB-Support/kb-support/issues' ); ?></li>
                    <li><?php printf( __( '<a href="%s" target="_blank">Send us a Pull Request</a> with your bug fixes and/or new features', 'kb-support' ), 'https://www.google.co.uk/url?sa=t&rct=j&q=&esrc=s&source=web&cd=2&cad=rja&uact=8&ved=0ahUKEwikn8uql5fQAhXiDsAKHcP6AIQQFgggMAE&url=https%3A%2F%2Fhelp.github.com%2Farticles%2Fcreating-a-pull-request%2F&usg=AFQjCNEyxULKOpCMlFly-Rcy8_YemfrOhQ&sig2=OSYkosRNJKTjCkbKTS8Qdg&bvm=bv.137904068,d.bGg' ); ?></li>
                    <li><?php printf( __( '<a href="%s" target="_blank">Translate KB Support</a> into different languages', 'kb-support' ), 'https://kb-support.com/articles/translating-kb-support/' ); ?></li>
                    <li><?php _e( 'Provide feedback and suggestions on enhancements', 'kb-support' ); ?></li>
                    <li><?php _e( 'Assist with maintaining documentation', 'kb-support' ); ?></li>
                </ul>
			</div>

		</div>
		<?php
	} // getting_started_screen

	/**
	 * Parse the MDJM readme.txt file
	 *
	 * @since	1.0
	 * @return	str		$readme		HTML formatted readme file
	 */
	public function parse_readme() {
		$file = file_exists( KBS_PLUGIN_DIR . 'readme.txt' ) ? KBS_PLUGIN_DIR . 'readme.txt' : null;

		if ( ! $file ) {
			$readme = '<p>' . __( 'No valid changelog was found.', 'kb-support' ) . '</p>';
		} else {
			$readme = file_get_contents( $file );
			$readme = nl2br( esc_html( $readme ) );
			$readme = explode( '== Changelog ==', $readme );
			$readme = end( $readme );

			$readme = preg_replace( '/`(.*?)`/', '<code>\\1</code>', $readme );
			$readme = preg_replace( '/[\040]\*\*(.*?)\*\*/', ' <strong>\\1</strong>', $readme );
			$readme = preg_replace( '/[\040]\*(.*?)\*/', ' <em>\\1</em>', $readme );
			$readme = preg_replace( '/= (.*?) =/', '<h4>\\1</h4>', $readme );
			$readme = preg_replace( '/\[(.*?)\]\((.*?)\)/', '<a href="\\2">\\1</a>', $readme );
		}

		return $readme;
	} // parse_readme

	/**
	 * Sends user to the Welcome page on first activation of KBS as well as each
	 * time KBS is upgraded to a new version
	 *
	 * @access	public
	 * @since	1.0
	 * @return	void
	 */
	public function welcome() {
		// Bail if no activation redirect
		if ( ! get_transient( '_kbs_activation_redirect' ) )	{
			return;
		}

		// Delete the redirect transient
		delete_transient( '_kbs_activation_redirect' );

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) )	{
			return;
		}

		$upgrade = get_option( 'kbs_version_upgraded_from' );

		if ( ! $upgrade ) { // First time install
			wp_safe_redirect( admin_url( 'index.php?page=kbs-getting-started' ) ); exit;
		} else { // Update
			wp_safe_redirect( admin_url( 'index.php?page=kbs-about' ) ); exit;
		}
	} // welcome
}
new KBS_Welcome();