<?php
namespace X\Service\XRouter;
use X\Core\Service\XService;
use X\Service\XRouter\Router\RouterInterface;
use X\Service\XRouter\Router\MapUrlRouter;
/**
 * @author michael
 */
class Service extends XService {
    /** 服务名称 */
    protected static $serviceName = 'XRouter';
    
    /** @var RouterInterface */
    private $router = null;
    /** @var CurrentRequest */
    private $request = null;
    
    /**
     * {@inheritDoc}
     * @see \X\Core\Service\XService::start()
     */
    public function start() {
        parent::start();
        
        $this->request = CurrentRequest::buildFromCurrentRequest();
        
        $config = $this->getConfiguration();
        $router = $config->get('router', MapUrlRouter::class);
        $router = new $router($this);
        
        if ( !($router instanceof RouterInterface) ) {
            throw new \Exception("XRequest router must be instance of `".RouterInterface::class.'`');
        }
        $this->router = $router;
        
        $this->setupRequestParams();
    }
    
    /** @return void */
    private function setupRequestParams() {
        $result = $this->router->route($this->request->getUrl());
        parse_str($result, $params);
        $_GET = array_merge($_GET, $params);
    }
    
    /** @return \X\Service\XRequest\RouterInterface */
    public function getRouter () {
        return $this->router;
    }
}