# 理解php的output buffer

标签（空格分隔）： PHP

---

## 理论篇

### 1.  磁盘高速缓存(Disk Cache)

操作系统中使用磁盘高速缓存技术来提高磁盘的I/O速度，对高速缓存复制的访问要比原始数据访问更为高效。例如，正在运行的进程的指令既存储在磁盘上，也存储在物理内存上，也被复制到CPU的二级和一级高速缓存中。

不过，磁盘高速缓存技术不同于通常意义下的介于CPU与内存之间的小容量高速存储器，而是 **指利用内存中的存储空间来暂存从磁盘中读出的一系列盘块中的信息。因此，磁盘高速缓存在逻辑上属于磁盘，物理上则是驻留在内存中的盘块。**

高速缓存在内存中分为两种形式：一种是在内存中开辟一个单独的存储空间作为磁速缓存，大小固定；另一种是把未利用的内存空间作为一个缓沖池，供请求分页系统和磁盘I/O时共享。

### 2. 缓冲区(Buffer)

高速设备（如CPU）和低速设备（如磁盘）的通信都要经过缓存区，高速设备永远不会直接去访问低速设备。所以缓冲区是计算机中暂时存放输出或输入信息的内存区域。缓和高速部件和低速部件之间通信速度不匹配的矛盾。

### 3. PHP输出缓冲区

输出缓冲区顾名思义是输出信息暂时存放的内存区域，通过ob\_*系列函数来控制输出缓冲区。

当php脚本执行结束(会自动调用ob\_flush())或强制刷新(手动调用ob\_fush())缓冲区后，才会把数据发送给Nginx fastcgi客户端。当然PHP还有其他的缓冲区，比如字符串缓冲区[finfo::buffer](http://cn2.php.net/manual/zh/function.finfo-buffer.php)。

PHP的输出缓冲区默认是开启，并且大小是4096字节。开启后对所有php页面都生效。

另外一种在页面中单独开启缓冲区的办法是ob_start()函数。

```
// ob_start()有三个参数，$chunk_size是用来设置缓冲区大小，可以设置0-4096，默认是0表示大小不限。
bool ob_start ([ callback $output_callback [, int $chunk_size [, bool $erase ]]] )
```

**注意：一个ob_start()就是一个新的缓冲区，缓冲区是互相叠加的**

![20170125103047647.png-47.3kB][1]


### 4. Nginx缓冲区

Nginx默认不会实时把php-fpm响应的数据返回给客户端，而是暂存在Nginx缓冲区中。当php脚本执行结束(自动调用flush())或强制刷新(手动flush())缓冲区后，才会把数据发送给客户端。

### 5. 浏览器缓冲区

浏览器默认不会实时显示从Nginx返回的数据，而是把接受到的数据暂存在浏览器缓冲区中，当缓冲区满后，才会开始显示。不同的浏览器缓冲区大小不同。实际测试发现Mac 下chrome和safari都需要输出1024字节。没有找到刷新缓冲区的办法，可以通过发送额外的空格来解决。

或者通过curl来请求，通过–no-buffer来禁用curl buffer。

```
curl 'niliu.me' --no_buffer
```

## 实例篇

### 实时输出

```php
// ob_get_level() 返回多少个缓冲区（因为缓冲区是叠加的，也可以称之为多级缓冲区）
var_dump(ob_get_level()); // out: 1, 一级缓冲区
if (ob_get_level() == 0) {
    // 如果没缓冲区，就开启新的PHP缓冲区
    ob_start();
}
for ($i = 0;$i < 10;$i ++) {
    echo "Line to show.";
    // nginx fastcgi缓冲区大小是4K，需要发送额外4K空格；
    //  Apache其实不需要
    //  echo str_pad('',4096)."\n";

    // php缓存刷入Apache/nginx
    ob_flush();

    // 从Apache/nginx刷到浏览器
    flush();
    //此时，浏览器应该显示了， 如果浏览器不是即时显示，就输空格撑满浏览器buffer
    // echo str_repeat(" ",1024);

    sleep(2);
}
echo "Done.";
ob_end_flush();  // 冲刷出（送出）输出缓冲区内容并关闭缓冲
var_dump(ob_get_level()); // out: 0, 无缓冲区
```

### 模板渲染

```php
class Template{
    /**
     * 渲染方法
     *
     * @access public
     * @param obj 信息类
     * @param string 模板文件名
     */
    public function render($context, $tpl){
        $closure = function($tpl){
            ob_start();
            include $tpl;
            return ob_end_flush();
        };
         
        // PHP7： $closure->call($context, $tpl);
        $closure = $closure->bindTo($context, $context);
        $closure($tpl);
    }
}
```

### 静态页生成

```php
// 开启缓冲区
ob_start();

// 业务逻辑省略
// .....

$this->assign('模板中的变量分配');
$tpl->display('default_1.tpl');

// 获取缓冲区中解析变量后的模板
$html = ob_get_contents();
// 清空并关闭缓冲区
ob_end_clean();

// 把html写入文件
// 业务逻辑省略 ...
```


  [1]: http://static.zybuluo.com/a5635268/xid8hjcrd0fkz0dgwclzgdy8/20170125103047647.png