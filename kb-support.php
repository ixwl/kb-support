<?php
/**
 * Plugin Name: KB Support
 * Plugin URI: https://kb-support.com/
 * Description: The ultimate help desk and knowledge base support tool plugin for WordPress.
 * Version: 1.1.1
 * Date: 11 September 2017
 * Author: KB Support Team
 * Author URI: https://kb-support.com/
 * Text Domain: kb-support
 * Domain Path: /languages
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * GitHub Plugin URI: https://github.com/KB-Support/kb-support
 * Tags:  Helpdesk, Help Desk, Support, Customer Support, Service, Service Desk, ITIL, Support Helpdesk, Ticket, Ticket System, Support Tickets, Helpdesk Tickets, Knowledgebase, Knowledge Base, Service Level, SLA
 *
 *
 * KB Support is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 * 
 * KB Support is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with KB Support; if not, see https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package		KBS
 * @category	Core
 * @author		Mike Howard
 * @version		1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) )
	exit;

if ( ! class_exists( 'KB_Support' ) ) :
/**
 * Main KB_Support Class.
 *
 * @since 1.0
 */
final class KB_Support {
	/** Singleton *************************************************************/

	/**
	 * @var		KB_Support The one true KB_Support
	 * @since	1.0
	 */
	private static $instance;

	/**
	 * KBS Roles Object.
	 *
	 * @var		obj		KBS_Roles
	 * @since	1.0
	 */
	public $roles;

	/**
	 * KBS Emails.
	 *
	 * @var		obj		KBS_Emails
	 * @since	1.0
	 */
	public $emails;

	/**
	 * KBS Email Tags.
	 *
	 * @var		obj		KBS_Email_Template_Tags
	 * @since	1.0
	 */
	public $email_tags;

	/**
	 * KBS HTML Elements.
	 *
	 * @var		obj		KBS_HTML_Elements
	 * @since	1.0
	 */
	public $html;

	/**
	 * KBS Customers.
	 *
	 * @var		obj		KBS_DB_Customers
	 * @since	1.0
	 */
	public $customers;

	/**
	 * KBS Customer Meta.
	 *
	 * @var		obj		KBS_DB_Customer_Meta
	 * @since	1.0
	 */
	public $customer_meta;

	/**
	 * KBS Knowledgebase.
	 *
	 * @var		obj		KBS_Knowledgebase
	 * @since	1.0
	 */
	public $KB;

	/**
	 * Main KB_Support Instance.
	 *
	 * Insures that only one instance of KB_Support exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since	1.0
	 * @static
	 * @static	var		arr		$instance
	 * @uses	KB_Support::setup_constants()	Setup the constants needed.
	 * @uses	KB_Support::includes()			Include the required files.
	 * @uses	KB_Support::load_textdomain()	Load the language files.
	 * @see KBS()
	 * @return	obj	KB_Support	The one true KB_Support
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof KB_Support ) )	{
			do_action( 'before_kbsupport_init' );

			self::$instance = new KB_Support;
			self::$instance->setup_constants();

			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

			self::$instance->includes();
			self::$instance->roles         = new KBS_Roles();
			self::$instance->emails        = new KBS_Emails();
			self::$instance->email_tags    = new KBS_Email_Template_Tags();
			self::$instance->html          = new KBS_HTML_Elements();
			self::$instance->customers     = new KBS_DB_Customers();
			self::$instance->customer_meta = new KBS_DB_Customer_Meta();
			self::$instance->KB            = new KBS_Knowledgebase();

			do_action( 'kbsupport_init' );
		}

		return self::$instance;

	}
	
	/**
	 * Throw error on object clone.
	 *
	 * The whole idea of the singleton design pattern is that there is a single
	 * object therefore, we don't want the object to be cloned.
	 *
	 * @since	1.0
	 * @access	protected
	 * @return	void
	 */
	public function __clone() {
		// Cloning instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'kb-support' ), '1.0' );
	} // __clone

