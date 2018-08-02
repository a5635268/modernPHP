# PHPstorm使用攻略

标签（空格分隔）： 行软使用

---

## 快捷键

### 搜索,查找,替换

    CTRL + N  类名查找
    ctrl + f 当前文件内容查找
    ctrl + shift + n 查找文件
    ctrl + F12 在当前类文件里快速查找方法
    CTRL + SHIFT + ALT + N   全项目函数名查找
    ctrl + r 查找替换
    Ctrl + Shift + F 全局查找
    Ctrl + Shift + R 全局查找替换
    Alt + F1 + 回车 当前文档追溯
     

### 跳转

    ctrl+b(鼠标左键) 跳到变量申明处
    F11 添加/取消书签
    Ctrl + F11 书签标记列表
    Ctrl +＃[0-9] 转到编号书签
    Shift + F11 显示书签
    ALT+ ↑/↓  在方法间快速移动定位
    ctrl+g 跳转行

  
### 操作

    ctrl+alt+l 重新格式化代码  (设置代码样式：File -> Settings -> Code Style ->PHP)
    ctrl+shift+u 字母大小写转换
    CTRL+ALT ←/→  返回上次编辑的位置
    ALT+ ←/→  标签切换
    shift+F6 重命名 (在侧边栏)
    ctrl+d 复制当前行
    ctrl+y 删除当前行
    ctrl+/ 行注释
    ctrl+shift+/ 块注释
    ctrl+x 剪切行
    ctrl+shift+v 复制多个文本
    Ctrl+E。可以快速打开你最近编辑的文件。
    按Ctrl + F12文件结构弹出
    ctrl + shift + up: 行移动
    ctrl + '-/+',ctrl + shift + '-/+': 可以折叠项目中的任何代码块，包括htm中的任意nodetype=3的元素，function,或对象直接量等等。它不是选中折叠，而是自动识别折叠。

###查看

    ctrl+q 查看代码注释
    Alt+Shift+C，可以看到项目最近的修改。这就是它的版本集成功能特性
    F4 查看源码 在侧边栏用
    ctrl+[] 匹配 {}[]
    ctrl+shift+]/[ 选中块代码
    ctrl+shift+i 查看声明处
  
###其他

    Alt + F1键 选择当前文件或符号中的任何视图 (重要)
    alt + '7': 显示当前的函数结构。
    alt + 1:file
    
    

## 其它使用技巧

  1.本地修改记录：在项目名称上右键，点击Local History | Show History。你可以看到项目文件各个历史版本；Alt+Shift+C，可以看到项目最近的修改。这就是它的版本集成功能特性。

  2.最近编辑：ctrl+e

  3.代码分界线：打开File | Setting | Editor，选择Appearance下面的Show Method Separators。它会将你的代码按方法，用灰色线框进行智能分割。你还可以使用：alt+↑或↓，在方法之间进行跳转。

  4.代码输入提示：IDE基于系统函数库，关联项目文件的方法名，当前文件内容，内部文件路径（使用【Ctrl+空格】补全）进行代码提示。

  5.粘贴板：使用Ctrl+Shift+V。可以选择需要粘贴的最近内容。

  6.皮肤切换：Ctrl+反引号，可以快速切换皮肤。

  7.快速查看样式：在HTML标签上进行右键，选择Show Applied Styles For Tag。可以快速查看该标签应用的样式。类似于前端开发工程师常用的firebug。

  8.代码片段:Live Temp-> \$VISION\$ -> http://jingyan.baidu.com/article/8275fc86badd6346a03cf6aa.html

  9.alt+6 可以查看添加了//TODO注释的代码片段,一般我们在开发过程中由于时间或者各方面的时间来不及完成的代码,往往会先将逻辑写出来,实现留待以后添加的内容都会加上//TODO注释

 10.Ctrl + Shift + A 是一个比较重要的快捷键，主要用于寻找PHPStorm IDE内所有的动作。
 
 11.选中函数shift+f1就直接看手册了
 
 12.phpStorm 配置关联php手册 http://www.cnblogs.com/keygle/p/3281395.html
 
 13.phpStorm 配置FTP http://www.cnblogs.com/jikey/p/3486621.html
 
 14.phpstorm 开启远程debug http://www.cnblogs.com/xuxiang/p/4077099.html
 


### PhpStorm更换主题，调整背景和字体颜色

从这个网站（http://phpstorm-themes.com/）下载各类主题的xml文件，然后将文件放到phpStorm的文件夹中，比如：C:\Users\USERNAME\.WebIde90\config\colors(最好通过导出数据查看当前的配置 File -> Export sttings)，如果此时正好开着PHPStorm，那么需要重启一下该IDE，以便载入改成你添加的主题，在IDE的左上角菜单中，选择File > Settings > Editor > Colors & Fonts ，然后在下拉菜单中，选中你刚才添加的主题名称，保存设置后，就大功告成了。

### 备份地址和插件地址更改,可放入同步盘内

![20131108115533137.jpg-14.2kB][1]

![20131108121239772.jpg-15.1kB][2]



### 创建远程项目

http://wwwquan.com/show-66-121-1.html

### windows下集成svn

http://www.php-class.com/article/info-1400639830.html
https://sliksvn.com/download/

  [1]: http://static.zybuluo.com/a5635268/kxz7t8avh4ef2tmpiofi2eju/20131108115533137.jpg
  [2]: http://static.zybuluo.com/a5635268/gg6xoymnpt44uub0f5bsfchk/20131108121239772.jpg