import map from 'lodash/map';
import assign from 'lodash/assign';
import omit from 'lodash/omit';

import { PaymentMethod, BillingType, CreditCard } from '../Common/PaymentMethod';
import { stringToDate } from './utils';
import { OloUser, OloBillingAccount, OloDeliveryAddress } from './user';
import { OloOrderStatus, buildOrderStatus } from './orders';
import { makeRequest } from './makeRequest';
/* --------------------------------------------------------------------------*/
/* Types                                                                     */
/* --------------------------------------------------------------------------*/
export type OloDeliverymode = 'pickup'|'curbside'|'dispatch';
export type OloTimemode = 'asap'|'advance';

export interface OloCoupon {
  couponcode: string;
  description?: string;
  coupondiscount?: number;
}

export interface OloBasket {
  id: string;
  restaurantId: string;
  earliestreadytime?: Date;
  timemode: OloTimemode;
  deliverymode: OloDeliverymode;
  deliveryAddress?: OloDeliveryAddress;
  coupon?: OloCoupon;
  allowsTip: boolean;
  tip: number;
  subtotal: number;
  tax: number;
  customerhandoffcharge: number;
  total: number;
  products: OloBasketProduct[];
}

export interface OloBasketProduct {
  id: string;
  productId: string;
  name: string;
  quantity: number;
  basecost: number;
  totalcost: number;
  specialinstructions: string;
  customdata: string;
  recipient: string;
  choices: OloBasketProductChoice[];
}

export interface OloBasketProductChoice {
  id: string;
  optionid: string;
  name: string;
  cost: number;
  quantity: number;
  customfields?: OloBasketProductCustomField[];
}

export interface OloBasketProductCustomField {
  fieldid: number;
  value: string;
}

export interface OloBasketNewProduct {
  productid: string;
  quantity: number;
  specialinstructions?: string;
  recipient?: string;
  customdata?: string;
  choices?: OloBasketNewProductChoice[];
}

export interface OloBasketNewProductChoice {
  choiceid: string;
  quantity: number;
  customfields?: OloBasketProductCustomField[];
}

export interface OloBasketValidation {
  basketId: string;
  tax: number;
  customerhandoffcharge: number;
  subtotal: number;
  total: number;
}

/* --------------------------------------------------------------------------*/
/* Utility Functions                                                         */
/* --------------------------------------------------------------------------*/

// tslint:disable-next-line:no-any
function buildChoice(choice: any): OloBasketProductChoice {
  return {
    id: choice.id.toString(),
    optionid: choice.optionid.toString(),
    name: String(choice.name),
    cost: Number(choice.cost),
    quantity: Number(choice.quantity),
    customfields: map(choice.customfields, customfield => {
      return {
        fieldid: Number(customfield.fieldid),
        value: String(customfield.value),
      };
    }),
  };
}

function buildCoupon(basketData: any):OloCoupon|undefined {
  if(!basketData.coupon) {
    return undefined;
  }

  const coupon = basketData.coupon;
  return {
    couponcode: coupon.couponcode,
    description: coupon.description,
    coupondiscount: Number(basketData.coupondiscount)
  }
}

// tslint:disable-next-line:no-any
function buildBasket(basket: any): OloBasket {
  return {
    id: basket.id.toLowerCase(),
    restaurantId: basket.vendorid.toString(),
    earliestreadytime: stringToDate(basket.earliestreadytime),
    timemode: String(basket.timemode) as OloTimemode,
    deliverymode: String(basket.deliverymode) as OloDeliverymode,
    deliveryAddress: basket.deliveryaddress,
    coupon: buildCoupon(basket),
    allowsTip: Boolean(basket.allowstip),
    tip: Number(basket.tip),
    subtotal: Number(basket.subtotal),
    tax: Number(basket.salestax),
    customerhandoffcharge: Number(basket.customerhandoffcharge),
    total: Number(basket.total),
    products: map(basket.products, product => ({
      id: product.id.toString(),
      productId: product.productId.toString(),
      name: product.name,
      quantity: Number(product.quantity),
      basecost: Number(product.basecost),
      totalcost: Number(product.totalcost),
      specialinstructions: product.specialinstructions,
      customdata: product.customdata,
      recipient: product.recipient ? String(product.recipient) : '',
      choices: map(product.choices, buildChoice),
    })),
  };
}

/* --------------------------------------------------------------------------*/
/* API Endpoints                                                             */
/* --------------------------------------------------------------------------*/

export async function getBasket(basketId: string): Promise<OloBasket> {
  return buildBasket(await makeRequest(`/v1.1/baskets/${basketId}`, 'GET'));
}

export async function createBasket(restaurantId: string, authtoken?: string): Promise<OloBasket> {
  const data = { vendorid: restaurantId };
  if(authtoken) {
    assign(data, { authtoken });
  }
  return buildBasket(await makeRequest(`/v1.1/baskets/create`, 'POST', data));
}

export async function addBasketProduct(basketId: string, newBasketProduct: OloBasketNewProduct): Promise<OloBasket> {
  const products = { products: [newBasketProduct] };
  const basketProductBatchResult = await makeRequest(`/v1.1/baskets/${basketId}/products/batch`, 'POST', products);
  const errors = basketProductBatchResult.errors;
  if (errors.length > 0) {
    throw new Error(errors[0].message);
  }
  const basketData = basketProductBatchResult.basket;
  return buildBasket(basketData);
}

