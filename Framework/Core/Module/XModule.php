<?php
namespace X\Core\Module;
use X\Core\X;
use X\Core\Component\ConfigurationArray;
use X\Core\Component\ClassHelper;
/**
 * 模块基础类
 * @author Michael Luthor <michaelluthor@163.com>
 */
abstract class XModule {
    /** 
     * 保存当前模块的配置数据
     * @var ConfigurationArray
     * */
    private $configuration = null;
    
    /**
     * 模块执行方法，在实现类中实现
     * @param array $parameters 运行参数
     * @return mixed
     */
    abstract public function run($parameters=array());
    
    /**
     * 初始化模块
     * @param array $config 配置信息
     * @return void
     */
    public function __construct( $config=array() ) {
        $this->configuration = new ConfigurationArray();
        $this->configuration->setValues($config);
        
        $this->onLoaded();
    }
    
    /** 
     * 通过模块类静态获取模块的快捷方式
     * @return self 
     * */
    public static function getModule() {
        return X::system()->getModuleManager()->get(self::getModuleName());
    }
    
    /**
     * 当模块被加载后执行
     * @return boolean
     */
    protected function onLoaded() {
        return true;
    }
    
    /**
     * 获取当前模块名称
     * @return string
     */
    public function getName() {
        $className = get_class($this);
        $className = explode('\\', $className);
        return $className[count($className)-2];
    }
    
    /**
     * 获取当前模块名称的静态方法
     * @return string
     */
    public static function getModuleName() {
        $className = get_called_class();
        $className = explode('\\', $className);
        return $className[count($className)-2];
    }
    
    /**
     * 根据路径获取相对于当前模块的绝对路径
     * @param string $path 相对路径
     * @return string
     */
    public function getPath( $path=null ) {
        return ClassHelper::getPathRelatedClass($this, $path);
    }
    
    /**
     * 获取当前模块的配置
     * @return \X\Core\Component\ConfigurationArray
     */
    public function getConfiguration( ) {
        return $this->configuration;
    }
}