document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('theme-toggle');
    if (!toggle) return;

    toggle.addEventListener('click', function () {
        const html = document.documentElement;
        const current = html.getAttribute('data-bs-theme');
        const next = current === 'dark' ? 'light' : 'dark';

        html.setAttribute('data-bs-theme', next); // instant visual flip


        $.ajax({
            url: '/theme/toggle',
            type: 'GET',
            success: function (response) {
                console.log(response);
            },
            error: function (xhr) {
                console.error(xhr.responseText);
            }
        });
    });
});
