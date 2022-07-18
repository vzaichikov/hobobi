<?php

namespace hobotix;

class hoboSimpleDaemon
{
    private $pid = '';

    public function __construct($pid)
    {
        $this->pid = dirname(__FILE__) . '/../pids/' . $pid . '.pid';
        if (!$this->check()) {
            echoLine('process doesnt exist, starting');
            $this->start();
        } else {
            echoLine('running');
            die();
        }
    }

    function start()
    {
        echoLine('my pid: ' . getmypid());
        file_put_contents($this->pid, getmypid());
    }

    function stop()
    {
        unlink($this->pid);
    }

    function check(): bool
    {
        if (file_exists($this->pid) && $pid = (int)trim(file_get_contents($this->pid))){
            echoLine('pid: ' . $pid);
            return posix_kill($pid, 0);
        }

        return false;
    }
}