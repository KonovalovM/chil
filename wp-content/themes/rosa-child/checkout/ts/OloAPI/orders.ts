import map from 'lodash/map';
import { makeRequest } from './makeRequest';
import { stringToDate } from './utils';

export interface OloOrderStatusProductChoice {
  name: string;
  quantity: number;
}

export interface OloOrderStatusProduct {
  name: string;
  quantity: number;
  totalcost: number;
  specialinstructions: string;
  custompassthroughdata?: string;
  choices: OloOrderStatusProductChoice[];
}

export interface OloOrderStatus {
  id: string;
  oloid: string;
  vendorid: string;
  status: string;
  subtotal: number;
  salestax: number;
  totalfees: number;
  customerhandoffcharge: number,
  discount: number;
  tip: number;
  total: number;
  deliverymode: string;
  timeplaced: Date;
  readytime: Date;
  vendorname: string;
  products: OloOrderStatusProduct[];
  unavailableproducts: OloOrderStatusProduct[];
}

// tslint:disable-next-line:no-any
export function buildOrderStatus(orderStatusData: any): OloOrderStatus {
  // tslint:disable-next-line:no-any
  const createOrderStatusProduct = (orderStatusProductData: any): OloOrderStatusProduct => {
    return {
      name: String(orderStatusProductData.name),
      quantity: Number(orderStatusProductData.quantity),
      totalcost: Number(orderStatusProductData.totalcost),
      specialinstructions: String(orderStatusProductData.specialinstructions),
      custompassthroughdata: orderStatusProductData.custompassthroughdata ? String(orderStatusProductData.custompassthroughdata) : undefined,
      choices: map(orderStatusProductData.choices, choiceData => {
        return {
          name: String(choiceData.name),
          quantity: Number(choiceData.quantity),
        };
      }),
    };
  };

  return {
    id: String(orderStatusData.id),
    oloid: String(orderStatusData.oloid),
    vendorid: orderStatusData.vendorid.toString(),
    status: String(orderStatusData.status),
    subtotal: Number(orderStatusData.subtotal),
    salestax: Number(orderStatusData.salestax),
    totalfees: Number(orderStatusData.totalfees),
    customerhandoffcharge: Number(orderStatusData.customerhandoffcharge),
    discount: Number(orderStatusData.discount),
    tip: Number(orderStatusData.tip),
    total: Number(orderStatusData.total),
    deliverymode: String(orderStatusData.deliverymode),
    timeplaced: stringToDate(String(orderStatusData.timeplaced)),
    readytime: stringToDate(String(orderStatusData.readytime)),
    vendorname: String(orderStatusData.vendorname),
    products: map(orderStatusData.products, createOrderStatusProduct),
    unavailableproducts: map(orderStatusData.unavailableproducts, createOrderStatusProduct),
  };
}

export async function getOrderStatus(orderId: string) {
  return buildOrderStatus(await makeRequest(`/v1.1/orders/${orderId}`, 'GET'));
}
