export type TimeModePayload =
	| {
		type: 'asap';
		data: undefined;
	}
	| {
		type: 'advance';
		data: {
			timewanted: Date
		} | undefined;
	}