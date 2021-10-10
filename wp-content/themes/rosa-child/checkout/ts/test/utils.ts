/// <reference path="../Common/global.d.ts" />
import dotenv from 'dotenv';
dotenv.config({ path: '../../../../.env' });

export function loadEnv() {
  global.MAPBOX_API_ACCESS_TOKEN = process.env.MAPBOX_API_ACCESS_TOKEN || '';
  global.OLO_API_BASE_URL = process.env.OLO_API_BASE_URL || '';
}

export function createRandomString(arr: string, length: number) {
  let ans = '';
  for (let i = 0; i < length; i++) {
    ans += arr[Math.floor(Math.random() * arr.length)];
  }
  return ans;
}
