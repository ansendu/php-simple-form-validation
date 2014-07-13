php-simple-form-validation
==========================

PHP Simple Form Validation

##Requirements
php5.3+

##How to use


####eg:

```
$filter = array(
    array('name', 'require', '请输入姓名'),
    array('age', 'lt', 2, '年龄必须小于2'),
    array('level', 'require,egt', 10, '等级必须大于10才能领取'),
    array('email', 'email', 'Email格式不正确'),

    array('pack', 'require', '背包必填'),
    array('pack', 'regexp', '\w+', '背包输入格式有误'),

    array('type', 'require,in', array(1, 2, 3), '类型选择错误'),

    array('school', 'callback', function ($data) {
        return $data == 'Tsinghua';
    }, '仅限清华大学在校学生注册')
);

$data = array(
    'name'   => 'wudi',
    'age'    => 12,
    'email'  => '1000@qq.com',
    'pack'   => '87 **((&(86',
    'school' => 'sss',
    'type'   => 2,
    'level'  => 12
);

if (!Validation::execute($data, $filter)) {
    var_dump(Validation::error());
    var_dump(Validation::firstError());
}

```

####result:

```

array(3) {
  [0]=>
  array(2) {
    [0]=>
    string(3) "age"
    [1]=>
    string(19) "年龄必须小于2"
  }
  [1]=>
  array(2) {
    [0]=>
    string(4) "pack"
    [1]=>
    string(24) "背包输入格式有误"
  }
  [2]=>
  array(2) {
    [0]=>
    string(6) "school"
    [1]=>
    string(36) "仅限清华大学在校学生注册"
  }
}
string(19) "年龄必须小于2"

```
