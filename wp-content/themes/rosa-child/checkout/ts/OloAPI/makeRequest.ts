import { OloError, NetworkError } from '../Common/errorTypes';
import { logError } from '../Utilities/errorLogger';
// Support server-side fetch for tests.
const fetch = typeof window === 'undefined' ? require('node-fetch') : window.fetch;

import bignumJSON from 'json-bignum';

const parseResponse = (responseText: string) => {
  // catch firewall block error
  if(responseText === 'error code: 1020') {
    throw new Error('Your location is blocked');
  }

  try {
    return bignumJSON.parse(responseText);
  } catch (error) {
    throw new OloError('Parse error on the server response ' + responseText);
  }
};

export async function makeRequest(pathAndQuery: string, method: 'GET' | 'POST' | 'PUT' | 'DELETE', data?: unknown) {
  const baseUrl = OLO_API_BASE_URL;
  const url = baseUrl + pathAndQuery;

  const headers = {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  };
  const init: { method: string; headers: Record<string, string>; body?: string } = {
    method,
    headers,
  };
  if (data) {
    init.body = bignumJSON.stringify(data);
  }

  const response = await fetch(url, init);
  const responseText = await response.text();

  if (!response.ok) {
    const error = responseText ? parseResponse(responseText) : new Error(response.statusText);
    throw error;
  }

  return responseText ? parseResponse(responseText) : responseText;
}
