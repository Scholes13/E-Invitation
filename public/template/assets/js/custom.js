/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */

"use strict";

// datatables
$(document).ready( function () {
    $('#table-1').DataTable();
});

function toast(success, error, warning) {
    let toastrSuccess = success;
    let toastrError = error;
    let toastrWarning = warning;
    if(toastrSuccess) {
        iziToast.success({
            title: 'Berhasil',
            message: toastrSuccess,
            position: 'topRight'
        });
    }
    if(toastrError) {
        iziToast.error({
            title: 'Gagal',
            message: toastrError,
            position: 'topRight'
        });
    }
    if(toastrWarning) {
        iziToast.warning({
            title: 'Peringatan',
            message: toastrWarning,
            position: 'topRight'
        });
    }
}