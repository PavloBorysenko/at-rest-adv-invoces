jQuery(document).ready(function($) {
    const $dateInput = $('#invoice-date');
    const $yearInput = $('#invoice-year');
    const $monthInput = $('#invoice-month');

    function updateFields(date) {
        const year = date.getFullYear();
        const month = date.getMonth() + 1;
        const monthNames = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        $yearInput.val(year);
        $monthInput.val(monthNames[month - 1]);
    }

    $dateInput.datepicker({
        dateFormat: 'yy-mm-dd',
        maxDate: 0,
        onSelect: function(dateText) {
            const parts = dateText.split('-');
            const date = new Date(parts[0], parts[1] - 1, parts[2]);
            updateFields(date);
        }
    });

    const today = new Date();
    $dateInput.datepicker('setDate', today);
    updateFields(today);

});

document.addEventListener('DOMContentLoaded', function() {
    const button = document.getElementById('create-invoice');
    if (!button) return;

    button.addEventListener('click', function(e) {
        e.preventDefault();
        
        const spinner = button.nextElementSibling;
        const answerDiv = document.getElementById('invoice-ansver-text');
        
        const postId = button.dataset.postId;
        const date = document.getElementById('invoice-date').value;
        const productPrice = document.getElementById('product-price').value;

        button.disabled = true;
        spinner.classList.add('is-active');
        answerDiv.classList.remove('error-message');
        answerDiv.classList.remove('success-message');  

        const formData = new FormData();
        formData.append('action', 'create_advertisement_invoice');
        formData.append('nonce', atRestInvoiceData.nonce);
        formData.append('post_id', postId);
        formData.append('date', date);
        formData.append('product_price', productPrice);

        fetch(atRestInvoiceData.ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                answerDiv.textContent = data.data.message;
                answerDiv.classList.add('success-message');
                location.reload();

            } else {
                answerDiv.textContent = data.data.message;
                answerDiv.classList.add('error-message');
                button.disabled = false;
                spinner.classList.remove('is-active');
            }
        })
        .catch(() => {
            answerDiv.textContent = 'An error occurred. Please try again.';
            answerDiv.classList.add('error-message');
            button.disabled = false;
            spinner.classList.remove('is-active');
        });
    });
});
