<?php
/*

Created by Nigamanth Srivatsan
MIT License, https://github.com/nigamanthsrivatsan/easy-php-router

*/

// simple router first
class SimpleRouter {
    /*
     * SimpleRouter, a simple router class for your PHP application
     * string request: the server's request
     * string parsed_url: the parsed URI of the request
     * string method: the method of the request (GET, POST, HEAD)
     * string requested_path: the path of the request 
    */
    public $request = $_SERVER['REQUEST_URI'];
    public $parsed_url = parse_url($_SERVER['REQUEST_URI']);
    public $method = $_SERVER['REQUEST_METHOD'];
    public $requested_path = $parsed_url['path'];

    function __construct(array $routes = null) {
        /*
         * Initializes the router with the application's required routes
         * For formatting/documentation please read https://github.com/nigamanthsrivatsan/easy-php-router
         * array $routes: the application's routes
         */
        if ($routes == null) {
            // no routes were given
            throw new Exception("When initializing the SimpleRouter class, you must provide routes for your application's router.");
        } else if (count($routes) == 0) {
            // an empty route class was given
            throw new Exception("When initializing the SimpleRouter class, you must provide at-least 1 routes for your application's router.");
        }

        $foundUrl = false;

        foreach ($routes as $url => $route) {
            if ($this->request == $url) {
                require __DIR__ . $route;
                $foundUrl = true;
                break;
            }
        }

        if (!$foundUrl) {
            http_response_code(404);
            throw new Exception("The requested route was not found in your routing file.");
        }
    }
}

// complex router

class Router {
    public $request = $_SERVER['REQUEST_URI'];
    public $parsed_url = parse_url($_SERVER['REQUEST_URI']);
    public $method = $_SERVER['REQUEST_METHOD'];
    public $requested_path = $parsed_url['path'];
    
    function __construct(array $routes = null) {
        set_error_handler(function ($error_type) {

            switch ($error_type) {
                case 2 || E_WARNING || 8 || E_NOTICE:
                    $this->php_warning++;
                    break;
            }

        }, E_ALL);

        if ($routes == null) {
            throw new Exception("When initializing the Router class, you must provide routes for your application's router.");
        } else if (count($routes) == 0) {
            throw new Exception("When initializing the Router class, you must provide at-least 1 routes for your application's router.");
        }

        $_404_exists = $this->checkRouteFunction($routes, '404');
        if (!$_404_exists) {
            echo "Warning! You have not setup a 404 route for your application, the router will still run however you will receive a lousy 404 page.\nWe highly recommend setting up your own 404 page.";
        }

        foreach ($routes as $route) {
            if (isset($route["get_parms"])) {
                $this->param_check($route['get_parms'], $_GET);
            } 
            if (isset($route["post_parms"])) {
                $this->param_check($route["post_parms"], $_POST);
            }
        
            if (isset($route['pattern'])) {
                if (!preg_match($route['pattern'], $this->requested_path, $matches)) {
                    continue;
                }
            } else if (isset($route['string'])) {
                if ($route['string'] !== $this->requested_path) {
                    continue;
                }
            } else {
                // args not given in config
                throw new Exception("Missing required parameter (string or pattern) in route.");
            }
        
            // check that req method is supported
            if (!in_array($this->method, $route["methods"])) {
                $this->respond(
                    405,
                    '<h1>Error 405: Method Not Allowed</h1>',
                    ['allow' => implode(', ', $route['methods'])]
                );
            }
        
            if (isset($route['function'])) {
                if (!is_callable('feature_' . $route['function'])) {
                    $this->respond(
                        405,
                        '<h1>Error 500: Internal Server Error</h1> <p>Specified route-handler does not exist.</p>' . '<pre>'. htmlspecialchars($route['function']) .'</pre>'
                    );
                }
            }
        
            if (isset($matches[1])) {
                call_user_func('feature_' . $route['function'], $matches);
            } else {
                // do the same thing without adding the matches
                call_user_func('feature_' . $route['function']);
            }
        }

        if (!$_404_exists) {
            $this->respond(404, '<h1>404. Page Not Found</h1><p>This page is not recognized ...</p>');
        }
    }

    function checkRouteFunction(array $routes, string $function) {
        $i = 0;
        while ($i < count($routes)) {
            $function = $routes[$i]['function'];
            if (mb_strtolower($function) == $function) {
                return true;
            }
        }

        return false;
    }

    function respond($code, $html, $headers = []) {
        $default_headers = ['content-type' => 'text/html; charset=utf-8'];
        $headers = $headers + $default_headers;

        http_response_code($code);

        foreach ($headers as $key => $value) {
            header($key . ":" . $value);
        }

        echo $html;
        exit();
    }

    function feature($page, array $matches = []) {
        if (count($matches) == 0) {
            $content = "<p> Showing the " . $page . " </p>";
        } else {
            $content = '<pre>Sub-path: ' . $matches["1"] . '</pre>';
            $content .= "<p> Showing the " . $page . "</p>";
        }
    
        $this->respond(200, $content);
    }

    function feature_post($page, array $matches, $message, $specialCharacter) {
        $this->feature($page, $matches);
        $content = "<p>" . $message . "</p>";
        $content .= '<p> Your ' . $specialCharacter . " was: </p>";
        $content .= '<pre>' . htmlspecialchars($_POST($specialCharacter)) . '</pre>';
        $this->respond(200, $content);
    }

    function feature_get($page, array $matches, $message, $specialCharacter) {
        $this->feature($page, $matches);
        $content = "<p>" . $message . "</p>";
        $content .= '<p> Your ' . $specialCharacter . " was: </p>";
        $content .= '<pre>' . htmlspecialchars($_GET($specialCharacter)) . '</pre>';
        $this->respond(200, $content);
    }
    
    function param_check($allowed_parameters, $parameters) {
        $invalid_parameters = [];
        foreach ($parameters as $p_name => $p_value) {
            if (!in_array($p_name, $allowed_parameters)) {
                $invalid_parameters[] = $p_name;
            }
        }

        if (count($invalid_parameters) == 0) {
            return true;
        } else {
            $invalid_parameters_string = "";
            $c = 1;
            $count = count($invalid_parameters);
            foreach ($invalid_parameters as $i) {
                $invalid_parameters_string .= $i . ",";
                if ($count == $c) {
                    $invalid_parameters .= $i . ".";
                }
                $count = $count + 1;
            }

            echo " <p><b>Invalid request:</b>" . $invalid_parameters_string . "not allowed.</p>";
            exit();
        }
    }
}

?>