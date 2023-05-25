import 'datatables.net-bs5';

const dataTable = {
    init: () => {
        let _dataTableClass = $("#dataTable");
        _dataTableClass.DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
            }
        });
    }
}

$(function () {
    dataTable.init()
});