<?php
if (!function_exists('activeSegment')) {
    function activeSegment($routeName)
    {
        return request()->routeIs($routeName);
    }
}
