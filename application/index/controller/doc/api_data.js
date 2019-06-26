define({ "api": [
  {
    "type": "get",
    "url": "/article/index",
    "title": "获取文章数据",
    "name": "article_get_data",
    "group": "Article",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>用来获取文章的父id,0=获取所有,可传多个值(1,2,3),以逗号分隔</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "page",
            "description": "<p>页数</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "num",
            "description": "<p>每页文章数</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "成功返回": [
          {
            "group": "成功返回",
            "type": "Array",
            "optional": false,
            "field": "data",
            "description": "<p>文章数组</p>"
          },
          {
            "group": "成功返回",
            "type": "Number",
            "optional": false,
            "field": "code",
            "description": "<p>状态标识码</p>"
          },
          {
            "group": "成功返回",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>状态信息</p>"
          },
          {
            "group": "成功返回",
            "type": "Number",
            "optional": false,
            "field": "count",
            "description": "<p>文章数量</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "./Article.php",
    "groupTitle": "Article"
  },
  {
    "type": "post",
    "url": "/base/upload",
    "title": "文件上传",
    "name": "file_upload",
    "group": "Base",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "file",
            "description": "<p>上传的文件</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "成功返回": [
          {
            "group": "成功返回",
            "type": "String",
            "optional": false,
            "field": "data",
            "description": "<p>图片上传后的保存名称:&quot;20190625\\b75a7da75f420c85529729ab907720e5.jpg&quot;</p>"
          },
          {
            "group": "成功返回",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>上传信息</p>"
          },
          {
            "group": "成功返回",
            "type": "Number",
            "optional": false,
            "field": "code",
            "description": "<p>状态标识码</p>"
          },
          {
            "group": "成功返回",
            "type": "String",
            "optional": false,
            "field": "location",
            "description": "<p>同data,供富文本编辑器使用</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "./Base.php",
    "groupTitle": "Base"
  },
  {
    "type": "Method",
    "url": "/base/move_file",
    "title": "文件移动",
    "name": "move_file",
    "group": "Base",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "file_save_name",
            "description": "<p>文件保存名:&quot;20190626\\6159e602f3befeccd8f83ebcd74702b3.jpg&quot;</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "./Base.php",
    "groupTitle": "Base"
  },
  {
    "type": "get",
    "url": "/base/type_tree",
    "title": "获取树状结构",
    "name": "type_tree",
    "group": "Base",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>树状结构最外层的id</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "成功返回": [
          {
            "group": "成功返回",
            "type": "String",
            "optional": false,
            "field": "data",
            "description": "<p>树状结构</p>"
          },
          {
            "group": "成功返回",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>信息</p>"
          },
          {
            "group": "成功返回",
            "type": "Number",
            "optional": false,
            "field": "code",
            "description": "<p>状态标识码</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "样本数据:",
          "content": "{\n    \"data\": [\n    {\n    \"id\": 17,\n    \"parent\": \"13\",\n    \"name\": \"后端\",\n    \"son\": [\n    {\n    \"id\": 18,\n    \"parent\": \"17\",\n    \"name\": \"php\"\n    },\n    {\n    \"id\": 21,\n    \"parent\": \"17\",\n    \"name\": \"mysql\"\n    }\n    ]\n    }\n    ],\n    \"message\": \"返回成功\",\n    \"code\": 200\n    }",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "./Base.php",
    "groupTitle": "Base"
  },
  {
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "optional": false,
            "field": "varname1",
            "description": "<p>No type.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "varname2",
            "description": "<p>With type.</p>"
          }
        ]
      }
    },
    "type": "",
    "url": "",
    "version": "0.0.0",
    "filename": "./doc/main.js",
    "group": "D__WWW_cms_api_application_index_controller_doc_main_js",
    "groupTitle": "D__WWW_cms_api_application_index_controller_doc_main_js",
    "name": ""
  }
] });
