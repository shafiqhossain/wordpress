<?php get_header(); ?>

<style>
    .error-page {
        text-align: center;
        padding: 100px 20px;
        background-color: #f2f2f2; /* Replace with your site's background color */
        color: #333; /* Replace with your site's text color */
    }
    .error-page h1 {
        font-size: 72px;
        margin-bottom: 20px;
        color: #004FBD; /* Replace with your site's primary color */
    }
    .error-page p {
        font-size: 24px;
        margin-bottom: 30px;
    }
    .error-page a {
        display: inline-block;
        padding: 10px 20px;
        background-color: #004FBD; /* Replace with your site's primary color */
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
    }
    .error-page a:hover {
        background-color: #004FBD; /* Replace with a darker shade of your primary color */
    }
</style>

<div class="error-page">
    <h1>404</h1>
    <p>Oops! The page you are looking for cannot be found.</p>
    <a href="<?php echo home_url(); ?>">Go to Homepage</a>
</div>

<?php get_footer(); ?>
