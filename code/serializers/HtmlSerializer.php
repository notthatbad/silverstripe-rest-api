<?php

/**
 * Serializer for html.
 *
 * @todo Use a better way to display the result in html
 */
class HtmlSerializer extends ViewableData implements IRestSerializer {

    /**
     * The content type
     * @var string
     */
    private $contentType = "text/html";

    /**
     * The given data will be serialized into an html string using a Silverstripe template.
     *
     * @param array $data
     * @return string an html string
     */
    public function serialize($data) {
        return $this->renderWith('Result', new ArrayData(['Data' => print_r($data, true)]));
    }

    public function contentType() {
        return $this->contentType;
    }
}