<?php
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
$_HEADER = [];
class Router
{
	protected $routes = [];

	function add(string $method, string $route, callable $func): ?callable
	{
		return $this->routes[strtoupper($method)][$route] = $func;
	}

	function __invoke(...$args)
	{
		return $this->run(...$args);
	}

	function run(string $host): ?mixed
	{
		$this->loop = React\EventLoop\Factory::create();

		$this->loop->addSignal(SIGINT, function ($signal) {
			echo "Shutting down...", PHP_EOL;
		   exit(1);
		});

		$this->server = new React\Http\Server($this->loop, function (ServerRequestInterface $request) {
			$uri = $request->getUri()->getPath();
			$func = $this->match($request->getMethod(), $uri);
			echo $uri, " => ";

			global $_HEADER;
			$_GET = $request->getQueryParams();
			$_POST = $request->getParsedBody() ?? [];
			$_REQUEST = $_GET + $_POST;
			$headers = $request->getHeaders();
			$_HEADER = array_reduce(array_keys($headers), function($carry, $key) use ($headers) {
				return $carry + [strtoupper($key) => $headers[$key][0]];
			}, []);

			$func_name = is_string($func)?$func:"Closure";
			echo $func_name, "()";

			ob_start();
			$res = $func($request);
			$text = ob_get_clean();
			if (!is_numeric($res))
			{
				if ($res === true or $res === null)
					$res = 200;
				else
					$res = 404;
			}
			echo " => {$res}", PHP_EOL;
			if (DEBUG) var_dump($text);
			return new React\Http\Message\Response($res, ['Content-Type'=>'text/html'], $text);
		});
		$this->server->on('error', function (Exception $e) {
		    echo 'Error: ', $e->getMessage(), PHP_EOL;
		    // echo $e->getTraceAsString(), PHP_EOL;
		});

		$this->socket = new React\Socket\Server($host, $this->loop);
		$this->server->listen($this->socket);
		echo "Server running at http://{$host}\n";

		return $this->loop->run();
	}

	function match(string $method, string $uri)
	{
		$routes = array_merge($this->routes['ALL']??[], $this->routes[$method]??[]);
		foreach ($routes as $route => $func)
		{
			if (preg_match("#^{$route}$#", $uri))
				return $func;
		}
		return "do_404";
	}
}
$Router = new Router($_SERVER['SCRIPT_NAME']);

function do_404($request)
{
	include "404.php";
	return 404;
}
