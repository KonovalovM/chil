import React from 'react';
import { OloRestaurantCalendar } from '../OloAPI';
import { TimeModePayload } from '../Common/TimeModePayload';
import { TimePicker 	} from './TimePicker';

interface TimeSelectorProps {
	calendar: OloRestaurantCalendar;
	earliestreadytime?: Date;
	timezone: string;
	timeModePayload: TimeModePayload;
	updateTimeModePayload: (timeModePayload: TimeModePayload) => void;
	showValidationErrors: boolean;
}

export function TimeSelector(props: TimeSelectorProps) {
	const { calendar, earliestreadytime, timezone, timeModePayload, updateTimeModePayload, showValidationErrors } = props;

	if(timeModePayload.type === "asap") {
		return null;
	}

	if(timeModePayload.type === "advance") {
		return (
			<TimePicker
				calendar={calendar}
        earliestreadytime={earliestreadytime}
        timezone={timezone}
        timeModePayload={timeModePayload}
        updateTimeModePayload={updateTimeModePayload}
        showValidationErrors={showValidationErrors}
			/>
		)
	}

	return null;
}