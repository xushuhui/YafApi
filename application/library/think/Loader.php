<?php

namespace think;


class Loader
{
    protected static $instance = [];
    // 类名映射
    protected static $map = [];

    // 命名空间别名
    protected static $namespaceAlias = [];

    // PSR-4
    private static $prefixLengthsPsr4 = [];
    private static $prefixDirsPsr4    = [];
    private static $fallbackDirsPsr4  = [];

    // PSR-0
    private static $prefixesPsr0     = [];
    private static $fallbackDirsPsr0 = [];

    // 自动加载的文件
    private static $autoloadFiles = [];

    // 自动加载
    public static function autoload($class)
    {
        // 检测命名空间别名
        if (!empty(self::$namespaceAlias)) {
            $namespace = dirname($class);
            if (isset(self::$namespaceAlias[$namespace])) {
                $original = self::$namespaceAlias[$namespace] . '\\' . basename($class);
                if (class_exists($original)) {
                    return class_alias($original, $class, false);
                }
            }
        }

        if ($file = self::findFile($class)) {

            // Win环境严格区分大小写
            if (IS_WIN && pathinfo($file, PATHINFO_FILENAME) != pathinfo(realpath($file), PATHINFO_FILENAME)) {
                return false;
            }

            __include_file($file);
            return true;
        }
    }

    /**
     * 查找文件
     * @param $class
     * @return bool
     */
    private static function findFile($class)
    {
        if (!empty(self::$map[$class])) {
            // 类库映射
            return self::$map[$class];
        }

        // 查找 PSR-4
        $logicalPathPsr4 = strtr($class, '\\', DS) . EXT;

        $first = $class[0];
        if (isset(self::$prefixLengthsPsr4[$first])) {
            foreach (self::$prefixLengthsPsr4[$first] as $prefix => $length) {
                if (0 === strpos($class, $prefix)) {
                    foreach (self::$prefixDirsPsr4[$prefix] as $dir) {
                        if (is_file($file = $dir . DS . substr($logicalPathPsr4, $length))) {
                            return $file;
                        }
                    }
                }
            }
        }

        // 查找 PSR-4 fallback dirs
        foreach (self::$fallbackDirsPsr4 as $dir) {
            if (is_file($file = $dir . DS . $logicalPathPsr4)) {
                return $file;
            }
        }

        // 查找 PSR-0
        if (false !== $pos = strrpos($class, '\\')) {
            // namespaced class name
            $logicalPathPsr0 = substr($logicalPathPsr4, 0, $pos + 1)
            . strtr(substr($logicalPathPsr4, $pos + 1), '_', DS);
        } else {
            // PEAR-like class name
            $logicalPathPsr0 = strtr($class, '_', DS) . EXT;
        }

        if (isset(self::$prefixesPsr0[$first])) {
            foreach (self::$prefixesPsr0[$first] as $prefix => $dirs) {
                if (0 === strpos($class, $prefix)) {
                    foreach ($dirs as $dir) {
                        if (is_file($file = $dir . DS . $logicalPathPsr0)) {
                            return $file;
                        }
                    }
                }
            }
        }

        // 查找 PSR-0 fallback dirs
        foreach (self::$fallbackDirsPsr0 as $dir) {
            if (is_file($file = $dir . DS . $logicalPathPsr0)) {
                return $file;
            }
        }

        return self::$map[$class] = false;
    }

    // 注册classmap
    public static function addClassMap($class, $map = '')
    {
        if (is_array($class)) {
            self::$map = array_merge(self::$map, $class);
        } else {
            self::$map[$class] = $map;
        }
    }

    // 注册命名空间
    public static function addNamespace($namespace, $path = '')
    {
        if (is_array($namespace)) {
            foreach ($namespace as $prefix => $paths) {
                self::addPsr4($prefix . '\\', rtrim($paths, DS), true);
            }
        } else {
            self::addPsr4($namespace . '\\', rtrim($path, DS), true);
        }
    }

    // 添加Ps0空间
    private static function addPsr0($prefix, $paths, $prepend = false)
    {
        if (!$prefix) {
            if ($prepend) {
                self::$fallbackDirsPsr0 = array_merge(
                    (array) $paths,
                    self::$fallbackDirsPsr0
                );
            } else {
                self::$fallbackDirsPsr0 = array_merge(
                    self::$fallbackDirsPsr0,
                    (array) $paths
                );
            }

            return;
        }

        $first = $prefix[0];
        if (!isset(self::$prefixesPsr0[$first][$prefix])) {
            self::$prefixesPsr0[$first][$prefix] = (array) $paths;

            return;
        }
        if ($prepend) {
            self::$prefixesPsr0[$first][$prefix] = array_merge(
                (array) $paths,
                self::$prefixesPsr0[$first][$prefix]
            );
        } else {
            self::$prefixesPsr0[$first][$prefix] = array_merge(
                self::$prefixesPsr0[$first][$prefix],
                (array) $paths
            );
        }
    }

