<?php
/**
 * vim: et ts=4 sts=4 sw=4
 *
 * Smarty 派生类
 * 只适用于 PHP5
 *
 * @author XTG <xutiangong@eyou.net>
 * @copyright 2006 eYou Corporation.
 * @version eyou_smarty.class.php v1.00 2007/02/01
 */

/**
 * path configure
 */
require_once PATH_PROJ_LIB.'smarty-3.1.27/libs/Smarty.class.php';
spl_autoload_register("__autoload");
/**
 * Smarty 派生类
 * 只适用于 PHP5
 *
 * @package SMARTY
 * @see Smarty
 */
class Mmail_smarty extends Smarty
{
    /**
     * 每套模板编译之后的id,每套模板的此id不同,就可以实现多套模板的互相不干扰
     *
     * @var string
     */
    protected $__tpl_compile_id = '';

    /**
     * 如果执行的模板目录不存在的话,则自动取得默认的模板进行display
     *
     * @var boolean
     */
    protected $__is_auto_display_default = false;

    /**
     * 默认的模板目录,如果指定的模板目录($this->template_dir)不存在想要的模板,则自动到这个目录去找默认模板
     * 可以是一个目录,也可以是一个目录数组,前面的元素具有高优先级,在搜寻模板的时候,依次向后搜寻
     *
     * @var mixed
     */
    protected $__default_template_dir = '';


    /**
     * 构造函数,处理初始化的参数设置
     *
     * @param string $tpl_dir 模板目录,此参数可以在此构造函数中给定,也可以用set_tpl_path()设置
     * @param string $com_dir 模板编译目录,此参数可以在此构造函数中给定,也可以用set_compile_path()设置
     * @param boolean $display_default 是否在未找到指定模板的情况下自动执行默认模板
     * @param mixed $default_tpl_dir 默认模板目录
     *
     * @return void
     */
    public function __construct($tpl_dir=null, $com_dir=null, $com_id=null, $display_default=false, $default_tpl_dir=null)
    {
        if (null !== $tpl_dir) {
            $this->template_dir = $tpl_dir; //smarty: 模板目录
        }
        if (null !== $com_id) {
            $this->__tpl_compile_id = $com_id; //模板编译id
        }
        if (null !== $com_dir) {
            $this->compile_dir = $com_dir; //smarty: 模板编译目录
        }
        if (null !== $default_tpl_dir) {
            $this->__default_template_dir = $default_tpl_dir; //默认模板目录
        }
        $this->left_delimiter = '{{'; //smarty: 识别的左边界符
        $this->right_delimiter = '}}'; //smarty: 识别的右边界符
        $this->use_sub_dirs = true; //smarty: 是否建立子目录
        $this->compile_check = true; //smarty: 是否检查模板修改,以进行重新编译
        $this->__is_auto_display_default = (boolean) $display_default; //是否在未找到指定模板的情况下自动执行默认模板

        parent::Smarty();
    }

    /**
     * 设置模板目录
     *
     * @param string $tpl_dir 模板目录
     *
     * @return void
     */
    public function set_tpl_path($tpl_dir)
    {
        $this->template_dir = $tpl_dir;
    }

    /**
     * 设置模板目录
     *
     * @param string $com_dir 模板目录
     *
     * @return void
     */
    public function set_compile_path($com_dir)
    {
        $this->compile_dir = $com_dir;
    }

    /**
     * 设置模板编译id
     *
     * @param string $com_id 模板编译id
     *
     * @return void
     */
    public function set_compile_id($com_id)
    {
        $this->__tpl_compile_id = $com_id;
    }

    /**
     * 设置默认模板
     *
     * @param boolean $display_default 是否在未找到指定模板的情况下自动执行默认模板
     * @param mixed $default_tpl_dir 默认模板目录
     *
     * @return void
     */
    public function set_display_default($display_default=false, $default_tpl_dir=null)
    {
        $this->__is_auto_display_default = (boolean) $display_default;
        if (null !== $default_tpl_dir) {
            $this->__default_template_dir = $default_tpl_dir;
        }
    }

    /**
     * 设置调试模式
     *
     * @param boolean $debug 是否执行调试模式
     * @param string $debug_ctrl 使用什么字符串进行debug的url控制
     *
     * @return void
     */
    public function set_debug($debug=true, $debug_ctrl=null)
    {
        $this->debugging = (boolean) $debug;
        if (null !== $debug_ctrl) {
            $this->debugging_ctrl = $debug_ctrl;
        }
    }

    /**
     * 执行输出模板
     *
     * @param string $resource_name 模板名字
     * @param string $cache_id 缓存id
     * @param string $compile_id 模板编译id
     *
     * @return void
     *
     * @see Smarty::display()
     */
    public function display($resource_name, $cache_id=null, $compile_id=null)
    {
        //处理在没有找到指定模板的情况下,自动获取默认模板
        if ($this->__is_auto_display_default && !file_exists($this->template_dir.$resource_name)) {
            $array_default_tpl_dir = (array) $this->__default_template_dir;
            foreach ($array_default_tpl_dir as $this_default_tpl_dir) {
                //如果在默认模板目录下找到了指定的模板,则把此默认模板目录设置为当前模板目录
                if (file_exists($this_default_tpl_dir.$resource_name)) {
                    $this->set_tpl_path($this_default_tpl_dir);
                    break;
                }
            }
        }

        if (null !== $compile_id) {
            $this->__tpl_compile_id = $compile_id;
        } else {
            $compile_id = $this->__tpl_compile_id;
        }
        return parent::display($resource_name, $cache_id, $compile_id);
    }
}


?>
