<?php
/**
 * @apiDefine FoundError
 *
 * @apiErrorExample 访问失败:
{
"code": "502",
"message": "链接失败",
}
 */

/**
 * @api {post} /:token 令牌
 * @apiName Token
 * @apiGroup Token
 * @apiParam {String} [token=thycms] 用来识别api调用者
 */

/**
 * @api {get} /article/:id 文章数据获取
 * @apiName Article
 * @apiGroup Article
 *
 * @apiParam {String} id 单个文章id,或文章id集合(用","分割)
 * @apiParam {String} id:0 获取所有
 *
 * @apiSuccess (成功返回) {Number} code 状态标识码
 * @apiSuccess (成功返回) {String} message  状态信息
 * @apiSuccess (成功返回) {Array} data  文章数组
 *
 * @apiParamExample {js} 请求样式:
 * data:{"id":"130"}
 * data:{"id":"129,130"}
 *
 * @apiSuccessExample 样本数据:
{
"code": "200",
"message": "链接成功",
"data": [{
"id": 130,
"pid": "22",
"title": "imagetools",
"keywords": "",
"description": "https:\/\/www.tiny.cloud\/docs\/plugins\/imagetools\/",
"img": "",
"content": "<!DOCTYPE html>\r\n<html>\r\n<head>\r\n<\/head>\r\n<body>\r\n\r\n<\/body>\r\n<\/html>",
"time": "2019-03-14 11:05:44",
"order": 0,
"click": 0,
"system": 0,
"img1": "20190427\\ba79fe2829e42419bd71c0b04e090d0c.jpg",
"img2": "",
"img3": "",
"img4": "",
"img5": "",
"img6": ""
}]
}
 * @apiUse FoundError
 */

/**
 * @api {get} /getType 获取分类名称和id(无树状结构)
 * @apiName Category
 * @apiGroup Category
 * @apiParam {String} id 父分类id
 * @apiSuccessExample 样本数据:
 * {"一级分类":1,"产品中心":2,"人才招聘":3,"前端":15,"php":18,"js":19,"css":20,"笔记":13,"git":16,"后端":17,"vue":14,"mysql":21,"tinymce":22,"ui":52}
*/


/**
 * @api {get} /category 分类数据获取
 * @apiName category
 * @apiGroup Category
 *
 * @apiParam {String} id 单个分类id,或分类id集合(用","分割)
 * @apiParam {String} num 每页的文章数
 * @apiParam {String} page 页码
 *
 * @apiParamExample {js} 请求样式:
 * data:{id:19,num:"6",page:1}
 * data:{id:'18,19',num:6,page:1}
 * @apiSuccess (成功返回) {Number} count 文章数量
 *
 * @apiSuccessExample 样本数据:
 * {
"code": "200",
"message": "链接成功",
"data": [{
"id": 19,
"code": "1,13,15,19,",
"name": "js",
"parent": "15",
"order": 0,
"type": 1,
"keywords": "",
"description": "",
"img": "",
"content": ""
}],
"count":1
}
 * @apiUse FoundError
 *
 */


/**
 * @api {get} /count 获取文章数量
 * @apiName Count
 * @apiGroup Category
 *
 * @apiParam {String} id 单个分类id,或分类id集合(用","分割)
 * @apiParam {String} id:0 获取所有
 *
 * @apiSuccess (成功返回) {String} code 状态标识码
 * @apiSuccess (成功返回) {String} message 状态信息
 * @apiSuccess (成功返回) {Number} data 统计数量
 *
 * @apiParamExample {js} 请求样式:
 * data:{"id":"19"}
 * data:{"id":"18,19"}
 *
 * @apiSuccessExample 样本数据:
 *{"code":"200","message":"链接成功","data":21}
 *
 * @apiUse FoundError
 */

/**
 * @api {get} /getTypeTree 分类树结构获取
 * @apiName getTypeTree
 * @apiGroup Category
 *
 * @apiParam {String} id 单个父辈id
 *
 * @apiParamExample {js} 请求样式:
 * data:{"id":"15"}
 *
 * @apiSuccessExample 样本数据:
 * [{
"id": 15,
"code": "1,13,15,",
"name": "前端",
"parent": "13",
"order": 0,
"type": 1,
"keywords": "",
"description": "",
"img": "",
"content": "",
"son": [{
"id": 19,
"code": "1,13,15,19,",
"name": "js",
"parent": "15",
"order": 0,
"type": 1,
"keywords": "",
"description": "",
"img": "",
"content": ""
}, {
"id": 20,
"code": "1,13,15,20,",
"name": "css",
"parent": "15",
"order": 0,
"type": 1,
"keywords": "",
"description": "",
"img": "",
"content": ""
}, {
"id": 14,
"code": "1,13,15,14,",
"name": "vue",
"parent": "15",
"order": 0,
"type": 1,
"keywords": "",
"description": "",
"img": "",
"content": ""
}, {
"id": 22,
"code": "1,13,15,22,",
"name": "tinymce",
"parent": "15",
"order": 0,
"type": 1,
"keywords": "",
"description": "",
"img": "",
"content": "<!DOCTYPE html>\r\n<html>\r\n<head>\r\n<\/head>\r\n<body>\r\n\r\n<\/body>\r\n<\/html>"
}]
}]
 */

/**
 * @api {get} /article_search 文章搜索
 * @apiName search
 * @apiGroup Search
 *
 * @apiParam {String} keywords 搜索关键词
 *
 * @apiSuccess (成功返回) {String} code 状态标识码
 * @apiSuccess (成功返回) {String} message 状态信息
 * @apiSuccess (成功返回) {Number} data 文章
 * @apiSuccess (成功返回) {Number} count 文章数量
 *
 * @apiParamExample {js} 请求样式:
 * data:{keywords:"string"}
 *
 * @apiSuccessExample 样本数据:
 *{
"code": "200",
"message": "链接成功",
"data": [{
"id": 276,
"pid": "53",
"title": "图片引用路径403错误",
"keywords": "",
"description": "",
"img": "",
"content": "",
"time": "2019-06-11 11:32:54",
"order": 0,
"click": 0,
"system": 0,
"img1": "",
"img2": "",
"img3": ""
}],
"count": 1
}
 *
 * @apiUse FoundError
 */

