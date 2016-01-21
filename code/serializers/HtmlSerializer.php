<?php

/**
 * Serializer for html.
 * @author Christian Blank <c.blank@notthatbad.net>
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
        $list = $this->recursive($data, 1);
        return $this->renderWith(['Result', 'Controller'], ['Data' => ArrayList::create($list)]);
    }

    public function contentType() {
        return $this->contentType;
    }

    private function recursive($data, $level) {
        $list = [];
        if(is_array($data)) {
            foreach ($data as $key => $value) {
                if(is_array($value)) {
                    $list[] = ArrayData::create(['Key' => $key, 'Value' => '', 'Heading' => true, 'Level' => $level]);
                    $list = array_merge($list, $this->recursive($value, $level+1));
                } else {
                    $list[] = ArrayData::create(['Key' => $key, 'Value' => $value, 'Level' => $level]);
                }
            }
        }
        return $list;
    }
}
