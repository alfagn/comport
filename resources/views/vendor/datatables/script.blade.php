(function () {
    function bootTable() {
        var table = document.getElementById('%1$s');
        if (!table || !window.jQuery) return;
        if (jQuery.fn.dataTable.isDataTable(table)) { jQuery(table).DataTable().columns.adjust(); return; }
        window.{{ config('datatables-html.namespace', 'LaravelDataTables') }} = window.{{ config('datatables-html.namespace', 'LaravelDataTables') }} || {};
        window.{{ config('datatables-html.namespace', 'LaravelDataTables') }}['%1$s'] = jQuery(table).DataTable(%2$s);
    }
    // The body script belongs to this page only. A once listener cannot
    // replay an earlier page's table configuration on a later SPA visit.
    document.addEventListener('livewire:navigated', bootTable, { once: true });
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootTable, { once: true });
    } else {
        bootTable();
    }
})();
@foreach ($scripts as $script)
@include($script)
@endforeach
