import { OloBillingAccount } from '../OloAPI';

export interface CreditCard {
  fullname: string;
  cardtype: string;
  sufix: string;
  year: number;
  month: number;
  zip: string;
  token: string;
}

export enum BillingType {
  OloBillingAccount,
  CreditCard,
}

export type PaymentMethod =
  | {
      id: string;
      type: BillingType.OloBillingAccount;
      method: OloBillingAccount;
    }
  | {
      id: string;
      type: BillingType.CreditCard;
      method: CreditCard;
    };
