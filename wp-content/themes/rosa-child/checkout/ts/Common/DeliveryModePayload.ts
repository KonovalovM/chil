import { OloDeliveryAddress } from '../OloAPI';

export interface Vehicle {
	make: string;
	model: string;
	color: string;
}

export type DeliveryModePayload =
 | {
 	type: 'pickup',
 	data: undefined
 }
 | {
 	type: 'curbside',
 	data: Vehicle|undefined
 }
 | {
 	type: 'dispatch',
 	data: OloDeliveryAddress|undefined
 }