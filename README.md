parsermodule
============
You need to add Zend library in one level with inc folder and add thi code to modules/index.php 

    set_include_path(implode(PATH_SEPARATOR, array(
        realpath('../library'),//the path
        get_include_path(),
    )));
    require "Zend/Loader/Autoloader.php";
    $autoloader = Zend_Loader_Autoloader::getInstance();
