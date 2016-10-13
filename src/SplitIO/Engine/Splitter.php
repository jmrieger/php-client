<?php
namespace SplitIO\Engine;

use SplitIO\Split as SplitApp;
use SplitIO\Grammar\Condition\Partition;

class Splitter
{
    /**
     * @param string $key
     * @param long $seed
     * @param array $partitions
     * @return null|string
     */
    public static function getTreatment($key, $seed, $partitions)
    {
        $logMsg = "Splitter evaluating partitions ... \n
        Bucketing Key: $key \n
        Seed: $seed \n
        Partitions: ". print_r($partitions, true);

        SplitApp::logger()->debug($logMsg);

        $bucket = abs(\SplitIO\hash($key, $seed) % 100) + 1;

        SplitApp::logger()->info("Butcket: ".$bucket);

        $accumulatedSize = 0;
        foreach ($partitions as $partition) {
            if ($partition instanceof Partition) {
                $accumulatedSize += $partition->getSize();
                if ($bucket <= $accumulatedSize) {
                    return $partition->getTreatment();
                }
            }
        }

        return null;
    }
}
