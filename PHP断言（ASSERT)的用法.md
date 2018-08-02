# PHP断言（ASSERT)的用法

标签（空格分隔）： PHP

---

## 简述

编写代码时，我们总是会做出一些假设，断言就是用于在代码中捕捉这些假设，可以将断言看作是异常处理的一种高级形式。**程序员断言在程序中的某个特定点该的表达式值为真。如果该表达式为假，就中断操作**。
可以在任何时候启用和禁用断言验证，因此可以在测试时启用断言，而在部署时禁用断言。同样，程序投入运行后，最终用户在遇到问题时可以重新起用断言。
使用断言可以创建更稳定，品质更好且不易于出错的代码。单元测试必须使用断言！

## PHP断言

```
# PHP5
bool assert ( mixed $assertion [, string $description ] ) 

# PHP7
bool assert ( mixed $assertion [, Throwable $exception ] )
```

**example1：**
```
// 断言操作选项函数
assert_options(ASSERT_ACTIVE, 1); // 默认是打开断言的

assert('1==2'); //  Warning: assert(): Assertion "1==2" failed in D:\wamp\www\XF9_Trunk_Website3.0\new\Public\index.php on line 3

echo 555555555555; // 默认情况下继续执行，对于调试很好，尤其是可以使用callback，但是生产环境就不建议使用了。
```

assert() 会检查指定的 assertion 并在结果为 FALSE 时采取适当的行动（视`assert_options`而定）。 

### assert_options

- `ASSERT_ACTIVE=1` // Assert函数的开关
- `ASSERT_WARNING =1` // 当表达式为false时，是否要输出警告性的错误提示,issue a PHP warning for each failed assertion
- `ASSERT_BAIL= 0` // 是否要中止运行；terminate execution on failed assertions
- `ASSERT_QUIET_EVAL= 0` // 是否关闭错误提示，在执行表达式时；disable error_reporting during assertion expression evaluation
- `ASSERT_CALLBACK= (NULL)` // 是否启动回调函数 user function to call on failed assertions

```
// Active assert and make it quiet
assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 0);
assert_options(ASSERT_QUIET_EVAL, 1);

// Create a handler function
function my_assert_handler($file, $line, $code)
{
    echo "<hr>Assertion Failed:File '$file'<br />Line '$line'<br />Code '$code'<br /><hr />";
}

// Set up the callback
assert_options(ASSERT_CALLBACK, 'my_assert_handler');

// Make an assertion that should fail
assert('mysql_query("")');
```


### 安全性

```
function fo(){
 file_put_contents('a.php','www.bo56.com');
 return true;
}
$func = $_GET["func"];
assert("$func()");
```

如果 assertion 是字符串，它将会被 assert() 当做 PHP 代码来执行。跟eval()类似, 不过`eval($assertion)`只是执行符合php编码规范的$code_str。





