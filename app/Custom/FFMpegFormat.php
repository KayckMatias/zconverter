<?php

namespace App\Custom;

use FFMpeg\Format\Video\X264;

class FFMpegFormat extends X264
{
    public function getAvailableAudioCodecs()
    {
        return ['copy', 'aac', 'libvo_aacenc', 'libfaac', 'libmp3lame', 'libtwolame', 'libfdk_aac'];
    }

    public function getAvailableVideoCodecs()
    {
        return ['libx264', 'libx265', 'mpeg4', 'libxvid'];
    }
}
