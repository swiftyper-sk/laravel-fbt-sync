<?php

namespace Swiftyper\fbt\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Swiftyper\fbt\SwiftyperSignatureValidator;

class IntlController extends Controller
{
    public function sync(Request $request, SwiftyperSignatureValidator $signatureValidator)
    {
        if ($signatureValidator->isValid($request)) {
            Artisan::call('swiftyper:fbt --upload');
            Artisan::call('swiftyper:fbt --deploy');

            return response()->noContent();
        }

        abort(403);
    }

    public function upload(Request $request, SwiftyperSignatureValidator $signatureValidator)
    {
        if ($signatureValidator->isValid($request)) {
            Artisan::call('swiftyper:fbt --upload');

            return response()->noContent();
        }

        abort(403);
    }

    public function deploy(Request $request, SwiftyperSignatureValidator $signatureValidator)
    {
        if ($signatureValidator->isValid($request)) {
            Artisan::call('swiftyper:fbt --deploy');

            return response()->noContent();
        }

        abort(403);
    }
}
