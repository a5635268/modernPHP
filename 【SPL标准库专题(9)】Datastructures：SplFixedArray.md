SplFixedArray主要是处理数组相关的主要功能，与普通php array不同的是，**它是固定长度的，且以数字为键名的数组**，优势就是比普通的数组处理更快。

## 类摘要

```php
SplFixedArray  implements Iterator   , ArrayAccess   , Countable   {
    /* 方法 */
    public __construct  ([ int $size  = 0  ] )
    public int count  ( void )
    public mixed current  ( void )
    
    //↓↓导入PHP数组，返回SplFixedArray对象；
    public static SplFixedArray fromArray  ( array $array  [, bool $save_indexes  = true  ] )
    
    //↓↓把SplFixedArray对象数组导出为真正的数组；
    public array toArray  ( void )
    public int getSize  ( void )
    public int key  ( void )
    public void next  ( void )
    public bool offsetExists  ( int $index  )
    public mixed offsetGet  ( int $index  )
    public void offsetSet  ( int $index  , mixed  $newval  )
    public void offsetUnset  ( int $index  )
    public void rewind  ( void )
    public int setSize  ( int $size  )
    public bool valid  ( void )
    public void __wakeup  ( void )
}
```

## Example

```php
# Example1：
$arr = new SplFixedArray(4);
try{
  $arr[0] = 'php';
  $arr[1] = 'Java';
  $arr[3] = 'javascript';
  $arr[5] = 'mysql';
}catch(RuntimeException $e){
  //由于是定长数组，所以$arr超过定长4;就会抛出异常。
  echo $e->getMessage(),' : ',$e->getCode();
}

#Example2：
// public static SplFixedArray fromArray ( array $array [, bool $save_indexes = true ] )
$arr = [
  '4' => 'php',
  '5' => 'javascript',
  '0' => 'node.js',
  '2' => 'linux'
];

//第二参数默认为true的话，保持原索引，如果为false的话，就重组索引;
//如下，如果重组了索引，那数组长度就为4；如果不重组长度就为6;
$arrObj = SplFixedArray::fromArray($arr);

$arrObj->rewind();
while($arrObj->valid()){
  echo $arrObj->key(),'=>',$arrObj->current();
  echo PHP_EOL;
  $arrObj->next();
}


//↓↓由定长数组对象转换为真正的数组
$arr = $arrObj->toArray();
print_r($arr);
```