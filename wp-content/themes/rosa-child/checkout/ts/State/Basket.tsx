import React from 'react';
import { observable, action } from 'mobx';
import map from 'lodash/map';
import assign from 'lodash/assign';
import { loadValue, saveValue, removeValue } from './localStorage';
import { makeWithObservable } from '../Utilities/componentHelper';
import { LoadingStatus } from '../Common/Types';
import { PaymentMethod } from '../Common/PaymentMethod';
import { DeliveryModePayload } from '../Common/DeliveryModePayload';
import {
  OloBasket,
  OloBasketNewProduct,
  OloBasketNewProductChoice,
  OloRestaurantMenuProduct,
  OloOrderStatus,
  OloUser,
  OloDeliveryAddress,
  getBasket,
  createBasket,
  addBasketProduct,
  editBasketProduct,
  deleteBasketProduct,
  setDeliveryMode as oloSetDeliveryMode,
  setDeliveryAddress,
  setCustomField,
  setTimeWanted as oloSetTimeWanted,
  deleteTimeWanted as oloDeleteTimeWanted,
  applyCoupon as oloApplyCoupon,
  removeCoupon as oloRemoveCoupon,
  addTip as oloAddTip,
  createFromOrder as oloCreateFromOrder,
  validateBasket as oloValidateBasket,
  submitBasket as oloSubmitBasket,
} from '../OloAPI';
import { getChoicesText } from '../Utilities/modelHelper';

/* --------------------------------------------------------------------------*/
/* State                                                                     */
/* --------------------------------------------------------------------------*/
export type BasketState = (
  | {
      status: LoadingStatus.progress;
    }
  | {
      error: Error;
      status: LoadingStatus.error;
    }
  | {
      data: OloBasket | undefined;
      status: LoadingStatus.success;
    }
) & {
  open: boolean;
};

const basketState = observable({
  data: undefined,
  status: LoadingStatus.progress,
  open: false,
} as BasketState);

/* --------------------------------------------------------------------------*/
/* Helper Functions                                                          */
/* --------------------------------------------------------------------------*/

const setBasketError = action((error: Error) => {
  assign(basketState, { error, status: LoadingStatus.error });
});

const setBasketState = action((newBasket: OloBasket | undefined) => {
  assign(basketState, { data: newBasket, status: LoadingStatus.success });
  newBasket ? saveValue('basketId', newBasket.id) : removeValue('basketId');
});

const setBasketProperties = action((properties: any) => {
  const basketData = requireBasketData();
  if(basketState.status == LoadingStatus.success) {
    basketState.data = assign({}, basketData, properties) as OloBasket;
  }
});

const requireBasketData = () => {
  if (basketState.status !== LoadingStatus.success || !basketState.data) {
    throw new Error('Unable to access basket: There is no basket');
  }
  return basketState.data;
};

const maybeCreateBasketData = async (restaurantId: string, authtoken: string|undefined) => {
  if (basketState.status !== LoadingStatus.success) {
    throw new Error('Unable to create basket: Basket loading failed');
  }
  if (!basketState.data) {
    const newBasket = await createBasket(restaurantId, authtoken);
    setBasketState(newBasket);
    return newBasket;
  } else {
    return basketState.data;
  }
};

function handleBasketErrors<Arguments extends unknown[], ReturnType>(
  operation: (...args: Arguments) => Promise<ReturnType>
): (...args: Arguments) => Promise<ReturnType> {
  return async (...args: Arguments) => {
    try {
      return await operation(...args);
    } catch (error) {
      setBasketError(error);
      throw error;
    }
  };
}

export const createBasketNewProduct = (
  product: OloRestaurantMenuProduct,
  quantity: number,
  selectedChoices: OloBasketNewProductChoice[],
  specialinstructions?: string
): OloBasketNewProduct => {
  const choices = map(selectedChoices, selectedChoice => {
    return {
      choiceid: selectedChoice.choiceid,
      quantity: selectedChoice.quantity,
      customfields: [],
    };
  });

  return {
    productid: product.id,
    quantity,
    specialinstructions,
    customdata: product.modifiers ? getChoicesText(product.modifiers, selectedChoices) : '',
    choices,
  };
};

/* --------------------------------------------------------------------------*/
/* State Interaction Functions                                               */
/* --------------------------------------------------------------------------*/

