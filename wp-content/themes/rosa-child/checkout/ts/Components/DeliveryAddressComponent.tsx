import React from 'react';
import { OloDeliveryAddress } from '../OloAPI';

export function DeliveryAddressComponent({ deliveryAddress } : { deliveryAddress: OloDeliveryAddress }) {
	const deliveryAddressText = `${deliveryAddress.streetaddress} - ${deliveryAddress.city}, ${deliveryAddress.zipcode}`;

	return (
		<div className="deliveryAddressComponent">
			<strong>{deliveryAddressText}</strong>
		</div>
	);
}