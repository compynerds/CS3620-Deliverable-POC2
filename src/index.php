<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ParameterBag;
//use Project1\Domain\StringLiteral;
//use Project1\Domain\User;

require_once __DIR__ . '/../vendor/autoload.php';

//$bill = new User(
//    new StringLiteral('bill@email.com'),
//    new StringLiteral('jones'),
//    new StringLiteral('bjones')
//);
//$bill->setId(new StringLiteral('1'));
//
//$charlie = new User(
//    new StringLiteral('$charlie@email.com'),
//    new StringLiteral('jones'),
//    new StringLiteral('cjones')
//);
//$charlie->setId(new StringLiteral('2'));
//
//$dawn = new User(
//    new StringLiteral('$dawn@email.com'),
//    new StringLiteral('jones'),
//    new StringLiteral('djones')
//);
//
//$users = [$bill,$charlie, $dawn];

$app = new Silex\Application();

$checkPayload = function (Request $request, $app) {

    $response = new Response();
    if(empty($request->getContent())) {
        $response->setStatusCode(405);
        $response->setContent(json_encode(["Message","You haven't sent a payload"]));
        return $response;
    }
    return;
};

$payloadCheckEmpty = function (Request $request, $app) {

    $response = new Response();
    if(empty($request->getContent())) {
        return ;
    }
    $response->setStatusCode(405);
    $response->setContent(json_encode(["Message: ","You should not be sending a payload"]));
    return $response;
};


$app->before(function(Request $request)
{
    $password = $request->getPassword();
    $user = $request->getUser();

    if($user !== 'Proffessor')//input this in POSTman as the username
    {
        $response = new Response();
        $response->setStatusCode(401);
        return $response;
    }
    if($password !== '123pass')//input this in POSTman as the password
    {
        $response = new Response();
        $response->setStatusCode(401);
        return $response;
    }
    if($request->getContent() !== "")
    {
        $response = new Response();
        $response->setStatusCode(401);
        return $response;
    }
    return;
});

$app->get('/', function() use($app)
{
    return '<h1>Welcome to Project 1</h1>';
})->before($payloadCheckEmpty);

$app->get('/users', function (Request $request) use ($app)
{
    $response = new Response();

    if(empty($request->getContent())) {
        $response = new Response();
        $response->setSTatusCode(405);
        return $response;
    }

    $response->setStatusCode(200);
    $response->setContent(json_encode($users));//return the list of users stored in the array
    return $response;
})->before($payloadCheckEmpty);

$app->get('/users/{id}', function($id, Request $request) use ($app, $users){

    $response = new Response();

    if(empty($request->getContent())) {
        $response = new Response();
        $response->setStatusCode(405);
        return $response;
    }

    $max = count($users);

    for($i = 0; $i < $max; $i++)
    {
//        $newId =
      if($users[$i]->getId() === $id )
      {
          $response->setStatusCode(200);
          $response->setContent(json_encode($users[$i]));
      }
   }
})->before($payloadCheckEmpty);

$app->post('/user', function(Request $request) use ($app)
{
    $response = new Response();
    $response->setStatusCode(200);
    $response->setContent(json_encode(["Message: ", "Payload good"]));
    //sanitize payload(create a function to clear and escape sequences)
    //add user to array/repo
    //set the user id in the object after it's set in the repo
    return $response;
})->before($checkPayload);

$app->post('/user/{id}', function(Request $request) use ($app)
{
    $response = new Response();
    $response->setStatusCode(400); //immediate reject because the ID is auto-assigned by the DB
    return $response;
})->before($checkPayload);

$app->put('/user', function() use ($app)
{
    $response = new Response();
    $response->setStatusCode(401); // or some kind of error code
    return $response; //auto-reject if no id in param
})->before($checkPayload);

$app->put('/user/{id}', function($id, Request $request) use ($app)
{
    $response = new Response();
    if($id === false)//change this to a for each that will move through the array holding the users
    {
        $response->setStatusCode(401);
        return $response;
    }
    $response->setStatusCode(200);
    return $response;
});

$app->delete('/user', function(Request $request) use ($app)
{
    $response = new Response();

    if(empty($request->getContent())) {
        $response = new Response();
        $response->setStatusCode(405);
        return $response;
    }

    $response->setStatusCode(400);// or some kind of error code this is just a filler
    return $response;// auto reject if ID isn't specified in the param
})->before($payloadCheckEmpty);

$app->delete('/user/{id}', function($id, Request $request) use ($app)//id means grab the params from the http request and use it in the block
{
    $response = new Response();

    if($id === false)
    {
       $response = new Response();
       $response->setStatusCode(400);// set the status code to wrong pa
       return $response;
    }
    $response->setStatusCode(200);
    //find user and delete them(make this  a function) TODO: this needs to be implemented still
    $response->setContent(json_encode(["Message: ", "Delete Successful"]));
    return $response;
})->before($payloadCheckEmpty);

$app->run();




//for project one just do the middle-ware this will
//405 error for no body, and bad payload
//request authorization header fully functioning
//reject a GET request with a body immediately
//delete shouldn't have a body either
//update does use the body
//so create and update will use the body the rest won't so reject them is they do.
//the update param will be the ID and it will be used for the update
//name email username password for the payload

