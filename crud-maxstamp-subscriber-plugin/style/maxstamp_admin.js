/* Maxstamp Admin JS */
document.addEventListener('DOMContentLoaded', function () {
    // Dismissible alerts (Bootstrap .close button)
    document.querySelectorAll('.alert .close').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            this.closest('.alert').style.display = 'none';
        });
    });
});
