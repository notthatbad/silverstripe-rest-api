<?php

/**
 * Extension for data objects which should be identifiable by a slug.
 *
 * The data objects need a Title attribute or getTitle method, which will be used to generate the slug. If no title is
 * provided, the extension uses a generic combination with class name and object id.
 * @author Christian Blank <c.blank@notthatbad.net>
 */
class SlugableExtension extends DataExtension {
    private static $db = [
        'URLSegment' => 'Varchar(200)'
    ];

    private static $indexes = [
        "URLSegment" => [
            'type' => 'unique',
            'value' => 'URLSegment'
        ]
    ];

    private static $defaults = [
        'Title' => 'New Element',
        'URLSegment' => 'new-element'
    ];


    /**
     * Set URLSegment to be unique on write
     */
    public function onBeforeWrite() {
        parent::onBeforeWrite();

        $defaults = $this->owner->config()->defaults;
        $URLSegment = $this->owner->URLSegment;

        // If there is no URLSegment set, generate one from Title
        if((!$URLSegment || $URLSegment == $defaults['URLSegment']) && $this->owner->Title != $defaults['Title']) {
            $URLSegment = $this->generateURLSegment($this->owner->Title);
        } else if($this->owner->isChanged('URLSegment')) {
            // Make sure the URLSegment is valid for use in a URL
            $segment = preg_replace('/[^A-Za-z0-9]+/','-',$this->owner->URLSegment);
            $segment = preg_replace('/-+/','-',$segment);
            // If after sanitising there is no URLSegment, give it a reasonable default
            if(!$segment) {
                $segment = $this->fallbackUrl();
            }
            $URLSegment = $segment;
        }
        // Ensure that this object has a non-conflicting URLSegment value.
        $count = 2;
        $ID = $this->owner->ID;

        while($this->lookForExistingURLSegment($URLSegment, $ID)) {
            $URLSegment = preg_replace('/-[0-9]+$/', null, $URLSegment) . '-' . $count;
            $count++;
        }

        $this->owner->URLSegment = $URLSegment;
    }

    /**
     * Check if there is already a database entry with this url segment
     *
     * @param string $urlSegment
     * @param int $id
     * @return bool
     */
    protected function lookForExistingURLSegment($urlSegment, $id) {
        return $this->owner->get()->filter(
            'URLSegment', $urlSegment
        )->exclude('ID', $id)->exists();
    }

    /**
     * Generate a URL segment based on the title provided.
     *
     * If {@link Extension}s wish to alter URL segment generation, they can do so by defining
     * updateURLSegment(&$url, $title).  $url will be passed by reference and should be modified.
     * $title will contain the title that was originally used as the source of this generated URL.
     * This lets extensions either start from scratch, or incrementally modify the generated URL.
     *
     * @param string $title the given title
     * @return string generated url segment
     */
    public function generateURLSegment($title) {
        $filter = URLSegmentFilter::create();
        $t = $filter->filter($title);

        // Fallback to generic page name if path is empty (= no valid, convertable characters)
        if(!$t || $t == '-' || $t == '-1') {
            $t = $this->fallbackUrl();
        }

        // Hook for extensions
        $this->owner->extend('updateURLSegment', $t, $title);

        return $t;
    }

    private function fallbackUrl() {
        $className = strtolower(get_class($this->owner));
        return "$className-{$this->owner->ID}";
    }

}
