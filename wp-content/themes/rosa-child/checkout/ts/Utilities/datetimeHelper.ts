import { format } from 'date-fns';
import { zonedTimeToUtc, utcToZonedTime } from 'date-fns-tz';

export const isSameDay = (date1: Date, date2: Date) => {
  return date1.getFullYear() === date2.getFullYear() && date1.getMonth() === date2.getMonth() && date1.getDate() === date2.getDate();
};

export function toTimeZone(utcDatetime: Date, timezone: string) {
  return utcToZonedTime(utcDatetime, timezone);
}

export function getTodayUtc() {
  const userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
  const today = new Date();
  return zonedTimeToUtc(today, userTimeZone);
}

export function formatDate(date: Date) {
  return format(date, 'MM/dd/yyyy');
}

export function formatTime(datetime: Date) {
  return format(datetime, 'h:mm aa');
}
