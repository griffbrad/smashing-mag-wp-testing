<?php
/**
 * This is just a simple HTML template for our dashboard panel.  When it comes
 * to HTML output, there are a couple different concerns you might want to
 * address with your testing:
 *
 * 1) Are we properly escaping input to prevent cross-site scripting?
 * 2) Using a DOM library like Zend\Dom\Query from Zend Framework 2, you could
 *    run CSS queries against your HTML output to make sure it is structured
 *    correctly.
 */
?>

<table cellspacing="0" class="wp-list-table widefat fixed posts">
	<thead>
		<tr>
			<th>User</th>
			<th>Last Activity</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $users as $user ) :?>
		<tr>
			<td><?php echo esc_html( $user['user_nicename'] );?></td>
			<td><?php echo esc_html( $this->format_time( $user['last_activity'] ) );?></td>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>
