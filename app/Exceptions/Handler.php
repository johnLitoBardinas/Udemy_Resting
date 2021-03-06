 <?php

namespace App\Exceptions;

use Exception;
use App\Traits\ApiResponser;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception); // in log file exception
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof ValidationException) { // form validation exception
            $this->convertValidationExceptionToResponse($exception, $request);
        }

        if ($exception instanceof ModelNotFoundException) { // model instance not found
            $modelName = strtolower(class_basename($exception->getModel()));

            return $this->errorResponse("Does not exists any {$modelName} with the specified identificator", 404);
        }

        if ($exception instanceof AuthenticationException) { // user authentication exception
            $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof AuthorizationException) { // user was not authorize to do the thing
            return $this->errorResponse($exception->getMessages(), 403);
        }

        if ($exception instanceof NotFoundHttpException) { // missing endpoint
            return $this->errorResponse('The specified URL cannot be found', 404);
        }

        if ($exception instanceof MethodNotAllowedHttpException) { // invalid HTTP method eZ
            return $this->errorResponse('The specified method for the request is invalid', 405);
        }

        if ($exception instanceof HttpException) { // any other http exception shit

            # 12:08 PM Custom Vernt
            if ($exception->getStatusCode() == '429') {
                return $this->errorResponse(['error' => 'Too Many Request', 'headers' => $exception->getHeaders()], $exception->getStatusCode());
            }
            return $this->errorResponse($exception->getMessages(), $exception->getStatusCode());

        }

        # 12:21 PM 01-07-2019 { Handling removal of related resource }
        if ($exception instanceof QueryException) {

            $errCode = $exception->errorInfo[1];

            if ($errCode == 1451) {
                return $this->errorResponse('Cannot remove this resource permanently. It is related with any other resource', 409);
            }
        }

        # 1:59 AM 05-12-2020 { Handling csrf exception } No need ??
        if ($exception instanceof TokenMismatchException) {
            return redirect()->back()->withInput($request->input());
        }

        if (config('app.debug')) {
            return parent::render($request, $exception);
        }

        return $this->errorResponse('Unexpected Exception. Try later', 500);
    }

    # 11:27 AM 1-07-2019 { Unable to find the convertValidationExceptionToResponse??}
    /**
     * Create a response object from the given valiation exception
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();

        if ($this->isFrontend($request)) {
            return $request->ajax() ? response()->json($errors, 422) : redirect()->back()->withInput($request->input())->withErrors($errors);
        }

        return $this->errorResponse($errors, 422);
    }

    # 11:57 AM 1-07-2019 { AuthenticationException }
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($this->isFrontend($request)) {
            return redirect()->guest('login');
        }
        return $this->errorResponse('Unauthenticated', 401);
    }

    # 02:25 AM 05-12-2020 { Determining if the request come from the front-end }
    private function isFrontend($request)
    {
       return $request->acceptsHtml() && collect($request->route()->middleware())->contain('web');
    }


}
