<?php

use Phalcon\Mvc\View;
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Url as UrlResolver;

class Services extends FactoryDefault
{
    public function __construct($config)
    {
        parent::__construct();
        
        $this->setShared('config', $config);
        
        $this->bindServices();
    }
    
    protected function bindServices()
    {
        $reflection = new \ReflectionObject($this);
        $methods = $reflection->getMethods();
        
        foreach ($methods as $method) 
        {
            if ((strlen($method->name) > 4) && (strpos($method->name, 'init') === 0)) 
            {
                $this->set(lcfirst(substr($method->name, 4)), $method->getClosure($this));
            }
        }
    }
    
    protected function initUrl()
    {
        $url = new UrlResolver();
        
        $url->setBaseUri($this->get('config')->application->baseUri);
        
        return $url;
    }
    
    protected function initRouter()
    {
        $router = require APP_PATH . 'protected/Config/routes.php';
        
        return $router;
    }
    
    protected function initView()
    {
        $view = new View();

        return $view;
    }

    protected function initDb()
    {
        $config = (array) $this->get('config')->db;
        
        $dbClass = 'Phalcon\Db\Adapter\Pdo\\' . $config['adapter'];
        
        unset($config['adapter']);

        return new $dbClass($config);
    }
    
    protected function initDbmysql()
    {
        $config = (array) $this->get('config')->dbmysql;
        
        $dbClass = 'Phalcon\Db\Adapter\Pdo\Mysql';
        
        unset($config['adapter']);

        return new $dbClass($config);
    }
}