export function withBasketState<PropType extends { basketState: BasketState }>(
  WrappedComponent: React.FunctionComponent<PropType>
): React.FunctionComponent<Omit<PropType, 'basketState'>> {
  return (props: Omit<PropType, 'basketState'>) => {
    return <WrappedComponent {...(props as PropType)} basketState={basketState} />;
  };
}

export const withBasket = makeWithObservable('basket', basketState);

export const loadBasket = handleBasketErrors(async () => {
  const basketId = loadValue('basketId');
  if (!basketId) {
    setBasketState(undefined);
    return;
  }

  try {
    setBasketState(await getBasket(basketId));
  } catch (error) {
    if (error.message === 'Could not load shopping basket') {
      setBasketState(undefined);
      return;
    }
    if (error.message === 'This order has already been placed') {
      setBasketState(undefined);
      return;
    }
    throw error;
  }
});

export const addProduct = async (restaurantId: string, authtoken: string|undefined, product: OloBasketNewProduct) => {
  const basketData = await maybeCreateBasketData(restaurantId, authtoken);
  setBasketState(await addBasketProduct(basketData.id, product));
};

export const editProduct = async (product: OloBasketNewProduct) => {
  const basketData = requireBasketData();
  setBasketProperties(await editBasketProduct(basketData.id, product));
};

export const deleteProduct = async (idBasketProduct: string) => {
  const basketData = requireBasketData();
  setBasketState(await deleteBasketProduct(basketData.id, idBasketProduct));
};

export const setDeliveryMode = async (deliveryMode: string) => {
  const basketData = requireBasketData();
  setBasketProperties(await oloSetDeliveryMode(basketData.id, deliveryMode));
}

export const setPickupMode = async () => {
  const basketData = requireBasketData();
  setBasketProperties(await oloSetDeliveryMode(basketData.id, 'pickup'));
}

export const setDispatchMode = async (deliveryAddress: OloDeliveryAddress) => {
  const basketData = requireBasketData();
  setBasketProperties(await setDeliveryAddress(basketData.id, deliveryAddress));
}

export const setCurbsideMode = async (curbsideCustomFields: Array<{id: string, value: string}>) => {
  const basketData = requireBasketData();
  setBasketProperties(await oloSetDeliveryMode(basketData.id, 'curbside'));
  await Promise.all(map(curbsideCustomFields, customfield => setCustomField(basketData.id, customfield)));
}

export const setTimeWanted = async (date: Date) => {
  const basketData = requireBasketData();
  setBasketProperties(await oloSetTimeWanted(basketData.id, date));
};

export const deleteTimeWanted = async () => {
  const basketData = requireBasketData();
  await oloDeleteTimeWanted(basketData.id);
};

export const applyCoupon = async (couponcode: string) => {
  const basketData = requireBasketData();
  setBasketProperties(await oloApplyCoupon(basketData.id, couponcode));
}

export const removeCoupon = async () => {
  const basketData = requireBasketData();
  setBasketProperties(await oloRemoveCoupon(basketData.id));
}

export const addTip = async (amount: number) => {
  const basketData = requireBasketData();
  setBasketProperties(await oloAddTip(basketData.id, amount));
}

export async function createFromOrder(authtoken: string, orderid: string) {
  setBasketState(await oloCreateFromOrder(authtoken, orderid));
}

export async function validateBasket() {
  const basketData = requireBasketData();
  return await oloValidateBasket(basketData.id);
}

export async function submitBasket(user: OloUser, paymentMethod: PaymentMethod): Promise<OloOrderStatus> {
  const basketData = requireBasketData();
  try {
    const orderStatus = await oloSubmitBasket(basketData.id, user, paymentMethod);
    cleanBasket();
    return orderStatus;
  } catch (error) {
    if (error.message === 'Credit Card not supported by this location') {
      throw new Error('Please re-enter your Credit Card information to process your payment');
    }
    throw error;

  }
}

export const cleanBasket = () => {
  setBasketState(undefined);
};

export const getBasketData = () => {
  return basketState.status === LoadingStatus.success ? basketState.data : undefined;
};

export const closeBasket = action(() => {
  basketState.open = false;
});

export const openBasket = action(() => {
  basketState.open = true;
});
