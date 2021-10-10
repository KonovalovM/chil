import React from 'react';
import { DeliveryModePayload } from '../Common/DeliveryModePayload';
import { OloUser, OloDeliverymode } from '../OloAPI';
import { DeliveryAddressFields } from './DeliveryAddressFields';
import { VehicleFields } from './VehicleFields';

interface DeliveryModePayloadSelectorProps {
	user: OloUser;
	deliveryModePayload: DeliveryModePayload;
	updateDeliveryModePayload: (deliveryModePayload: DeliveryModePayload) => void;
	showValidationErrors: boolean;
}

export function DeliveryModePayloadSelector(props: DeliveryModePayloadSelectorProps) {
	const { user, deliveryModePayload, updateDeliveryModePayload, showValidationErrors } = props;

	if(deliveryModePayload.type === 'curbside') {
		return (
			<div>
				<VehicleFields
					deliveryModePayload={deliveryModePayload}
					updateDeliveryModePayload={updateDeliveryModePayload}
					showValidationErrors={showValidationErrors}
				/>
		</div>);
	}

	if(deliveryModePayload.type === 'dispatch' ) {
		return (
			<div>
				<DeliveryAddressFields
					isGuestUser={user.type === 'guest'}
					deliveryModePayload={deliveryModePayload}
					updateDeliveryModePayload={updateDeliveryModePayload}
					showValidationErrors={showValidationErrors}
				/>
			</div>
		);
	}

	return null;
}