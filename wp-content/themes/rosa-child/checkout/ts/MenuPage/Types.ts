import { OloRestaurantMenuProductModifier } from '../OloAPI';

export interface ProductModifierInfo {
  productId: string;
  modifiers: OloRestaurantMenuProductModifier[];
}
