<?php

namespace CubeTrap\event;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\ItemTypeIds;
use CubeTrap;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\block\Block;
use pocketmine\event\block\BlockPlaceEvent;

class EventHandler implements Listener
{
    public function PlayerItemUseEvent(PlayerItemUseEvent $event) : void
    {
        $item = $event->getItem();
        if ($item->getTypeId() == ItemTypeIds::NETHERITE_SCRAP)
        {
            $player = $event->getPlayer();
            $player->getInventory()->removeItem($item->setCount(1));
            CubeTrap::create($player);
        }
    }

    public function BlockBreakEvent(BlockBreakEvent $event) : void
    {
        foreach (CubeTrap::getData() as $data)
        {
            /** @var Block $block */
            foreach ($data["blocks"] as $block)
            {
                if ($event->getBlock()->getPosition()->floor()->equals($block->getPosition()->floor())):
                    $event->setDrops([]);
                endif;
            }
        }
    }

    public function BlockPlaceEvent(BlockPlaceEvent $event) : void
    {
        foreach (CubeTrap::getData() as $data)
        {
            /** @var Block $block */
            foreach ($data["blocks"] as $block)
            {
                if ($event->getBlockAgainst()->getPosition()->floor()->equals($block->getPosition()->floor())):
                    $event->cancel();
                endif;
            }
        }
    }
}
