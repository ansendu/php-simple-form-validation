php-simple-form-Validator
==========================

PHP Simple Form Validator

##Requirements
php5.3+

##How to use


####eg:
##Validator 数据校验使用说明

### 1、Validator::registerFilter(array filter)

####参数说明

参数 |类型|可选| 示意
------------ | ------------- | ------------ | -----------
filter|array | 否  | 校验规则


####功能：注册过滤规则
#####返回值：
无

注：可多次调用，系统会自动叠加规则

### 2、Validator::execute(array data, array filter = array())

####参数说明

参数 |类型|可选| 示意
------------ | ------------- | ------------ | -----------
data |array| 否  | 需要校验的原始数据
filter|array | 是  | 校验规则，此处为快捷操作，底层回去调用 registerFilter(filter)

####返回值

``Boolean``

所有规则全部校验通过之后 会返回 ``true`` 其中一条失败则 返回 ``false``


### 3、Validator::error()
获取规则校验错误信息

####返回值
整个返回值为``array``类型

key   值为校验数据字段
value 值为错误信息  (来源：``filter`` 规则)


####eg:

```
array (size=3)
  'mobile'   => string 'mobile_fromat_error' (length=19)
  'password' => string 'password_required' (length=17)
  'captcha'  => string 'captcha_verify_error' (length=20)
```

### 4、Validator::firstError()

####返回值

返回规则校验中，第一条错误信息，如果没有错误(不应调用)则返回 ``NULL``


### 5、filter 规则说明

整个过滤规则数组``array``类型，每条``array``规则如下：

``字段``  | ``校验类型`` | ``校验规则需要的参数`` |  ``错误信息``

注：
若 “校验类型” 不需要参数，则 “校验规则需要的参数” 自动变为 “错误信息”

校验类型 可以轻度组合。

例如：
```php
array('user', 'required,in', array(1,2,3), '用户名错误');
```

此时 系统会将 array(1,2,3) 参数分别传递给 required 和 in 校验方法。
由于required不需要参数则无视，in 需要参数。

复杂多种情况校验，推荐使用多条规则：
例如拆开上面的组合：

```php
array(
	array('user', 'required',  '用户名必填'),	//校验是否存在
    array('user', 'in', array(1,2,3), '用户名错误');  //校验是否在 1,2,3 内
);
```
 
####校验类型列表

类型 | 说明 | 需要参数|其他
------------ | ------------- | ------------ | -------------
regexp | 正则匹配| 正则表达式| 对正则表达式会自动添加 /^正则$/i
ip | IP地址  | 否|无
email | Email地址  | 否|无
url | URL地址  | 否|无
int | 整型 | 否|无
float | 浮点型  | 否|无
array | 数组类型  | 否|无
number | 数字  | 否|无
lt | > 小于  | 比较值|无
elt | >= 小余等于  | 比较值|无
gt | < 大于 | 比较值|无
egt | <= 大于等于  | 比较值|无
eq | 等于  | 比较值|无
neq | 不等于 | 比较值|无
in | 在数组内 | 数组数据|无
required | 必选  | 否|无
callback | 回调函数  | 参数必须保证``is_callable``|回调函数需要接收一个参数为本字段值，并且需要返回 布尔值 ``Boolean``
其他 | 不在类型表内  |否| 直接返回``false``


### 示例代码

```php

//定义规则
$filter = array(
    array('name', 'required', '请输入姓名'),
    array('age', 'lt', 2, '年龄必须小于2'),
    array('level', 'required,egt', 10, '等级必须大于10才能领取'),
    array('email', 'email', 'Email格式不正确'),

    array('pack', 'required', '背包必填'),
    array('pack', 'regexp', '\w+', '背包输入格式有误'),

    array('type', 'required,in', array(1, 2, 3), '类型选择错误'),

    array('school', 'callback', function ($data) {
        return $data == 'Tsinghua';
    }, '仅限清华大学在校学生注册')
);

//需要校验的数据
$data = array(
    'name'   => 'wudi',
    'age'    => 12,
    'email'  => '1000@qq.com',
    'pack'   => '87 **((&(86',
    'school' => 'sss',
    'type'   => 2,
    'level'  => 12
);

//开始校验
if (!Validator::execute($data, $filter)) {
    var_dump(Validator::error());  //获取所有错误信息
    var_dump(Validator::firstError());   //获取第一条错误信息
}

```

####输出结果

```
array(3) {
  [age]=> string(19) "年龄必须小于2",
  [pack]=> string(19) "背包输入格式有误",
  [school]=> string(19) "仅限清华大学在校学生注册",
}

string(19) "年龄必须小于2"
```
