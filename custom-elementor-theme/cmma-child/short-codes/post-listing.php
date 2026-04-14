<?php

// Fetch jobs data from BambooHR API
function getJobsData($apikey = '31cf1ba37d2c1f5351f10c5c8010f28bea43e6f7') {
  $api_url = "https://{$apikey}:x@api.bamboohr.com/api/gateway.php/smma/v1/applicant_tracking/jobs?statusGroups=Open&sortBy=count&sortOrder=ASC";
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $api_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_USERPWD, "$apikey:x");

  $response = curl_exec($ch);
  curl_close($ch);

  return $response ? json_decode($response, true) : [];
}

// Group jobs by department and sort departments alphabetically
function groupJobsByDepartment($jobs) {
	$grouped = [];
	foreach ($jobs as $job) {
		$department = $job['department']['label'] ?? 'Other';
		$grouped[$department][] = $job;
	}
	ksort($grouped); // Sort departments alphabetically
	return $grouped;
}

// Render job listings and register shortcode
function renderJobListings() {
	ob_start();

	$jobs_data = groupJobsByDepartment(getJobsData());
	if ($jobs_data) { ?>
		<div class="smma-container">
			<div class="panel-wrapper">
				<div class="smma-listing">
					<?php foreach ($jobs_data as $department => $jobs) { ?>
						<div class="smma-listing-category">
							<?php if(count($jobs_data) > 1) { ?>
								<div class="smma-listing-name"><?= htmlspecialchars($department); ?></div>
							<?php } ?>
							<?php foreach ($jobs as $job) { ?>
								<div class="smma-listing-item">
									<h3><a href="<?= $job['postingUrl']; ?>" target="_blank"><?= htmlspecialchars($job['title']['label']); ?></a></h3>
									<p><?= htmlspecialchars($job['location']['label']); ?></p>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	<?php }

  return ob_get_clean();
}
add_shortcode('smma_post_listing', 'renderJobListings');