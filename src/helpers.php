<?php

function qs_url($path = null, $qs = array(), $secure = null)
{
    $url = app('url')->to($path, $secure);
    if (count($qs)){
        foreach($qs as $key => $value){
            $qs[$key] = sprintf('%s=%s',$key, urlencode($value));
        }
        $url = sprintf('%s?%s', $url, implode('&', $qs));
    }
    return $url;
}
function prettyPrintJson($value='')
{
    return stripcslashes(json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}
function settings($name,$default = '')
{
	if (!is_array(config('settings.'.$name)) && json_decode(config('settings.'.$name),1)) {
		return json_decode(config('settings.'.$name),1)? json_decode(config('settings.'.$name),1):$default;
	}
	return config('settings.'.$name, $default);
}
function rebuildUrl($url, $params = [])
{
    if (count($params)) {
        $parsedUrl = parse_url($url);
        if ($parsedUrl['path'] == null) {
            $url .= '/';
        }
        $separator = ($parsedUrl['query'] == NULL) ? '?' : '&';
        return $url .= $separator . http_build_query($params);
    }
    return $url;
}
function findHashTag($string)
{
    preg_match_all("/#(\\w+)/", $string, $matches);
    return $matches[1];
}
// flash message to session [class, message]
if (!function_exists('flash')) {
    function flash($data = [])
    {
        session()->flash('flash', $data);
    }
}

// create activity log
if (!function_exists('activity')) {
    function activity($message, $data = [], $model = null)
    {
        // unset hidden form fields
        foreach (['_token', '_method', '_submit'] as $unset_key) {
            if (isset($data[$unset_key])) {
                unset($data[$unset_key]);
            }
        }

        // create model
        app(config('lap.models.activity_log'))->create([
            'user_id' => auth()->check() ? auth()->user()->id : null,
            'model_id' => $model ? $model->id : null,
            'model_class' => $model ? get_class($model) : null,
            'message' => $message,
            'data' => $data ? $data : null,
        ]);
    }
}