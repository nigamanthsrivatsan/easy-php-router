# php-router-template

A template for a PHP Router with the **LAMP** Stack (Linux, Apache, MySQL and PHP). 

### Why? Just why?

At the start of every PHP project I had done, it started with a basic router handler. And rather than wasting my time on a PHP router I wanted to tackle the real problems. <br> <br> This is a problem which can be sovled with this template. 

## Simple vs Complex Router

If your project is a simple and small project which you just need to test a fundamental concept or is a trial run, you would not want to bore yourself with complicated and unnecessary features like handling GET, POST requests, seeing if a request's method is accepted, throwing errors and more.

This inspired me to make 2 solutions, for the PHP router template. <br>
1. <a href="#simple"> **Simple Router** </a> <br>
2. <a href="#complex"> **Complex Router** </a>

<h3 id="simple"> Simple Router Setup </h3>

1. To start, clone or fork this repository and get started with your project!
2. Go to `config/simple-routes.php`
3. Simply enter the routes associated with your application in this format: <br> <br>
    ```php
    $routes = [
        "/" => '/views/index.php',
        "/about" => "/views/about.php"
    ];
    ```

    The route has to be relative to the file that the code is being executed in, this is being called in `simple-router.php` from the main directory. Keep that in mind when routing.

    **Note:** If you have an application that has a route such as `application.com/users/username`, where username can vary you must use the complex router. 

4. If you're using Apache, then change `example.htaccess` to `.htaccess` and run your server! 🎉

5. If you're not, your router is made and your application is ready to be started! 🚀

<h3 id="complex"> Complex Router Setup </h3>

Before you need to set this up, you need to know the following:

* This doesn't necesarily need to be used with the LAMP stack, however I would recommend doing so.
* This is recommended for bigger projects.  
* You can use Heroku to host this. 
* You require a good understanding of PHP in order to setup this router.

Steps to set it up:

1. To start, clone or fork this repository and get started with your project 🤔

2. Change the configuration files ⚙️

    Go to `config/routes.php` and add in your application's routes in the format given below:

    ```php
    $routes = [
        [
            "pattern" => '/^\/users\/([a-z0-1_-]+)$/',
            "methods" => ["POST"],
            "function" => "users" 
        ],
        [
            "string" => "/",
            "methods" => ["GET"],
            "function" => "mainpage",
            'get_parms' => ['hello']
        ],
        [
            'string' => '/contact',
            'methods' => ['POST'],
            'function' => 'submit_contact_form',
            'post_parms' => ['name', 'email', 'message']
        ],
    ];
    ```  

    The pattern is if your application works like this: `xyz.com/users/username` is supposed to return a user's profile. 

    String is if you don't require a pattern, like `xyz.com/` or `xyz.com/contact`.
    The get parameters and post parameters are also to be given here.

    Additionally, you can add your 404 page as shown up, but put that into the 404 array. <br>
    **Note:** You are required to have routes in order for your router to run, however a 404 is not required. Yet we still recommend a custom 404 page. 

3. Rename `example.htaccess` to `.htaccess`

4. Run your server!