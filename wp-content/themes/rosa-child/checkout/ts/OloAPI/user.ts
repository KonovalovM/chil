import map from 'lodash/map';
import assign from 'lodash/assign';
import memoize from 'memoizee';
import { makeRequest } from './makeRequest';
import { OloOrderStatus, buildOrderStatus } from './orders';

export type OloUser  =
  | {
    emailaddress: string;
    firstname: string;
    lastname: string;
    type: 'user';
    authtoken: string;
    cardsuffix?: string;
  }
  | {
    emailaddress: string;
    firstname: string;
    lastname: string;
    type: 'guest';
    contactnumber: string;
  };

export interface OloBillingAccount {
  accountid: string;
  accounttype: string;
  cardtype?: string;
  cardsuffix?: string;
  description: string;
  removable: boolean;
  expiration?: string;
  isdefault: boolean;
}

export interface OloDeliveryAddress {
  id: string;
  streetaddress: string;
  city: string;
  zipcode: string;
  isdefault: boolean;
  phonenumber?: string;
  building?: string;
  specialinstructions?: string;
}

function buildUser(userData: any): OloUser {
  return {
    emailaddress: userData['emailaddress'] as string,
    firstname: userData['firstname'] as string,
    lastname: userData['lastname'] as string,
    type: 'user' as 'user',
    authtoken: userData['authtoken'] as string,
    cardsuffix: userData['cardsuffix'] ? userData['cardsuffix'] as string : undefined
  };
}

export async function getUser(authtoken: string): Promise<OloUser> {
  const userData = await makeRequest(`/v1.1/users/${authtoken}`, 'GET');
  return buildUser(userData);
}

export const getContactNumber = memoize(
  async (authtoken: string): Promise<string> => {
    const contactDetails = await makeRequest(`/v1.1/users/${authtoken}/contactdetails`, 'GET');
    return contactDetails.contactdetails;
  },
  { promise: true, maxAge: 5 * 60 * 1000 }
);

export async function updateContactNumber(authtoken: string, contactNumber: string): Promise<string> {
  const contactDetails = await makeRequest(`/v1.1/users/${authtoken}/contactdetails`, 'PUT', { contactdetails: contactNumber });
  return contactDetails.contactdetails;
}

export async function changePassword(authtoken: string, currentpassword: string, newpassword: string): Promise<string> {
  const changePasswordPost = await makeRequest(`/v1.1/users/${authtoken}/password`, 'POST', { currentpassword, newpassword });
  return changePasswordPost.newpassword;
}

export async function updateUser(authtoken: string, firstname: string, lastname: string, emailaddress: string): Promise<OloUser> {
  const userData = await makeRequest(`/v1.1/users/${authtoken}`, 'PUT', { firstname, lastname, emailaddress });
  return buildUser(userData);
}

export async function authenticateUser(email: string, password: string, basketid?: string): Promise<OloUser> {
  const data = { login: email, password };
  if(basketid) {
    assign(data, { basketid });
  }
  const response = await makeRequest('/v1.1/users/authenticate', 'POST', data);
  return buildUser(response.user);
}

export async function logoutUser(authtoken: string): Promise<void> {
  await makeRequest(`/v1.1/users/${authtoken}`, 'DELETE');
}

export async function resetUserPassword(email: string) {
  await makeRequest('/v1.1/users/forgotpassword', 'POST', { emailaddress: email });
}

export async function createUser(firstname: string, lastname: string, emailaddress: string, password: string, contactnumber: string): Promise<OloUser> {
  const newUserData = await makeRequest('/v1.1/users/create', 'POST', { firstname, lastname, emailaddress, password, contactnumber });
  return buildUser(newUserData);
}

export async function getDeliveryAddresses(authtoken: string) : Promise<OloDeliveryAddress[]> {
  const response = await makeRequest(`/v1.1/users/${authtoken}/userdeliveryaddresses`, 'GET');
  return map(response.deliveryaddresses, deliveryAddressData => {
    return {
      id: deliveryAddressData.id,
      streetaddress: deliveryAddressData.streetaddress,
      city: deliveryAddressData.city,
      zipcode: deliveryAddressData.zipcode,
      phonenumber: deliveryAddressData.phonenumber,
      isdefault: deliveryAddressData.isdefault,
      building: deliveryAddressData.building,
      specialinstructions: deliveryAddressData.specialinstructions
    };
  });
}

export async function deleteDeliveryAddress(authtoken: string, deliveryAddressId: string) {
  return await makeRequest(`/v1.1/users/${authtoken}/userdeliveryaddresses/${deliveryAddressId}`, 'DELETE');
}

export async function getUserBillingAccounts(authtoken: string): Promise<OloBillingAccount[]> {
  const userbillingaccounts = await makeRequest(`/v1.1/users/${authtoken}/billingaccounts`, 'GET');
  return map(userbillingaccounts.billingaccounts, billingaccount => {
    return {
      accountid: billingaccount.accountid.toString(),
      accounttype: String(billingaccount.accounttype),
      cardtype: billingaccount.cardtype ? String(billingaccount.cardtype) : undefined,
      cardsuffix: billingaccount.cardsuffix ? String(billingaccount.cardsuffix) : undefined,
      description: String(billingaccount.description),
      removable: Boolean(billingaccount.removable),
      expiration: billingaccount.expiration ? String(billingaccount.expiration) : undefined,
      isdefault: Boolean(billingaccount.isdefault),
    };
  });
}

export async function deleteUserBillingAccount(authtoken: string, billingAccountId: string) {
  return makeRequest(`/v1.1/users/${authtoken}/billingaccounts/${billingAccountId}`, 'DELETE');
}

export async function getRecentOrders(authtoken: string): Promise<OloOrderStatus[]> {
  const ordersData = await makeRequest(`/v1.1/users/${authtoken}/recentorders`, 'GET');
  return map(ordersData.orders, buildOrderStatus);
}
