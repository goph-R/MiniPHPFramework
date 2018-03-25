<?php

class Router {

    const PARAMETER_REGEX = '/\:[a-zA-Z0-9_-]+/';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var InstanceManager
     */
    private $im;

    private $map = [];
    private $useLocale;
    private $rewrite;
    private $routeParameter;

    public function __construct(InstanceManager $im) {
        $this->im = $im;
        $this->config = $im->get('config');
        $this->request = $im->get('request');
        $this->rewrite = $this->config->get('router.rewrite');
        $this->useLocale = $this->config->get('router.use_locale');
        $this->routeParameter = $this->config->get('router.route_parameter', 'route');
        $this->add('', $this->config->get('router.default.controller'), $this->config->get('router.default.method'));
    }

    public function add($route, $controller, $method) {
        $prefix = $this->useLocale ? ':locale/' : '';
        $item = [
            'route' => $prefix.$route,
            'controller' => $controller,
            'method' => $method
        ];
        $matches = [];
        preg_match_all(self::PARAMETER_REGEX, $prefix.$route, $matches, PREG_PATTERN_ORDER);
        if ($matches[0]) {
            $item['parameters'] = $matches[0];
        }
        $this->map[] = $item;
    }

    public function usingLocale() {
        return $this->useLocale;
    }

    public function query($route) {
        foreach ($this->map as $item) {
            if (isset($item['parameters'])) {
                $valuesInRoute = $this->fetchParameterValues($route, $item['route']);
                if ($valuesInRoute) {
                    $valueByName = $this->getValueByName($valuesInRoute, $item['parameters']);
                    // TODO: preg_replace, because ':nameLonger/:name' route can cause an issue
                    $routeForSearch = str_replace(array_keys($valueByName), array_values($valueByName), $item['route']);
                    if ($route == $routeForSearch) {
                        $this->setRequestParameters($valueByName);
                        return $item;
                    }
                }
            } else if ($route == $item['route']) {
                return $item;
            }
        }
        return null;
    }

    private function setRequestParameters($valueByName) {
        foreach ($valueByName as $name => $value) {
            $this->request->set(substr($name, 1), $value);
        }
    }

    private function fetchParameterValues($route, $itemRoute) {
        $regex = preg_replace(self::PARAMETER_REGEX, '([^/]+)', $itemRoute);
        $regex = '/'.str_replace('/', '\\/', $regex).'/';
        $matches = [];
        preg_match_all($regex, $route, $matches, PREG_SET_ORDER);
        return isset($matches[0]) ? $matches[0] : null;
    }

    private function getValueByName($values, $parameters) {
        $result = [];
        for ($i = 0; $i < count($parameters); $i++) {
            $name = $parameters[$i];
            $value = $values[$i+1];
            $result[$name] = $value;
        }
        return $result;
    }

    public function queryCurrent() {
        $value = $this->request->get($this->routeParameter);
        $result = $this->query($value);
        if (!$result && $this->useLocale) {
            $result = $this->query($this->request->getDefaultLocale().'/'.$value);
        }
        return $result;
    }

    public function getUrl($path = '', $params = [], $escapeAmp=true) {
        // TODO: search $params in the routes
        $prefix = $this->useLocale ? $this->request->get('locale').'/' : '';
        if ($this->rewrite) {
            $qmark = $params ? '?' : '';
            $url = $this->getBaseUrl().$prefix.$path;
        } else {
            $qmark = '?';
            $params[$this->routeParameter] = $prefix.$path;
            $url = $this->getBaseUrl().'index.php';

        }
        $url .= $qmark.http_build_query($params, '', $escapeAmp ? '&amp;' : '&');
        return $url;
    }

    public function getBaseUrl() {
        return $this->config->get('router.base_url');
    }

}