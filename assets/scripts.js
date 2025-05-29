import DataGrid from "./js/DataGrid/DataGrid"

(() => {
    document.querySelectorAll('.js-sydatagrid').forEach((it) => {
        (new DataGrid()).init(it);
    })
})()