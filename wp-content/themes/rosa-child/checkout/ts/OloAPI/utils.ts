import find from 'lodash/find';
import memoize from 'lodash/memoize';
import map from 'lodash/map';

import { addZeroPadding } from '../Utilities/formatHelper';

export function dateToStringAPIFormat(date: Date): string {
  const year = date.getFullYear();
  const month = addZeroPadding(date.getMonth() + 1);
  const day = addZeroPadding(date.getDate());
  return `${year}${month}${day}`;
}

//dateString format: "yyyymmdd hh:mm"
export function stringToDate(dateString: string) {
  const year = Number(dateString.slice(0, 4));
  const month = Number(dateString.slice(4, 6)) - 1;
  const day = Number(dateString.slice(6, 8));
  const hour = Number(dateString.slice(9, 11));
  const minutes = Number(dateString.slice(12));

  return new Date(year, month, day, hour, minutes);
}

export function utcOffsetToTimeZone(utcOffset: number) {
  const hour = addZeroPadding(Math.abs(utcOffset));
  const sign = utcOffset >= 0 ? '+' : '-';
  return `${sign}${hour}:00`;
}

export function parseImageUrl(
  imagepath: string | undefined,
  images: Array<{ groupname: string; filename: string }> | undefined,
  groupname: string,
  defaultImage: string
): string {
  const image = find(images, { groupname });
  if (!imagepath || !images || !image || !image.filename) {
    return defaultImage;
  }
  return imagepath + image.filename;
}

export function parseCalories(basecalories: number | undefined, maxcalories: number | undefined): number | { min: number; max: number } {
  return (basecalories && maxcalories ? { min: Number(basecalories), max: Number(maxcalories) } : Number(basecalories)) || 0;
}

export function parseMetadata(metadata: Array<{ key: string; value: string }> | undefined, key: string): string | undefined {
  if (!metadata) {
    return undefined;
  }

  const entry = find(metadata, { key });
  if (!entry) {
    return undefined;
  }
  return entry.value;
}

export function parseAvailability(availability: { now?: boolean; description?: string } | undefined): { available: boolean; description: string } {
  if (availability === undefined) {
    return { available: true, description: '' };
  }
  return {
    available: Boolean(availability.now),
    description: Boolean(availability.now) ? '' : availability.description || 'Unavailable',
  };
}

export const getRestaurantImages = memoize(() => {
  try {
    const element = document.getElementById('restaurants-image-json');
    if (!element) {
      return [];
    }
    const data = JSON.parse(element.innerHTML);
    return map(data.restaurants, (imageData: { olo_id: string; image: string }) => {
      return { restaurantId: imageData.olo_id, image: imageData.image };
    });
  } catch (error) {
    // tslint:disable-next-line:no-console
    console.error(error);
    return [];
  }
});
