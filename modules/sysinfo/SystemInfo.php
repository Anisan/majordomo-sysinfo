<?php
class SystemInfo
{

    /**
     * Return RAM Total in Bytes.
     *
     * @return int Bytes
     */
    public function getRamTotal()
    {
        $result = 0;
        if (PHP_OS == 'WINNT') {
            $lines = null;
            $matches = null;
            exec('wmic ComputerSystem get TotalPhysicalMemory /Value', $lines);
            if (preg_match('/^TotalPhysicalMemory\=(\d+)$/', $lines[2], $matches)) {
                $result = $matches[1];
            }
        } else {
            $fh = fopen('/proc/meminfo', 'r');
            while ($line = fgets($fh)) {
                $pieces = array();
                if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
                    $result = $pieces[1];
                    // KB to Bytes
                    $result = $result * 1024;
                    break;
                }
            }
            fclose($fh);
        }
        // KB RAM Total
        return (int) $result;
    }

    /**
     * Return free RAM in Bytes.
     *
     * @return int Bytes
     */
    public function getRamFree()
    {
        $result = 0;
        if (PHP_OS == 'WINNT') {
            $lines = null;
            $matches = null;
            exec('wmic OS get FreePhysicalMemory /Value', $lines);
            if (preg_match('/^FreePhysicalMemory\=(\d+)$/', $lines[2], $matches)) {
                $result = $matches[1] * 1024;
            }
        } else {
            $fh = fopen('/proc/meminfo', 'r');
            while ($line = fgets($fh)) {
                $pieces = array();
                if (preg_match('/^MemFree:\s+(\d+)\skB$/', $line, $pieces)) {
                    // KB to Bytes
                    $result = $pieces[1] * 1024;
                    break;
                }
            }
            fclose($fh);
        }
        // KB RAM Total
        return (int) $result;
    }

    /**
     * Return harddisk infos.
     *
     * @param sring $path Drive or path
     * @return array Disk info
     */
    public function getDiskSize($path = '/')
    {
        $result = array();
        $result['size'] = 0;
        $result['free'] = 0;
        $result['used'] = 0;

        if (PHP_OS == 'WINNT') {
            $lines = null;
            exec('wmic logicaldisk get FreeSpace^,Name^,Size /Value', $lines);
            foreach ($lines as $index => $line) {
                if ($line != "Name=$path") {
                    continue;
                }
                $result['free'] = explode('=', $lines[$index - 1])[1];
                $result['size'] = explode('=', $lines[$index + 1])[1];
                $result['used'] = $result['size'] - $result['free'];
                break;
            }
        } else {
            $lines = null;
            exec(sprintf('df /P %s', $path), $lines);
            foreach ($lines as $index => $line) {
                if ($index != 1) {
                    continue;
                }
                $values = preg_split('/\s{1,}/', $line);
                $result['size'] = $values[1] * 1024;
                $result['free'] = $values[3] * 1024;
                $result['used'] = $values[2] * 1024;
                break;
            }
        }
        return $result;
    }

    /**
     * Get CPU Load Percentage.
     *
     * @return float load percentage
     */
    public function getCpuLoadPercentage()
    {
        $result = -1;
        $lines = null;
        if (PHP_OS == 'WINNT') {
            $matches = null;
            exec('wmic.exe CPU get loadpercentage /Value', $lines);
            if (preg_match('/^LoadPercentage\=(\d+)$/', $lines[2], $matches)) {
                $result = $matches[1];
            }
        } else {
            // https://github.com/Leo-G/DevopsWiki/wiki/How-Linux-CPU-Usage-Time-and-Percentage-is-calculated
            //$tests = array();
            //$tests[] = 'cpu  3194489 5224 881924 305421192 603380 76 52143 106209 0 0';
            //$tests[] = 'cpu  3194490 5224 881925 305422568 603380 76 52143 106209 0 0';

            $checks = array();
            foreach (array(0, 1) as $i) {
                $cmd = '/proc/stat';
                #$cmd = 'grep \'cpu \' /proc/stat <(sleep 1 && grep \'cpu \' /proc/stat) | awk -v RS="" \'{print ($13-$2+$15-$4)*100/($13-$2+$15-$4+$16-$5) "%"}\'';
                #exec($cmd, $lines);
                $lines = array();
                $fh = fopen($cmd, 'r');
                while ($line = fgets($fh)) {
                    $lines[] = $line;
                }
                fclose($fh);
                //$lines = array($tests[$i]);

                foreach ($lines as $line) {
                    $ma = array();
                    if (!preg_match('/^cpu  (\d+) (\d+) (\d+) (\d+) (\d+) (\d+) (\d+) (\d+) (\d+) (\d+)$/', $line, $ma)) {
                        continue;
                    }
                    /**
                     * The meanings of the columns are as follows, from left to right:
                      1st column : user = normal processes executing in user mode
                      2nd column : nice = niced processes executing in user mode
                      3rd column : system = processes executing in kernel mode
                      4th column : idle = twiddling thumbs
                      5th column : iowait = waiting for I/O to complete
                      6th column : irq = servicing interrupts
                      7th column : softirq = servicing softirqs
                      8th column:
                      9th column:

                      Calculation:
                      sum up all the columns in the 1st line "cpu" :
                      ( user + nice + system + idle + iowait + irq + softirq )
                      this will yield 100% of CPU time

                      calculate the average percentage of total 'idle' out of 100% of CPU time :
                      ( user + nice + system + idle + iowait + irq + softirq ) = 100%
                      ( idle ) = X %

                      TOTAL USER = %user + %nice
                      TOTAL CPU = %user + %nice + %system
                      TOTAL IDLE = %iowait + %steal + %idle
                     */
                    $total = $ma[1] + $ma[2] + $ma[3] + $ma[4] + $ma[5] + $ma[6] + $ma[7] + $ma[8] + $ma[9];
                    //$totalCpu = $ma[1] + $ma[2] + $ma[3];
                    //$result = (100 / $total) * $totalCpu;
                    $ma['total'] = $total;
                    $checks[] = $ma;
                    break;
                }

                if ($i == 0) {
                    // Wait before checking again.
                    sleep(1);
                }
            }

            // Idle - prev idle
            $diffIdle = $checks[1][4] - $checks[0][4];

            // Total - prev total
            $diffTotal = $checks[1]['total'] - $checks[0]['total'];

            // Usage in %
            $diffUsage = (1000 * ($diffTotal - $diffIdle) / $diffTotal + 5) / 10;
            $result = $diffUsage;
        }
        return (float) $result;
    }
    
        /**
     * getUpTime.
     *
     * @return string uptime
     */
    public function getUpTime()
    {
        if (PHP_OS == 'WINNT') {
            return "Unknow";
        }
        else
        {
            $lines = array();
            $fh = fopen('/proc/uptime', 'r');
            while ($line = fgets($fh)) {
                $lines[] = $line;
            }
            fclose($fh);
                
            // Get contents
            $contents = $lines[0];
            if ($contents === false) {
                return 'Unknown';
            }
            // Seconds
            list($seconds) = explode(' ', $contents, 1);
            // Get it textual, as in days/minutes/hours/etc
            $uptime = $this->secondsConvert(ceil($seconds));
            // Now find out when the system was booted
            $lines = array();
            $fh = fopen('/proc/stat', 'r');
            while ($line = fgets($fh)) {
                $lines[] = $line;
            }
            fclose($fh);
            $contents = $lines[0];
            // Ugh
            if ($contents === false) {
                return $uptime;
            } // Settle for just uptime
            // Get date of boot
            if (preg_match('/^btime (\d+)$/m', $contents, $boot) != 1) {
                return $uptime;
            }
            // Okay?
            return "Unknow";
        }
    }
    
    public static function secondsConvert($uptime)
    {
        // Method here heavily based on freebsd's uptime source
        $uptime += $uptime > 60 ? 30 : 0;
        $years = floor($uptime / 31556926);
        $uptime %= 31556926;
        $days = floor($uptime / 86400);
        $uptime %= 86400;
        $hours = floor($uptime / 3600);
        $uptime %= 3600;
        $minutes = floor($uptime / 60);
        $seconds = floor($uptime % 60);
        // Send out formatted string
        $return = array();
        if ($years > 0) {
            $return[] = $years.' '.($years > 1 ? 'years' : substr('years', 0, strlen('years') - 1));
        }
        if ($days > 0) {
            $return[] = $days.' days';
        }
        if ($hours > 0) {
            $return[] = $hours.' hours';
        }
        if ($minutes > 0) {
            $return[] = $minutes.' minutes';
        }
        if ($seconds > 0) {
            $return[] = $seconds.(date('m/d') == '06/03' ? ' sex' : ' seconds');
        }
        return implode(', ', $return);
    }

}