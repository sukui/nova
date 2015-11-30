<?php
/**
 * Service call dispatcher
 * User: moyo
 * Date: 9/22/15
 * Time: 2:30 PM
 */

namespace Kdt\Iron\Nova\Service;

use Kdt\Iron\Nova\Foundation\Protocol\TException as BizException;
use Kdt\Iron\Nova\Foundation\Traits\InstanceManager;
use Exception as SysException;
use Thrift\Exception\TApplicationException;

class Dispatcher
{
    /**
     * Instance mgr
     */
    use InstanceManager;

    /**
     * @var Finder
     */
    private $finder = null;

    /**
     * @var Convert
     */
    private $convert = null;

    /**
     * Dispatcher constructor.
     */
    public function __construct()
    {
        $this->finder = Finder::instance();
        $this->convert = Convert::instance();
    }

    /**
     * @param $serviceName
     * @param $methodName
     * @param $arguments
     * @return mixed
     */
    public function call($serviceName, $methodName, $arguments)
    {
        try
        {
            $hostingCtrl = $this->finder->getServiceControllerInstance($serviceName);
            if (method_exists($hostingCtrl, $methodName))
            {
                $useInterface = $this->finder->isInterfaceCompatible($serviceName);
                $arguments = $useInterface ? $this->convert->inputArgsToFuncArray($arguments, $this->finder->getInputStruct($serviceName, $methodName)) : $this->convert->inputArgsToIronInput($arguments);
                $buffer = call_user_func_array([$hostingCtrl, $methodName], $arguments);
                $data = $useInterface ? $buffer : $this->convert->outputArrayToStruct($buffer, $this->finder->getOutputStruct($serviceName, $methodName));
                $state = $sign = 'success';
            }
            else
            {
                throw new TApplicationException('dispatcher.service.method.missing', TApplicationException::WRONG_METHOD_NAME);
            }
        }
        catch (SysException $e)
        {
            $data = $e;
            if ($e instanceof BizException)
            {
                $state = 'success';
                $sign = 'biz-exception';
            }
            else
            {
                $state = 'failed';
                $sign = 'sys-exception';
            }
        }
        return ['state' => $state, 'sign' => $sign, 'data' => $data];
    }
}