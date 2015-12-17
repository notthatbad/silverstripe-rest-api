<?php

/**
 * Useful extensions for data lists.
 */
class DataListExtension extends Extension
{

    /**
     * Returns the first element in a data list, that has the given url segment.
     *
     * Works in the same way as DataList::byID.
     *
     * @param string $url the url segment
     * @return DataObject the object, that has the given url segment
     */
    public function byURL($url)
    {
        $URL = Convert::raw2sql($url);
        return $this->owner->filter('URLSegment', $URL)->first();
    }
}
