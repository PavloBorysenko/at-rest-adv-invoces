jQuery(function($) {

    $('#advertisement_search').select2({
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    action: 'admin_post_advertise_search',
                    term: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            }
        },
        placeholder: 'Start typing advertisement name',
        minimumInputLength: 2,
    });
});