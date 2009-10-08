<?
/**  
 *  Abstract Module
 * 
 */
    class AbstractModule
    {
        var $name;
        var $desc;
        var $publicFunctions; //array()
        var $cmdFunctions; //array()
        var $publicURLs; //array()

        function execute($funcName, $args, $silent)
        {
            if (!$funcName)
                trigger_error("funcName can`t be empty\n", PM_ERROR);

            for ($i = 0; $i < count($this->publicFunctions); $i++)
            {
                if ($funcName == $this->publicFunctions[$i])
                {
                    if (method_exists($this, $funcName) == TRUE)
                    {
                        return $this->{$funcName}($args);
                    }
                    else
                        trigger_error("Method [$funcName()] is not found in a class [" . $this->{"name"} . 
                                      "] definition, although found in [publicFunctions] array.", PM_FATAL);
                }
                
            }
            
            if (isset($this->cmdFunctions) && count($this->cmdFunctions) > 0)
            foreach ($this->cmdFunctions as $cmd => $fName)
            {
                if ($funcName == $cmd)
                {
                    if (method_exists($this, $fName) == TRUE)
                    {
                        return $this->{$fName}($args);
                    }
                    else
                        trigger_error("Command [$funcName] is not found in a class [" . $this->{"name"} . 
                                      "] definition, although found in [cmdFunctions] array.", PM_FATAL);
                }
                
            }
            if (!$silent)
                trigger_error("Undefined or not allowed method [$funcName] in class [" 
                           . get_class($this) . "]", PM_ERROR);
            else 
                return NULL;
        }
    }

?>