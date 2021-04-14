<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/13
 * Time: 20:19
 */

namespace app\exception;


class ErrorCode
{
    // 10x系列为鉴权相关 如未登录 没有权限
    const AUTH_TOKEN_INVALID = '100';// token为空
    const AUTH_TOKEN_ERROR = '101';// token格式错误
    const AUTH_USER_NONE = '102';// token获取信息失败
    const AUTH_USER_BAN = '103';// token用户被封禁
    const AUTH_USER_CANNOT = '104';// 用户没有该操作权限节点

    // 20x为业务逻辑正常
    const SUCCESS = '200';// 服务成功，逻辑执行正常
    const SUCCESS_NO_NEED_RUN = '201';// 服务成功，逻辑无需再次运行（不是第一次提交了）
    const SUCCESS_WAIT_DO = '202';// 服务提交成功，等待处理，常见于投递入队操作


    // 30x系列为逻辑转移 如刷卡提交成功 需要接着请求查询接口（需要在data中带上next_node）
    // eg: {"code":"301", "data":{"next_node":"api/index.order/query","msg":"success"}}
    const TRANSFER_NEXT_API_SUCCESS = '300';// api正常，请求下一api
    const TRANSFER_NEXT_API_FAIL = '301';// api正常，请求下一api
    const TRANSFER_NEW_API = '302';// api失效 请求新api
    const TRANSFER_TEMP = '303';// api临时转移，也就是暂不可用
    const TRANSFER_IP_INVALID = '304';// api需使用指定IP通过 如每个用户的应用白名单

    // 40x系列为参数相关 如参数缺失 参数格式错误等
    const PARAM_EMPTY = '400';// 参数为空
    const PARAM_FORMAT_ERROR = '401';// 参数格式错误
    const ACTION_NOT_FOUND = '402';// 方法不存在
    const METHOD_NOT_ALLOWED = '403';// 请求方式不允许

    // 50x系列为常见业务逻辑错误
    const WAF_SERVER_UNAVAILABLE = '500';// 服务器不活跃 整体超载
    const WAF_IP_FILTER = '501';// IP限流
    const WAF_GATEWAY_TIME_OUT = '502';// 如果当前服务为网关，未及时从远端服务器获取请求
    const THIRD_PART_TIME_OUT = '503';// 第三方服务超时，常用语curl 微服务场景等
    const THIRD_PART_ERROR = '504';// 第三方服务出错
    const THIRD_PART_FAIL = '505';// 第三方服务失败

    // 60x系列 其他针对单个api的错误码 根据具体逻辑定义
}