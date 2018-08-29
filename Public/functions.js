
/**
 * 监听侧边导航双击时间
 * @param  {[type]} node [description]
 * @return {[type]}      [description]
 */
function treeDbClick(node) {
    switch (node.type) {
        case 'conf' :
            showDb(node)
        break;
        case 'db' :
            showTable(node);
        break;
        case 'table' :
            showData(node);
        break;
    }

}


function showDb(node) {
    if (node.children != undefined) {
        return false;
    }
    $.ajax({
        type : 'post',
        url : window.APIS.getDbLists,
        data : {
            type : node.id
        },
        dataType : 'json',
        error : function(data) {
            $.messager.alert('警告', '配置加载错误');
        },
        success : function(data) {
            if (data.code != 0) {
                $.messager.alert('警告', data.msg);
                return false;
            }
            var rows = new Array();
            $.each(data.data, function(n, item){
                rows.push({
                    id : item,
                    text : item,
                    type : 'db',
                    conf : node.id,
                    iconCls : 'icon-db'
                });
            });    
            $('#sidebar').tree('append', {
                parent : node.target,
                data : rows
            });
        }
    });
}

function showTable(node) {
    if (node.children != undefined) {
        return false;
    }
    $.ajax({
        type : 'post',
        url : window.APIS.getTableLists,
        data : {
            type : node.conf,
            dbName : node.id
        },
        dataType : 'json',
        error : function(data) {
            $.messager.alert('警告', '配置加载错误');
        },
        success : function(data) {
            if (data.code != 0) {
                $.messager.alert('警告', data.msg);
                return false;
            }
            var rows = new Array();
            $.each(data.data, function(n, item){
                rows.push({
                    id : item,
                    text : item,
                    type : 'table',
                    conf : node.conf,
                    db : node.id,
                    iconCls : 'icon-table'
                });
            });    
            $('#sidebar').tree('append', {
                parent : node.target,
                data : rows
            });
        }
    });
}


function showData(node)
{
    $.ajax({
        type : 'post',
        url : window.APIS.getDataLists,
        data : {
            type : node.conf,
            dbName : node.db,
            tableName : node.id
        },
        dataType : 'json',
        error : function(data) {
            $.messager.alert('警告', '数据异常');
        },
        success : function(data) {
            if (data.code != 0) {
                $.messager.alert('警告', data.msg);
                return false;
            }
            
            setDatas(data.data);
        }
    });
}


function newWindow() {
    var node = $('#sidebar').tree('getSelected');
    if (node.type != 'conf') {

        //获取标识
        var se = $('#sidebar').tree('getSelected');
        if (node.type == 'table') {
            var node = $('#sidebar').tree('getParent',$('#sidebar').tree('getParent', se.target).target);
        } else if (node.type == 'db') {
            var node = $('#sidebar').tree('getParent', se.target);
        }
        if (node.id.indexOf('mongo') != -1) {
            var prompt = `{find : test, filter : {}}`;
        } else {
            var prompt = '';
        }

        $('#tabs').tabs('add', {
            title : 'Query',
            closable : true,
            content : `<input class="easyui-textbox" data-options="multiline:true, width:'100%', height:'100%', prompt:'`+prompt+`'"/>`,
        });
    }
}

function select() {
    $.messager.progress({
        text : 'querying',
        interval : 100
    });

    var query = $($('#tabs').tabs('getSelected')).find('input').val();
    var selected = $('#sidebar').tree('getSelected');

    if (selected.type == 'conf') {
        $.messager.alert('警告', '请选择数据库');
        return false;
    } else if (selected.type == 'db') {
        var type = selected.conf;
        var dbName = selected.id;
    } else if (selected.type == 'table') {
        var type = selected.conf;
        var dbName = selected.db;
    }

    $.ajax({
        type : 'post',
        url : window.APIS.getDataLists,
        data : {
            type : type,
            dbName : dbName,
            query : query
        },
        dataType : 'json',
        error : function(data) {
            $.messager.progress('close');
            $.messager.alert('警告', '数据异常');
        },
        success : function(data) {
            $.messager.progress('close');
            if (data.code != 0) {
                $.messager.alert('警告', data.msg);
                return false;
            }

            setDatas(data.data);
        }
    });

}

function setDatas(data) {

    if (data.length < 0 || data == false) {
        $.messager.alert('警告', '无数据');
        return false;
    }

    //获取列名
    var columns = new Array();
    $.each(data[0], function(keyName, item) {
        columns.push({
            field : keyName,
            title : keyName,
            width : 10,
            formatter : function(value, row, index) {
                if (typeof(value) == 'string' || value == null)
                    return value;
                else {
                    if (value['$oid'] != undefined) {
                        return value['$oid'];
                    }
                    return JSON.stringify(value);
                }
            }
        });
    });

    $('#tables').datagrid({
        width : '98%',
        fitColumns : true,
        rownumbers : true,
        columns : [columns],
        data : data,
        onDblClickRow : function(index, row) {
            datasDblClick(row);
        }
    });
}

function datasDblClick(row) {
    console.log(row);
    $('#window').window({
        title : 'Detail',
        width : '70%',
        height : '500px',
        modal : true,
        content : `<textarea style="width:100%; height:100%">`+formatJson(row, '', '')+'</textarea>'
    });
}

function formatJson(data, key, t) {
    if (typeof(data) == 'object') {
        var string = '';
        $.each(data, function(n, v) {
             string += formatJson(v, n, t + "\t");
        });
        return string;
    } else {
        return t + key + ' : ' + data + "\r\n";
    }
}
