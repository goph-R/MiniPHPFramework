<?php

class MediaInput extends Input {

    /**
     * @var Router
     */
    private $router;

    /**
     * @var MediaService
     */
    private $mediaService;

    private $options;

    public function __construct($name, $defaultValue='', $options=[]) {
        parent::__construct($name, $defaultValue);
        $im = InstanceManager::getInstance();
        $this->router = $im->get('router');
        $this->mediaService = $im->get('mediaService');
        $this->options = $options;
        $this->view->addStyle('components/admin/mediaBrowser/static/mediaInput.css');
        $this->view->addScript('components/admin/mediaBrowser/static/mediaInput.js');
        $this->classes[] = 'mediainput';
    }

    public function fetch() {
        // HTML
        $id = $this->getId();
        $result = '<div'.$this->getClassHtml().'>';
        $result .= '<input type="hidden"';
        $result .= ' id="'.$id.'"';
        $result .= ' name="'.$this->getName().'"';
        $result .= ' value="'.$this->view->escape($this->getValue()).'"';
        $result .= '>';
        $result .= '<span id="'.$id.'_display"></span>';
        $result .= '</div>';

        // JavaScript
        $options = $this->options;
        $options['mediaBrowserUrl'] = $this->router->getUrl('mediabrowser');
        $options['mediaThumbnailUrl'] = $this->router->getUrl('media/thumbnail');
        $jsonOptions = json_encode($options);
        $this->view->addScriptContent("MediaInput.init('$id', $jsonOptions);\n");
        $jsonFile = 'null';
        if ($this->getValue()) {
            $record = $this->mediaService->findById($this->getValue());
            if ($record) {
                $jsonFile = json_encode($record->getAttributes());
            }
        }
        $this->view->addScriptContent("MediaInput.setValue('$id', $jsonFile)");
        return $result;
    }

}