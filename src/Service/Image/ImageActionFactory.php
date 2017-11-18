<?php
/*
 * This file is part of ImgCache.
 *
 * (c) Igor Lazarev <strider2038@rambler.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Strider2038\ImgCache\Service\Image;

use Strider2038\ImgCache\Core\ActionFactoryInterface;
use Strider2038\ImgCache\Core\ActionInterface;
use Strider2038\ImgCache\Exception\InvalidRouteException;

/**
 * @author Igor Lazarev <strider2038@rambler.ru>
 */
class ImageActionFactory implements ActionFactoryInterface
{
    private const ACTION_ID_GET = 'get';
    private const ACTION_ID_CREATE = 'create';
    private const ACTION_ID_REPLACE = 'replace';
    private const ACTION_ID_DELETE = 'delete';

    /** @var GetAction */
    private $getAction;

    /** @var CreateAction */
    private $createAction;

    /** @var ReplaceAction */
    private $replaceAction;

    /** @var DeleteAction */
    private $deleteAction;

    public function __construct(
        GetAction $getAction,
        CreateAction $createAction,
        ReplaceAction $replaceAction,
        DeleteAction $deleteAction
    ) {
        $this->getAction = $getAction;
        $this->createAction = $createAction;
        $this->replaceAction = $replaceAction;
        $this->deleteAction = $deleteAction;
    }

    public function createAction(string $actionId): ActionInterface
    {
        $map = $this->getActionsMap();

        if (array_key_exists($actionId, $map)) {
            $action = $map[$actionId];
        } else {
            throw new InvalidRouteException(sprintf('Action "%s" not found', $actionId));
        }

        return $action;
    }

    private function getActionsMap(): array
    {
        return [
            self::ACTION_ID_GET => $this->getAction,
            self::ACTION_ID_CREATE => $this->createAction,
            self::ACTION_ID_REPLACE => $this->replaceAction,
            self::ACTION_ID_DELETE => $this->deleteAction
        ];
    }
}
