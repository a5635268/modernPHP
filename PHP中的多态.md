# PHP中的多态


多态的概念一般是强类型语言来谈的,因为强类型语言它必须要声明参数类型,比如一个手电筒对象的打开方法其参数申明了只能是蓝光,就不能传其他光。但可以用父类渲染的方式使其多态，比如声明一个光的父类，让其它颜色的光都继承自这个光的父类，申明其参数为父类光，然后传光的任何子类都可以；这就是强类型的多态；

但php是弱类型的动态语言，不检测参数类型，传什么都可以；**但在php5.3版本可以声明参数为某对象；当声明参数为某类实例化后的对象时，就得用父类渲染的方式令其多态；**

```php
<?php
class Glass { 
	public function display() { 
	} 
} 

class RedGlass extends Glass{ 
	public function display() { 
		echo '红光照耀<br />'; 
	} 
} 
class BlueGlass extends Glass { 
	public function display() { 
		echo '蓝光照耀<br />'; 
	} 
} 
class GreenGlass extends Glass { 
	public function display() { 
		echo '绿光照耀<br />'; 
	} 
} 
class Pig { 
	public function display() { 
		echo '八戒下凡,哼哼坠地!<br />'; 
	} 
} 

class Light { 
	public function ons(Glass $g) { 
	//这里的$g对象必须是由Glass或Glass的子类实例化而来;本用法只能在php5.3里面使用;   
	//如果在遵循了PSR规范的框架里面,这里的Glass类同样可以写成这样的模式 Home\Controller\Glass $g
	//用玻璃渲染颜色 
	$g->display(); 
	} 
} 
// 造手电筒 
$light = new Light(); 
// 造红玻璃 
$red = new RedGlass(); 
// 造蓝玻璃 
$blue = new BlueGlass(); 

// 红灯亮 
$light->ons($red); //把对象传进方法内;

// 蓝灯亮 
$light->ons($blue); 

// 猪八戒降生 
$pig = new Pig(); //$pig不是由Glass或Glass的子类实例化,所以会报错了;
$light->ons($pig); 	
?>
```



