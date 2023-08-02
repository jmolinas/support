<?php

namespace JMolinas\Support\Http\Middleware;

use Illuminate\Http\Request;

class XSSProtection
{
    /**
     * The following method loops through all request input and strips out all tags from
     * the request. This to ensure that users are unable to set ANY HTML within the form
     * submissions, but also cleans up input.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param callable $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        if (!in_array(strtolower($request->method()), ['put', 'post', 'get'])) {
            return $next($request);
        }

        $input = $request->all();

        array_walk_recursive($input, function (&$input) {
            $input = trim($input);
            $input = filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        });

        $request->merge($input);

        return $next($request);
    }
}