export async function editBasketProduct(basketId: string, newBasketProduct: OloBasketNewProduct): Promise<OloBasket> {
  const products = { products: [newBasketProduct] };
  const basketProductBatchResult = await makeRequest(`/v1.1/baskets/${basketId}/products/batch`, 'PUT', products);
  const errors = basketProductBatchResult.errors;
  if (errors.length > 0) {
    throw new Error(errors[0].message);
  }
  const basketData = basketProductBatchResult.basket;
  return buildBasket(basketData);
}

export async function deleteBasketProduct(basketId: string, basketProductId: string): Promise<OloBasket> {
  return buildBasket(await makeRequest(`/v1.1/baskets/${basketId}/products/${basketProductId}`, 'DELETE'));
}

export async function validateBasket(basketId: string): Promise<OloBasketValidation> {
  const basketValidationData = await makeRequest(`/v1.1/baskets/${basketId}/validate`, 'POST');
  return {
    basketId: String(basketValidationData.basketid),
    tax: Number(basketValidationData.tax),
    customerhandoffcharge: Number(basketValidationData.customerhandoffcharge),
    subtotal: Number(basketValidationData.subtotal),
    total: Number(basketValidationData.total)
  };
}

export async function setDeliveryMode(basketId: string, deliverymode: string) {
  return buildBasket(await makeRequest(`/v1.1/baskets/${basketId}/deliverymode`, 'PUT', { deliverymode }));
}

export async function setDeliveryAddress(basketId: string, deliveryAddress: OloDeliveryAddress) {
  const data = deliveryAddress.id === '' ? omit(deliveryAddress, ['id']) : deliveryAddress;
  return await makeRequest(`/v1.1/baskets/${basketId}/dispatchaddress`, 'PUT', data);
}

export async function setCustomField(basketId: string, customfield: { id: string, value: string }) {
  return await makeRequest(`/v1.1/baskets/${basketId}/customfields`, 'PUT', customfield);
}

export async function setTimeWanted(basketId: string, date: Date) {
  const timewanted = {
    ismanualfire: false,
    year: date.getFullYear(),
    month: date.getMonth() + 1,
    day: date.getDate(),
    hour: date.getHours(),
    minute: date.getMinutes(),
  };
  return buildBasket(await makeRequest(`/v1.1/baskets/${basketId}/timewanted`, 'PUT', timewanted));
}

export async function deleteTimeWanted(basketId: string) {
  return buildBasket(await makeRequest(`/v1.1/baskets/${basketId}/timewanted`, 'DELETE'));
}

export async function applyCoupon(basketId: string, couponcode: string ) {
  return buildBasket(await makeRequest(`/v1.1/baskets/${basketId}/coupon`, 'PUT', { couponcode }));
}

export async function removeCoupon(basketId: string) {
  return buildBasket(await makeRequest(`/v1.1/baskets/${basketId}/coupon`, 'DELETE'));
}

export async function addTip(basketId: string, amount: number ) {
  return buildBasket(await makeRequest(`/v1.1/baskets/${basketId}/tip`, 'PUT', { amount }));
}

export async function createFromOrder(authtoken: string, orderid: string) {
  const url = `/v1.1/baskets/createfromorder?authtoken=${authtoken}`;
  const data = { id: orderid };
  return buildBasket(await makeRequest(url, 'POST', data));
}

function getUserData(user: OloUser) {
  const userData = { usertype: user.type };
  if(user.type === 'user') {
    assign(userData, { authtoken: user.authtoken });
  }
  if(user.type === 'guest') {
    assign(userData, {
      firstname: user.firstname,
      lastname: user.lastname,
      emailaddress: user.emailaddress,
      contactnumber: user.contactnumber,
      guestoptin: false
    });
  }

  return userData;
}

function getPaymentData(paymentMethod: PaymentMethod, saveonfile: boolean) {

  if(paymentMethod.type === BillingType.CreditCard) {
    const creditcard = paymentMethod.method;
    return {
      billingmethod: 'creditcard',
      creditcardtoken: creditcard.token,
      saveonfile: saveonfile ? 'true' : 'false'
    }
  }
  else if(paymentMethod.type === BillingType.OloBillingAccount) {
    const billingaccount = paymentMethod.method;
    return {
      billingmethod: 'billingaccount',
      billingaccountid: billingaccount.accountid,
    }
  }

  throw new Error("Unexpected error: Invalid payment data");
}

export async function submitBasket(basketid: string, user: OloUser, paymentMethod: PaymentMethod) {
  const userData = getUserData(user);
  const paymentData = getPaymentData(paymentMethod, user.type === 'user');

  const basketSubmitData = {
    basketid
  }
  assign(basketSubmitData, userData, paymentData);

  const url = paymentMethod.type === BillingType.CreditCard ? '/baskets/submit' : `/v1.1/baskets/${basketid}/submit`;
  return buildOrderStatus(await makeRequest(url, 'POST', basketSubmitData));
}