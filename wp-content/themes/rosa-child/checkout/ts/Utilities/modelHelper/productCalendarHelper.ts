import find from 'lodash/find';
import { differenceInHours, addMinutes } from 'date-fns';

import { OloRestaurantCalendar } from '../../OloAPI';
import { formatTime, toTimeZone, isSameDay, getTodayUtc } from '../datetimeHelper';

//calendar is in the local time of the restaurant
export const getFirstAvailableDay = (calendar: OloRestaurantCalendar, restaurantTimezone: string) => {
  const today = toTimeZone(getTodayUtc(), restaurantTimezone);
  const firstRangeAvailable = find(calendar, ({ start, end }) => {
    if (today >= start && today <= end) {
      return true;
    }
    if (today <= end) {
      return true;
    }
    return false;
  });
  if(!firstRangeAvailable) {
    return undefined;
  }

  return addMinutes(firstRangeAvailable.start, 20);
};

export const isRestaurantOpen = (calendar: OloRestaurantCalendar, restaurantTimezone: string) => {
  const today = toTimeZone(getTodayUtc(), restaurantTimezone);
  const todayRange = find(calendar, range => today > range.start && today < range.end);
  return todayRange ? true : false;
};

export const getCalendarString = (calendar: OloRestaurantCalendar, restaurantTimezone: string) => {
  if (!calendar.length) {
    return 'Calendar not available';
  }

  //current hour of the restaurant timezone
  const today = toTimeZone(getTodayUtc(), restaurantTimezone);

  const todayRange = find(calendar, range => today > range.start && today < range.end);
  if (!todayRange) {
    return 'Closed';
  }

  const startDate = todayRange.start;
  const endDate = todayRange.end;

  if (differenceInHours(endDate, startDate) === 24) {
    return 'Open 24 hours';
  }

  return `Open now until ${formatTime(endDate)}`;
};

export function getDateString(date: Date, restaurantTimezone: string) {
  const todayLocalToRestaurant = toTimeZone(getTodayUtc(), restaurantTimezone);

  if (isSameDay(todayLocalToRestaurant, date)) {
    return 'Today';
  } else if (date.getDate() - todayLocalToRestaurant.getDate() === 1) {
    return 'Tomorrow';
  } else {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
  }
}
