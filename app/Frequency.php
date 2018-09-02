<?php

namespace App;

use Carbon\Carbon;

class Frequency
{
    protected $nth, $what, $at;

    protected $valid = false;

    public function __construct($frequency)
    {
        return $this->parse($frequency);
    }

    protected function parse($frequency)
    {
        $values = explode(' ', $frequency);

        // Make sure the frequency has a nth, what and at value
        if (count($values) !== 3) {
            return false;
        }

        $nth = (int) $values[0];
        $what = (string) $values[1];
        $at = $values[2];

        // Validate the at and what parameter
        if ($what === 'weeks') {
            if ($at <= 0 || $at > 6) {
                return false;
            }
        } elseif ($what === 'months') {
            if ($at != 'end' && ($at < 1 || $at > 28)) {
                return false;
            }
        } else {
            return false;
        }

        $this->nth = $nth;
        $this->what = $what;
        $this->at = $at;

        $this->valid = true;
    }

    public function getNth()
    {
        return $this->nth;
    }

    public function getWhat()
    {
        return $this->what;
    }

    public function getAt()
    {
        return $this->at;
    }

    public function next($after = null)
    {
        if (is_null($after)) {
            $after = Carbon::now();
        }

        if ($this->what == 'months') {
            $after->day = 1;
            $after->addMonth($this->nth);
            if ($this->at == 'end') {
                $after = $after->endOfMonth();
            } else {
                $after->day = $this->at;
            }
        } elseif ($this->what == 'weeks') {
            $after->addWeek($this->nth);
            $after->addDay($this->at - $after->dayOfWeek);
        }

        return $after;
    }

    public function isValid()
    {
        return $this->valid;
    }

    public function display()
    {
        if ($this->what == 'months' && $this->at == 'end') {
            $text = 'End of every ';
        } else {
            $text = 'Every ';
        }

        switch ($this->nth) {
            case 1:
                break;

            case 2:
                $text .= 'other ';
                break;

            case 3:
                $text .= '3rd ';
                break;

            default:
                $text .= $this->nth . 'th ';
                break;
        }

        if ($this->what == 'weeks') {
            $text .= 'week on ';

            $days = [
                'Sunday',
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday',
            ];

            $text .= $days[$this->at];
        } elseif ($this->what == 'months') {
            $text .= 'month ';

            if ($this->at != 'end') {
                $text .= 'on the ';

                switch ($this->at) {
                    case 1:
                        $text .= '1st';
                        break;

                    case 2:
                        $text .= '2nd';
                        break;

                    case 3:
                        $text .= '3rd';
                        break;

                    default:
                        $text .= $this->at . 'th';
                        break;
                }
            }
        }

        return $text;
    }
}
