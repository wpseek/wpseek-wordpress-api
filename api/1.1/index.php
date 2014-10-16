<?php
error_reporting(E_ALL);
@ini_set('display_errors', 1);

define('BULLET_ROOT', __DIR__);

require BULLET_ROOT . '/vendor/autoload.php';
require BULLET_ROOT . '/src/Bullet/App.php';
require BULLET_ROOT . '/helpers/apiHelper.php';
require BULLET_ROOT . '/helpers/Array2XML.php';

$apidata = array();

$app = new Bullet\App();

$app->path('wordpress', function($request) use($app) {
	
	$app->path('release', function($request) use($app) {
		$app->path('latest', function($request) use($app) {
			$app->get(function($request) use($app) {
				$i = new apiHelper();
				$apidata = $i->getLatestRelease();
			
				$http_code = ($i->is_error ? 404 : 200);
				
				$app->format('json', function() use($apidata, $app, $http_code) {
					return $app->response($http_code, $apidata)->header('Content-Type', 'application/json');
				});
				
				$app->format('xml', function($request) use($apidata, $app, $http_code) {					
					$xml = Array2XML::createXML('response', $apidata);
					return $app->response($http_code, $xml->saveXML())->header('Content-Type', 'application/xml');
				});
			});
		});
	});
	
	$app->path('function', function($request) use($app) {
		$app->path('info', function($request) use($app) {
			$app->param('slug', function($request, $func) use($app) {
				$i = new apiHelper();
				$apidata = $i->getFunction($func);
				
				$http_code = ($i->is_error ? 404 : 200);
				
				$app->format('json', function() use($apidata, $app, $http_code) {
					return $app->response($http_code, $apidata)->header('Content-Type', 'application/json');
				});
				
				$app->format('xml', function($request) use($apidata, $app, $http_code) {				
					$xml = Array2XML::createXML('response', $apidata);
					return $app->response($http_code, $xml->saveXML())->header('Content-Type', 'application/xml');
				});
			});
		});
		
		$app->path('related', function($request) use($app) {
			$app->param('slug', function($request, $func) use($app) {
				$i = new apiHelper();
				$apidata = $i->getRelatedFunctions($func, $request);
				
				$http_code = ($i->is_error ? 404 : 200);
				
				$app->format('json', function() use($apidata, $app, $http_code) {
					return $app->response($http_code, $apidata)->header('Content-Type', 'application/json');
				});
				
				$app->format('xml', function($request) use($apidata, $app, $http_code) {				
					$xml = Array2XML::createXML('response', $apidata);
					return $app->response($http_code, $xml->saveXML())->header('Content-Type', 'application/xml');
				});
			});
		});
	});
	
	$app->path('functions', function($request) use($app) {
		$app->get(function($request) use($app) {
			$i = new apiHelper();
			$apidata = $i->getFunctions();
			
			$http_code = ($i->is_error ? 404 : 200);
			
			$app->format('json', function() use($apidata, $app, $http_code) {
				return $app->response($http_code, $apidata)->header('Content-Type', 'application/json');
			});
			
			$app->format('xml', function($request) use($apidata, $app, $http_code) {				
				$xml = Array2XML::createXML('response', $apidata);
				return $app->response($http_code, $xml->saveXML())->header('Content-Type', 'application/xml');
			});
			
			$app->format('plain', function($request) use($apidata, $app, $http_code) {				
				return $app->response($http_code, implode("\n", $apidata['items']))->header('Content-Type', 'text/plain');
			});
		});
	});
	
	$app->path('topics', function($request) use($app) {
		$app->param('slug', function($request, $func) use($app) {
			$i = new apiHelper();
			$apidata = $i->getTopics($func, $request);
			
			$http_code = ($i->is_error ? 404 : 200);
			
			$app->format('json', function() use($apidata, $app, $http_code) {
				return $app->response($http_code, $apidata)->header('Content-Type', 'application/json');
			});
			
			$app->format('xml', function($request) use($apidata, $app, $http_code) {				
				$xml = Array2XML::createXML('response', $apidata);
				return $app->response($http_code, $xml->saveXML())->header('Content-Type', 'application/xml');
			});
		});
	});
});

$app->on('Exception', function(Request $request, Response $response, \Exception $e) use($app) {
	if( $request->format() === 'json' ) {
		$response->content(array(
			'exception' => get_class($e),
			'message' => $e->getMessage()
		));
	} else {
		$response->content($e);
	}
});

echo $app->run(new Bullet\Request());