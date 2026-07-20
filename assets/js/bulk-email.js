jQuery(document).ready(function ($) {

    let offset = 0;

    let totalSent = 0;
    let totalFailed = 0;

    $('#pem-start-bulk-email').on('click', function (e) {

        e.preventDefault();

        const campaign = $('#campaign_id').val();

        if (campaign === '') {
            alert('Please select a campaign.');
            return;
        }

        offset = 0;
        totalSent = 0;
        totalFailed = 0;

        $('#pem-progress').val(0);
        $('#pem-sent-count').text(0);
        $('#pem-failed-count').text(0);

        $('#pem-start-bulk-email')
            .prop('disabled', true)
            .val('Sending...');

        sendBatch();

    });

    function sendBatch() {

        $.ajax({

            url: ajaxurl,

            type: 'POST',

            dataType: 'json',

            data: {

                action: 'pem_send_bulk_email',

                nonce: pemBulk.nonce,

                campaign_id: $('#campaign_id').val(),

                contact_group: $('[name="contact_group"]').val(),

                batch_size: $('[name="batch_size"]').val(),

                offset: offset

            },

            success: function (response) {

                console.log(response);

                if (!response.success) {

                    alert(response.data);

                    $('#pem-start-bulk-email')
                        .prop('disabled', false)
                        .val('🚀 Start Bulk Email');

                    return;

                }

                totalSent += parseInt(response.data.sent);
                totalFailed += parseInt(response.data.failed);

                offset = parseInt(response.data.completed);

                $('#pem-sent-count').text(totalSent);
                $('#pem-failed-count').text(totalFailed);

                let remaining =
                    response.data.total - response.data.completed;

                if (remaining < 0) {
                    remaining = 0;
                }

                $('#pem-remaining-count').text(remaining);

                let percent = Math.round(
                    (response.data.completed / response.data.total) * 100
                );

                $('#pem-progress-bar')
    .css('width', percent + '%')
    .text(percent + '%');

                if (response.data.finished) {

                    $('#pem-start-bulk-email')
                        .prop('disabled', false)
                        .val('🚀 Start Bulk Email');

                    alert(
                        'Bulk Email Completed\n\n' +
                        'Sent : ' + totalSent +
                        '\nFailed : ' + totalFailed
                    );

                } else {

                    setTimeout(function () {

                        sendBatch();

                    }, 300);

                }

            },

            error: function () {

                alert('AJAX Error.');

                $('#pem-start-bulk-email')
                    .prop('disabled', false)
                    .val('🚀 Start Bulk Email');

            }

        });

    }

});