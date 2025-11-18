<?php

namespace App\Helpers;

class MachineStatusHelper
{
    /**
     * Cek apakah mesin berhenti berdasarkan data dan ambang batas.
     *
     * @param array $data
     * @param int $rpmThreshold
     * @param int $counterThreshold
     * @return bool
     */
    public static function isStopped(array $data, int $rpmThreshold = 0, int $counterThreshold = 0): bool
    {
        $rpm = $data['rpm'] ?? null;
        $counter = $data['counter'] ?? null;

        return is_numeric($rpm) && is_numeric($counter)
            && ($rpm <= $rpmThreshold || $counter <= $counterThreshold);
    }
}
