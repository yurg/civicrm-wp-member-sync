<?php
/**
 * List Roles template.
 *
 * Main template for the Association Rules page when syncing Roles.
 *
 * @package Civi_WP_Member_Sync
 * @since 0.1
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?><!-- assets/templates/list_roles.php -->
<div class="wrap">

	<h1><?php esc_html_e( 'CiviCRM Member Sync', 'civicrm-wp-member-sync' ); ?></h1>

	<h2 class="nav-tab-wrapper">
		<a href="<?php echo $urls['settings']; ?>" class="nav-tab"><?php esc_html_e( 'Settings', 'civicrm-wp-member-sync' ); ?></a>
		<a href="<?php echo $urls['list']; ?>" class="nav-tab nav-tab-active"><?php esc_html_e( 'Association Rules', 'civicrm-wp-member-sync' ); ?></a>
		<a href="<?php echo $urls['manual_sync']; ?>" class="nav-tab"><?php esc_html_e( 'Manual Synchronize', 'civicrm-wp-member-sync' ); ?></a>
	</h2>

	<h3><?php esc_html_e( 'All Association Rules', 'civicrm-wp-member-sync' ); ?><?php

	// If we don't have all our Membership Types populated.
	if ( ! $have_all_types ) {

		// Show the 'Add New' button.
		?> <a class="add-new-h2" href="<?php echo $urls['rules']; ?>"><?php esc_html_e( 'Add New', 'civicrm-wp-member-sync' ); ?></a><?php

	}

	?></h3>

	<?php

	// Get sync rule.
	$sync_rule = '';
	$sync_rule_raw = filter_input( INPUT_GET, 'syncrule' );
	if ( ! empty( $sync_rule_raw ) ) {
		$sync_rule = trim( wp_unslash( $sync_rule_raw ) );
	}

	// If we've updated, show message.
	if ( ! empty( $sync_rule ) ) {
		echo '<div id="message" class="updated"><p>';

		// Switch message based on result.
		switch ( $sync_rule ) {
			case 'edit':
				esc_html_e( 'Association Rule updated.', 'civicrm-wp-member-sync' );
				break;
			case 'add':
				esc_html_e( 'Association Rule added.', 'civicrm-wp-member-sync' );
				break;
			case 'delete':
				esc_html_e( 'Association Rule deleted.', 'civicrm-wp-member-sync' );
				break;
			case 'delete-all':
				esc_html_e( 'Association Rules deleted.', 'civicrm-wp-member-sync' );
				break;
		}

		echo '</p></div>';
	}

	// If we've updated, show message (note that this will only display if we have JS turned off).
	if ( isset( $this->errors ) && is_array( $this->errors ) ) {

		// Init messages.
		$error_messages = [];

		// Construct array of messages based on error code.
		foreach ( $this->errors as $error_code ) {
			$error_messages[] = $this->error_strings[ $error_code ];
		}

		// Show them.
		echo '<div id="message" class="error"><p>' . implode( '<br>', $error_messages ) . '</p></div>';

	}

	?>

	<table cellspacing="0" class="wp-list-table widefat fixed striped">

		<thead>
			<tr>
				<th class="manage-column column-type" id="civi_member_type_id" scope="col"><?php esc_html_e( 'CiviCRM Membership Type', 'civicrm-wp-member-sync' ); ?></th>
				<th class="manage-column column-current-code" id="current_rule" scope="col"><?php esc_html_e( 'Current Member Codes', 'civicrm-wp-member-sync' ); ?></th>
				<th class="manage-column column-current-wp-role" id="wp_mem_role" scope="col"><?php esc_html_e( 'Current WP Role', 'civicrm-wp-member-sync' ); ?></th>
				<?php

				/**
				 * Allows extra columns to be added after "Current WP Role".
				 *
				 * @since 0.4
				 */
				do_action( 'civi_wp_member_sync_list_roles_th_after_current' );

				?>
				<th class="manage-column column-expired-code" id="expiry_rule" scope="col"><?php esc_html_e( 'Expired Member Codes', 'civicrm-wp-member-sync' ); ?></th>
				<th class="manage-column column-expired-wp-role" id="expired_wp_role" scope="col"><?php esc_html_e( 'Expiry WP Role', 'civicrm-wp-member-sync' ); ?></th>
				<?php

				/**
				 * Allows extra columns to be added after "Expiry WP Role".
				 *
				 * @since 0.4
				 */
				do_action( 'civi_wp_member_sync_list_roles_th_after_expiry' );

				?>
				<?php

				/**
				 * Allows extra columns to be added.
				 *
				 * @since 0.3.9
				 */
				do_action( 'civi_wp_member_sync_list_roles_th' );

				?>
			</tr>
		</thead>

		<tbody class="civi_wp_member_sync_table" id="the-comment-list">
			<?php

			// Loop through our data array, keyed by type ID.
			foreach ( $data as $key => $item ) {

				// Construct URLs for this item.
				$edit_url = $urls['rules'] . '&mode=edit&type_id=' . $key;
				$delete_url = wp_nonce_url(
					$urls['list'] . '&syncrule=delete&type_id=' . $key,
					'civi_wp_member_sync_delete_link',
					'civi_wp_member_sync_delete_nonce'
				);

				?>
				<tr>
					<td class="comment column-comment has-row-actions column-primary">
						<strong>
							<a href="<?php echo $edit_url; ?>"><?php echo $this->plugin->members->membership_name_get_by_id( $key ); ?></a>
						</strong>
						<div class="row-actions">
							<span class="edit"><a href="<?php echo $edit_url; ?>"><?php esc_html_e( 'Edit', 'civicrm-wp-member-sync' ); ?></a> | </span>
							<span class="delete"><a href="<?php echo $delete_url; ?>" class="submitdelete"><?php esc_html_e( 'Delete', 'civicrm-wp-member-sync' ); ?></a></span>
						</div>
					</td>
					<td><?php echo $this->plugin->members->status_rules_get_current( $item['current_rule'] ); ?></td>
					<td><?php echo esc_html( $this->plugin->users->wp_role_name_get( $item['current_wp_role'] ) ); ?></td>
					<?php

					/**
					 * Allows extra columns to be added after "Current WP Role".
					 *
					 * @since 0.4
					 *
					 * @param int $key The current key (type ID).
					 * @param array $item The current item.
					 */
					do_action( 'civi_wp_member_sync_list_roles_td_after_current', $key, $item );

					?>
					<td><?php echo $this->plugin->members->status_rules_get_current( $item['expiry_rule'] ); ?></td>
					<td><?php echo esc_html( $this->plugin->users->wp_role_name_get( $item['expired_wp_role'] ) ); ?></td>
					<?php

					/**
					 * Allows extra columns to be added after "Expired WP Role".
					 *
					 * @since 0.4
					 *
					 * @param int $key The current key (type ID).
					 * @param array $item The current item.
					 */
					do_action( 'civi_wp_member_sync_list_roles_td_after_expiry', $key, $item );

					?>
					<?php

					/**
					 * Allows extra columns to be added.
					 *
					 * @since 0.3.9
					 *
					 * @param int $key The current key (type ID).
					 * @param array $item The current item.
					 */
					do_action( 'civi_wp_member_sync_list_roles_td', $key, $item );

					?>
				</tr>
				<?php

			}

			?>
		</tbody>

	</table>

	<?php if ( ! empty( $data ) ) : ?>

		<form method="post" id="civi_wp_member_sync_rules_form" action="<?php echo $this->admin_form_url_get(); ?>">

			<?php wp_nonce_field( 'civi_wp_member_sync_rule_action', 'civi_wp_member_sync_nonce' ); ?>

			<p class="submit">
				<input class="button button-secondary delete" type="submit" id="civi_wp_member_sync_clear_submit" name="civi_wp_member_sync_clear_submit" value="<?php esc_attr_e( 'Clear Association Rules', 'civicrm-wp-member-sync' ); ?>" /><br />
				<span class="description"><?php esc_html_e( 'Warning: this will delete all your existing Association Rules.', 'civicrm-wp-member-sync' ); ?></span>
			</p>

		</form>

	<?php endif; ?>

</div><!-- /.wrap -->
