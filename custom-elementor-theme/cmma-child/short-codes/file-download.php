<?php
function _heroFileDownload() {
    ob_start();
    $downloadBtn = get_field('file_download_enable');
    $filedownloadBtn = get_field('filedownload_color_scheme');
    if ($downloadBtn) : ?>
        <div class="smma-download-btn color-scheme-<?php echo $filedownloadBtn; ?>">
            <a href="<?= get_field('file_download_sheet_file'); ?>" target="_blank">
                <?= get_field('file_download_button_text'); ?>
                <svg width="14" height="13" viewBox="0 0 14 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M6.88209 9.13281C6.89609 9.15024 6.91399 9.16434 6.93442 9.17403C6.95485 9.18373 6.97727 9.18877 7 9.18877C7.02273 9.18877 7.04515 9.18373 7.06558 9.17403C7.08601 9.16434 7.10391 9.15024 7.11791 9.13281L9.21417 6.54974C9.29091 6.45495 9.22166 6.31458 9.09626 6.31458H7.70936V0.145833C7.70936 0.065625 7.64198 0 7.55963 0H6.43663C6.35428 0 6.2869 0.065625 6.2869 0.145833V6.31276H4.90374C4.77834 6.31276 4.70909 6.45313 4.78583 6.54792L6.88209 9.13281ZM13.8503 8.49479H12.7273C12.6449 8.49479 12.5775 8.56042 12.5775 8.64063V11.4479H1.42246V8.64063C1.42246 8.56042 1.35508 8.49479 1.27273 8.49479H0.149733C0.0673797 8.49479 0 8.56042 0 8.64063V12.25C0 12.5727 0.267647 12.8333 0.59893 12.8333H13.4011C13.7324 12.8333 14 12.5727 14 12.25V8.64063C14 8.56042 13.9326 8.49479 13.8503 8.49479Z" fill="currentcolor"/>
                </svg>
            </a>
        </div>
        <?php
    endif;
    return ob_get_clean();
}
add_shortcode('smma_file_download', '_heroFileDownload');