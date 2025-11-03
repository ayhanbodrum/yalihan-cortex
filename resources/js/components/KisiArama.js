$('#kisiArama').select2({
    ajax: {
        url: '/api/kisiler',
        dataType: 'json',
        delay: 500,
        data: function (params) {
            return { q: params.term };
        },
        processResults: function (data) {
            return { results: data };
        },
    },
    minimumInputLength: 2,
    placeholder: 'Kişi ara...',
    allowClear: true,
});
