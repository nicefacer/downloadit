<?php

interface Services_Request
{
    /**
     * Perform internal request validation.
     * To reduce server load
     *
     * @return array errors that found, if no errors found, empty array
     */
    public function validate();

    /**
     * Return data for current request
     *
     * @return array
     */
    public function getData();
}