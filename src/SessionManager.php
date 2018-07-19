<?php
namespace Itxiao6\Session;
use Itxiao6\Session\Tools\Config;
use Itxiao6\Session\Tools\Http;
use Itxiao6\Session\Tools\Session;
use Itxiao6\Session\Tools\Swoole;

/**
 * Class SessionManager
 * @package Itxiao6\Session
 */
class SessionManager
{
    /**
     * session 构造器配置
     * @var null|Config
     */
    protected $config = null;
    /**
     * HTTP 操作
     * @var null | Http
     */
    protected $http = null;
    /**
     * session 实例
     * @var null|Session
     */
    protected $session = null;
    /**
     * 驱动
     * @var null|\Doctrine\Common\Cache\Cache
     */
    protected $deiver = null;
    /**
     * session swoole 模式 实例
     * @var null|SessionManager
     */
    protected static $session_swoole_interface = null;
    /**
     * session 普通模式实例
     * @var null
     */
    protected static $session_interface = null;

    /**
     * 单例获取 swoole 模式的实例
     * @param \swoole_http_request $request
     * @param \swoole_http_response $response
     * @return SessionManager|null
     * @throws \Exception
     */
    public static function getSwooleSessionInterface($request,$response)
    {
        /**
         * 判断是否已经实例化过session
         */
        if(self::$session_swoole_interface){
            self::$session_swoole_interface = new static($request,$response);
        }
        return self::$session_swoole_interface;
    }

    /**
     * 单例获取
     * @return SessionManager|null
     * @throws \Exception
     */
    public static function getSessionInterface()
    {
        /**
         * 判断是否已经实例化过session
         */
        if(self::$session_interface){
            self::$session_interface = new static();
        }
        return self::$session_interface;
    }

    /**
     * SessionManager constructor.
     * @param null|\swoole_http_request $request
     * @param null|\swoole_http_response $response
     * @throws \Exception
     */
    protected function __construct($request = null,$response = null)
    {
        /**
         * 判断是否为SWOOLE
         */
        if($request != null && $response != null){
            /**
             * 判断参数是否合法
             */
            if($request instanceof \swoole_http_request && $response instanceof \swoole_http_response){
                /**
                 * 实例化swoole
                 */
                $this -> http = (new Swoole()) -> request($request) -> response($response);
            }else{
                throw new \Exception('Request or Response invalid');
            }
        }else{
            /**
             * 实例化php-fpm模式的工具
             */
            $this -> http = (new Http());
        }
        /**
         * 实例化配置实例
         */
        $this -> config = new Config();
//        /**
//         * 获取请求内的session id
//         */
//        $session_id = isset($request -> RawRequest() -> cookie[$this -> config('session_name')])?$request -> RawRequest() -> cookie[$this -> config('session_name')]:false;
//        /**
//         * 判断是否需要重置 session id
//         */
//        if(in_array($session_id,[null,false,''])){
//            $this -> session_id(self::getARandLetter(30));
//        }else{
//            $this -> session_id($session_id);
//        }
    }

    /**
     * 配置操作
     * @return Config|null
     */
    public function config(){return $this -> config;}

    /**
     * http
     * @return Http|null
     */
    public function http(){return $this -> http;}

    /**
     * 会话操作
     * @return Session|null
     */
    public function session(){return $this -> session;}

    /**
     * 获取驱动
     * @return \Doctrine\Common\Cache\Cache|null
     */
    public function get_driver(){return $this -> deiver;}

    /**
     * 设置驱动
     * @param \Doctrine\Common\Cache\Cache $deiver
     * @return $this
     */
    public function set_deiver(\Doctrine\Common\Cache\Cache $deiver){$this -> deiver = $deiver;return $this;}

    /**
     * 启动会话
     * @return mixed
     * @throws \Exception
     */
    public function start()
    {
        /**
         * 启动会话
         */
        $this -> session = new Session($this);
        return $this;
    }

}