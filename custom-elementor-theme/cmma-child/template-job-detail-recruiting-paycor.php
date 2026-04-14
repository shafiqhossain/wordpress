<?php
/* Template Name: Job Detail - Recruiting Paycor */

get_header();

$job_id = get_query_var('jobs-demo-detail-page');
$request_uri = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim($request_uri, '/'));

if (!$job_id && isset($_GET['gni'])) {
	$job_id = $_GET['gni'];
}

if (count($parts) > 3 || !$job_id) {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part(404);
    exit();
}


$job = cmma_get_jobs_data($job_id);

$jobSummaryContent = "";
$content = $job['summary'];

$doc = new DOMDocument();
@$doc->loadHTML($content);

// Create an XPath object to query the DOM
$xpath = new DOMXPath($doc);

// Find the first div
$firstDiv = $xpath->query('//div[1]')->item(0);

// Get the text content of the first div
$jobSummaryContent = $firstDiv->textContent;

// Remove the first div from the document
$firstDiv->parentNode->removeChild($firstDiv);

// Get the remaining HTML content
$content = $doc->saveHTML();


if ($job && count(array_keys($job))) {?>
	<section class="cmma-job-detials">
		<div class="cmma-container">
			<div class="panel-wrapper">
				<div class="cmma-job-detail-header panel-wrapper">
					<div class="cmma-job-detail-header-left">
						<h2><?= $job['title'];?></h2>
						<p><?= $jobSummaryContent ?></p>
						<a href="<?= site_url('open-positions');?>" class="cmma-button cmma-button-type-text">
							<span class="cmma-button-text">Open Positions</span>
							<span class="cmma-button-icon"><svg width="8" height="24" viewBox="0 0 8 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M0 18.0072L6.18055 13.484L0 8.99255V7L8 12.9147V14.085L0 20V18.0072Z" fill="currentColor"></path>
							</svg></span>
						</a>
					</div>
					<div class="cmma-job-detail-header-right">
						<div class="cmma-job-detail-items">
							<div class="label">Location</div>
							<p><?= $job['location']; ?>, <?= $job['state']; ?></p>
						</div>

						<?php if ($job['remotetype']) { ?>
							<div class="cmma-job-detail-items">
								<div class="label">Status</div>
								<p><?= $job['remotetype']; ?></p>
							</div>
						<?php } ?>

						<?php if ($job['department']) { ?>
							<div class="cmma-job-detail-items">
								<div class="label">Expertise</div>
								<p><?= $job['department']; ?></p>
							</div>
						<?php } ?>

						<?php if ($job['priority']) { ?>
							<div class="cmma-job-detail-items">
								<div class="label">Experience</div>
								<p><?= $job['priority']; ?></p>
							</div>
						<?php } ?>
					</div>
				</div>

				<div class="panel-wrapper">
					<div class="">
						<?= $content; ?>
					</div>
				</div>
			</div>
		</div>
	</section>


	<style>
		#gnewtonCareerBody {
			max-width: 100% !important;
			background: #E6EDF8 !important;
			width: 100%;
			padding: 12.5rem 0;
		}
	</style>

	<section id="gnewtonCareerBody">
		<div id="gnewtonCareerLoader">
			<h3>Loading....</h3>
		    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/loader.gif" >
		</div>

		<div id="gnewtonCareerIframe" style="display: none;">
			<script id="gnewtonjs" type="text/javascript" src="//recruitingbypaycor.com/career/iframe.action?clientId=8a7883c68e824f98018e8be5625203da"></script>
		</div>
	</section>
    <?php
} else {
    // Expert not found, return 404
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part(404);
    exit();
}

add_action('wp_footer', function () {
	?>
		<script>
			jQuery(document).ready(function() {
				setTimeout(function() {
					jQuery('#gnewtonCareerLoader').hide();
					jQuery('#gnewtonCareerIframe').show();
				}, 5000);
			});

		</script>
	<?php
});

get_footer();
?>