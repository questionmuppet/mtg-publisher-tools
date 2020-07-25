/**
 * Scripts for navigating data tables
 */

document.addEventListener('DOMContentLoaded', function() {
    jQuery(".mtgtools-table-filter-input").each(function(index, element) {
        jQuery(element).on("input", dataTableUpdater.createUpdateHandler() );
    });
});

/**
 * Data table updater
 */
let dataTableUpdater = (function($) {

    return {

        /**
         * Create update handler
         */
        createUpdateHandler: function() {

            /**
             * DOM references
             */
            let tableBody;

            /**
             * Update table rows
             */
            function updateTable(event) {
                cacheTableBody(event.target);
                $.post({
                    url: ajaxurl,
                    data: {
                        'action': 'mtgtools_update_table',
                        '_wpnonce': mtgtoolsDataTable.nonce,
                        'tab': event.target.dataset.dashboard_tab,
                        'table': event.target.dataset.table_key,
                        'filter': event.target.value
                    },
                    success: handleResponse
                });
            }

            /**
             * Cache <tbody> element
             */
            function cacheTableBody(target) {
                if (!tableBody) {
                    tableBody = $(target)
                        .closest('div.mtgtools-table-wrapper')
                        .find('tbody');
                }
            }

            /**
             * Handle response from server
             */
            function handleResponse(data, status) {
                if ('success' === status && data.success) {
                    updateRows(data.data.transients.tableBody);
                } else {
                    console.log("Error encountered. Data returned by server:");
                    console.log(data.data);
                }
            }

            /**
             * Update table rows with new data
             */
            function updateRows(transient) {
                tableBody.html(transient);
            }

            return updateTable;
        }

    };  // End of dataTableUpdater object

})(jQuery);