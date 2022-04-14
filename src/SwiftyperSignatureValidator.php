<?php

namespace Swiftyper\fbt;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SwiftyperSignatureValidator
{
    public function isValid(Request $request): bool
    {
        if (! \config('swiftyper.verify_signature')) {
            return true;
        }

        $signatureHeaderContent = $request->header('X-Swiftyper-Signature');

        $signature = Str::after($signatureHeaderContent, 'sha256=');

        if (! $signature) {
            return false;
        }

        $signingSecret = \config('swiftyper.api_key');

        if (empty($signingSecret)) {
            return false;
        }

        $computedSignature = hash_hmac('sha256', $request->getContent(), $signingSecret);

        return hash_equals($signature, $computedSignature);
    }
}
