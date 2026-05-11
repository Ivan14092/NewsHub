<?php

namespace App\Scheduler;

use App\Message\UpdatePricesMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('prices')]
final class PricesUpdateSchedule implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(RecurringMessage::every('5 minutes', new UpdatePricesMessage('BTC')))
            ->add(RecurringMessage::every('1 hour', new UpdatePricesMessage('USD')));
    }
}