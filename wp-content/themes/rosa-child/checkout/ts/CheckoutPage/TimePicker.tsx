import map from 'lodash/map';
import range from 'lodash/range';

import { differenceInMinutes, addMinutes } from 'date-fns';

import React, { useState, useEffect, useCallback } from 'react';
import classnames from 'classnames';

import { TimeModePayload } from '../Common/TimeModePayload';
import { isSameDay, toTimeZone, formatTime, getTodayUtc } from '../Utilities/datetimeHelper';
import { getDateString, isRestaurantOpen } from '../Utilities/modelHelper';
import { getAsset } from '../Utilities/assetsHelper';

import { OloRestaurantCalendar } from '../OloAPI';

const cellWidth = 120;
const delta = 3;

interface CalendarRange {
  start: Date;
  end: Date;
}

export function TimePicker(props: {
  calendar: OloRestaurantCalendar;
  earliestreadytime?: Date;
  timezone: string;
  timeModePayload: TimeModePayload;
  updateTimeModePayload: (timeModePayload: TimeModePayload) => void;
  showValidationErrors: boolean;
}) {
  const { calendar, earliestreadytime, timezone, timeModePayload, updateTimeModePayload, showValidationErrors } = props;
  const [currentRange, setCurrentRange] = useState<CalendarRange>(calendar[0]);
  const [times, setTimes] = useState<Date[]>([]);
  const [timePosition, setTimePosition] = useState<number>(0);

  //when the currentRange change, the time should be reset
  useEffect(() => {
    updateTimeModePayload({ type: timeModePayload.type, data: undefined });
    const today = toTimeZone(getTodayUtc(), timezone);
    if (today > currentRange.end && !isRestaurantOpen(calendar, timezone)) {
      setTimes([]);
      setTimePosition(0);
      return;
    }

    const earliestime =  earliestreadytime ? earliestreadytime : today;
    const start = roundTime(addMinutes(isSameDay(earliestime, currentRange.start) ? earliestime : currentRange.start, 5));
    const end = currentRange.end;

    setTimes(createTimeRange(start, end));
    setTimePosition(0);
  }, [currentRange]);

  const handleCurrentRangeChange = useCallback(
    (event: React.FormEvent<HTMLSelectElement>) => setCurrentRange(unserializeCalendarRange(event.currentTarget.value)),
    [setCurrentRange]
  );

  const next = useCallback(() => {
    const maxPosition = times.length - delta;
    const nextPosition = timePosition + delta;

    setTimePosition(nextPosition > maxPosition ? maxPosition : nextPosition);
  }, [times, timePosition, setTimePosition]);

  const previous = useCallback(() => {
    const minPosition = 0;
    const prevPosition = timePosition - delta;

    setTimePosition(prevPosition < minPosition ? minPosition : prevPosition);
  }, [timePosition, setTimePosition]);

  const selectTimeHandler = useCallback(
    (time: Date) => {
      return () => {
        updateTimeModePayload({
          type: 'advance',
          data: { timewanted: time }
        });
      };
    },
    [updateTimeModePayload]
  );

  const isSelected = useCallback(
    (time: Date) => {
      const timewanted = timeModePayload.type === 'advance' && timeModePayload.data ? timeModePayload.data.timewanted : undefined;
      return !timewanted ? false : timewanted.getTime() === time.getTime();
    },
    [timeModePayload]
  );

  const stylePosition = { transform: `translateX(${-1 * timePosition * cellWidth}px)` };

  return (
    <div className="timePicker">
      {showValidationErrors && timeModePayload.data === undefined ? <div className="errorMessage">*Select an schedule time.</div> : null }
      <h3>Choose day</h3>
      <select value={serializeCalendarRange(currentRange)} onChange={handleCurrentRangeChange}>
        {map(calendar, (calendarRange, index) => {
          return (
            <option key={index} value={serializeCalendarRange(calendarRange)}>
              {getDateString(calendarRange.start, timezone)}
            </option>
          );
        })}
      </select>

      <h3>Choose time</h3>
      {!times.length ? (
        <p className="errorMessage">No time available for this day. Please choose another day.</p>
      ) : (
        <div className="picker">
          <button type="button" onClick={previous}>
            <img src={getAsset('previous.svg')} />
          </button>
          <div className="timesWrapper">
            <div className="times" style={stylePosition}>
              {map(times, (time, index) => {
                return (
                  <div key={index} onClick={selectTimeHandler(time)} className={classnames('time', { selected: isSelected(time) })}>
                    {formatTime(time)}
                  </div>
                );
              })}
            </div>
          </div>
          <button type="button" onClick={next}>
            <img src={getAsset('next.svg')} />
          </button>
        </div>
      )}
    </div>
  );
}

function serializeCalendarRange(calendarRange: CalendarRange): string {
  return JSON.stringify({ start: calendarRange.start.toJSON(), end: calendarRange.end.toJSON() });
}

function unserializeCalendarRange(serializedRange: string): CalendarRange {
  const data = JSON.parse(serializedRange);
  return { start: new Date(data.start), end: new Date(data.end) };
}

function roundTime(date: Date) {
  const minutes = date.getMinutes();

  const roundMinutes = 15 * Math.ceil(minutes / 15);

  const newDate = new Date(date);
  newDate.setMinutes(roundMinutes);
  return newDate;
}

function createTimeRange(start: Date, end: Date) {
  const diff = differenceInMinutes(end, start);
  const step = 15;
  const numTimes = diff / step;

  const ranges = range(0, numTimes);
  return map(ranges, i => addMinutes(start, i * step));
}
