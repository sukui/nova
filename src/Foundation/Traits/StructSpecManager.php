<?php
/**
 * Spec mgr (for struct)
 * User: moyo
 * Date: 9/25/15
 * Time: 2:31 PM
 */

namespace Kdt\Iron\Nova\Foundation\Traits;

trait StructSpecManager
{
    /**
     * @var array
     */
    public $_TSPEC = [];

    /**
     * @var array
     */
    protected $structSpec = [];

    /**
     * @return array
     */
    public function getStructSpec()
    {
        return $this->structSpec;
    }

    /**
     * for php-ext:thrift-protocol
     */
    private function staticSpecInjecting()
    {
        $this->_TSPEC = $this->structSpec;
    }
}