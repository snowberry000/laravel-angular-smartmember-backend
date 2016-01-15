<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
		if($e instanceof NotFoundHttpException || $e instanceof MethodNotAllowedHttpException)
		{
			$error = array("message" => 'Route not found, please try again.', "code" => 404);
			return response()->json($error)->setStatusCode(404);
		}

        if (env("APP_ENV") == "production")
        {
            
            if (get_class($e) ==  "Symfony\Component\HttpKernel\Exception\HttpException"){
                $error["message"] = $e->getMessage();
                return response()->json($error)->setStatusCode($e->getStatusCode()); 
            }

            $notifier = new \Airbrake\Notifier(array(
                'projectId' => \Config::get("airbrake.projectId"),
                'projectKey' => \Config::get('airbrake.projectKey')
            ));

            \Airbrake\Instance::set($notifier);
            // Some of the parameters in our request might make airbrake server
            // to reject the log because of illformed JSON. The filter below 
            // should take care of it. 
            $notifier->addFilter(function ($notice) {
              if (isset($notice['environment']['PHP_AUTH_USER'])) {
                  $notice['environment']['PHP_AUTH_USER'] = 'FILTERED';
              }
              
              if (isset($notice['environment']['PHP_AUTH_PW'])) {
                  $notice['environment']['PHP_AUTH_PW'] = 'FILTERED';
              }
              return $notice;
            });

            $re = \Airbrake\Instance::notify($e);
            $error = array("message" => 'Oops, something went wrong! Please try again soon', "code" => 500);
            return response()->json($error)->setStatusCode(500);
        }

        
        if (get_class($e) == "Symfony\Component\HttpKernel\Exception\HttpException"){
            $error = array("message"=> $e->getMessage(), "code" => $e->getStatusCode());
            return response()->json($error)->setStatusCode($e->getStatusCode());
        }

        $error["message"] = $e->getMessage() . get_class($e) . " in " . $e->getFile() . " at " . $e->getLine();
        return response()->json($error)->setStatusCode(500);  
    }
}
