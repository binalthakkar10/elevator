<?php

class elevator{

    const ALARM = 999;
    const OPEN = 1;
    const CLOSE = 0;
    const MOVING = 9;
    public $floors = array();

    public function move_up($current, $floor) {
        while ($current < $floor && $current <= count($this->floors)) {
            $current++;
        }
        return $current;
    }
    public function move_down($current, $floor) {
        while ($current > $floor && $current > 1) {
            $current--;
        }
        return $current;
    }
   
    public function stand($floor) {
        return $floor;
    }
    public function maintenance($floors) {
        if (is_array($floors)) {
            sort($floors);
            return $floors;
        } else {
            return array($floors);
        }
    }
    public function set_signal($signal = CLOSE) {
        return $signal;
    }
}

class log {
    private $log_dir;
    private $log_data = '';
    public $readable = FALSE;
    public $data = array();
    private $signal = array(999 => 'maintenance', 0 => 'close', 1 => 'open', 9 => 'keep moving');
    public function __construct($file) {
        $this->log_dir = dirname($file) . '/';
    }
    public function record() {
        $log_data = '';
        foreach ($this->data as $key => $value) {
            if (is_array($value)) {
                $d = '';
                foreach ($value as $v) {
                    $d .= $this->signal[$v] . ' ';
                }
                $log_data .= $key . ' ' . $d . ' ';
            } else {
                $log_data .= $key . ' ' . $value . ' ';
            }
        }
        
        $this->log_data[] = $log_data;
       
        $log_file = $this->log_dir . 'elevator.log';
        file_put_contents($log_file, $log_data . PHP_EOL, FILE_APPEND);
        chmod($log_file, 0644);
        
        if ($this->readable){
            $this->readable();
        }
    }
    public function readable() {
        print_r($this->log_data[0] . '<br>');
    }
}

class my_elevator extends elevator {
    private $maintenance = array();
    public $request_floor = array(); 
    public $current_floor;   
    public $floors = array(1, 2, 3, 4, 5, 6, 7, 8);
    
    private function __to_floors($sort = SORT_ASC) {
        if (is_array($this->request_floor)) {
            $floors = $this->request_floor;
            array_multisort($floors, $sort);
            return $floors;
        } else {
            return array($this->request_floor);
        }
    }
    
    public function maintenance($floors) {
        $this->maintenance = parent::maintenance($floors);
    }
    
    public function move_up($current, $to_floor) {
        $passed_floor = array();
        $flag_maintenance = FALSE;
        $log = new log(__FILE__);
        $log->readable = TRUE;
        
        while ($current < $to_floor && $current <= count($this->floors)) {
            $current++;
            if ($current == $to_floor) {
                
                $passed_floor[] = $to_floor;
                
                foreach ($this->maintenance as $m_floor) {
                    if ($to_floor == $m_floor) {
                        
                        $flag_maintenance = TRUE;
                        break;
                    }
                }
                
                if ($flag_maintenance) {
                    $flag_maintenance = FALSE;
                    $log->data = array(
                        'elevator' => 'Going UP',
                        'floor' => $to_floor,
                        'signal' => array(
                            $this->set_signal(parent::ALARM),
                            $this->set_signal(parent::CLOSE),
                            $this->set_signal(parent::MOVING),
                        ),
                    );
                    $log->record();
                } else {
                    $log->data = array(
                        'elevator' => 'Going UP',
                        'floor' => $to_floor,
                        'signal' => array(
                            $this->set_signal(parent::OPEN),
                            $this->set_signal(parent::CLOSE),
                        ),
                    );
                    $log->record();
                }
            }
        }
        return implode('', $passed_floor);
    }
    
    public function move_down($current, $to_floor) {
        $passed_floor = array();
        $flag_maintenance = FALSE;
        $log = new log(__FILE__);
        $log->readable = TRUE;
        //
        while ($current > $to_floor && $current > 1) {
            $current--;
            if ($current == $to_floor) {
                
                $passed_floor[] = $to_floor;
                
                foreach ($this->maintenance as $m_floor) {
                    if ($to_floor == $m_floor) {
                       
                        $flag_maintenance = TRUE;
                        break;
                    }
                }
             
                if ($flag_maintenance) {
                    $flag_maintenance = FALSE;
                    $log->data = array(
                        'elevator' => 'Going Down',
                        'floor' => $to_floor,
                        'signal' => array(
                            $this->set_signal(parent::ALARM),
                            $this->set_signal(parent::CLOSE),
                            $this->set_signal(parent::MOVING),
                        ),
                    );
                    $log->record();
                } else {
                    $log->data = array(
                        'elevator' => 'Going Down',
                        'floor' => $to_floor,
                        'signal' => array(
                            $this->set_signal(parent::OPEN),
                            $this->set_signal(parent::CLOSE),
                        ),
                    );
                    $log->record();
                }
            }
        }
        return implode('', $passed_floor);
    }
  
    public function stand($to_floor) {
        $passed_floor = array();
        $flag_maintenance = FALSE;
        $log = new log(__FILE__);
        $log->readable = TRUE;
       
        if ($this->current_floor == $to_floor) {
        
            $passed_floor[] = $to_floor;
           
            foreach ($this->maintenance as $m_floor) {
                if ($to_floor == $m_floor) {
                    
                    $flag_maintenance = TRUE;
                    break;
                }
            }
        }
       
        if ($flag_maintenance) {
            $flag_maintenance = FALSE;
            $log->data = array(
                'elevator' => 'Stading At',
                'floor' => $to_floor,
                'signal' => array(
                    $this->set_signal(parent::ALARM),
                    $this->set_signal(parent::CLOSE),
                    $this->set_signal(parent::MOVING),
                ),
            );
            $log->record();
        } else {
            $log->data = array(
                'elevator' => 'Stading At',
                'floor' => $to_floor,
                'signal' => array(
                    $this->set_signal(parent::OPEN),
                    $this->set_signal(parent::CLOSE),
                ),
            );
            $log->record();
        }
        return implode('', $passed_floor);
    }
    
    public function call_evelator() {
        $current = $this->current_floor;
        $passed_floor = array();
        $move_status = 'up';
        
        if (in_array($current, $this->__to_floors())) {
            $move_status = 'stand';
        } else {
            
            foreach ($this->__to_floors() as $to_floor) {
                if ($current < $to_floor) {
                  
                    $move_status = 'up';
                    break;
                } else if ($current > $to_floor) {
                   
                    $move_status = 'down';
                    break;
                }
            }
        }
     
        switch ($move_status) {
            case 'up':
                foreach ($this->__to_floors() as $to_floor) {
                    $passed_floor[] = $this->move_up($current, $to_floor);
                }
                break;
            case 'down':
                foreach ($this->__to_floors(SORT_DESC) as $to_floor) {
                    $passed_floor[] = $this->move_down($current, $to_floor);
                }
                break;
            case 'stand':
                $passed_floor[] = $this->stand($current);
                break;
        }
        
        $last_floor = $passed_floor[count($passed_floor) - 1];
        $remaining_floor = array_diff($this->__to_floors(), $passed_floor);
        
        foreach ($remaining_floor as $to_floor) {
            if ($last_floor < $to_floor) {
                $this->move_up($current, $to_floor);
            } else if ($last_floor > $to_floor) {
                $this->move_down($current, $to_floor);
            }
        }
    }
}