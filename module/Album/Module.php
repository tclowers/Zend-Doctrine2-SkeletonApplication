<?php
namespace Album;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Album\Model\Album;
use Album\Model\AlbumTable;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

class Module implements AutoloaderProviderInterface, ConfigProviderInterface {
	// use composer.json to manage autoload config
	public function getAutoloaderConfig() { }

	/*public function getAutoloaderConfig() {
		return array (
			'Zend\Loader\ClassMapAutoloader' => array (
				__DIR__ . 'autoload_classmap.php',
			),
			'Zend\Loader\StandardAutoloader' => array (
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}*/

	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}

	// At present we don't need this since the native Zend 2 Model
	// was swapped out for Docrine2's Entity, and that dir no longer exists
	// seems like there may be a need for another service config in the future
	/*public function getServiceConfig() {
		return array(
			'factories' => array(
				'Album\Model\AlbumTable' => function($sm) {
					$tableGateway = $sm->get('AlbumTableGateway');
					$table = new AlbumTable($tableGateway);
					return $table;
				},
				'AlbumTableGateway' => function($sm) {
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$resultSetPrototype = new ResultSet();
					$resultSetPrototype->setArrayObjectPrototype(new Album());
					return new TableGateway('album', $dbAdapter, null, $resultSetPrototype);
				},
			),
		);
	}*/

}