    // 添加Psr4空间
    private static function addPsr4($prefix, $paths, $prepend = false)
    {
        if (!$prefix) {
            // Register directories for the root namespace.
            if ($prepend) {
                self::$fallbackDirsPsr4 = array_merge(
                    (array) $paths,
                    self::$fallbackDirsPsr4
                );
            } else {
                self::$fallbackDirsPsr4 = array_merge(
                    self::$fallbackDirsPsr4,
                    (array) $paths
                );
            }
        } elseif (!isset(self::$prefixDirsPsr4[$prefix])) {
            // Register directories for a new namespace.
            $length = strlen($prefix);
            if ('\\' !== $prefix[$length - 1]) {
                throw new Exception("A non-empty PSR-4 prefix must end with a namespace separator.");
            }
            self::$prefixLengthsPsr4[$prefix[0]][$prefix] = $length;
            self::$prefixDirsPsr4[$prefix]                = (array) $paths;
        } elseif ($prepend) {
            // Prepend directories for an already registered namespace.
            self::$prefixDirsPsr4[$prefix] = array_merge(
                (array) $paths,
                self::$prefixDirsPsr4[$prefix]
            );
        } else {
            // Append directories for an already registered namespace.
            self::$prefixDirsPsr4[$prefix] = array_merge(
                self::$prefixDirsPsr4[$prefix],
                (array) $paths
            );
        }
    }

    // 注册命名空间别名
    public static function addNamespaceAlias($namespace, $original = '')
    {
        if (is_array($namespace)) {
            self::$namespaceAlias = array_merge(self::$namespaceAlias, $namespace);
        } else {
            self::$namespaceAlias[$namespace] = $original;
        }
    }

    // 注册自动加载机制
    public static function register($autoload = '')
    {
        // Composer自动加载支持
        if (is_dir(VENDOR_PATH . 'composer')) {
            self::registerComposerLoader();
        }

        // 自动加载extend目录
        self::$fallbackDirsPsr4[] = rtrim(EXTEND_PATH, DS);
    }

    // 注册composer自动加载
    private static function registerComposerLoader()
    {
        if (is_file(VENDOR_PATH . 'composer/autoload_namespaces.php')) {
            $map = require VENDOR_PATH . 'composer/autoload_namespaces.php';
            foreach ($map as $namespace => $path) {
                self::addPsr0($namespace, $path);
            }
        }

        if (is_file(VENDOR_PATH . 'composer/autoload_psr4.php')) {
            $map = require VENDOR_PATH . 'composer/autoload_psr4.php';
            foreach ($map as $namespace => $path) {
                self::addPsr4($namespace, $path);
            }
        }

        if (is_file(VENDOR_PATH . 'composer/autoload_classmap.php')) {
            $classMap = require VENDOR_PATH . 'composer/autoload_classmap.php';
            if ($classMap) {
                self::addClassMap($classMap);
            }
        }

        if (is_file(VENDOR_PATH . 'composer/autoload_files.php')) {
            $includeFiles = require VENDOR_PATH . 'composer/autoload_files.php';
            foreach ($includeFiles as $fileIdentifier => $file) {
                if (empty(self::$autoloadFiles[$fileIdentifier])) {
                    __require_file($file);
                    self::$autoloadFiles[$fileIdentifier] = true;
                }
            }
        }
    }





    /**
     * 实例化验证类 格式：[模块名/]验证器名
     * @param string $name         资源地址
     * @param string $layer        验证层名称
     * @param bool   $appendSuffix 是否添加类名后缀
     * @param string $common       公共模块名
     * @return Object|false
     * @throws Exception
     */
    public static function validate($name = '', $layer = 'validate', $appendSuffix = false, $common = 'common')
    {
        $name = $name ?: Config::get('default_validate');
        if (empty($name)) {
            return new Validate;
        }
        $guid = $name . $layer;
        if (isset(self::$instance[$guid])) {
            return self::$instance[$guid];
        }
        if (false !== strpos($name, '\\')) {
            $class  = $name;
            $module = Request::instance()->module();
        } else {
            if (strpos($name, '/')) {
                list($module, $name) = explode('/', $name);
            } else {
                $module = Request::instance()->module();
            }
            $class = self::parseClass($module, $layer, $name, $appendSuffix);
        }
        if (class_exists($class)) {
            $validate = new $class;
        } else {
            $class = str_replace('\\' . $module . '\\', '\\' . $common . '\\', $class);
            if (class_exists($class)) {
                $validate = new $class;
            } else {
                throw new Exception('class not exists:' . $class, $class);
            }
        }
        self::$instance[$guid] = $validate;
        return $validate;
    }



    /**
     * 字符串命名风格转换
     * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
     * @param string  $name 字符串
     * @param integer $type 转换类型
     * @param bool    $ucfirst 首字母是否大写（驼峰规则）
     * @return string
     */
    public static function parseName($name, $type = 0, $ucfirst = true)
    {
        if ($type) {
            $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $name);
            return $ucfirst ? ucfirst($name) : lcfirst($name);
        } else {
            return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
        }
    }

    /**
     * 解析应用类的类名
     * @param string $module 模块名
     * @param string $layer  层名 controller model ...
     * @param string $name   类名
     * @param bool   $appendSuffix
     * @return string
     */
    public static function parseClass($module, $layer, $name, $appendSuffix = false)
    {
        $name  = str_replace(['/', '.'], '\\', $name);
        $array = explode('\\', $name);
        $class = self::parseName(array_pop($array), 1) . (App::$suffix || $appendSuffix ? ucfirst($layer) : '');
        $path  = $array ? implode('\\', $array) . '\\' : '';
        return App::$namespace . '\\' . ($module ? $module . '\\' : '') . $layer . '\\' . $path . $class;
    }

    /**
     * 初始化类的实例
     * @return void
     */
    public static function clearInstance()
    {
        self::$instance = [];
    }
}

/**
 * 作用范围隔离
 *
 * @param $file
 * @return mixed
 */
function __include_file($file)
{
    return include $file;
}

function __require_file($file)
{
    return require $file;
}
