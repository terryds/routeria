# Routeria

Routeria is a lightweight and easy-to-use routing component.

## Installing
Routeria installation using Composer  
```
composer require terrydjony/routeria ~1,0
```

## Usage

The installed Routeria and all of the components is in the `vendor` folder.  
In order to use it, you just need to require the autoload.  
And, you need to load the namespace using `use` keyword.  

```php
require_once __DIR__ . '/vendor/autoload.php';
```


### Configuration (.htaccess)

Before using Routeria, you need to turn your rewrite engine on and add rules so any requests to non-existing directory or filename will be rewritten to index.php.
```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [QSA,L]
```

### Simple Callback Routing

For a simple callback route, you just need to use `Routeria` class which belongs to the `Routeria` namespace.  
The Request component of Symfony HttpFoundation is required to tell the request path to the router.
```php
use Symfony\Component\HttpFoundation\Request;
use Routeria\Routeria;

$request = Request::createFromGlobals();
$router = new Routeria;
$router->get('/', function() { echo 'Hello World';});

$router->route($request->getPathInfo(), $request->getMethod());
```

Don't forget to write line `->route($request->getPathInfo(), $request->getMethod());` to make it work

### Using Named Parameters

```php
use Symfony\Component\HttpFoundation\Request;
use Routeria\Routeria;

$request = Request::createFromGlobals();
$router = new Routeria;
$callback = function($fname, $lname) {
  echo "Hello $fname $lname. Nice to meet ya!";
};
$router->get('/greet/{fname:alpha}/{lname:alpha}', $callback);

$router->route($request->getPathInfo(), $request->getMethod());
```

The order of parameters in the callback doesn't matter.  
You just need to specify all the necessary variables.  
  
There are six placeholders available,  
`INT` for integers (regex: [0-9]+)  
`ALPHA` for alphabets (regex: [a-zA-Z_-]+)  
`ALNUM` for alphanumeric characters (regex: [a-zA-Z0-9_-]+)  
`HEX` for hexadecimals (regex: [0-9A-F]+)  
`ALL` for all characters (regex: .+)  
`WORD` is an alias for `ALPHA`


### Routing with specific HTTP Method

You can also perform other http methods routing easily. (even the custom one)
```php
use Symfony\Component\HttpFoundation\Request;
use Routeria\Routeria;

$request = Request::createFromGlobals();
$router = new Routeria;
$router->get('/', function() { echo 'HTTP METHOD : GET';});
$router->post('/', function() { echo 'HTTP METHOD : POST';});
$router->put('/', function() { echo 'HTTP METHOD : PUT';});
$router->delete('/', function() { echo 'HTTP METHOD : DELETE';});
$router->add('/', function() { echo 'HTTP METHOD : CUSTOM';}, 'CUSTOM');

$router->route($request->getPathInfo(), $request->getMethod());
```

Different method, different route.

### Dispatch Controller

You can also dispatch a controller using Routeria.

```php
use Symfony\Component\HttpFoundation\Request;
use Routeria\Routeria;

class User {
  public function getInfo($id, $name) {
    echo 'Hello ' . $name . ' ID: ' . $id;
  }
}

$request = Request::createFromGlobals();
$router = new Routeria;
$router->get('/user/{name:alpha}/{id:int}', 'User::getInfo');
$router->route($request->getPathInfo(), $request->getMethod());
```

If you go to '/user/terry/35', the router will dispatch the getInfo method so it prints 'Hello terry ID: 35'.  
Don't forget to specify the namespace if the class has.

### Converting arguments

```php
use Symfony\Component\HttpFoundation\Request;
use Routeria\Routeria;

$request = Request::createFromGlobals();
$router = new Routeria;
$router->get('/posts/{title:alpha}', function($title) { echo '<h1>'.$title.'</h1>';})
    ->convert(function(title) {
      return ucwords(str_replace('-', ' ', $title));
    });
$router->route($request->getPathInfo(), $request->getMethod());
```

The converter in this example changes all hypens into spaces in the title argument.  
So, if you go to '/posts/lorem-ipsum-dolor-sit-amet', it will print `<h1>lorem ipsum dolor sit amet</h1>`.  


### Custom route collection

You can define your own route collection by implementing `RouteProviderInterface`.
```php
use Symfony\Component\HttpFoundation\Request;
use Routeria\Routeria;
use Routeria\RouteCollection;
use Routeria\ControllerRoute;
use Routeria\RouteProviderInterface;

class BlogCollection implements RouteProviderInterface {
  public function register(RouteCollection $collection) {
    $blogRoutes = array(
      'index' => new ControllerRoute('/','Blog::index','GET'),
      'post' => new ControllerRoute('/{id:int}/{title:alnum}','Blog::showPost','GET'),
      'page' => new ControllerRoute('/page/{title:alpha}','Blog::showPage','GET')
      );

    $collection->addRoutes($blogRoutes);
  }
}

$request = Request::createFromGlobals();
$router = new Routeria;

$collection = new BlogCollection;
$router->register($collection);
$router->route($request->getPathInfo(), $request->getMethod());
```
You need your own blog controller to make it work.


## Contribute to this library

Please contribute to this project by forking it, make good commits and then perform a pull request.  
Thanks for your support.

