<?php
namespace CroudTech\RecurringTaskScheduler\Repository;

use Carbon\Carbon;
use CroudTech\RecurringTaskScheduler\Contracts\ScheduleEventRepositoryContract;
use CroudTech\RecurringTaskScheduler\Model\ScheduleEvent;
use CroudTech\Repositories\Contracts\RepositoryContract;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ScheduleEventRepository extends BaseRepository implements RepositoryContract, ScheduleEventRepositoryContract {

    /**
     * Return the model name for this repository
     *
     * @method getModelName
     * @return string
     */
    public function getModelName() : string
    {
        return ScheduleEvent::class;
    }

    /**
     * @param Collection | QueryBuilder $this->query()
     * @param $request
     * @return void
     */
    protected function modifyApiPaginateQuery(Request $request)
    {
        if (empty($request['all_events'])) {
            $this->query()->futureEvents();
        }

        unset($request['all_events']);

        parent::modifyApiPaginateQuery($request);
    }

    /**
     * Get all events for the specified timestamp
     *
     * @return Collection
     */
    public function getEventsForTimestamp(Carbon $timestamp) : Collection
    {
        return $this->query()
            ->whereBetween('date', [$timestamp->copy()->startOfDay(), $timestamp])
            ->whereNull('triggered_at')
            ->with('schedule')
            ->get();
    }
}
