jQuery(document).ready(function ($) {

    let offset = 0;
    let limit = 25;

    let sent = 0;
    let failed = 0;

    $('#pem-start-bulk-email').on('click', function (e) {

        e.preventDefault();

        offset = 0;
        sent = 0;
        failed = 0;

        processBatch();

    });

    function processBatch() {

        $.ajax({

            url: ajaxurl,

            type: 'POST',

            dataType: 'json',

            data: {

                action: 'pem_bulk_email_process',

                security: pemBulk.nonce,

                campaign_id: $('#campaign_id').val(),

                offset: offset,

                limit: limit

            },

            success: function (response) {

                if (!response.success) {

                    alert(response.data.message);

                    return;

                }

                offset += response.data.processed;

                sent += response.data.sent;

                failed += response.data.failed;

                $('#pem-sent-count').text(sent);

                $('#pem-failed-count').text(failed);

                let total = parseInt($('#pem-total-count').text());

                let processed = sent + failed;

                $('#pem-remaining-count').text(total - processed);

                let percent = Math.round((processed / total) * 100);

                $('#pem-progress').val(percent);

                if (response.data.processed == limit) {

                    processBatch();

                } else {

                    alert('Bulk Email Completed');

                }

            }

        });

    }

});