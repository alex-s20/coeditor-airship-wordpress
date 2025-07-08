<?php
/**
 * Main airship plugin page template
 * 
 * @package CoeditorAirship
 */

$airship_client = new \Coeditor\WordpressAirship\Classes\AirshipClient();
$scheduled_notifications = [];
$notifications_error = null;

try {
	$scheduled_notification_data = $airship_client->get_scheduled_notifications();
	$scheduled_notifications = $scheduled_notification_data['schedules'] ?? [];
} catch (Exception $e) {
	$notifications_error = $e->getMessage();
}

if (isset($_GET['confirm_delete'])) {
	try {
		$airship_client->delete_scheduled_notification($_GET['confirm_delete']);
	} catch (Exception $e) {
		echo '<div class="notice notice-error is-dismissible"><p>' . esc_html($e->getMessage()) . '</p></div>';
	}
}
?>


<div class="wrap">
	<h1><?php esc_html_e('Airship', 'coeditor-airship'); ?></h1>
</div>

<div class="coeditor-airship__scheduled-notifications">
	<h2><?php esc_html_e('Scheduled Notifications', 'coeditor-airship'); ?></h2>

	<?php if ($notifications_error): ?>
		<div class="notice notice-error is-dismissible">
			<p><?php echo esc_html('Error fetching notifications: ' . $notifications_error); ?></p>
		</div>

	<?php elseif (empty($scheduled_notifications)): ?>
		<p><?php esc_html_e('No scheduled notifications found.', 'coeditor-airship'); ?></p>

	<?php else: ?>
		<div class="wrap coeditor-airship__scheduled-notifications-list">
			<?php foreach ($scheduled_notifications as $notification): ?>
				<?php $schedule_id = basename($notification['url']); ?>
				<div class="coeditor-airship__scheduled-notification-item">
					<div class="coeditor-airship__scheduled-notification-actions">
						<a class="coeditor-airship__delete"
							href="/wp-admin/admin.php?page=airship&confirm_delete=<?php echo $schedule_id; ?>">Delete
							notification</a>
					</div>

					<!-- Web Notification Preview -->
					<div class="coeditor-airship__scheduled-notification-preview-web">
						<div class="coeditor-airship__web-icon">
							<img src="<?php echo esc_url(COEDITOR_AIRSHIP_ASSETS . '/images/web.png'); ?>" alt="Web Icon">
						</div>

						<div class="notification-content">
							<div class="notification-title">
								<?php echo esc_html($notification['push']['notification']['web']['alert']['title'] ?? 'No title'); ?>
							</div>
							<div class="notification-body">
								<?php echo esc_html($notification['push']['notification']['web']['alert']['body'] ?? 'No message'); ?>
							</div>

							<?php if (!empty($notification['push']['notification']['web']['icon'])): ?>
								<img src="<?php echo esc_url($notification['push']['notification']['web']['icon']); ?>"
									alt="Notification Icon" style="max-width: 300px; height: auto; margin-top: 1rem;">
							<?php endif; ?>
						</div>
					</div>

					<!-- iOS Notification Preview -->
					<div class="coeditor-airship__scheduled-notification-preview-ios">
						<div class="coeditor-airship__apple-icon">
							<img src="<?php echo esc_url(COEDITOR_AIRSHIP_ASSETS . '/images/apple.png'); ?>" alt="Apple Icon">
						</div>

						<div class="notification-content">
							<div class="notification-title">
								<?php echo esc_html($notification['push']['notification']['ios']['alert']['title'] ?? 'No title'); ?>
							</div>
							<div class="notification-body">
								<?php echo esc_html($notification['push']['notification']['ios']['alert']['body'] ?? 'No message'); ?>
							</div>
							<?php if (!empty($notification['push']['notification']['ios']['media_attachment']['url'])): ?>
								<img src="<?php echo esc_url($notification['push']['notification']['ios']['media_attachment']['url']); ?>"
									alt="Notification Media" style="max-width: 300px; height: auto; margin-top: 1rem;">
							<?php endif; ?>
						</div>
					</div>

					<!-- Android Notification Preview -->
					<div class="coeditor-airship__scheduled-notification-preview-android">
						<div class="coeditor-airship__android-icon">
							<img src="<?php echo esc_url(COEDITOR_AIRSHIP_ASSETS . '/images/android.png'); ?>"
								alt="Android Icon">
						</div>

						<div class="notification-content">
							<div class="notification-title">
								<?php echo esc_html($notification['push']['notification']['android']['title'] ?? 'No title'); ?>
							</div>
							<div class="notification-body">
								<?php echo esc_html($notification['push']['notification']['android']['alert'] ?? 'No message'); ?>
							</div>
							<?php if (!empty($notification['push']['notification']['android']['style']['big_picture'])): ?>
								<img src="<?php echo esc_url($notification['push']['notification']['android']['style']['big_picture']); ?>"
									alt="Notification Media" style="max-width: 300px; height: auto; margin-top: 1rem;">
							<?php endif; ?>
						</div>
					</div>
					<table class="coeditor-airship__scheduled-notification-details">
						<tr>
							<td><strong><?php esc_html_e('Schedule details', 'coeditor-airship'); ?></strong></td>
							<td>
								<ul>
									<?php if (isset($notification['schedule']['local_scheduled_time'])): ?>
										<li><?php esc_html_e('Local schedule time:', 'coeditor-airship'); ?>
											<?php echo esc_html($notification['schedule']['local_scheduled_time']); ?>
										</li>
									<?php endif; ?>

									<?php if (isset($notification['schedule']['scheduled_time'])): ?>
										<li><?php esc_html_e('Scheduled time:', 'coeditor-airship'); ?>
											<?php echo esc_html($notification['schedule']['scheduled_time']); ?>
										</li>
									<?php endif; ?>

									<?php if (isset($notification['schedule']['recurring'])): ?>
										<li><strong><?php esc_html_e('Recurring Schedule:', 'coeditor-airship'); ?></strong></li>

										<?php if (isset($notification['schedule']['recurring']['cadence']['type'])): ?>
											<li><?php esc_html_e('Recurrence Type:', 'coeditor-airship'); ?>
												<?php echo esc_html(ucfirst($notification['schedule']['recurring']['cadence']['type'])); ?>
											</li>
										<?php endif; ?>

										<?php if (isset($notification['schedule']['recurring']['cadence']['count'])): ?>
											<li><?php esc_html_e('Recurrence Count:', 'coeditor-airship'); ?>
												<?php echo esc_html($notification['schedule']['recurring']['cadence']['count']); ?>
											</li>
										<?php endif; ?>

										<?php if (isset($notification['schedule']['recurring']['cadence']['days_of_week'])): ?>
											<li><?php esc_html_e('Days of the Week:', 'coeditor-airship'); ?>
												<?php echo esc_html(implode(', ', $notification['schedule']['recurring']['cadence']['days_of_week'])); ?>
											</li>
										<?php endif; ?>

										<?php if (isset($notification['schedule']['recurring']['exclusions']) && is_array($notification['schedule']['recurring']['exclusions'])): ?>
											<li><strong><?php esc_html_e('Exclusions:', 'coeditor-airship'); ?></strong></li>
											<ul>
												<?php foreach ($notification['schedule']['recurring']['exclusions'] as $exclusion): ?>
													<?php if (isset($exclusion['date_range'])): ?>
														<li><?php echo esc_html($exclusion['date_range']); ?></li>
													<?php endif; ?>
												<?php endforeach; ?>
											</ul>
										<?php endif; ?>

										<?php if (isset($notification['schedule']['recurring']['target_timezone'])): ?>
											<li><?php esc_html_e('Target Timezone:', 'coeditor-airship'); ?>
												<?php echo esc_html($notification['schedule']['recurring']['target_timezone']); ?>
											</li>
										<?php endif; ?>

										<?php if (isset($notification['schedule']['recurring']['paused']) && $notification['schedule']['recurring']['paused']): ?>
											<li><strong><?php esc_html_e('Recurring schedule is paused.', 'coeditor-airship'); ?></strong>
											</li>
										<?php endif; ?>
									<?php endif; ?>
								</ul>
							</td>
						</tr>

						<tr>
							<td><strong><?php esc_html_e('Audience', 'coeditor-airship'); ?></strong></td>
							<td><?php echo esc_html($notification['push']['audience'] ?? 'Unknown'); ?></td>
						</tr>

						<?php if (!empty($notification['push']['notification']['ios']['actions']['open']['content'])): ?>
							<tr>
								<td><strong><?php esc_html_e('iOS Deep Link', 'coeditor-airship'); ?></strong></td>
								<td>
									<a href="<?php echo esc_url($notification['push']['notification']['ios']['actions']['open']['content']); ?>"
										target="_blank">
										<?php echo esc_html($notification['push']['notification']['ios']['actions']['open']['content']); ?>
									</a>
								</td>
							</tr>
						<?php endif; ?>

						<?php if (!empty($notification['push']['notification']['android']['actions']['open']['content'])): ?>
							<tr>
								<td><strong><?php esc_html_e('Android Deep Link:', 'coeditor-airship'); ?></strong></td>
								<td>
									<a href="<?php echo esc_url($notification['push']['notification']['android']['actions']['open']['content']); ?>"
										target="_blank">
										<?php echo esc_html($notification['push']['notification']['android']['actions']['open']['content']); ?>
									</a>
								</td>
							</tr>
						<?php endif; ?>

						<tr>
							<td><strong><?php esc_html_e('Notification URL', 'coeditor-airship'); ?></strong></td>
							<td>
								<a href="<?php echo esc_url($notification['url']); ?>" target="_blank">
									<?php echo esc_html($notification['url']); ?>
								</a>
							</td>
						</tr>
					</table>

				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>