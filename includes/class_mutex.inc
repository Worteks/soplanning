<?php

    class Mutex
    {
        private $id;
        private $sem_id;
        private $is_acquired = false;
        private $is_windows = false;
        private $filename = '';
        private $filepointer;

        function __construct()
        {
            if(substr(PHP_OS, 0, 3) == 'WIN')
                $this->is_windows = true;
        }

        public function init($id, $filename = '')
        {
            $this->id = $id;

            if($this->is_windows)
            {
                if(empty($filename)){
                    print "no filename specified";
                    return false;
                }
                else
                    $this->filename = $filename;
            }
            else
            {
                if(!($this->sem_id = sem_get($this->id, 1))){
                    print "Error getting semaphore";
                    return false;
                }
            }

            return true;
        }

        public function acquire()
        {
            if($this->is_windows)
            {
                if(($this->filepointer = @fopen($this->filename, "w+")) == false)
                {
                    print "error opening mutex file<br>";
                    return false;
                }
               
                if(flock($this->filepointer, LOCK_EX) == false)
                {
                    print "error locking mutex file<br>";
                    return false;
                }
            }
            else
            {
                if (! sem_acquire($this->sem_id)){
                    print "error acquiring semaphore";
                    return false;
                }
            }

            $this->is_acquired = true;
            return true;
        }

        public function release()
        {
            if(!$this->is_acquired)
                return true;

            if($this->is_windows)
            {
                if(flock($this->filepointer, LOCK_UN) == false)
                {
                    print "error unlocking mutex file<br>";
                    return false;
                }

                fclose($this->filepointer);
            }
            else
            {
                if (! sem_release($this->sem_id)){
                    print "error releasing semaphore";
                    return false;
                }
            }

            $this->is_acquired = false;
            return true;
        }

        public function getId()
        {
            return $this->sem_id;
        }
    }

?>