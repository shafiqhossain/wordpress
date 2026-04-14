#!/bin/bash

# Define an array of directories and corresponding output paths
directories=(
  "css/style.scss:css/style.css"
  "widgets/single-image/assets/css/style.scss:widgets/single-image/assets/css/style.css"
  "widgets/multiple-images/assets/css/style.scss:widgets/multiple-images/assets/css/style.css"
  "widgets/large-image/assets/css/style.scss:widgets/large-image/assets/css/style.css"
  "widgets/accordion/assets/css/style.scss:widgets/accordion/assets/css/style.css"
  "widgets/render-to-reality/assets/css/style.scss:widgets/render-to-reality/assets/css/style.css"
  "widgets/single-video/assets/css/style.scss:widgets/single-video/assets/css/style.css"
  "widgets/two-image/assets/css/style.scss:widgets/two-image/assets/css/style.css"
  "widgets/image-with-story/assets/css/style.scss:widgets/image-with-story/assets/css/style.css"
  "widgets/image-with-deep-dive/assets/css/style.scss:widgets/image-with-deep-dive/assets/css/style.css"
  "widgets/image-with-text/assets/css/style.scss:widgets/image-with-text/assets/css/style.css"
  "widgets/image-video-carousel/assets/css/style.scss:widgets/image-video-carousel/assets/css/style.css"
  "widgets/hero-slider/assets/css/style.scss:widgets/hero-slider/assets/css/style.css"
  "widgets/media-gallery/assets/css/style.scss:widgets/media-gallery/assets/css/style.css"
  "widgets/stats/assets/css/style.scss:widgets/stats/assets/css/style.css"
  "widgets/wysiwyg/assets/css/style.scss:widgets/wysiwyg/assets/css/style.css"
  "widgets/social-media/assets/css/style.scss:widgets/social-media/assets/css/style.css"
  "widgets/downloadable-content/assets/css/style.scss:widgets/downloadable-content/assets/css/style.css"
  "widgets/services/assets/css/style.scss:widgets/services/assets/css/style.css"
  "widgets/markets/assets/css/style.scss:widgets/markets/assets/css/style.css"
  "widgets/quotes/assets/css/style.scss:widgets/quotes/assets/css/style.css"
  "widgets/related-projects-and-perspectives/assets/css/style.scss:widgets/related-projects-and-perspectives/assets/css/style.css"
  "widgets/article-listing/assets/css/style.scss:widgets/article-listing/assets/css/style.css"
  "widgets/thinglink/assets/css/style.scss:widgets/thinglink/assets/css/style.css"
  "widgets/featured-text/assets/css/style.scss:widgets/featured-text/assets/css/style.css"
  "widgets/featured-articles/assets/css/style.scss:widgets/featured-articles/assets/css/style.css"
  "widgets/post-listing-with-filter/assets/css/style.scss:widgets/post-listing-with-filter/assets/css/style.css"
  "widgets/single-collection/assets/css/style.scss:widgets/single-collection/assets/css/style.css"
  "widgets/our-people-listing/assets/css/style.scss:widgets/our-people-listing/assets/css/style.css"
)

# Iterate through the directories and compile Sass files
for dir in "${directories[@]}"; do
  sass --watch "${dir}" --style compressed &
done

# Wait for all background Sass processes to finish
wait
