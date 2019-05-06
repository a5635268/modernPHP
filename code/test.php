<?php
$result = 0;

// 在回调生成的时候进行赋值;
$four = function() use ($result){
    //0 回调生成的时候赋值，也就是$result = 0;
    var_dump($result);
};

// 如果使用引用,就能使use里面的变量完成延迟绑定,也就是在调用的时候再赋值;
$three = function() use (&$result){
    //1 在调用的时候再赋值进去，也就是1，注意对象类型也属于引用；
    var_dump($result);
};

$result += 1;


$four(); // outputs int(0)
$three();    // outputs int(1)





