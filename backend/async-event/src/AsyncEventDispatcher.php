<?php

declare(strict_types=1);
/**
 * Copyright (c) The Magic , Distributed under the software license
 */

namespace Dtyq\AsyncEvent;

use Dtyq\AsyncEvent\Kernel\Annotation\AsyncListener;
use Dtyq\AsyncEvent\Kernel\Service\AsyncEventService;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Engine\Coroutine;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class AsyncEventDispatcher implements EventDispatcherInterface
{
    private array $asyncListeners;

    private ListenerProviderInterface $listeners;

    private LoggerInterface $logger;

    private AsyncEventService $asyncEventService;

    public function __construct(
        ListenerProviderInterface $listeners,
        LoggerInterface $logger,
        AsyncEventService $asyncEventService
    ) {
        $this->listeners = $listeners;
        $this->logger = $logger;
        $this->asyncEventService = $asyncEventService;

        $this->asyncListeners = AnnotationCollector::getClassesByAnnotation(AsyncListener::class);
    }

    public function dispatch(object $event): object
    {
        $eventName = get_class($event);

        $syncListeners = [];
        $asyncListeners = [];
        foreach ($this->listeners->getListenersForEvent($event) as $listener) {
            $listenerName = $this->getListenerName($listener);
            if (isset($this->asyncListeners[$listenerName])) {
                $asyncListeners[$listenerName] = $listener;
            } else {
                $syncListeners[$listenerName] = $listener;
            }
        }

        // 投递异步事件
        foreach ($asyncListeners as $listenerName => $listener) {
            Coroutine::create(function () use ($event, $listener, $eventName, $listenerName) {
                $eventRecord = $this->asyncEventService->buildAsyncEventData($eventName, $listenerName, $event);
                $this->asyncEventService->create($eventRecord);
                try {
                    $listener($event);
                    $this->asyncEventService->complete($eventRecord['id']);
                } catch (Throwable $exception) {
                } finally {
                    $this->dump($eventRecord['id'], $eventRecord['listener'], $eventRecord['event'], $exception ?? null);
                }
            });
        }

        // 剩下的直接同步执行
        foreach ($syncListeners as $listenerName => $listener) {
            $listener($event);
            $this->dump(0, $listenerName, $eventName);
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }
        }

        return $event;
    }

    private function dump(int $recordId, string $listenerName, string $eventName, ?Throwable $exception = null): void
    {
        if ($exception) {
            $this->logger->error(sprintf('[event_fail][%d]Event %s handled by %s listener. [exception]%s [trace]%s', $recordId, $eventName, $listenerName, $exception->getMessage(), $exception->getTraceAsString()));
        } else {
            $this->logger->debug(sprintf('[event_success][%d]Event %s handled by %s listener.', $recordId, $eventName, $listenerName));
        }
    }

    private function getListenerName($listener): string
    {
        $listenerName = '[ERROR TYPE]';
        if (is_array($listener)) {
            $listenerName = is_string($listener[0]) ? $listener[0] : get_class($listener[0]);
        } elseif (is_string($listener)) {
            $listenerName = $listener;
        } elseif (is_object($listener)) {
            $listenerName = get_class($listener);
        }
        return $listenerName;
    }
}
