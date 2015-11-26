<?php
/**
 * @author a.itsekson
 * @createdAt: 26.11.2015 18:15
 */

namespace Api\Service;


interface AnnotatedServiceInterface
{

    /**
     * @param AnnotationsHelper $helper
     * @return array
     */
    public function getColumns(AnnotationsHelper $helper);

    /**
     * @param AnnotationsHelper $helper
     * @return array
     */
    public function getFilters(AnnotationsHelper $helper);

    /**
     * @param AnnotationsHelper $helper
     * @return array
     */
    public function getGroupings(AnnotationsHelper $helper);

    /**
     * @return array
     */
    public function getParameters();

    /**
     * @return AnnotationsHelper
     */
    public function getAnnotationsHelper();


}