import { captureException } from '@sentry/react';
import { OloError } from '../Common/errorTypes';

export function logError(error: Error) {
	if(error instanceof OloError) {
		captureException(error);
	}
}
