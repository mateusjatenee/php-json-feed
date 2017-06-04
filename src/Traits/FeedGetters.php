<?php

namespace Mateusjatenee\JsonFeed\Traits;

trait FeedGetters
{
    /**
     * Gets the home page url
     *
     * @return string
     */
    public function getHomePageUrl()
    {
        return $this->get('home_page_url');
    }

    /**
     * Gets the feed description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->get('description');
    }

    /**
     * Gets the feed title
     * @return string
     */
    public function getTitle()
    {
        return $this->get('title');
    }

    /**
     * Gets the feed url
     * @return string
     */
    public function getFeedUrl()
    {
        return $this->get('feed_url');
    }

    /**
     * Gets the feed icon
     * @return string
     */
    public function getIcon()
    {
        return $this->get('icon');
    }

    /**
     * Gets the feed's next page url
     * @return string
     */
    public function getNextUrl()
    {
        return $this->get('next_url');
    }

    /**
     * Gets wether the feed is expired (i.e no more updates)
     * @return bool
     */
    public function getExpired()
    {
        return $this->get('expired');
    }

    /**
     * Gets the feed's favicon
     * @return string
     */
    public function getFavicon()
    {
        return $this->get('favicon');
    }

    /**
     * Gets the feed's author
     * @return array
     */
    public function getAuthor()
    {
        return $this->get('author');
    }

    /**
     * Gets the number of comments
     * @return integer
     */
    public function getUserComment()
    {
        return $this->get('user_comment');
    }

    public function getHubs()
    {
        return $this->get('hubs');
    }

    protected function get($property)
    {
        return $this->properties[$property] ?? null;
    }
}
