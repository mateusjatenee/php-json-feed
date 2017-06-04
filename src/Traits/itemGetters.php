<?php

namespace Mateusjatenee\JsonFeed\Traits;

trait ItemGetters
{
    public function getDatePublished()
    {
        return method_exists($this->object, 'getFeedDatePublished') ? $this->object->getFeedDatePublished()->toRfc3339String() : null;
    }

    public function getContentText()
    {
        return $this->call('getFeedContentText');
    }

    public function getTitle()
    {
        return $this->call('getFeedTitle');
    }

    public function getAuthor()
    {
        return $this->call('getFeedAuthor');
    }

    public function getTags()
    {
        return $this->call('getFeedTags');
    }

    public function getContentHtml()
    {
        return $this->call('getFeedContentHtml');
    }

    public function getSummary()
    {
        return $this->call('getFeedSummary');
    }

    public function getImage()
    {
        return $this->call('getFeedImage');
    }

    public function getBannerImage()
    {
        return $this->call('getFeedBannerImage');
    }

    public function getId()
    {
        return $this->call('getFeedId');
    }

    public function getUrl()
    {
        return $this->call('getFeedUrl');
    }

    public function getExternalUrl()
    {
        return $this->call('getFeedExternalUrl');
    }

    public function getDateModified()
    {
        return method_exists($this->object, 'getFeedDateModified') ? $this->object->getFeedDateModified()->toRfc3339String() : null;
    }

    public function call($method)
    {
        return method_exists($this->object, $method) ? $this->object->$method() : null;
    }

}
