define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'project/index' + location.search,
                    add_url: 'project/add',
                    edit_url: 'project/edit',
                    del_url: 'project/del',
                    multi_url: 'project/multi',
                    import_url: 'project/import',
                    table: 'project',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                fixedColumns: true,
                fixedRightNumber: 1,
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Title'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'goal', title: __('Goal'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'minimum', title: __('Minimum'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'one_category_id', title: __('One_category_id')},
                        {field: 'two_category_id', title: __('Two_category_id')},
                        {field: 'maxmum', title: __('Maxmum'), operate: 'LIKE', table: table, class: 'autocontent', formatter: Table.api.formatter.content},
                        {field: 'cover_image', title: __('Cover_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'detail_image', title: __('Detail_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'update_time', title: __('Update_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'is_recommend', title: __('是否首页推荐'), searchList: {"1":__('Yes'),"0":__('No')}, table: table, formatter: Table.api.formatter.toggle},
                        {field: 'is_home', title: __('是否首页显示'), searchList: {"1":__('Yes'),"0":__('No')}, table: table, formatter: Table.api.formatter.toggle},
                        {field: 'category.name', title: __('Category.name'), operate: 'LIKE'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {

            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },

        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
