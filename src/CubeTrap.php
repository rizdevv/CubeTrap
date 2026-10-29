<?php

use pocketmine\plugin\PluginBase;
use CubeTrap\event\EventHandler;
use CubeTrap\scheduler\TaskHandler;
use pocketmine\player\Player;
use pocketmine\block\VanillaBlocks;
use pocketmine\block\Block;

class CubeTrap extends PluginBase
{
    private static array $data = [];
    private static int $index = 0;

    public function onEnable() : void
    {
        $this->getServer()->getPluginManager()->registerEvents(new EventHandler(), $this);
        $this->getScheduler()->scheduleRepeatingTask(new TaskHandler(), 20);
    }

    public static function getData(?int $index = null) : ?array
    {
        return $index !== null ? self::$data[$index] ?? null : self::$data;
    }

    public static function addData(int $index, ?int $time = null, ?array $position = null, ?array $block = null) : void
    {
        is_int($time) and self::$data[$index]["time"] = $time;
        is_array($position) and self::$data[$index]["positions"] = $position;
        is_array($block) and self::$data[$index]["blocks"] = $block;
    }

    public static function removeData(int $index) : void
    {
        unset(self::$data[$index]);
    }

    public static function create(Player $player) : void
    {
        $positions = [];
        $blocks = [];
        foreach ($player->getServer()->getOnlinePlayers() as $players)
        {
            $position = $players->getLocation();
            if ($player->getPosition()->floor()->distance($position->floor()) <= 2):
                $positions[$players->getName()] = $position;
                var_dump($positions);
            endif;
        }
        for ($x = -2; $x <= 2; $x++)
        {
            for ($y = -2; $y <= 2; $y++)
            {
                for ($z = -2; $z <= 2; $z++)
                {
                    $world = $player->getWorld();
                    $currentPosition = $player->getPosition()->add($x, $y, $z);
                    $blocks[] = $world->getBlock($currentPosition);
                    $world->setBlock($currentPosition, count(array_intersect([$x, $y, $z], [-2, 2])) > 0 ? mt_rand(1, 2) == 1 ? VanillaBlocks::OBSIDIAN() : VanillaBlocks::CRYING_OBSIDIAN() : VanillaBlocks::AIR());
                }
            }
        }
        self::addData(self::$index, 15, $positions, $blocks);
        self::$index++;
    }

    public static function remove(int $index) : void
    {
        $data = self::getData($index);
        if ($data)
        {
            /** @var Block $block */
            foreach ($data["blocks"] as $block)
            {
                $position = $block->getPosition();
                $position->getWorld()->setBlock($position, $block);
            }
        }
        self::removeData($index);
    }
}
