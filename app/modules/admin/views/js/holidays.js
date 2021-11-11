$(document).ready(function () {
    countdown.start(timesession);

    var $table = $('#holidaysList');
    var selections = []

    window.operateEvents = {
        'click .like': function (e, value, row, index) {
        alert('You click like action, row: ' + JSON.stringify(row))
        },
        'click .remove': function (e, value, row, index) {
            $table.bootstrapTable('remove', {
                field: 'id',
                values: [row.id]
            })
        }
    }

    initTable($table);
    //$('#locale').change(initTable);
});

function getIdSelections() {
    return $.map($table.bootstrapTable('getSelections'), function (row) {
    return row.id
    })
}

function responseHandler(res) {
    $.each(res.rows, function (i, row) {
    row.state = $.inArray(row.id, selections) !== -1
    })
    return res
}

function detailFormatter(index, row) {
    var html = []
    $.each(row, function (key, value) {
    html.push('<p><b>' + key + ':</b> ' + value + '</p>')
    })
    return html.join('')
}

function operateFormatter(value, row, index) {
    return [
    '<a class="like" href="javascript:void(0)" title="Like">',
    '<i class="fa fa-heart"></i>',
    '</a>  ',
    '<a class="remove" href="javascript:void(0)" title="Remove">',
    '<i class="fa fa-trash"></i>',
    '</a>'
    ].join('')
}

function totalTextFormatter(data) {
    return 'Total'
}

function totalNameFormatter(data) {
    return data.length
}

function totalPriceFormatter(data) {
    var field = this.field
    return '$' + data.map(function (row) {
    return +row[field].substring(1)
    }).reduce(function (sum, i) {
    return sum + i
    }, 0)
}

function initTable(table) {
    table.bootstrapTable('destroy').bootstrapTable({
    height: 550,
    //locale: $('#locale').val(),
    columns: [
        [{
        field: 'state',
        checkbox: true,
        align: 'center',
        valign: 'middle'
        }, {
        title: 'Item ID',
        field: 'id',
        align: 'center',
        valign: 'middle',
        sortable: true,
        footerFormatter: totalTextFormatter
        }, {
        title: 'Item Detail',
        align: 'center'
        }]
    ],
    iconsPrefix:'fa',
    icons:[{
        paginationSwitchDown: 'fa-caret-square-down',
        paginationSwitchUp: 'fa-caret-square-up',
        refresh: 'fa-sync',
        toggleOff: 'fa-toggle-off',
        toggleOn: 'fa-toggle-on',
        columns: 'fa-th-list',
        fullscreen: 'fa-arrows-alt',
        detailOpen: 'fa-plus',
        detailClose: 'fa-minus'
    }]
    })
    table.on('check.bs.table uncheck.bs.table ' +
    'check-all.bs.table uncheck-all.bs.table',
    function () {
    $remove.prop('disabled', !$table.bootstrapTable('getSelections').length)

    // save your data, here just save the current page
    selections = getIdSelections()
    // push or splice the selections if you want to save all data selections
    })
    table.on('all.bs.table', function (e, name, args) {
    console.log(name, args)
    })
    $remove.click(function () {
    var ids = getIdSelections()
    table.bootstrapTable('remove', {
        field: 'id',
        values: ids
    })
    $remove.prop('disabled', true)
    })
}