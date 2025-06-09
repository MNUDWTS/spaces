document.addEventListener('DOMContentLoaded', function() {
    const resourcesContainer = document.getElementById('resources-container');
    const paginationLinks = document.querySelectorAll('.pagination-link');

    function updateResources(page) {
        const data = {
            action: 'load_resources',
            nonce: spacesMnuResources.nonce,
            page: page,
        };

        jQuery.ajax({
            url: spacesMnuResources.ajaxurl,
            method: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    resourcesContainer.innerHTML = response.data.resources_html;
                    attachPaginationEventListeners();
                } else {
                    alert('Failed to load resources. Please try again.');
                }
            },
            error: function() {
                alert('An error occurred while loading resources.');
            }
        });
    }

    function attachPaginationEventListeners() {
        const newPaginationLinks = document.querySelectorAll('.pagination-link');
        newPaginationLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = this.getAttribute('data-page');
                updateResources(page);
            });
        });
    }

    attachPaginationEventListeners();
});
