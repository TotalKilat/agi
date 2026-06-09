import $ from 'jquery';
import DataTable from 'datatables.net-dt';
import select2 from 'select2';
import Swal from 'sweetalert2';

import 'datatables.net-dt/css/dataTables.dataTables.css';
import 'select2/dist/css/select2.css';
import 'sweetalert2/dist/sweetalert2.css';
import '../css/components.css';

window.$ = window.jQuery = $;
window.Swal = Swal;
select2(window, $);

const swalTheme = {
    confirmButtonColor: '#E2725B',
    cancelButtonColor: '#A08980',
};

function parseJson(value, fallback) {
    if (!value) {
        return fallback;
    }

    try {
        return JSON.parse(value);
    } catch {
        return fallback;
    }
}

function parseBoolean(value, fallback = true) {
    if (value === undefined) {
        return fallback;
    }

    return value !== 'false';
}

function showFlashMessage(page) {
    const message = page?.dataset.successMessage || page?.dataset.infoMessage;

    if (!message) {
        return;
    }

    Swal.fire({
        ...swalTheme,
        icon: page.dataset.successMessage ? 'success' : 'info',
        title: page.dataset.successMessage ? 'Success' : 'Information',
        text: message,
        timer: 2200,
        showConfirmButton: false,
    });
}

function initializeSelect2() {
    $('.js-select2').each(function () {
        const select = $(this);

        if (select.hasClass('select2-hidden-accessible')) {
            return;
        }

        select.select2({
            width: '100%',
            placeholder: select.data('placeholder') || null,
            allowClear: select.data('allow-clear') === true,
        });
    });
}

function getColumns(tableElement) {
    return Array.from(tableElement.querySelectorAll('thead th')).map((header) => ({
        data: header.dataset.column,
        name: header.dataset.name || header.dataset.column,
        orderable: parseBoolean(header.dataset.orderable),
        searchable: parseBoolean(header.dataset.searchable),
    }));
}

async function deleteRecord(button, page, table) {
    const recordLabel = button.dataset.recordLabel || 'record';
    const recordName = button.dataset.recordName || '';
    const displayName = recordName ? ` "${recordName}"` : '';
    const result = await Swal.fire({
        ...swalTheme,
        icon: 'warning',
        title: `Delete ${recordLabel}?`,
        text: `${recordLabel.charAt(0).toUpperCase() + recordLabel.slice(1)}${displayName} will be permanently deleted.`,
        showCancelButton: true,
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
    });

    if (!result.isConfirmed) {
        return;
    }

    try {
        const response = await fetch(button.dataset.url, {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': page.dataset.csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        const payload = await response.json();

        if (!response.ok) {
            throw new Error(payload.message || `The ${recordLabel} could not be deleted.`);
        }

        await Swal.fire({
            ...swalTheme,
            icon: 'success',
            title: 'Deleted',
            text: payload.message,
            timer: 1800,
            showConfirmButton: false,
        });

        table.ajax.reload(null, false);
    } catch (error) {
        Swal.fire({
            ...swalTheme,
            icon: 'error',
            title: 'Delete failed',
            text: error.message,
        });
    }
}

function initializeDataTable(tableElement, page) {
    const pluralLabel = tableElement.dataset.pluralLabel || 'records';
    const table = new DataTable(tableElement, {
        processing: true,
        serverSide: true,
        ajax: tableElement.dataset.url,
        order: parseJson(tableElement.dataset.order, [[1, 'asc']]),
        pageLength: Number(tableElement.dataset.pageLength || 10),
        columns: getColumns(tableElement),
        language: {
            search: '',
            searchPlaceholder: tableElement.dataset.searchPlaceholder || `Search ${pluralLabel}...`,
            lengthMenu: 'Show _MENU_ entries',
            info: `Showing _START_ to _END_ of _TOTAL_ ${pluralLabel}`,
            infoEmpty: `No ${pluralLabel} available`,
            zeroRecords: `No matching ${pluralLabel} found`,
        },
    });

    tableElement.addEventListener('click', (event) => {
        const button = event.target.closest('.js-delete-record');

        if (button) {
            deleteRecord(button, page, table);
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initializeSelect2();

    document.querySelectorAll('.js-crud-page').forEach((page) => {
        showFlashMessage(page);

        page.querySelectorAll('.js-data-table').forEach((table) => {
            initializeDataTable(table, page);
        });
    });
});
