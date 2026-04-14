document.addEventListener('DOMContentLoaded', function () {
    var gravityBtns = document.querySelectorAll('.gravity-btn');

    if (gravityBtns.length) {
        gravityBtns.forEach(function (gravityBtn) {
            gravityBtn.addEventListener('click', function () {
                var formFooterInput = document.querySelector('.gform_footer input');
                if (formFooterInput) {
                    formFooterInput.click();
                }
            });
        });
    }

    function toggleModal() {
        document.body.classList.toggle('no-scroll');
        var imgBlockWrapper = this.closest('.download-panel');
        if (imgBlockWrapper) {
            imgBlockWrapper.classList.toggle('active');
        }
    }

    // Open the modal when the document is ready
    var downlodableModalToggle = document.querySelector('.downlodable_panel_modal_toggle');
    if (downlodableModalToggle) {
        downlodableModalToggle.addEventListener('click', toggleModal);
    }

    var closeButtons = document.querySelectorAll('.close');
    if (closeButtons.length) {
        closeButtons.forEach(function (closeButton) {
            closeButton.addEventListener('click', toggleModal);
        });
    }

    // Close the modal when clicking outside the modal content
    window.addEventListener('click', function (e) {
        var modalView = document.getElementById('cmma_block_modal_view');
        if (modalView && e.target.id === 'cmma_block_modal_view') {
            modalView.classList.toggle('active');
        }
    });

    // Create a custom code for storing a cookie and open thanks message
    var gravityFormBtns = document.querySelectorAll('#gform_1 .gravity-btn');
    if (gravityFormBtns.length) {
        gravityFormBtns.forEach(function (gravityFormBtn) {
            gravityFormBtn.addEventListener('click', function () {
                document.cookie = 'Gravityform=submit';
            });
        });
    }

    // Function to get cookie value by name
    function getCookie(cookie) {
        return document.cookie.split(';').reduce(function (prev, c) {
            var arr = c.split('=');
            return (arr[0].trim() === cookie) ? arr[1] : prev;
        }, undefined);
    }

    // Function to delete cookie value by name
    function eraseCookie(name) {
        document.cookie = 'Gravityform' + '=; Max-Age=0';
    }

    var formCookie = getCookie('Gravityform');

    if (formCookie === 'submit') {
        var downlodableModalToggleElement = document.querySelector('.downlodable_panel_modal_toggle');
        if (downlodableModalToggleElement) {
            downlodableModalToggleElement.click();
            eraseCookie('Gravityform');
        }
    }
});