	/**
	 * Disable unserializing of the class.
	 *
	 * @since	1.0
	 * @access	protected
	 * @return	void
	 */
	public function __wakeup() {
		// Unserializing instances of the class is forbidden.
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'kb-support' ), '1.0' );
	} // __wakeup
	
	/**
	 * Setup plugin constants.
	 *
	 * @access	private
	 * @since	1.0
	 * @return	void
	 */
	private function setup_constants()	{

		if ( ! defined( 'KBS_VERSION' ) )	{
			define( 'KBS_VERSION', '1.1.1' );
		}

		if ( ! defined( 'KBS_PLUGIN_DIR' ) )	{
			define( 'KBS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		if ( ! defined( 'KBS_PLUGIN_URL' ) )	{
			define( 'KBS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}
		
		if ( ! defined( 'KBS_PLUGIN_FILE' ) )	{
			define( 'KBS_PLUGIN_FILE', __FILE__ );
		}

	} // setup_constants
			
	/**
	 * Include required files.
	 *
	 * @access	private
	 * @since	1.0
	 * @return	void
	 */
	private function includes()	{

		global $kbs_options;

		require_once KBS_PLUGIN_DIR . 'includes/admin/settings/register-settings.php';
		$kbs_options = kbs_get_settings();

		if ( file_exists( KBS_PLUGIN_DIR . 'includes/deprecated-functions.php' ) )	{
			require_once KBS_PLUGIN_DIR . 'includes/deprecated-functions.php';
		}

		require_once KBS_PLUGIN_DIR . 'includes/ajax-functions.php';
		require_once KBS_PLUGIN_DIR . 'includes/template-functions.php';
		require_once KBS_PLUGIN_DIR . 'includes/class-kbs-cache-helper.php';
		require_once KBS_PLUGIN_DIR . 'includes/post-types.php';
		require_once KBS_PLUGIN_DIR . 'includes/class-kbs-db.php';
		require_once KBS_PLUGIN_DIR . 'includes/class-kbs-stats.php';
		require_once KBS_PLUGIN_DIR . 'includes/class-kbs-roles.php';
		require_once KBS_PLUGIN_DIR . 'includes/class-kbs-cron.php';
		require_once KBS_PLUGIN_DIR . 'includes/class-kbs-logging.php';
		require_once KBS_PLUGIN_DIR . 'includes/class-kbs-license-handler.php';
		require_once KBS_PLUGIN_DIR . 'includes/class-kbs-knowledgebase.php';
		require_once KBS_PLUGIN_DIR . 'includes/article/article-actions.php';
		require_once KBS_PLUGIN_DIR . 'includes/article/class-kbs-article-stats.php';
		require_once KBS_PLUGIN_DIR . 'includes/article/class-kbs-articles-query.php';
		require_once KBS_PLUGIN_DIR . 'includes/article/article-functions.php';
		require_once KBS_PLUGIN_DIR . 'includes/article/article-restricted.php';
		require_once KBS_PLUGIN_DIR . 'includes/article/article-content.php';
		require_once KBS_PLUGIN_DIR . 'includes/article/article-search.php';
		require_once KBS_PLUGIN_DIR . 'includes/tickets/class-kbs-ticket-stats.php';
		require_once KBS_PLUGIN_DIR . 'includes/tickets/class-kbs-tickets-query.php';
		require_once KBS_PLUGIN_DIR . 'includes/tickets/class-kbs-ticket.php';
		require_once KBS_PLUGIN_DIR . 'includes/tickets/ticket-actions.php';
		require_once KBS_PLUGIN_DIR . 'includes/tickets/ticket-functions.php';
		require_once KBS_PLUGIN_DIR . 'includes/files.php';
		require_once KBS_PLUGIN_DIR . 'includes/formatting.php';
		require_once KBS_PLUGIN_DIR . 'includes/scripts.php';
		require_once KBS_PLUGIN_DIR . 'includes/emails/email-actions.php';
		require_once KBS_PLUGIN_DIR . 'includes/emails/class-kbs-emails.php';
		require_once KBS_PLUGIN_DIR . 'includes/emails/class-kbs-email-tags.php';
		require_once KBS_PLUGIN_DIR . 'includes/class-kbs-html-elements.php';
		require_once KBS_PLUGIN_DIR . 'includes/emails/email-functions.php';
		require_once KBS_PLUGIN_DIR . 'includes/emails/email-template.php';
		require_once KBS_PLUGIN_DIR . 'includes/class-kbs-form.php';
		require_once KBS_PLUGIN_DIR . 'includes/form-functions.php';
		require_once KBS_PLUGIN_DIR . 'includes/misc-functions.php';
		require_once KBS_PLUGIN_DIR . 'includes/login-register.php';
		require_once KBS_PLUGIN_DIR . 'includes/class-kbs-customer.php';
		require_once KBS_PLUGIN_DIR . 'includes/companies/class-kbs-company.php';
		require_once KBS_PLUGIN_DIR . 'includes/companies/class-kbs-companies-query.php';
		require_once KBS_PLUGIN_DIR . 'includes/companies/company-functions.php';
		require_once KBS_PLUGIN_DIR . 'includes/user-functions.php';
		require_once KBS_PLUGIN_DIR . 'includes/agent-functions.php';
		require_once KBS_PLUGIN_DIR . 'includes/class-kbs-db-customers.php';
		require_once KBS_PLUGIN_DIR . 'includes/class-kbs-db-customer-meta.php';
		require_once KBS_PLUGIN_DIR . 'includes/shortcodes.php';
		require_once KBS_PLUGIN_DIR . 'includes/sla.php';
		require_once KBS_PLUGIN_DIR . 'includes/widgets.php';

		if ( is_admin() )	{
			require_once KBS_PLUGIN_DIR . 'includes/admin/admin-pages.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/admin-notices.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/admin-plugin.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/dashboard-widgets.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/customers/customers-page.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/customers/customer-functions.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/customers/customer-actions.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/customers/contextual-help.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/tickets/tickets.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/tickets/metaboxes.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/tickets/contextual-help.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/article/article.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/article/metaboxes.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/article/contextual-help.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/companies/company.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/companies/metaboxes.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/companies/contextual-help.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/forms/forms.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/forms/metaboxes.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/forms/form-actions.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/forms/contextual-help.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/settings/display-settings.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/settings/contextual-help.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/thickbox.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/tools.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/import-export/import/import-functions.php';
            require_once KBS_PLUGIN_DIR . 'includes/admin/import-export/export/export-functions.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/extensions.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/upgrades/upgrade-functions.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/upgrades/upgrades.php';
			require_once KBS_PLUGIN_DIR . 'includes/admin/welcome.php';
		}

		require_once KBS_PLUGIN_DIR . 'includes/install.php';
		
	} // includes
	
	/**
	 * Load the text domain for translations.
	 *
	 * @access	private
	 * @since	1.0
	 * @return	void
	 */
	public function load_textdomain()	{

		load_plugin_textdomain( 
			'kb-support',
			false, 
			dirname( plugin_basename(__FILE__) ) . '/languages'
		);

	} // load_textdomain
	
} // class KB_Support
endif;

/**
 * The main function for that returns KB_Support
 *
 * The main function responsible for returning the one true KB_Support
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $kbs = KBS(); ?>
 *
 * @since	1.0
 * @return	obj		KB_Support	The one true KB_Support Instance.
 */
function KBS()	{
	return KB_Support::instance();
} // KBS

// Get KBS Running
KBS();
