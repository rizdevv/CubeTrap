<?php

namespace CubeTrap\scheduler;

use pocketmine\scheduler\Task;
use CubeTrap;
use pocketmine\Server;
use pocketmine\block\Block;
use pocketmine\entity\Location;

class TaskHandler extends Task
{
    public function onRun() : void
    {
        foreach (CubeTrap::getData() as $index => $data)
        {
            if ($data["time"] > 0)
            {
                $data["time"]--;
                CubeTrap::addData($index, $data["time"]);
            } else
            {
                foreach (Server::getInstance()->getOnlinePlayers() as $player)
                {
                    /** @var Block $block */
                    foreach ($data["blocks"] as $block)
                    {
                        /** @var Location $position */
                        foreach ($data["positions"] as $name => $position)
                        {
                            if ($name === $player->getName() and $block->getPosition()->floor()->equals($player->getPosition()->floor())):
                                $player->teleport($position);
                            endif;
                        }
                    }
                }
                CubeTrap::remove($index);
            }
        }
    }
}
